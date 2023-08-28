<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Produk;
use App\Models\ProdukHarga;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdukController extends Controller
{
    function index(Request $request) {
        $get = $request->query();

        if (count($get) !== 0) {
            dd($get);
        }

        $types = Produk::get_types();
        $produks = collect();
        $hargas = collect();
        $jumlah_produk = 0;
        foreach ($types as $tipe) {
            $produk_tipe = Produk::where('tipe', $tipe['tipe'])->orderBy('nama')->get();
            $produks->push($produk_tipe);

            $produk_hargas = collect();
            foreach ($produk_tipe as $produk) {
                $produk_hargas->push(ProdukHarga::where('produk_id', $produk->id)->where('status', 'DEFAULT')->latest()->first());
            }
            $hargas->push($produk_hargas);

            $jumlah_produk += count($produk_tipe);
        }
        // dd($produks->groupBy('tipe'));
        $label_supplier = Supplier::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
        $label_produk = Produk::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'produks.index',
            'parent_route' => 'home',
            'profile_menus' => Menu::get_profile_menus(),
            'spk_menus' => Menu::get_spk_menus(),
            'produks' => $produks,
            'label_supplier' => $label_supplier,
            'label_produk' => $label_produk,
            'types' => $types,
            'jumlah_produk' => $jumlah_produk,
            'tipe_packing' => Produk::get_tipe_packing(),
            'hargas' => $hargas,
        ];
        // dd($hargas);
        return view('produks.index', $data);
    }

    function store(Request $request) {
        $post = $request->post();
        // dd($post);
        $request->validate([
            'tipe' => 'required',
            // 'supplier_nama' => 'required',
            // 'supplier_id' => 'required',
            'nama' => 'required',
            'nama_nota' => 'required',
            'harga' => 'required',
            'tipe_packing' => 'required',
            'aturan_packing' => 'required',
        ]);

        $exist_nama_produk = null;
        // $exist_nama_produk = Produk::where('supplier_id', $post['supplier_id'])->where('nama', $post['nama'])->first();
        if ($post['supplier_id'] !== null) {
            $exist_nama_produk = Produk::where('supplier_id', $post['supplier_id'])->where('nama', $post['nama'])->first();
        }
        elseif ($post['supplier_nama'] !== null) {
            $exist_nama_produk = Produk::where('supplier_nama', $post['supplier_nama'])->where('nama', $post['nama'])->first();
        }
        else {
            $exist_nama_produk = Produk::where('nama', $post['nama'])->first();
        }

        if ($exist_nama_produk) {
            $request->validate(['error'=>'required'],['error.required'=>'produk exist already']);
        }

        $success_ = '';

        $user = Auth::user();
        $produk = Produk::create([
            'tipe' => $post['tipe'],
            'nama' => $post['nama'],
            'nama_nota' => $post['nama_nota'],
            'supplier_nama' => $post['supplier_nama'],
            'supplier_id' => $post['supplier_id'],
            'tipe_packing' => $post['tipe_packing'],
            'aturan_packing' => $post['aturan_packing'],
            'keterangan' => $post['keterangan'],
            'creator' => $user->username,
        ]);

        $success_ .= '-produk created-';

        ProdukHarga::create([
            'produk_id' => $produk->id,
            'harga' => $post['harga']
        ]);

        $success_ .= '-produk_harga created-';

        return back()->with('success_', $success_);
    }

    function show(Produk $produk) {
        // dd($produk);

        $label_supplier = Supplier::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
        $label_produk = Produk::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();

        $produk_harga = ProdukHarga::where('produk_id', $produk->id)->where('status', 'DEFAULT')->latest()->first();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'produks.index',
            'parent_route' => 'home',
            'profile_menus' => Menu::get_profile_menus(),
            'spk_menus' => Menu::get_spk_menus(),
            'produk' => $produk,
            'label_supplier' => $label_supplier,
            'label_produk' => $label_produk,
            'types' => Produk::get_types(),
            'tipe_packing' => Produk::get_tipe_packing(),
            'produk_harga' => $produk_harga,
        ];

        return view('produks.show', $data);
    }

    function update(Produk $produk, Request $request) {
        $post = $request->post();
        // dump($post);
        // dd($produk);

        $request->validate([
            'tipe' => 'required',
            'nama' => 'required',
            'nama_nota' => 'required',
            'harga' => 'required',
            'tipe_packing' => 'required',
            'aturan_packing' => 'required',
        ]);

        $exist_nama_produk = null;
        if ($post['supplier_id'] !== null) {
            $exist_nama_produk = Produk::where('id', '!=', $produk->id)->where('supplier_id', $post['supplier_id'])->where('nama', $post['nama'])->first();
        }
        elseif ($post['supplier_nama'] !== null) {
            $exist_nama_produk = Produk::where('id', '!=', $produk->id)->where('supplier_nama', $post['supplier_nama'])->where('nama', $post['nama'])->first();
        }
        else {
            $exist_nama_produk = Produk::where('id', '!=', $produk->id)->where('nama', $post['nama'])->first();
        }

        if ($exist_nama_produk) {
            $request->validate(['error'=>'required'],['error.required'=>'produk exist already']);
        }

        $success_ = '';

        $user = Auth::user();
        $produk->update([
            'tipe' => $post['tipe'],
            'nama' => $post['nama'],
            'nama_nota' => $post['nama_nota'],
            'supplier_nama' => $post['supplier_nama'],
            'supplier_id' => $post['supplier_id'],
            'tipe_packing' => $post['tipe_packing'],
            'aturan_packing' => $post['aturan_packing'],
            'keterangan' => $post['keterangan'],
            'creator' => $user->username,
        ]);

        $success_ .= '-produk updated-';

        $produk_harga = ProdukHarga::find($post['produk_harga_id']);
        $produk_harga->harga = $post['harga'];
        $produk_harga->save();

        $success_ .= '-produk_harga updated-';

        return back()->with('success_', $success_);
    }

    function delete(Produk $produk) {
        $produk->delete();

        return redirect()->route('produks.index')->with('danger_', '-produk deleted!!!-');
    }
}