<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourierPasswordResetController extends Controller
{
    public function requestForm()
    {
        return view('kurir.password-reset-request');
    }

    public function submitRequest(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|max:50',
        ]);

        DB::transaction(function () use ($data): void {
            $courier = DB::table('petugas')
                ->where('username', trim($data['username']))
                ->where('role', 'Kurir')
                ->lockForUpdate()
                ->first();

            if (! $courier || $courier->password_reset_status === 'Menunggu Verifikasi') {
                return;
            }

            DB::table('petugas')
                ->where('id_petugas', $courier->id_petugas)
                ->update([
                    'password_reset_requested_at' => now(),
                    'password_reset_status' => 'Menunggu Verifikasi',
                ]);
        });

        return redirect()->route('kurir.password-reset.request')
            ->with('success', 'Jika username tersebut terdaftar sebagai Kurir, permintaan akan diteruskan kepada Admin untuk verifikasi.');
    }
}
