<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    function index(Request $request) {
        $get = $request->query();
        // dd($get);
        $data = [
            // 'goback' => 'home',
            // 'user_role' => $user_role,
            'menus' => Menu::get(),
            'route_now' => 'pelanggans.index',
            'profile_menus' => Menu::get_profile_menus(),
        ];
        if (isset($get['nama_pelanggan'])) {
            if ($get['nama_pelanggan'] !== null) {
                $pelanggans = Pelanggan::where();

                return view('pelanggans.index', $data);
            }
        }

        $pelanggans = Pelanggan::orderBy('nama')->get();
        $data += ['pelanggans' => $pelanggans];
        dd($data);

        return view('pelanggans.index', $data);
    }
}
