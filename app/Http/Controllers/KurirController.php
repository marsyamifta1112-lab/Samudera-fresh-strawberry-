<?php

namespace App\Http\Controllers;

use App\Support\OrderMetadata;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;

class KurirController extends Controller
{
    public function forcePasswordChangeForm()
    {
        abort_unless((bool) session('must_change_password', false), 404);

        return view('kurir.password-force-change');
    }

    public function updateTemporaryPassword(Request $request)
    {
        abort_unless((bool) session('must_change_password', false), 404);

        $data = $request->validate([
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $updated = DB::table('petugas')
            ->where('id_petugas', session('id_petugas'))
            ->where('role', 'Kurir')
            ->where('must_change_password', true)
            ->update([
                'password' => Hash::make($data['password']),
                'must_change_password' => false,
            ]);

        abort_if(! $updated, 403);

        $request->session()->put('must_change_password', false);

        return redirect()->route('kurir.dashboard')
            ->with('success', 'Password berhasil diperbarui.');
    }

    public function profile()
    {
        $petugas = $this->currentCourier();

        return view('kurir.profile', compact('petugas'));
    }

    public function updateProfile(Request $request)
    {
        $petugas = $this->currentCourier();
        $data = $request->validate([
            'nama_petugas' => 'required|string|max:100',
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('petugas', 'username')->ignore($petugas->id_petugas, 'id_petugas'),
            ],
            'no_telp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        DB::table('petugas')
            ->where('id_petugas', $petugas->id_petugas)
            ->where('role', 'Kurir')
            ->update($data);

        session(['nama_petugas' => $data['nama_petugas']]);

        return redirect()
            ->route('kurir.profile')
            ->with('success', 'Profil kurir berhasil diperbarui.');
    }

    public function dashboard()
    {
        $pengiriman = $this->shipmentQuery()
            ->orderByDesc('pg.id_pengiriman')
            ->get()
            ->map($this->restoreCustomerNote(...));

        $ringkasan = [
            'hari_ini' => $pengiriman->filter(
                fn ($item) => $item->tanggal_pengiriman
                    && Carbon::parse($item->tanggal_pengiriman)->isToday()
            )->count(),
            'dikirim' => $pengiriman->where('status_pengiriman', 'dikirim')->count(),
            'diterima' => $pengiriman->where('status_pengiriman', 'diterima')->count(),
        ];

        $tujuan = $pengiriman
            ->whereNotIn('status_pengiriman', ['diterima'])
            ->map(fn ($item) => $this->destinationFromAddress($item->alamat_pengiriman))
            ->filter()
            ->unique()
            ->take(5);

        return view('kurir.dashboard', compact('ringkasan', 'tujuan'));
    }

    public function shipments()
    {
        $pengiriman = $this->shipmentQuery()
            ->orderByRaw("CASE status_pengiriman WHEN 'menunggu' THEN 1 WHEN 'diproses' THEN 2 WHEN 'dikirim' THEN 3 ELSE 4 END")
            ->orderByDesc('pg.id_pengiriman')
            ->paginate(10);
        $pengiriman->getCollection()->transform($this->restoreCustomerNote(...));

        return view('kurir.shipments', compact('pengiriman'));
    }

    public function show($id)
    {
        $pengiriman = $this->shipmentQuery()
            ->where('pg.id_pengiriman', $id)
            ->firstOrFail();
        $pengiriman = $this->restoreCustomerNote($pengiriman);

        return view('kurir.detail', compact('pengiriman'));
    }

    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status_pengiriman' => ['required', 'in:diproses,dikirim,diterima'],
        ]);

        $pengiriman = DB::transaction(function () use ($id, $data) {
            $shipment = DB::table('pengiriman')
                ->where('id_pengiriman', $id)
                ->where('id_petugas', session('id_petugas'))
                ->lockForUpdate()
                ->first();

            abort_if(! $shipment, 404);

            $transitions = [
                'menunggu' => ['diproses'],
                'diproses' => ['dikirim'],
                'dikirim' => ['diterima'],
                'diterima' => [],
            ];

            if (! in_array($data['status_pengiriman'], $transitions[$shipment->status_pengiriman] ?? [], true)) {
                return null;
            }

            DB::table('pengiriman')
                ->where('id_pengiriman', $shipment->id_pengiriman)
                ->update([
                    'status_pengiriman' => $data['status_pengiriman'],
                    'tanggal_pengiriman' => in_array($data['status_pengiriman'], ['dikirim', 'diterima'], true)
                        ? ($shipment->tanggal_pengiriman ?? now())
                        : $shipment->tanggal_pengiriman,
                ]);

            if ($data['status_pengiriman'] === 'diterima') {
                DB::table('pesanan')
                    ->where('id_pesanan', $shipment->id_pesanan)
                    ->update(['status_pesanan' => 'selesai']);
            } elseif (in_array($data['status_pengiriman'], ['diproses', 'dikirim'], true)) {
                DB::table('pesanan')
                    ->where('id_pesanan', $shipment->id_pesanan)
                    ->where('status_pesanan', 'menunggu')
                    ->update(['status_pesanan' => 'diproses']);
            }

            return $shipment;
        });

        if (! $pengiriman) {
            return back()->with('error', 'Status pengiriman tidak dapat diubah dari tahap saat ini.');
        }

        if ($data['status_pengiriman'] === 'diterima') {
            return redirect()
                ->route('kurir.confirmation', $id)
                ->with('success', 'Pesanan berhasil dikonfirmasi telah diterima.');
        }

        return redirect()
            ->route('kurir.shipments.show', $id)
            ->with('success', 'Status pengiriman berhasil diperbarui.');
    }

    public function confirmation($id)
    {
        $pengiriman = $this->shipmentQuery()
            ->where('pg.id_pengiriman', $id)
            ->where('pg.status_pengiriman', 'diterima')
            ->firstOrFail();
        $pengiriman = $this->restoreCustomerNote($pengiriman);

        return view('kurir.confirmation', compact('pengiriman'));
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Kamu berhasil keluar dari akun.');
    }

    private function shipmentQuery()
    {
        return DB::table('pengiriman as pg')
            ->join('pesanan as p', 'p.id_pesanan', '=', 'pg.id_pesanan')
            ->where('pg.id_petugas', session('id_petugas'))
            ->select(
                'pg.id_pengiriman',
                'pg.id_pesanan',
                'pg.nama_penerima',
                'pg.no_telp_penerima',
                'pg.alamat_pengiriman',
                'pg.status_pengiriman',
                'pg.tanggal_pengiriman',
                'p.total_harga',
                'p.catatan',
                'p.tanggal_pemesanan'
            );
    }

    private function currentCourier(): object
    {
        $petugas = DB::table('petugas')
            ->where('id_petugas', session('id_petugas'))
            ->where('role', 'Kurir')
            ->first();

        abort_if(! $petugas, 404);

        return $petugas;
    }

    private function destinationFromAddress(?string $address): ?string
    {
        if (! $address) {
            return null;
        }

        $parts = array_filter(array_map('trim', explode(',', $address)));

        return $parts ? (string) end($parts) : null;
    }

    private function restoreCustomerNote(object $shipment): object
    {
        $shipment->catatan = OrderMetadata::customerNote($shipment->catatan);

        return $shipment;
    }
}
