<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    function index(Request $request) {
        $get = $request->query();
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'pembelians.index',
            'profile_menus' => Menu::get_profile_menus(),
        ];
        return view('pembelians.index', $data);
    }
}
