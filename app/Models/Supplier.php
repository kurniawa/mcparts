<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }

    public function barangsOrderedByName()
    {
        return $this->hasMany(Barang::class)->orderBy('nama', 'asc');
    }
}
