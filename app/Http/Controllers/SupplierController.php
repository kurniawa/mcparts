<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Menu;
use App\Models\Pelanggan;
use App\Models\Supplier;
use App\Models\SupplierAlamat;
use App\Models\SupplierKontak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    function index(Request $request) {
        $get = $request->query();

        $suppliers = Supplier::orderBy('nama')->get();

        if (isset($get['supplier_nama'])) {
            if ($get['supplier_nama'] !== null) {
                $suppliers = Supplier::where('nama','LIKE', "%$get[supplier_nama]%")->orderBy('nama')->get();
            }
        }

        // SUPPLIER_ALAMAT_KONTAK
        $alamats = collect();
        $supplier_kontaks = collect();
        foreach ($suppliers as $supplier) {
            $supplier_alamat = SupplierAlamat::where('supplier_id', $supplier->id)->where('tipe', 'UTAMA')->first();
            $alamat = null;
            if ($supplier_alamat !== null) {
                $alamat = Alamat::find($supplier_alamat->alamat_id);
            }
            $supplier_kontak = SupplierKontak::where('supplier_id', $supplier->id)->where('is_aktual', 'yes')->first();
            $alamats->push($alamat);
            $supplier_kontaks->push($supplier_kontak);
        }
        // END - ALAMAT_KONTAK

        $label_supplier = Supplier::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'suppliers.index',
            'parent_route' => 'pembelians.index',
            'profile_menus' => Menu::get_profile_menus(),
            'pembelian_menus' => Menu::get_pembelian_menus(),
            'bentuks' => Pelanggan::bentuks(),
            'tipe_kontaks' => Alamat::tipe_kontaks(),
            'suppliers' => $suppliers,
            'alamats' => $alamats,
            'supplier_kontaks' => $supplier_kontaks,
            'label_supplier' => $label_supplier,
        ];
        // dd($pembelians);
        return view('suppliers.index', $data);
    }

    function store(Request $request) {
        $post = $request->post();
        // dd($post);
        // VALIDASI
        // VALIDASI DATA PELANGGAN
        $request->validate(['nama'=>'required']);
        if ($post['initial'] !== null) {
            $request->validate(['initial'=>'max:5']);
        }
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
        // STORE DATA_SUPPLIER
        $user = Auth::user();
        $supplier = Supplier::create([
            'bentuk' => $post['bentuk'],
            'nama' => $post['nama'],
            'nama_pemilik' => $post['nama_pemilik'],
            'initial' => $post['initial'],
            'keterangan' => $post['keterangan'],
            'creator' => $user->username,
            'updater' => $user->username,
        ]);
        $success_ .= '-supplier created-';
        // END - STORE DATA_SUPPLIER
        // STORE KONTAK
        if ($post['tipe'] !== null && $post['nomor'] !== null) {
            SupplierKontak::create([
                'supplier_id' => $supplier->id,
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

            SupplierAlamat::create([
                'supplier_id' => $supplier->id,
                'alamat_id' => $alamat->id,
                'tipe' => 'UTAMA',
            ]);
            $success_ .= '-alamat, supplier_alamat created-';
        }
        // END - STORE ALAMAT
        $feedback = [
            'success_' => $success_
        ];
        return back()->with($feedback);
    }

    function show(Supplier $supplier) {
        $supplier_alamats = SupplierAlamat::where('supplier_id', $supplier->id)->get();
        $alamats = collect();
        foreach ($supplier_alamats as $supplier_alamat) {
            $alamat = Alamat::find($supplier_alamat->alamat_id);
            $alamats->push($alamat);
        }
        $supplier_kontaks = SupplierKontak::where('supplier_id', $supplier->id)->get();

        $data = [
            // 'goback' => 'home',
            // 'user_role' => $user_role,
            'menus' => Menu::get(),
            'route_now' => 'suppliers.show',
            'profile_menus' => Menu::get_profile_menus(),
            'supplier' => $supplier,
            'supplier_alamats' => $supplier_alamats,
            'alamats' => $alamats,
            'supplier_kontaks' => $supplier_kontaks,
            'tipe_kontaks' => Alamat::tipe_kontaks(),
        ];
        // dd($alamats);
        // dd($alamat_ekspedisis);
        return view('suppliers.show', $data);
    }

    function alamat_add(Supplier $supplier, Request $request) {
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

        SupplierAlamat::create([
            'supplier_id' => $supplier->id,
            'alamat_id' => $alamat_new->id,
        ]);
        $success_ .= '-pelanggan_alamat created-';

        $feedback = [
            'success_' => $success_
        ];

        return back()->with($feedback);
    }

    function alamat_utama(Supplier $supplier, Alamat $alamat) {
        $success_ = '';
        // dd($alamat);
        $supplier_alamats = SupplierAlamat::where('supplier_id', $supplier->id)->get();
        foreach ($supplier_alamats as $supplier_alamat) {
            $supplier_alamat->tipe = 'CADANGAN';
            $supplier_alamat->save();
            $success_ .= '-CADANGAN-';
        }
        $supplier_alamat = SupplierAlamat::where('supplier_id', $supplier->id)->where('alamat_id', $alamat->id)->first();
        $supplier_alamat->tipe = 'UTAMA';
        $supplier_alamat->save();
        $success_ .= '-UTAMA-';

        $feedback = [
            'success_' => $success_,
        ];
        return back()->with($feedback);
    }

    function alamat_edit(Supplier $supplier, Alamat $alamat, Request $request) {
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

    function alamat_delete(Supplier $supplier, Alamat $alamat) {
        $alamat->delete();
        return back()->with('danger_', '-alamat deleted!-');
    }

    function kontak_add(Supplier $supplier, Request $request) {
        $post = $request->post();

        $success_ = '';

        $request->validate([
            'tipe'=>'required',
            'nomor'=>'required',
        ]);

        SupplierKontak::create([
            'supplier_id' => $supplier->id,
            'tipe' => $post['tipe'],
            'kodearea' => $post['kodearea'],
            'nomor' => $post['nomor'],
        ]);
        $success_ .= '-supplier_kontak created-';

        $feedback = [
            'success_' => $success_,
        ];
        return back()->with($feedback);
    }

    function kontak_delete(SupplierKontak $supplier_kontak) {
        // dd($supplier_kontak);
        $supplier_kontak->delete();
        return back()->with('danger_', '-kontak deleted!-');
    }

    function kontak_edit(SupplierKontak $supplier_kontak, Request $request) {
        $post = $request->post();

        $supplier_kontak->update([
            'supplier_id' => $supplier_kontak->supplier_id,
            'tipe' => $post['tipe'],
            'kodearea' => $post['kodearea'],
            'nomor' => $post['nomor'],
        ]);

        return back()->with('success_', '-kontak edited.-');
    }

    function kontak_utama(Supplier $supplier, SupplierKontak $supplier_kontak) {
        $supplier_kontaks = SupplierKontak::where('supplier_id', $supplier->id)->get();
        foreach ($supplier_kontaks as $element) {
            $element->is_aktual = 'no';
            $element->save();
        }
        $supplier_kontak->is_aktual = 'yes';
        $supplier_kontak->save();
        return back()->with('success_', '-kontak utama updated.-');
    }

    function delete(Supplier $supplier) {
        $supplier->delete();
        return back()->with('danger_', '-supplier deleted-');
    }
}
