<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    public $timestamps = false;

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_produk');
    }
}