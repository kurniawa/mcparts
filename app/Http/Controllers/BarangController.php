<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
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
            $jumlah_sub = (int)($post['jumlah_sub'] * 100);
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
}
