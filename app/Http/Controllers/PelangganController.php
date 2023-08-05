<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Ekspedisi;
use App\Models\EkspedisiAlamat;
use App\Models\Menu;
use App\Models\Pelanggan;
use App\Models\PelangganAlamat;
use App\Models\PelangganEkspedisi;
use App\Models\PelangganKontak;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    function index(Request $request) {
        $get = $request->query();
        // dd($get);

        $pelanggans = Pelanggan::orderBy('nama')->get();

        if (isset($get['nama_pelanggan'])) {
            if ($get['nama_pelanggan'] !== null) {
                $pelanggans = Pelanggan::where('nama','LIKE', "%$get[nama_pelanggan]%")->orderBy('nama')->get();
            }
        }

        // RESLLER_ALAMAT_KONTAK
        $resellers = collect();
        $alamats = collect();
        $pelanggan_kontaks = collect();
        foreach ($pelanggans as $pelanggan) {
            $reseller = null;
            if ($pelanggan->reseller_id !== null) {
                $reseller = Pelanggan::find($pelanggan->reseller_id);
            }
            $resellers->push($reseller);
            $pelanggan_alamat = PelangganAlamat::where('pelanggan_id', $pelanggan->id)->where('tipe', 'UTAMA')->first();
            $alamat = Alamat::find($pelanggan_alamat->alamat_id);
            $pelanggan_kontak = PelangganKontak::where('pelanggan_id', $pelanggan->id)->where('is_aktual', 'yes')->first();
            $alamats->push($alamat);
            $pelanggan_kontaks->push($pelanggan_kontak);
        }
        // END - ALAMAT_KONTAK

        $data = [
            // 'goback' => 'home',
            // 'user_role' => $user_role,
            'menus' => Menu::get(),
            'route_now' => 'pelanggans.index',
            'profile_menus' => Menu::get_profile_menus(),
            'pelanggans' => $pelanggans,
            'alamats' => $alamats,
            'pelanggan_kontaks' => $pelanggan_kontaks,
            'resellers' => $resellers,
        ];

        // dd($data);

        return view('pelanggans.index', $data);
    }

    function show(Pelanggan $pelanggan) {
        // dd($pelanggan);
        $reseller = null;
        if ($pelanggan->reseller_id !== null) {
            $reseller = Pelanggan::find($pelanggan->reseller_id);
        }
        $pelanggan_alamats = PelangganAlamat::where('pelanggan_id', $pelanggan->id)->get();
        $alamats = collect();
        foreach ($pelanggan_alamats as $pelanggan_alamat) {
            $alamat = Alamat::find($pelanggan_alamat->alamat_id);
            $alamats->push($alamat);
        }
        $pelanggan_kontaks = PelangganKontak::where('pelanggan_id', $pelanggan->id)->get();

        $pelanggan_ekspedisis = PelangganEkspedisi::where('pelanggan_id', $pelanggan->id)->where('is_transit', 'no')->get();
        $ekspedisis = collect();
        $alamat_ekspedisis = collect();
        foreach ($pelanggan_ekspedisis as $pelanggan_ekspedisi) {
            $ekspedisi = Ekspedisi::find($pelanggan_ekspedisi->ekspedisi_id);
            $ekspedisis->push($ekspedisi);

            $ekspedisi_alamat = EkspedisiAlamat::where('ekspedisi_id', $ekspedisi->id)->where('tipe', 'UTAMA')->first();
            $alamat_ekspedisis->push(Alamat::find($ekspedisi_alamat->alamat_id));
        }

        $pelanggan_transits = PelangganEkspedisi::where('pelanggan_id', $pelanggan->id)->where('is_transit', 'yes')->get();
        $transits = collect();
        $alamat_transits = collect();
        foreach ($pelanggan_transits as $pelanggan_transit) {
            $transit = Ekspedisi::find($pelanggan_transit->ekspedisi_id);
            $transits->push($transit);

            $transit_alamats = EkspedisiAlamat::where('ekspedisi_id', $transit->id)->get();
            $alamat_transits_2 = collect();
            foreach ($transit_alamats as $transit_alamat) {
                $alamat_ekspedisi = Alamat::find($transit_alamat->alamat_id);
                $alamat_transits_2->push($alamat_ekspedisi);
            }
            $alamat_transits->push($alamat_transits_2);
        }
        $data = [
            // 'goback' => 'home',
            // 'user_role' => $user_role,
            'menus' => Menu::get(),
            'route_now' => 'pelanggans.show',
            'profile_menus' => Menu::get_profile_menus(),
            'pelanggan' => $pelanggan,
            'pelanggan_alamats' => $pelanggan_alamats,
            'alamats' => $alamats,
            'pelanggan_kontaks' => $pelanggan_kontaks,
            'reseller' => $reseller,
            'pelanggan_ekspedisis' => $pelanggan_ekspedisis,
            'ekspedisis' => $ekspedisis,
            'pelanggan_transits' => $pelanggan_transits,
            'transits' => $transits,
            'alamat_ekspedisis' => $alamat_ekspedisis,
            'alamat_transits' => $alamat_transits,
        ];
        dd($alamat_ekspedisis);
        return view('pelanggans.show', $data);
    }
}
