<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Menu;
use App\Models\Spk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    function home() {
        $user_role = Auth::user()->role;
        $spks = Spk::latest()->limit(5)->get();
        $data = [
            // 'goback' => 'home',
            'user_role' => $user_role,
            'menus' => Menu::get(),
            'route_now' => 'home',
            'profile_menus' => Menu::get_profile_menus(),
        ];
        // dump($user_role);
        // dump($spks);
        // dd($data);
        return view('app', $data);
    }

    function info() {
        $data = [
            'goback' => 'home',
        ];
        return view('about.index', $data);
    }
}
