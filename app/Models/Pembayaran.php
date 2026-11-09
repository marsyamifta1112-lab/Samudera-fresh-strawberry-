<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';
    public $timestamps = false;

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }
}