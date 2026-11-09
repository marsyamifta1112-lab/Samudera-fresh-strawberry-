<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';

    protected $primaryKey = 'id_pelanggan';

    public $timestamps = false;

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_pelanggan');
    }
}
