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
            $alamat_ekspedisi = null;
            if ($ekspedisi_alamat !== null) {
                $alamat_ekspedisi = Alamat::find($ekspedisi_alamat->alamat_id);
            }
            $alamat_ekspedisis->push($alamat_ekspedisi);
        }

        $pelanggan_transits = PelangganEkspedisi::where('pelanggan_id', $pelanggan->id)->where('is_transit', 'yes')->get();
        $transits = collect();
        $alamat_transits = collect();
        foreach ($pelanggan_transits as $pelanggan_transit) {
            $transit = Ekspedisi::find($pelanggan_transit->ekspedisi_id);
            $transits->push($transit);

            $transit_alamat = EkspedisiAlamat::where('ekspedisi_id', $transit->id)->where('tipe', 'UTAMA')->first();
            $alamat_transit = null;
            if ($transit_alamat !== null) {
                $alamat_transit = Alamat::find($transit_alamat->alamat_id);
            }
            $alamat_transits->push($alamat_transit);
        }

        $label_ekspedisis = Ekspedisi::select('id', 'nama as label', 'nama as value')->get();

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
            'label_ekspedisis' => $label_ekspedisis,
        ];
        // dd($alamats);
        // dd($alamat_ekspedisis);
        return view('pelanggans.show', $data);
    }

    function alamat_add(Pelanggan $pelanggan) {
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'pelanggans.show',
            'profile_menus' => Menu::get_profile_menus(),
            'pelanggan' => $pelanggan,
        ];
        return view('pelanggans.alamat_add', $data);
    }

    function alamat_add_post(Pelanggan $pelanggan, Request $request) {
        $post = $request->post();
        // dump($post);

        $request->validate([
            'short' => 'required',
            'long' => 'required',
        ]);
        // dump($post['long']);
        $post['long'] = json_encode(preg_split("/\r\n|\n|\r/", $post['long']));
        // dd($post['long']);
        $success_ = '';

        $alamat_new = Alamat::create([
            'jalan' => $post['jalan'],
            'komplek' => $post['komplek'],
            'rt' => $post['rt'],
            'rw' => $post['rw'],
            'desa' => $post['desa'],
            'kelurahan' => $post['kelurahan'],
            'kecamatan' => $post['kecamatan'],
            'kota' => $post['kota'],
            'kodepos' => $post['kodepos'],
            'kabupaten' => $post['kabupaten'],
            'provinsi' => $post['provinsi'],
            'pulau' => $post['pulau'],
            'negara' => $post['negara'],
            'short' => $post['short'],
            'long' => $post['long'],
        ]);
        $success_ .= '-alamat created-';

        PelangganAlamat::create([
            'pelanggan_id' => $pelanggan->id,
            'alamat_id' => $alamat_new->id,
        ]);
        $success_ .= '-pelanggan_alamat created-';

        $feedback = [
            'success_' => $success_
        ];

        return redirect(route('pelanggans.show', $pelanggan->id))->with($feedback);
    }

    function delete_alamat(Alamat $alamat) {
        $alamat->delete();
        return back()->with('danger_', '-alamat deleted!-');
    }

    function alamat_edit(Pelanggan $pelanggan, Alamat $alamat) {
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'pelanggans.show',
            'profile_menus' => Menu::get_profile_menus(),
            'pelanggan' => $pelanggan,
            'alamat' => $alamat,
        ];
        // dd($alamat->long);
        // dd(json_decode($alamat->long, true));
        return view('pelanggans.alamat_edit', $data);
    }

    function alamat_edit_post(Pelanggan $pelanggan, Alamat $alamat, Request $request) {
        $post = $request->post();

        $request->validate([
            'short' => 'required',
            'long' => 'required',
        ]);
        $post['long'] = json_encode(preg_split("/\r\n|\n|\r/", $post['long']));

        $success_ = '';
        $alamat->update([
            'jalan' => $post['jalan'],
            'komplek' => $post['komplek'],
            'rt' => $post['rt'],
            'rw' => $post['rw'],
            'desa' => $post['desa'],
            'kelurahan' => $post['kelurahan'],
            'kecamatan' => $post['kecamatan'],
            'kota' => $post['kota'],
            'kodepos' => $post['kodepos'],
            'kabupaten' => $post['kabupaten'],
            'provinsi' => $post['provinsi'],
            'pulau' => $post['pulau'],
            'negara' => $post['negara'],
            'short' => $post['short'],
            'long' => $post['long'],
        ]);
        $success_ .= '-alamat updated-';

        $feedback = [
            'success_' => $success_
        ];
        return redirect()->route('pelanggans.show', $pelanggan->id)->with($feedback);
    }

    function alamat_utama(Pelanggan $pelanggan, Alamat $alamat) {
        $success_ = '';
        $pelanggan_alamats = PelangganAlamat::where('pelanggan_id', $pelanggan->id)->get();
        foreach ($pelanggan_alamats as $pelanggan_alamat) {
            $pelanggan_alamat->tipe = 'CADANGAN';
            $pelanggan_alamat->save();
            $success_ .= '-CADANGAN-';
        }
        $pelanggan_alamat = PelangganAlamat::where('pelanggan_id', $pelanggan->id)->where('alamat_id', $alamat->id)->first();
        $pelanggan_alamat->tipe = 'UTAMA';
        $pelanggan_alamat->save();
        $success_ .= '-UTAMA-';

        $feedback = [
            'success_' => $success_,
        ];
        return back()->with($feedback);
    }

    function kontak_add(Pelanggan $pelanggan, Request $request) {
        $post = $request->post();

        $success_ = '';

        $request->validate([
            'tipe'=>'required',
            'nomor'=>'required',
        ]);

        PelangganKontak::create([
            'pelanggan_id' => $pelanggan->id,
            'tipe' => $post['tipe'],
            'kodearea' => $post['kodearea'],
            'nomor' => $post['nomor'],
        ]);
        $success_ .= '-pelanggan_kontak created-';

        $feedback = [
            'success_' => $success_,
        ];
        return back()->with($feedback);
    }

    function kontak_delete(PelangganKontak $pelanggan_kontak) {
        // dd($pelanggan_kontak);
        $pelanggan_kontak->delete();
        return back()->with('danger_', '-kontak deleted!-');
    }

    function kontak_edit(PelangganKontak $pelanggan_kontak, Request $request) {
        $post = $request->post();

        $pelanggan_kontak->update([
            'pelanggan_id' => $pelanggan_kontak->pelanggan_id,
            'tipe' => $post['tipe'],
            'kodearea' => $post['kodearea'],
            'nomor' => $post['nomor'],
        ]);

        return back()->with('success_', '-kontak edited.-');
    }

    function kontak_utama(Pelanggan $pelanggan, PelangganKontak $pelanggan_kontak) {
        $pelanggan_kontaks = PelangganKontak::where('pelanggan_id', $pelanggan->id)->get();
        foreach ($pelanggan_kontaks as $element) {
            $element->is_aktual = 'no';
            $element->save();
        }
        $pelanggan_kontak->is_aktual = 'yes';
        $pelanggan_kontak->save();
        return back()->with('success_', '-kontak utama updated.-');
    }

    function ekspedisi_add(Pelanggan $pelanggan, Request $request) {
        $post = $request->post();

        $cek_pelanggan_ekspedisi_sama = PelangganEkspedisi::where('pelanggan_id', $pelanggan->id)->where('ekspedisi_id', $post['ekspedisi_id'])->where('is_transit', 'no')->first();

        if ($cek_pelanggan_ekspedisi_sama !== null) {
            $request->validate(['error'=>'required'],['error.required'=>'ekspedisi sudah ada']);
        }

        PelangganEkspedisi::create([
            'pelanggan_id' => $pelanggan->id,
            'ekspedisi_id' => $post['ekspedisi_id'],
            'is_transit' => 'no',
            'tipe' => 'CADANGAN'
        ]);

        return back()->with('success_', 'pelanggan_ekspedisi created.');
    }

    function ekspedisi_delete(PelangganEkspedisi $pelanggan_ekspedisi) {
        // $ekspedisi = Ekspedisi::find($pelanggan_ekspedisi->ekspedisi_id);
        // dd($ekspedisi);
        $pelanggan_ekspedisi->delete();
        return back()->with('danger_', 'pelanggan_ekspedisi deleted!');
    }

    function ekspedisi_utama(Pelanggan $pelanggan, PelangganEkspedisi $pelanggan_ekspedisi) {
        $success_ = '';
        $pelanggan_ekspedisis = PelangganEkspedisi::where('pelanggan_id', $pelanggan->id)->where('is_transit','no')->get();
        foreach ($pelanggan_ekspedisis as $element) {
            $element->tipe = 'CADANGAN';
            $element->save();
            $success_ .= '-CADANGAN-';
        }

        $pelanggan_ekspedisi->tipe = 'UTAMA';
        $pelanggan_ekspedisi->save();
        $success_ .= '-UTAMA-';

        $feedback = [
            'success_' => $success_,
        ];

        return back()->with($feedback);

    }

    function transit_add(Pelanggan $pelanggan, Request $request) {
        $post = $request->post();

        $cek_pelanggan_transit_sama = PelangganEkspedisi::where('pelanggan_id', $pelanggan->id)->where('ekspedisi_id', $post['transit_id'])->where('is_transit', 'yes')->first();

        if ($cek_pelanggan_transit_sama !== null) {
            $request->validate(['error'=>'required'],['error.required'=>'transit sudah ada']);
        }

        PelangganEkspedisi::create([
            'pelanggan_id' => $pelanggan->id,
            'ekspedisi_id' => $post['transit_id'],
            'is_transit' => 'yes',
            'tipe' => 'CADANGAN'
        ]);

        return back()->with('success_', 'pelanggan_ekspedisi created.');
    }

    function transit_delete(PelangganEkspedisi $pelanggan_ekspedisi) {
        // $ekspedisi = Ekspedisi::find($pelanggan_ekspedisi->ekspedisi_id);
        // dd($ekspedisi);
        $pelanggan_ekspedisi->delete();
        return back()->with('danger_', 'pelanggan_ekspedisi deleted!');
    }

    function transit_utama(Pelanggan $pelanggan, PelangganEkspedisi $pelanggan_ekspedisi) {
        $success_ = '';
        // dump($pelanggan);
        // dd($pelanggan_ekspedisi);
        $pelanggan_transits = PelangganEkspedisi::where('pelanggan_id', $pelanggan->id)->where('is_transit','yes')->get();
        foreach ($pelanggan_transits as $element) {
            $element->tipe = 'CADANGAN';
            $element->save();
            $success_ .= '-CADANGAN-';
        }

        $pelanggan_ekspedisi->tipe = 'UTAMA';
        $pelanggan_ekspedisi->save();
        $success_ .= '-UTAMA-';

        $feedback = [
            'success_' => $success_,
        ];

        return back()->with($feedback);

    }
}
