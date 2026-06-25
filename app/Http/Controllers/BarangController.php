<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\GoodsPrice;
use App\Models\Menu;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    function index(Request $request) {
        $suppliers = Supplier::with(['barangs' => function ($query) use ($request) {
            $query->orderBy('nama', 'asc');
            if ($request->filled('barang_id')) {
                $query->where('id', $request->barang_id);
            }

            if ($request->filled('barang_nama')) {
                $query->where('nama', 'like', '%' . $request->barang_nama . '%');
            }

        }])
        ->when($request->filled('supplier_id'), function ($query) use ($request) {
            $query->where('id', $request->supplier_id);
        })
        ->when($request->filled('supplier_nama'), function ($query) use ($request) {
            $query->where('nama', 'like', '%' . $request->supplier_nama . '%');
        })
        ->orderBy('nama')
        ->get();


        $label_supplier = Supplier::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
        $label_barang = Barang::select('id', 'nama as label', 'nama as value', 'satuan_sub', 'satuan_main', 'satuan_sub', 'harga_main', 'jumlah_main', 'harga_total_main')->orderBy('nama')->get();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'barangs.index',
            'parent_route' => 'pembelians.index',
            'profile_menus' => Menu::get_profile_menus(),
            'pembelian_menus' => Menu::get_pembelian_menus(),
            'suppliers' => $suppliers,
            // 'barangs' => $barangs,
            'label_supplier' => $label_supplier,
            'label_barang' => $label_barang,
        ];
        // dd($barangs[0][0]);
        return view('barangs.index', $data);
    }

    function store(Request $request) {
        $post = $request->post();
        // dd($post);
        $request->validate([
            'supplier_nama' => 'required',
            'supplier_id' => 'required',
            'barang_nama' => 'required',
            'satuan_main' => 'required',
            'jumlah_main' => 'required',
            'harga_main' => 'required',
            'harga_total_main' => 'required',
        ]);

        $exist_barang = Barang::where('supplier_id', $post['supplier_id'])->where('nama', $post['barang_nama'])->first();
        if ($exist_barang) {
            $request->validate(['error'=>'required'],['error.required'=>'barang exist']);
        }

        $success_ = '';

        $satuan_sub = $post['satuan_sub'];
        $jumlah_sub = null;
        $harga_sub = null;
        $harga_total_sub = null;

        if ($satuan_sub !== null) {
            if ($post['jumlah_sub'] === null || $post['jumlah_sub'] === 0) {
                $jumlah_sub = 100;
            } else {
                $jumlah_sub = (float)($post['jumlah_sub']);
            }
            $harga_sub = $post['harga_sub'];
            $harga_total_sub = $post['harga_total_sub'];
        }

        $barang = Barang::create([
            'supplier_id' => $post['supplier_id'],
            'supplier_nama' => $post['supplier_nama'],
            'nama' => $post['barang_nama'],
            'satuan_main' => $post['satuan_main'],
            'satuan_sub' => $satuan_sub,
            'harga_main' => $post['harga_main'],
            'harga_sub' => $harga_sub,
            'jumlah_main' => (float)($post['jumlah_main']),
            'jumlah_sub' => $jumlah_sub,
            'harga_total_main' => $post['harga_total_main'],
            'harga_total_sub' => $harga_total_sub,
            'keterangan' => $post['keterangan'],
        ]);

        $success_ .= '-barang created-';

        $feedback = [
            'success_' => $success_,
        ];
        return back()->with($feedback);
    }

    function show(Barang $barang) {
        $label_supplier = Supplier::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
        $label_barang = Barang::select('id', 'nama as label', 'nama as value', 'satuan_sub', 'satuan_main', 'satuan_sub', 'harga_main', 'jumlah_main', 'harga_total_main')->orderBy('nama')->get();
        $label_produk = \App\Models\Produk::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
        
        $defaultPhoto = null;
        $subsidiaryPhotos = [];
        $priceChartData = $barang->goodsPrices()->orderBy('created_at')->get();
        list($pembelians, $pembelians_barangs) = $barang->pembelians();
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'barangs.index',
            'parent_route' => 'pembelians.index',
            'profile_menus' => Menu::get_profile_menus(),
            'pembelian_menus' => Menu::get_pembelian_menus(),
            'label_supplier' => $label_supplier,
            'label_barang' => $label_barang,
            'label_produk' => $label_produk,
            'barang' => $barang,
            'defaultPhoto' => $defaultPhoto,
            'subsidiaryPhotos' => $subsidiaryPhotos,
            'priceChartData' => $priceChartData,
            'pembelians' => $pembelians,
            'pembelians_barangs' => $pembelians_barangs,
        ];

        return view('barangs.show', $data);
    }

    function delete(Barang $barang) {
        // dd($barang);
        $barang->delete();
        return back()->with('danger_', '-barang deleted!-');
    }

    function edit(Barang $barang) {
        $label_supplier = Supplier::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
        $label_barang = Barang::select('id', 'nama as label', 'nama as value', 'satuan_sub', 'satuan_main', 'satuan_sub', 'harga_main', 'jumlah_main', 'harga_total_main')->orderBy('nama')->get();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'barangs.index',
            'parent_route' => 'pembelians.index',
            'profile_menus' => Menu::get_profile_menus(),
            'pembelian_menus' => Menu::get_pembelian_menus(),
            'label_supplier' => $label_supplier,
            'label_barang' => $label_barang,
            'barang' => $barang,
        ];

        return view('barangs.edit', $data);
    }

    function update(Barang $barang, Request $request) {
        $post = $request->post();
        // dump($barang);
        // dump($post);

        $post = $request->post();
        // dd($post);
        $request->validate([
            'supplier_nama' => 'required',
            'supplier_id' => 'required',
            'barang_nama' => 'required',
            'satuan_main' => 'required',
            'jumlah_main' => 'required',
            'harga_main' => 'required',
            'harga_total_main' => 'required',
        ]);

        $success_ = '';
        $user = Auth::user();

        $satuan_sub = $post['satuan_sub'];
        $jumlah_sub = null;
        $harga_sub = null;
        $harga_total_sub = null;

        if ($satuan_sub !== null) {
            if ($post['jumlah_sub'] === null || $post['jumlah_sub'] === 0) {
                $jumlah_sub = 100;
            } else {
                $jumlah_sub = (float)($post['jumlah_sub']);
            }
            $harga_sub = $post['harga_sub'];
            $harga_total_sub = $post['harga_total_sub'];
        }
        $harga_main_old = $barang->harga_main;
        DB::beginTransaction();
        try {
            $barang->update([
                'supplier_id' => $post['supplier_id'],
                'supplier_nama' => $post['supplier_nama'],
                'nama' => $post['barang_nama'],
                'satuan_main' => $post['satuan_main'],
                'satuan_sub' => $satuan_sub,
                'harga_main' => $post['harga_main'],
                'harga_sub' => $harga_sub,
                'jumlah_main' => (float)($post['jumlah_main']),
                'jumlah_sub' => $jumlah_sub,
                'harga_total_main' => $post['harga_total_main'],
                'harga_total_sub' => $harga_total_sub,
                'keterangan' => $post['keterangan'],
            ]);

            // Update harga barang di tabel good_prices juga
            $goods_price = GoodsPrice::where('goods_id', $barang->id)->where('unit', $barang->satuan_main)->where('price', $harga_main_old)->first();
            if ($goods_price) {
                $goods_price->price = $post['harga_main'];
                $goods_price->save();
                $success_ .= '-GoodsPrice updated-';
            } else {
                GoodsPrice::create([
                    'goods_id' => $barang->id,
                    'goods_slug' => $barang->nama,
                    'supplier_id' => $barang->supplier_id,
                    'supplier_name' => $barang->supplier_nama,
                    'unit' => $barang->satuan_main,
                    'price' => $post['harga_main'],
                    'created_by' => $user->username,
                ]);
                $success_ .= '-new GoodsPrice created-';
            }

            $success_ .= '-barang updated-';

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dump($post);

            $message = "Error: " . $th->getMessage()
                . "\n\nFile: " . $th->getFile()
                . "\n\nFile: " . $th->getLine()
                . "\n\nTrace: " . $th->getTraceAsString();
            dd($message);

            return back()->withErrors([
                'error Gagal menyimpan transaksi: ' . $th->getMessage(),
            ]);
        }

        $feedback = [
            'success_' => $success_,
        ];
        
        return back()->with($feedback);

    }
}
