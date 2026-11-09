<?php

namespace App\Http\Controllers;

use App\Support\CustomerEmailVerificationLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Throwable;

class AuthController extends Controller
{
    /* =========================
       LOGIN
    ========================= */

    public function showLogin()
    {
        return view('login');
    }

    /* =========================
       REGISTER - TAMPILAN
    ========================= */

    public function showRegister()
    {
        return view('register');
    }

    /* =========================
       REGISTER - PROSES
    ========================= */

    public function register(Request $request)
    {
        // Validasi data register
        $request->validate([
            'nama_pelanggan' => 'required',
            'email' => 'nullable|email|max:100',
            'username' => 'required',
            'password' => 'required|min:6',
        ]);

        // Cek username
        $usernameExists = DB::table('pelanggan')
            ->where('username', $request->username)
            ->exists();

        if ($usernameExists) {
            return back()
                ->withInput()
                ->with('error', 'Username sudah digunakan.');
        }

        // Cek email
        $email = $request->filled('email') ? trim($request->input('email')) : null;
        $emailExists = $email !== null
            && DB::table('pelanggan')->where('email', $email)->exists();

        if ($emailExists) {
            return back()
                ->withInput()
                ->with('error', 'Email sudah digunakan.');
        }

        $customerId = DB::table('pelanggan')->insertGetId([
            'nama_pelanggan' => $request->nama_pelanggan,
            'email' => $email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ], 'id_pelanggan');

        if ($email !== null) {
            try {
                CustomerEmailVerificationLink::send(
                    (int) $customerId,
                    $request->nama_pelanggan,
                    $email
                );
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        return redirect('/login')
            ->with('success', $email !== null
                ? 'Pendaftaran berhasil. Jika email dapat digunakan, tautan verifikasi telah dikirim.'
                : 'Pendaftaran berhasil. Setelah login, terbitkan kode pemulihan dari Profil agar dapat digunakan jika lupa password.');
    }

    /* =========================
       LOGIN - PROSES
    ========================= */

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string',
        ]);

        $identifier = trim($data['username']);
        $accounts = [];

        foreach (DB::table('pelanggan')
            ->where('username', $identifier)
            ->orWhere('email', $identifier)
            ->get() as $pelanggan) {
            $accounts[] = ['type' => 'Pelanggan', 'account' => $pelanggan];
        }

        foreach (DB::table('petugas')->where('username', $identifier)->get() as $petugas) {
            $accounts[] = ['type' => 'petugas', 'account' => $petugas];
        }

        $matches = array_values(array_filter(
            $accounts,
            fn (array $candidate) => is_string($candidate['account']->password)
                && Hash::check($data['password'], $candidate['account']->password)
        ));

        if (count($matches) !== 1) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Username/email atau password salah.');
        }

        $authenticated = $matches[0];
        $account = $authenticated['account'];

        if ($authenticated['type'] === 'petugas' && $account->status_akun !== 'Aktif') {
            return back()
                ->withInput($request->only('username'))
                ->with('error', $account->role === 'Kurir'
                    ? 'Akun Kurir Anda masih menunggu persetujuan Admin.'
                    : 'Username/email atau password salah.');
        }

        $request->session()->forget([
            'id_pelanggan',
            'nama_pelanggan',
            'id_petugas',
            'nama_petugas',
            'role',
            'must_change_password',
        ]);
        $request->session()->regenerate();

        if ($authenticated['type'] === 'Pelanggan') {
            $request->session()->put([
                'id_pelanggan' => $account->id_pelanggan,
                'nama_pelanggan' => $account->nama_pelanggan,
                'role' => 'Pelanggan',
            ]);

            return redirect('/onboarding');
        }

        if (! in_array($account->role, ['Admin', 'Kurir'], true)) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Username/email atau password salah.');
        }

        $request->session()->put([
            'id_petugas' => $account->id_petugas,
            'nama_petugas' => $account->nama_petugas,
            'role' => $account->role,
            'must_change_password' => $account->role === 'Kurir' && (bool) ($account->must_change_password ?? false),
        ]);

        if ($account->role === 'Kurir' && (bool) ($account->must_change_password ?? false)) {
            return redirect()->route('kurir.password.change');
        }

        return $account->role === 'Admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('kurir.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Kamu berhasil keluar dari akun.');
    }

    public function showRegisterKurir()
    {
        return view('kurir-register');
    }

    public function registerKurir(Request $request)
    {
        $data = $request->validate([
            'nama_petugas' => 'required|string|max:100',
            'username' => ['required', 'string', 'max:50', Rule::unique('petugas', 'username')],
            'password' => 'required|string|min:8|confirmed',
            'no_telp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:1000',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'Kurir';
        $data['status_akun'] = 'Menunggu Persetujuan';
        DB::table('petugas')->insert($data);

        return redirect()
            ->route('kurir.login')
            ->with('success', 'Permohonan akun berhasil dikirim. Silakan tunggu persetujuan Admin.');
    }
}
