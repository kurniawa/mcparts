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
        $until = date('Y') . "-" . date('m') . "-" . date('d') . " 23:59:59";

        $pembelians = collect();

        if (count($get) !== 0) {
            // dd($get);
            if ($get['supplier_nama'] && $get['supplier_id']) {
                if ($get['from_day'] === null || $get['from_month'] === null || $get['from_year'] === null || $get['to_day'] === null || $get['to_month'] === null || $get['to_year'] === null) {
                    // Filter Berdasarkan Nama Pelanggan - Tanpa Tanggal
                    $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->latest()->limit(500)->get();
                    // End - Filter Berdasarkan Nama Pelanggan - Tanpa Tanggal
                } else {
                    // Filter Berdasarkan Nama Pelanggan + Tanggal
                    $from = "$get[from_year]-$get[from_month]-$get[from_day]";
                    $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
                    $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->latest()->get();
                    // End - Filter Berdasarkan Nama Pelanggan + Tanggal
                }
            } else {
                // Filter hanya rentang waktu, tanpa nama_pelanggan
                if ($get['from_day'] === null || $get['from_month'] === null || $get['from_year'] === null || $get['to_day'] === null || $get['to_month'] === null || $get['to_year'] === null) {
                    $request->validate(['error'=>'required'],['error.required'=>'customer,time_range']);
                } else {
                    // Filter Berdasarkan Tanggal
                    $from = "$get[from_year]-$get[from_month]-$get[from_day]";
                    $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
                    $pembelians = Pembelian::whereBetween('created_at', [$from, $until])->latest()->get();
                    // End - Filter Berdasarkan Tanggal
                }
                // END - Filter hanya rentang waktu, tanpa nama_pelanggan
            }
        } else {
            $pembelians = Pembelian::whereBetween('created_at', [$from, $until])->latest()->limit(500)->get();
            // $pembelians = Pembelian::latest()->limit(100)('created_at')->get();
            // dump($from, $until);
            // dd($pembelians);
        }


        $pembelian_barangs_all = collect();
        $alamats = collect();
        $kontaks = collect();
        $grand_total = 0;
        $lunas_total = 0;

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
            $grand_total += $pembelian->harga_total;
            if ($pembelian->status_bayar === 'LUNAS') {
                $lunas_total += $pembelian->harga_total;
            }
        }

        $label_supplier = Supplier::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
        $label_barang = Barang::select('id', 'nama as label', 'nama as value', 'satuan_sub', 'satuan_main', 'satuan_sub', 'harga_main', 'jumlah_main', 'harga_total_main')->orderBy('nama')->get();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'pembelians.index',
            'parent_route' => 'pembelians.index',
            'profile_menus' => Menu::get_profile_menus(),
            'pembelian_menus' => Menu::get_pembelian_menus(),
            'pembelians' => $pembelians,
            'pembelian_barangs_all' => $pembelian_barangs_all,
            'alamats' => $alamats,
            'kontaks' => $kontaks,
            'label_supplier' => $label_supplier,
            'label_barang' => $label_barang,
            'grand_total' => $grand_total,
            'lunas_total' => $lunas_total,
        ];
        // dd($pembelians);
        return view('pembelians.index', $data);
    }

    function show(Pembelian $pembelian) {
        dd($pembelian);
    }

    function store(Request $request) {
        $post = $request->post();
        dd($post);
        $request->validate([
            'day' => 'required',
            'month' => 'required',
            'year' => 'required',
            'supplier_nama' => 'required',
            'supplier_id' => 'required',
        ]);

        $supplier = Supplier::find($post['supplier_id']);
        $user = Auth::user();

        $pembelian_new = Pembelian::create([
            'supplier_id' => $supplier->id,
            'supplier_nama' => $supplier->nama,
            'creator' => $user->username,
            'created_at' => date('Y-m-d H:i:s', strtotime("$post[year]-$post[month]-$post[day]" . " " . date("H:i:s"))),
        ]);

        // $isi = collect();
        $isi = array();
        $success_ = '';

        for ($i=0; $i < count($post['barang_id']); $i++) {
            $barang = Barang::find($post['barang_id'][$i]);
            // dd($barang);
            if ($barang == null || (int)$post['harga_t'][$i] == 0 || (int)$post['jumlah_main'][$i] == 0) {
                break;
            }
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
                // 'updater' => $user->username,
            ]);

            $success_ .= '-pembelian_barang created-';

            $exist_satuan_main = false;
            $exist_satuan_sub = false;
            if (count($isi) != 0) {
                for ($i=0; $i < count($isi); $i++) {
                    if ($isi[$i]['satuan'] == $pembelian_barang->satuan_main) {
                        $isi[$i]['jumlah'] = (int)$isi[$i]['jumlah'] + (int)($pembelian_barang->jumlah_main);
                        // dump($isi[$i]['jumlah']);
                        // dump($pembelian_barang->jumlah_main);
                        // dump('isi:');
                        // dump($isi);
                        $exist_satuan_main = true;
                    }
                    if ($isi[$i]['satuan'] == $pembelian_barang->satuan_sub) {
                        $isi[$i]['jumlah'] = (int)$isi[$i]['jumlah'] + (int)($pembelian_barang->jumlah_sub);
                        $exist_satuan_sub = true;
                    }
                }
                // foreach ($isi as $isi_item) {
                //     if ($isi_item['satuan'] == $pembelian_barang->satuan_main) {
                //         $isi_item['jumlah'] = (int)$isi_item['jumlah'] + (int)($pembelian_barang->jumlah_main);
                //         dump($isi_item['jumlah']);
                //         dump($pembelian_barang->jumlah_main);
                //         dump('isi:');
                //         dump($isi);
                //         $exist_satuan_main = true;
                //     }
                //     if ($isi_item['satuan'] == $pembelian_barang->satuan_sub) {
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
                if ($pembelian_barang->satuan_sub != null) {
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
        // cek apakah ada yang diinput ke pembelian_barang?
        $pembelian_barangs = PembelianBarang::where('pembelian_id', $pembelian_new->id)->get();
        if (count($pembelian_barangs) == 0) {
            $pembelian_new->delete();
            return back()->with('warnings_', '-pembelian di cancel karena tidak terdeteksi adanya barang-');
        }

        $nomor_nota = "N-$pembelian_new->id";
        if ($post['nomor_nota'] != null) {
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

    function pelunasan(Pembelian $pembelian, Request $request) {
        $post = $request->post();
        // dump($pembelian);
        // dd($post);

        $request->validate([
            'day' => 'required',
            'month' => 'required',
            'year' => 'required',
        ]);

        $pembelian->tanggal_lunas = date('Y-m-d', strtotime("$post[year]-$post[month]-$post[day]")) . " " . date('H:i:s');
        $pembelian->status_bayar = 'LUNAS';
        $pembelian->keterangan_bayar = $post['keterangan_bayar'];
        $pembelian->save();

        return back()->with('success_', '-data_pelunasan updated-');
    }

    function pembatalan_pelunasan(Pembelian $pembelian) {
        $pembelian->tanggal_lunas = null;
        $pembelian->status_bayar = 'BELUM';
        $pembelian->keterangan_bayar = null;
        $pembelian->save();

        return back()->with('warnings_', '-pelunasan dibatalkan-');
    }

    function edit(Pembelian $pembelian) {
        $pembelian_barangs = PembelianBarang::where('pembelian_id', $pembelian->id)->get();

        $label_supplier = Supplier::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
        $label_barang = Barang::select('id', 'nama as label', 'nama as value', 'satuan_sub', 'satuan_main', 'satuan_sub', 'harga_main', 'jumlah_main', 'harga_total_main')->orderBy('nama')->get();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'pembelians.index',
            'parent_route' => 'pembelians.index',
            'profile_menus' => Menu::get_profile_menus(),
            'pembelian_menus' => Menu::get_pembelian_menus(),
            'pembelian' => $pembelian,
            'pembelian_barangs' => $pembelian_barangs,
            'label_supplier' => $label_supplier,
            'label_barang' => $label_barang,
        ];
        return view('pembelians.edit', $data);
    }

    function update(Pembelian $pembelian, Request $request) {
        $post = $request->post();

        // dump($post);
        // dump($pembelian);

        $request->validate([
            'day' => 'required',
            'month' => 'required',
            'year' => 'required',
            'supplier_nama' => 'required',
            'supplier_id' => 'required',
        ]);

        $supplier = Supplier::find($post['supplier_id']);
        $user = Auth::user();

        $nomor_nota = "N-$pembelian->id";
        if ($post['nomor_nota'] !== null) {
            $nomor_nota = $post['nomor_nota'];
        }

        $pembelian->update([
            'nomor_nota' => $nomor_nota,
            'supplier_id' => $supplier->id,
            'supplier_nama' => $supplier->nama,
            'updater' => $user->username,
            'created_at' => date('Y-m-d H:i:s', strtotime("$post[year]-$post[month]-$post[day]" . " " . date("H:i:s"))),
        ]);

        // $isi = collect();
        $isi = array();
        $success_ = '';

        for ($i=0; $i < count($post['pembelian_barang_id']); $i++) {
            // if ($barang === null) { // kasus dimana barang memang sudah dihapus namun apa yang sudah tercantum pada nota pembelian, tidak terhapus, namun barang_id menjadi null
            // }
            if ($post['pembelian_barang_id'][$i] === 'new') {
                // dd($barang);
                $barang = Barang::find($post['barang_id'][$i]);
                $pembelian_barang = PembelianBarang::create([
                    'pembelian_id' => $pembelian->id,
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
                    // 'updater' => $user->username,
                ]);

                $success_ .= '-pembelian_barang created-';
            } else {
                $pembelian_barang = PembelianBarang::find($post['pembelian_barang_id'][$i]);
                // dd($pembelian_barang);
                try {
                    $pembelian_barang->update([
                        'barang_id' => $pembelian_barang->barang_id,
                        'barang_nama' => $pembelian_barang->barang_nama,
                        'satuan_main' => $pembelian_barang->satuan_main,
                        'jumlah_main' => (int)$post['jumlah_main'][$i] * 100,
                        'harga_main' => (int)$post['harga_main'][$i],
                        'satuan_sub' => $pembelian_barang->satuan_sub,
                        'jumlah_sub' => (int)$post['jumlah_sub'][$i] * 100,
                        'harga_sub' => $pembelian_barang->harga_sub,
                        'harga_t' => (int)$post['harga_t'][$i],
                        // 'status_bayar' => null,
                        // 'keterangan_bayar' => null,
                        // 'tanggal_lunas' => null,
                        // 'created_at' => $pembelian_barang->created_at, // sudah otomatis
                        // 'updated_at' => $pembelian_barang->updated_at,
                        // 'creator' => $user->username,
                        'updater' => $user->username,
                    ]);
                } catch (\Throwable $th) {
                    //throw $th;
                    dump($th);
                    dd($post['pembelian_barang_id'][$i]);
                }
            }

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
            }
            if (!$exist_satuan_main) {
                $isi[]=[
                    'satuan' => $pembelian_barang->satuan_main,
                    'jumlah' => (int)($pembelian_barang->jumlah_main),
                ];
            }
            if (!$exist_satuan_sub) {
                if ($pembelian_barang->satuan_sub !== null) {
                    $isi[]=[
                        'satuan' => $pembelian_barang->satuan_sub,
                        'jumlah' => (int)($pembelian_barang->jumlah_sub),
                    ];
                }
            }
        }

        $pembelian->update([
            'isi' => json_encode($isi),
            'harga_total' => $post['harga_total'],
            // 'status_bayar' => $status_bayar,
            // 'keterangan_bayar' => $keterangan_bayar,
            // 'tanggal_lunas' => $tanggal_lunas,
            // 'created_at' => $tanggal_lunas,
        ]);
        $success_ .= '-pembelian updated-';

        $feedback = [
            'success_' => $success_,
        ];

        return back()->with($feedback);
    }
}
