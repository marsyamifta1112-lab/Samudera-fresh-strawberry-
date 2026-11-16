<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    public $timestamps = false;
    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'harga_produk',
        'stok_produk',
        'foto_produk',
        'berat_gram',
        'is_available',
    ];
    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_produk');
    }
}