<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    function profile() {
        $user = Auth::user();
        $profile_picture = $user->profile_picture;
        $data = [
            // 'goback' => 'home',
            // 'user_role' => $user_role,
            'menus' => Menu::get(),
            'route_now' => 'home',
            'profile_menus' => Menu::get_profile_menus(),
            'profile_picture' => $profile_picture,
        ];
        return view('user.profile', $data);
    }

    function update_nama(Request $request) {
        $post = $request->post();
        dd($post);
        return back();
    }
}
