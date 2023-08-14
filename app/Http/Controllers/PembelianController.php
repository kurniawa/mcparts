<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Barang;
use App\Models\Menu;
use App\Models\Pembelian;
use App\Models\PembelianBarang;
use App\Models\Supplier;
use App\Models\SupplierAlamat;
use App\Models\SupplierKontak;
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    function index(Request $request) {
        $get = $request->query();
        $from = date('Y') . "-" . date('m') . "-01";
        $until = date('Y') . "-" . date('m') . "-" . date('m') . " 23:59:59";

        $pembelians = Pembelian::whereBetween('created_at', [$from, $until])->get();
        $pembelians = Pembelian::latest()->limit(100)->get();

        $pembelian_barangs_all = collect();
        $alamats = collect();
        $kontaks = collect();
        foreach ($pembelians as $pembelian) {
            $pembelian_barangs = PembelianBarang::where('pembelian_id', $pembelian->id)->get();
            $pembelian_barangs_all->push($pembelian_barangs);
            $supplier_alamat = SupplierAlamat::where('supplier_id', $pembelian->supplier_id)->where('tipe', 'UTAMA')->first();
            if ($supplier_alamat!== null) {
                $alamat = Alamat::find($supplier_alamat->alamat_id);
                $alamats->push($alamat);
            } else {
                $alamats->push(null);
            }
            $supplier_kontak = SupplierKontak::where('supplier_id', $pembelian->supplier_id)->where('tipe', 'UTAMA')->first();
            $kontaks->push($supplier_kontak);
        }

        $label_supplier = Supplier::select('id', 'nama as label', 'nama as value')->get();
        $label_barang = Barang::select('id', 'nama as label', 'nama as value', 'satuan_sub', 'satuan_main', 'satuan_sub', 'harga_main', 'jumlah_standar', 'harga_standar')->get();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'pembelians.index',
            'profile_menus' => Menu::get_profile_menus(),
            'pembelians' => $pembelians,
            'pembelian_barangs_all' => $pembelian_barangs_all,
            'alamats' => $alamats,
            'kontaks' => $kontaks,
            'label_supplier' => $label_supplier,
            'label_barang' => $label_barang,
        ];
        // dd($pembelians);
        return view('pembelians.index', $data);
    }

    function show(Pembelian $pembelian) {
        dd($pembelian);
    }
}
