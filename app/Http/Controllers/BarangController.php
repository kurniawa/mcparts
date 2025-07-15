<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Menu;
use App\Models\Supplier;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    function index(Request $request) {
        $get = $request->query();

        $suppliers = collect();
        $barangs = collect();
        

        if (count($get) !== 0) {
            // dd($get);

            if ($get['barang_id']) {
                $supplier_barangs = Barang::where('id', $get['barang_id'])->get();
                $suppliers = Supplier::where('id', $supplier_barangs[0]->supplier_id)->get();
                $barangs->push($supplier_barangs);
            } else if ($get['barang_nama']) {
                $suppliers_temp = Barang::where('nama', 'like', "%$get[barang_nama]%")->select('supplier_id')->groupBy('supplier_id')->orderBy('supplier_nama')->get();
                // dd($suppliers_temp);
                foreach ($suppliers_temp as $supplier_id) {
                    $suppliers->push(Supplier::find($supplier_id['supplier_id']));
                }
                foreach ($suppliers as $supplier) {
                    $supplier_barangs = Barang::where('supplier_id', $supplier->id)->get();
                    $barangs->push($supplier_barangs);
                }
                // $suppliers = Supplier::where('id', $supplier_barangs[0]->supplier_id)->get();
                // dd($suppliers);
            } else if ($get['supplier_nama']) {
                $suppliers = Supplier::where('nama', 'like', "%$get[supplier_nama]%")->get();
                foreach ($suppliers as $supplier) {
                    $supplier_barangs = Barang::where('supplier_id', $supplier->id)->get();
                    $barangs->push($supplier_barangs);
                }
                // dd($suppliers);
            } else if ($get['supplier_id']) {
                $suppliers = Supplier::where('id', $get['supplier_id'])->get();
                foreach ($suppliers as $supplier) {
                    $supplier_barangs = Barang::where('supplier_id', $get['supplier_id'])->get();
                    $barangs->push($supplier_barangs);
                }
            } else {
                $suppliers = Supplier::orderBy('nama')->get();
                foreach ($suppliers as $supplier) {
                    $supplier_barangs = Barang::where('supplier_id', $supplier->id)->get();
                    $barangs->push($supplier_barangs);
                }
            }

        } else {
            $suppliers = Supplier::orderBy('nama')->get();
            foreach ($suppliers as $supplier) {
                $supplier_barangs = Barang::where('supplier_id', $supplier->id)->get();
                $barangs->push($supplier_barangs);
            }
        }


        $label_supplier = Supplier::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
        $label_barang = Barang::select('id', 'nama as label', 'nama as value', 'satuan_sub', 'satuan_main', 'satuan_sub', 'harga_main', 'jumlah_main', 'harga_total_main')->orderBy('nama')->get();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'barangs.index',
            'parent_route' => 'pembelians.index',
            'profile_menus' => Menu::get_profile_menus(),
            'pembelian_menus' => Menu::get_pembelian_menus(),
            'suppliers' => $suppliers,
            'barangs' => $barangs,
            'label_supplier' => $label_supplier,
            'label_barang' => $label_barang,
        ];
        // dd($barangs[0][0]);
        return view('barangs.index', $data);
    }

    // public function index(Request $request)
    // {
    //     $get = $request->query();

    //     $suppliers = collect();
    //     $barangs = collect();

    //     // Awal query builder barang
    //     $barangQuery = Barang::with('goodsPrices'); // eager load relasi harga

    //     if (count($get) !== 0) {
    //         // Filter berdasarkan ID barang
    //         if (!empty($get['barang_id'])) {
    //             $barang = $barangQuery->where('id', $get['barang_id'])->first();
    //             if ($barang) {
    //                 $suppliers = Supplier::where('id', $barang->supplier_id)->get();
    //                 $barangs = collect([$barang]);
    //             }

    //         // Filter berdasarkan nama barang
    //         } elseif (!empty($get['barang_nama'])) {
    //             $barangs = $barangQuery
    //                 ->where('nama', 'like', "%{$get['barang_nama']}%")
    //                 ->get();
    //             $supplierIds = $barangs->pluck('supplier_id')->unique();
    //             $suppliers = Supplier::whereIn('id', $supplierIds)->orderBy('nama')->get();

    //         // Filter berdasarkan nama supplier
    //         } elseif (!empty($get['supplier_nama'])) {
    //             $suppliers = Supplier::where('nama', 'like', "%{$get['supplier_nama']}%")
    //                 ->orderBy('nama')
    //                 ->get();
    //             $barangs = $barangQuery
    //                 ->whereIn('supplier_id', $suppliers->pluck('id'))
    //                 ->get();

    //         // Filter berdasarkan ID supplier
    //         } elseif (!empty($get['supplier_id'])) {
    //             $suppliers = Supplier::where('id', $get['supplier_id'])->get();
    //             $barangs = $barangQuery
    //                 ->where('supplier_id', $get['supplier_id'])
    //                 ->get();

    //         } else {
    //             // Tidak ada filter valid, ambil semua supplier dan barang
    //             $suppliers = Supplier::orderBy('nama')->get();
    //             $barangs = $barangQuery->get();
    //         }

    //     } else {
    //         // Tidak ada query string, ambil semua supplier dan barang
    //         $suppliers = Supplier::orderBy('nama')->get();
    //         $barangs = $barangQuery->get();
    //     }

    //     // Dropdown label
    //     $label_supplier = Supplier::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
    //     $label_barang = Barang::select(
    //         'id',
    //         'nama as label',
    //         'nama as value',
    //         'satuan_sub',
    //         'satuan_main',
    //         'harga_main',
    //         'jumlah_main',
    //         'harga_total_main'
    //     )->orderBy('nama')->get();

    //     $data = [
    //         'menus' => Menu::get(),
    //         'route_now' => 'barangs.index',
    //         'parent_route' => 'pembelians.index',
    //         'profile_menus' => Menu::get_profile_menus(),
    //         'pembelian_menus' => Menu::get_pembelian_menus(),
    //         'suppliers' => $suppliers,
    //         'barangs' => $barangs,
    //         'label_supplier' => $label_supplier,
    //         'label_barang' => $label_barang,
    //     ];

    //     return view('barangs.index', $data);
    // }

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
                $jumlah_sub = (int)($post['jumlah_sub'] * 100);
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
            'jumlah_main' => (int)($post['jumlah_main'] * 100),
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
        dd($barang);
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

        $satuan_sub = $post['satuan_sub'];
        $jumlah_sub = null;
        $harga_sub = null;
        $harga_total_sub = null;

        if ($satuan_sub !== null) {
            if ($post['jumlah_sub'] === null || $post['jumlah_sub'] === 0) {
                $jumlah_sub = 100;
            } else {
                $jumlah_sub = (int)($post['jumlah_sub'] * 100);
            }
            $harga_sub = $post['harga_sub'];
            $harga_total_sub = $post['harga_total_sub'];
        }

        $barang->update([
            'supplier_id' => $post['supplier_id'],
            'supplier_nama' => $post['supplier_nama'],
            'nama' => $post['barang_nama'],
            'satuan_main' => $post['satuan_main'],
            'satuan_sub' => $satuan_sub,
            'harga_main' => $post['harga_main'],
            'harga_sub' => $harga_sub,
            'jumlah_main' => (int)($post['jumlah_main'] * 100),
            'jumlah_sub' => $jumlah_sub,
            'harga_total_main' => $post['harga_total_main'],
            'harga_total_sub' => $harga_total_sub,
            'keterangan' => $post['keterangan'],
        ]);

        $success_ .= '-barang updated-';

        $feedback = [
            'success_' => $success_,
        ];
        return back()->with($feedback);

    }
}
