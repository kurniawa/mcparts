<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Ekspedisi;
use App\Models\EkspedisiAlamat;
use App\Models\EkspedisiKontak;
use App\Models\Menu;
use Illuminate\Http\Request;

class EkspedisiController extends Controller
{
    function index(Request $request) {
        $get = $request->query();
        // dd($get);

        $ekspedisis = Ekspedisi::orderBy('nama')->get();

        if (isset($get['ekspedisi_nama'])) {
            if ($get['ekspedisi_nama'] !== null) {
                $ekspedisis = Ekspedisi::where('nama','LIKE', "%$get[ekspedisi_nama]%")->orderBy('nama')->get();
            }
        }

        // RESLLER_ALAMAT_KONTAK
        $alamats = collect();
        $ekspedisi_kontaks = collect();
        foreach ($ekspedisis as $ekspedisi) {
            $ekspedisi_alamat = EkspedisiAlamat::where('ekspedisi_id', $ekspedisi->id)->where('tipe', 'UTAMA')->first();
            $alamat = Alamat::find($ekspedisi_alamat->alamat_id);
            $ekspedisi_kontak = EkspedisiKontak::where('ekspedisi_id', $ekspedisi->id)->where('is_aktual', 'yes')->first();
            $alamats->push($alamat);
            $ekspedisi_kontaks->push($ekspedisi_kontak);
        }
        // END - ALAMAT_KONTAK

        $data = [
            // 'goback' => 'home',
            // 'user_role' => $user_role,
            'menus' => Menu::get(),
            'route_now' => 'ekspedisis.index',
            'profile_menus' => Menu::get_profile_menus(),
            'ekspedisis' => $ekspedisis,
            'alamats' => $alamats,
            'ekspedisi_kontaks' => $ekspedisi_kontaks,
        ];

        // dd($data);

        return view('ekspedisis.index', $data);
    }

    function show(Ekspedisi $ekspedisi) {
        // dd($ekspedisi);
        $ekspedisi_alamats = EkspedisiAlamat::where('ekspedisi_id', $ekspedisi->id)->get();
        $alamats = collect();
        foreach ($ekspedisi_alamats as $ekspedisi_alamat) {
            $alamat = Alamat::find($ekspedisi_alamat->alamat_id);
            $alamats->push($alamat);
        }
        $ekspedisi_kontaks = ekspedisiKontak::where('ekspedisi_id', $ekspedisi->id)->get();

        $label_ekspedisis = Ekspedisi::select('id', 'nama as label', 'nama as value')->get();

        $data = [
            // 'goback' => 'home',
            // 'user_role' => $user_role,
            'menus' => Menu::get(),
            'route_now' => 'ekspedisis.show',
            'profile_menus' => Menu::get_profile_menus(),
            'ekspedisi' => $ekspedisi,
            'ekspedisi_alamats' => $ekspedisi_alamats,
            'alamats' => $alamats,
            'ekspedisi_kontaks' => $ekspedisi_kontaks,
        ];
        // dd($alamats);
        // dd($alamat_ekspedisis);
        return view('ekspedisis.show', $data);
    }
}
