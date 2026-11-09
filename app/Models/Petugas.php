<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    protected $table = 'petugas';
    protected $primaryKey = 'id_petugas';
    public $timestamps = false;

    public function pengiriman()
    {
        return $this->hasMany(Pengiriman::class, 'id_petugas');
    }
}