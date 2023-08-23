<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accounting extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    static function get_instances_types() {
        return [
            'safe',
            'bank',
            'e-wallet',
        ];
    }

    static function get_instances_names() {
        return [
            'BCA',
            'BRI',
            'BNI',
            'Mandiri',
            'Danamon',
            'Maybank',
            'GoPay',
            'OVO',
            'ShopeePay',
            'storage',
        ];
    }
}
