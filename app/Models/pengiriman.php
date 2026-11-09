<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    protected $table = 'pengiriman';
    protected $primaryKey = 'id_pengiriman';
    public $timestamps = false;

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'id_petugas');
    }
}