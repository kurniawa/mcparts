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
use Illuminate\Support\Facades\Auth;

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

        $label_supplier = Supplier::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
        $label_barang = Barang::select('id', 'nama as label', 'nama as value', 'satuan_sub', 'satuan_main', 'satuan_sub', 'harga_main', 'jumlah_main', 'harga_total_main')->orderBy('nama')->get();

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

    function store(Request $request) {
        $post = $request->post();
        // dd($post);
        $request->validate([
            'day' => 'required',
            'month' => 'required',
            'year' => 'required',
            'supplier_nama' => 'required',
            'supplier_id' => 'required',
        ]);

        $supplier = Supplier::find($post['supplier_id']);

        $pembelian_new = Pembelian::create([
            'supplier_id' => $supplier->id,
            'supplier_nama' => $supplier->nama,
            'created_at' => date('Y-m-d H:i:s', strtotime("$post[year]-$post[month]-$post[day]" . " " . date("H:i:s"))),
        ]);

        $user = Auth::user();
        // $isi = collect();
        $isi = array();
        $success_ = '';

        for ($i=0; $i < count($post['barang_id']); $i++) {
            $barang = Barang::find($post['barang_id'][$i]);
            // dd($barang);
            $pembelian_barang = PembelianBarang::create([
                'pembelian_id' => $pembelian_new->id,
                'barang_id' => $barang->id,
                'barang_nama' => $barang->nama,
                'satuan_main' => $barang->satuan_main,
                'jumlah_main' => (int)$post['jumlah_main'][$i] * 100,
                'harga_main' => (int)$post['harga_main'][$i],
                'satuan_sub' => $barang->satuan_sub,
                'jumlah_sub' => (int)$post['jumlah_sub'][$i] * 100,
                'harga_sub' => $barang->harga_sub,
                'harga_t' => (int)$post['harga_t'][$i],
                // 'status_bayar' => null,
                // 'keterangan_bayar' => null,
                // 'tanggal_lunas' => null,
                // 'created_at' => $pembelian_barang->created_at, // sudah otomatis
                // 'updated_at' => $pembelian_barang->updated_at,
                'creator' => $user->username,
                'updater' => $user->username,
            ]);

            $success_ .= '-pembelian_barang created-';

            $exist_satuan_main = false;
            $exist_satuan_sub = false;
            if (count($isi) !== 0) {
                for ($i=0; $i < count($isi); $i++) {
                    if ($isi[$i]['satuan'] === $pembelian_barang->satuan_main) {
                        $isi[$i]['jumlah'] = (int)$isi[$i]['jumlah'] + (int)($pembelian_barang->jumlah_main);
                        // dump($isi[$i]['jumlah']);
                        // dump($pembelian_barang->jumlah_main);
                        // dump('isi:');
                        // dump($isi);
                        $exist_satuan_main = true;
                    }
                    if ($isi[$i]['satuan'] === $pembelian_barang->satuan_sub) {
                        $isi[$i]['jumlah'] = (int)$isi[$i]['jumlah'] + (int)($pembelian_barang->jumlah_sub);
                        $exist_satuan_sub = true;
                    }
                }
                // foreach ($isi as $isi_item) {
                //     if ($isi_item['satuan'] === $pembelian_barang->satuan_main) {
                //         $isi_item['jumlah'] = (int)$isi_item['jumlah'] + (int)($pembelian_barang->jumlah_main);
                //         dump($isi_item['jumlah']);
                //         dump($pembelian_barang->jumlah_main);
                //         dump('isi:');
                //         dump($isi);
                //         $exist_satuan_main = true;
                //     }
                //     if ($isi_item['satuan'] === $pembelian_barang->satuan_sub) {
                //         $isi_item['jumlah'] = (int)$isi_item['jumlah'] + (int)($pembelian_barang->jumlah_sub);
                //         $exist_satuan_sub = true;
                //     }
                // }
            }
            if (!$exist_satuan_main) {
                // $isi->push([
                //     'satuan' => $pembelian_barang->satuan_main,
                //     'jumlah' => (int)($pembelian_barang->jumlah_main),
                // ]);
                $isi[]=[
                    'satuan' => $pembelian_barang->satuan_main,
                    'jumlah' => (int)($pembelian_barang->jumlah_main),
                ];
                // dump('first isi:');
                // dump((int)($pembelian_barang->jumlah_main));
            }
            if (!$exist_satuan_sub) {
                if ($pembelian_barang->satuan_sub !== null) {
                    // $isi->push([
                    //     'satuan' => $pembelian_barang->satuan_sub,
                    //     'jumlah' => (int)($pembelian_barang->jumlah_sub),
                    // ]);
                    $isi[]=[
                        'satuan' => $pembelian_barang->satuan_sub,
                        'jumlah' => (int)($pembelian_barang->jumlah_sub),
                    ];
                }
            }
        }
        // dump('isi:');
        // dump($isi);

        $nomor_nota = "N-$pembelian_new->id";
        if ($post['nomor_nota'] !== null) {
            $nomor_nota = $post['nomor_nota'];
        }

        $pembelian_new->update([
            'nomor_nota' => $nomor_nota,
            'isi' => json_encode($isi),
            'harga_total' => $post['harga_total'],
            // 'status_bayar' => $status_bayar,
            // 'keterangan_bayar' => $keterangan_bayar,
            // 'tanggal_lunas' => $tanggal_lunas,
            // 'created_at' => $tanggal_lunas,
        ]);
        $success_ .= '-pembelian new created-';

        $feedback = [
            'success_' => $success_,
        ];

        return back()->with($feedback);
    }

    function delete(Pembelian $pembelian) {
        // dd($pembelian);
        $pembelian->delete();
        $feedback = [
            'danger_' => '-pembelian deleted!-'
        ];
        return back()->with($feedback);
    }
}
