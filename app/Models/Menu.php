<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public static function get() {
        $menus = collect([
            // ['name'=>'SPK','route'=>'spks.index'],
            // ['name'=>'Nota','route'=>'notas.index'],
            // ['name'=>'SJ','route'=>'sjs.index'],
            ['name'=>'Accounting','route'=>'accounting.index'],
            ['name'=>'Artisan','route'=>'artisan.index'],
        ]);
        // $menus = [
        //     ['name'=>'SPK','route'=>'spks'],
        //     ['name'=>'Nota','route'=>'notas'],
        //     ['name'=>'SJ','route'=>'srjalans'],
        // ];

        return $menus;
    }

    public static function get_profile_menus() {
        $menus = collect([
            ['name'=>'Your Profile','route'=>'user.profile'],
            ['name'=>'Settings','route'=>'settings'],
            ['name'=>'Log Out','route'=>'logout'],
        ]);

        return $menus;
    }
}
