<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Ekspedisi;
use App\Models\EkspedisiAlamat;
use App\Models\EkspedisiKontak;
use App\Models\Menu;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $alamats = collect();
        $ekspedisi_kontaks = collect();
        foreach ($ekspedisis as $ekspedisi) {
            $ekspedisi_alamat = EkspedisiAlamat::where('ekspedisi_id', $ekspedisi->id)->where('tipe', 'UTAMA')->first();
            $alamat = null;
            if ($ekspedisi_alamat !== null) {
                $alamat = Alamat::find($ekspedisi_alamat->alamat_id);
            }
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
            'parent_route' => 'home',
            'spk_menus' => Menu::get_spk_menus(),
            'ekspedisis' => $ekspedisis,
            'alamats' => $alamats,
            'ekspedisi_kontaks' => $ekspedisi_kontaks,
            'bentuks' => Pelanggan::bentuks(),
            'tipe_kontaks' => Alamat::tipe_kontaks(),
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
            'tipe_kontaks' => Alamat::tipe_kontaks(),
        ];
        // dd($alamats);
        // dd($alamat_ekspedisis);
        return view('ekspedisis.show', $data);
    }

    function store(Request $request) {
        $post = $request->post();
        // dd($post);
        // VALIDASI
        // VALIDASI DATA EKSPEDISI
        $request->validate(['nama'=>'required']);
        // VALIDASI KONTAK
        if ($post['tipe'] !== null) {
            $request->validate(['nomor'=>'required']);
        }elseif ($post['nomor'] !== null) {
            $request->validate(['tipe'=>'required']);
        }

        // VALIDASI ALAMAT
        if ($post['short'] !== null) {
            $request->validate(['long'=>'required']);
        } elseif ($post['long'] !== null) {
            $request->validate(['short'=>'required']);
        }
        // END - VALIDASI
        $success_ = '';
        // STORE DATA_EKSPEDISI
        $ekspedisi = Ekspedisi::create([
            'bentuk' => $post['bentuk'],
            'nama' => $post['nama'],
            'keterangan' => $post['keterangan'],
        ]);
        $success_ .= '-ekspedisi created-';
        // END - STORE DATA_EKSPEDISI
        // STORE KONTAK
        if ($post['tipe'] !== null && $post['nomor'] !== null) {
            EkspedisiKontak::create([
                'ekspedisi_id' => $ekspedisi->id,
                'tipe' => $post['tipe'],
                'kodearea' => $post['kodearea'],
                'nomor' => $post['nomor'],
                'is_aktual' => 'yes',
            ]);
        }
        // END - STORE KONTAK
        // STORE ALAMAT
        if ($post['short'] !== null && $post['long'] !== null) {
            $post['long'] = json_encode(preg_split("/\r\n|\n|\r/", $post['long']));
            $alamat = Alamat::create([
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

            EkspedisiAlamat::create([
                'ekspedisi_id' => $ekspedisi->id,
                'alamat_id' => $alamat->id,
                'tipe' => 'UTAMA',
            ]);
            $success_ .= '-alamat, pelanggan_alamat created-';
        }
        // END - STORE ALAMAT
        $feedback = [
            'success_' => $success_
        ];
        return back()->with($feedback);

    }

    function delete(Ekspedisi $ekspedisi) {
        $danger_ = '';
        $warnings_ = '';
        $ekspedisi_alamats = EkspedisiAlamat::where('ekspedisi_id', $ekspedisi->id)->get();
        foreach ($ekspedisi_alamats as $ekspedisi_alamat) {
            $alamat = Alamat::find($ekspedisi_alamat->alamat_id);
            $ekspedisi_alamat_other = EkspedisiAlamat::where('alamat_id', $alamat->id)->where('ekspedisi_id', '!=', $ekspedisi->id)->first();
            if ($ekspedisi_alamat_other === null) {
                $alamat->delete();
                $danger_ .= '-alamat deleted!-';
            } else {
                $warnings_ = '-alamat used together-';
            }
        }
        $ekspedisi->delete();
        $danger_ .= '-ekspedisi deleted!-';
        $feedback = [
            'danger_' => $danger_,
            'warnings_' => $warnings_,
        ];
        return redirect()->route('ekspedisis.index')->with($feedback);
    }

    function alamat_add(Ekspedisi $ekspedisi, Request $request) {
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

        EkspedisiAlamat::create([
            'ekspedisi_id' => $ekspedisi->id,
            'alamat_id' => $alamat_new->id,
        ]);
        $success_ .= '-pelanggan_alamat created-';

        $feedback = [
            'success_' => $success_
        ];

        return back()->with($feedback);
    }

    function alamat_utama(Ekspedisi $ekspedisi, Alamat $alamat) {
        $success_ = '';
        // dd($alamat);
        $ekspedisi_alamats = EkspedisiAlamat::where('ekspedisi_id', $ekspedisi->id)->get();
        foreach ($ekspedisi_alamats as $ekspedisi_alamat) {
            $ekspedisi_alamat->tipe = 'CADANGAN';
            $ekspedisi_alamat->save();
            $success_ .= '-CADANGAN-';
        }
        $ekspedisi_alamat = EkspedisiAlamat::where('ekspedisi_id', $ekspedisi->id)->where('alamat_id', $alamat->id)->first();
        $ekspedisi_alamat->tipe = 'UTAMA';
        $ekspedisi_alamat->save();
        $success_ .= '-UTAMA-';

        $feedback = [
            'success_' => $success_,
        ];
        return back()->with($feedback);
    }

    function alamat_edit(Ekspedisi $ekspedisi, Alamat $alamat, Request $request) {
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
        return back()->with($feedback);
    }

    function alamat_delete(Ekspedisi $ekspedisi, Alamat $alamat) {
        $alamat->delete();
        return back()->with('danger_', '-alamat deleted!-');
    }

    function kontak_add(Ekspedisi $ekspedisi, Request $request) {
        $post = $request->post();

        $success_ = '';

        $request->validate([
            'tipe'=>'required',
            'nomor'=>'required',
        ]);

        EkspedisiKontak::create([
            'ekspedisi_id' => $ekspedisi->id,
            'tipe' => $post['tipe'],
            'kodearea' => $post['kodearea'],
            'nomor' => $post['nomor'],
        ]);
        $success_ .= '-ekspedisi_kontak created-';

        $feedback = [
            'success_' => $success_,
        ];
        return back()->with($feedback);
    }

    function kontak_delete(EkspedisiKontak $ekspedisi_kontak) {
        // dd($ekspedisi_kontak);
        $ekspedisi_kontak->delete();
        return back()->with('danger_', '-kontak deleted!-');
    }

    function kontak_edit(EkspedisiKontak $ekspedisi_kontak, Request $request) {
        $post = $request->post();

        $ekspedisi_kontak->update([
            'ekspedisi_id' => $ekspedisi_kontak->ekspedisi_id,
            'tipe' => $post['tipe'],
            'kodearea' => $post['kodearea'],
            'nomor' => $post['nomor'],
        ]);

        return back()->with('success_', '-kontak edited.-');
    }

    function kontak_utama(Ekspedisi $ekspedisi, EkspedisiKontak $ekspedisi_kontak) {
        $ekspedisi_kontaks = EkspedisiKontak::where('ekspedisi_id', $ekspedisi->id)->get();
        foreach ($ekspedisi_kontaks as $element) {
            $element->is_aktual = 'no';
            $element->save();
        }
        $ekspedisi_kontak->is_aktual = 'yes';
        $ekspedisi_kontak->save();
        return back()->with('success_', '-kontak utama updated.-');
    }
}
