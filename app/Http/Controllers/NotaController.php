<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Nota;
use App\Models\NotaSrjalan;
use App\Models\Pelanggan;
use App\Models\PelangganProduk;
use App\Models\Produk;
use App\Models\ProdukHarga;
use App\Models\Spk;
use App\Models\SpkNota;
use App\Models\SpkProduk;
use App\Models\SpkProdukNota;
use App\Models\SpkProdukNotaSrjalan;
use App\Models\Srjalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotaController extends Controller
{
    function create_or_edit_jumlah_spk_produk_nota(Spk $spk, SpkProduk $spk_produk, Request $request) {
        $post = $request->post();
        // dump($post['jumlah']);
        // dump($post);
        // dump($spk);
        // dd($spk_produk);
        // VALIDASI
        // JUMLAH PADA NOTA YANG SUDAH ADA, MASIH BOLEH 0 TAPI TIDAK BOLEH < 0
        // kalau 0 masih gapapa, semisal sempet salah input, sebenarnya item ini belum selesai, maka reset ke 0.
        $jumlah_input = 0;
        // LOOPING UNTUK VALIDASI SEKALIGUS CONVERT MENJADI INTEGER
        for ($i=0; $i < count($post['jumlah']); $i++) {
            if ($post['jumlah'][$i] === null) {
                $post['jumlah'][$i] = '0';
            }
            $post['jumlah'][$i] = (int)$post['jumlah'][$i];
            if ($post['jumlah'][$i] < 0) {
                $request->validate(['error'=>'required'],['error.required'=>'jumlah < 0']);
            }
            $jumlah_input += $post['jumlah'][$i];
        }
        // dd($post['jumlah']);
        // JUMLAH TIDAK BOLEH LEBIH DARI JUMLAH TOTAL SPK_PRODUK
        if ($jumlah_input > $spk_produk->jumlah_total) {
            $request->validate(['error'=>'required'],['error.required'=>'jumlah_input > spk_produk->jumlah_total']);
        }
        // // CEK APAKAH SUDAH NOTA?
        // // Kalau sudah, cek jumlah spk_produk_notas nya, nota mana jumlahnya berapa, sisa jumlah nya berapa yang available
        // $sisa_available = 0;
        // $spk_produk_notas = SpkProdukNota::where('spk_produk_id', $spk_produk->id)->get();
        // if (count($spk_produk_notas) !== 0) {
        //     $jumlah_sudah_nota = 0;
        //     foreach ($spk_produk_notas as $spk_produk_nota) {
        //         $jumlah_sudah_nota+=$spk_produk_nota->jumlah;
        //     }
        //     $sisa_available = $spk_produk->jumlah_total - $jumlah_sudah_nota;
        //     if ($jumlah_input > $sisa_available) {
        //         $request->validate(['error'=>'required'],['error.required'=>'jumlah_input < sisa_available -> hapus/edit nota_item terlebih dahulu!']);
        //     }
        // }
        // CEK APAKAH MELEBIHI DARI PRODUKSI YANG SUDAH SELESAI
        // dump($spk->jumlah_selesai);
        // dd($jumlah_input);
        if ($jumlah_input > $spk_produk->jumlah_selesai) {
            $request->validate(['error'=>'required'],['error.required'=>'jumlah_input > $spk_produk->jumlah_selesai']);
        }
        // END - VALIDASI
        // MULAI CERATE/EDIT NOTA
        $success_ = '';
        $user = Auth::user();
        $produk = Produk::find($spk_produk->produk_id);
        for ($i=0; $i < count($post['nota_id']); $i++) {
            if ($post['jumlah'][$i] > 0) {
                if ($post['nota_id'][$i] === 'new') {
                    Nota::create_from_spk_produk($spk, $spk_produk, $post['jumlah'][$i]);
                    $success_ .= '- new nota -';
                } else {
                    $spk_produk_nota = SpkProdukNota::where('spk_produk_id',$spk_produk->id)->where('nota_id',$post['nota_id'][$i])->first();
                    // KALAU NULL BERARTI EMANG BELUM ADA YANG DIINPUT KE NOTA TERKAIT
                    // BERARTI BIKIN SPK_PRODUK_NOTA BARU

                    $harga_produk = Produk::get_harga_pelanggan($produk->id, $spk->pelanggan_id);
                    if ($spk_produk_nota === null) {
                        // dd($harga_produk);
                        $spk_produk_nota = SpkProdukNota::create([
                            'spk_id'=>$spk->id,
                            'produk_id'=>$spk_produk->produk_id,
                            'spk_produk_id'=>$spk_produk->id,
                            'nota_id'=>$post['nota_id'][$i],
                            'jumlah'=>$post['jumlah'][$i],
                            'nama_nota'=>$produk->nama_nota,
                            'harga'=>$harga_produk,
                            'harga_t'=>$harga_produk * $post['jumlah'][$i],
                        ]);
                        $success_ .= '- new spk_produk_nota -';
                    // END - BERARTI BIKIN SPK_PRODUK_NOTA BARU
                    } else {
                        $spk_produk_nota->jumlah = $post['jumlah'][$i];
                        $spk_produk_nota->harga_t = $post['jumlah'][$i] * $harga_produk;
                        $spk_produk_nota->save();
                        $success_ .= '- update spk_produk_nota -';
                    }

                    // MEMASTIKAN DATA NOTA TERUPDATE
                    $spk_produk_notas = SpkProdukNota::where('nota_id', $post['nota_id'][$i])->get();
                    $jumlah_total = 0;
                    $harga_total = 0;
                    foreach ($spk_produk_notas as $spk_produk_nota) {
                        $jumlah_total += $spk_produk_nota->jumlah;
                        $harga_total += $spk_produk_nota->harga_t;
                    }
                    $nota = Nota::find($post['nota_id'][$i]);
                    $nota->jumlah_total = $jumlah_total;
                    $nota->harga_total = $harga_total;
                    $nota->updated_by = $user->username;
                    $nota->save();
                    $success_ .= '- update nota -';
                    // END - MEMASTIKAN DATA NOTA TERUPDATE
                }
            }
        }
        // END - MULAI CREATE/EDIT NOTA
        return back()->with('success_', $success_);
    }

    function delete(Spk $spk, Nota $nota) {
        // dd($nota);
        $danger_ = '';
        $success_ = '';
        $nota_srjalans = NotaSrjalan::where('nota_id', $nota->id)->get();
        $nota->delete(); // akan menghapus spk_produk_nota, spk_produk_nota_srjalan terkait
        $danger_ .= '-nota deleted-';
        // KAJI ULANG SPK: status_nota, jumlah_sudah_nota
        // KAJI ULANG SPK_PRODUKS: jumlah_sudah_nota
        Nota::kaji_ulang_spk_dan_spk_produk($spk);
        Srjalan::kaji_ulang_spk_dan_spk_produk($spk);
        $success_ .= '-spk dan spk_produks: kaji ulang-';
        // END - PENGKAJIAN ULANG
        // KAJI ULANG SRJALAN TERKAIT
        foreach ($nota_srjalans as $nota_srjalan) {
            $srjalan = Srjalan::find($nota_srjalan->srjalan_id);
            $spk_produk_nota_srjalans = SpkProdukNotaSrjalan::where('srjalan_id', $srjalan->id)->get();
            if (count($spk_produk_nota_srjalans) === 0) {
                $srjalan->delete();
                $danger_ .= '-srjalan deleted-';
            } else {
                Srjalan::update_jumlah_packing_srjalan($srjalan);
            }
        }
        // END - KAJI ULANG SRJALAN TERKAIT
        $feedback = [
            'danger_' => $danger_,
            'success_' => $success_,
        ];
        return back()->with($feedback);
    }

    function nota_all(Spk $spk, Request $request) {
        $post = $request->post();
        // dump($post);
        // dd($spk);
        if (!isset($post['nota_id'])) {
            $request->validate(['error'=>'required'],['error.required'=>'isset($post[nota_id]) ?']);
        }

        $success_ = '';
        $nota = collect();
        $user = Auth::user();
        if ($post['nota_id'] === 'new') {
            $alamat_id = null;
            $kontak_id = null;
            $cust_long = null;
            $cust_short = null;
            $cust_kontak = null;
            $reseller_alamat_id = null;
            $reseller_kontak_id = null;
            $reseller_long = null;
            $reseller_short = null;
            $reseller_kontak = null;
            $pelanggan_data = Pelanggan::data($spk->pelanggan_id);
            $alamat_id = $pelanggan_data['alamat_id'];
            $kontak_id = $pelanggan_data['kontak_id'];
            $cust_long = $pelanggan_data['long'];
            $cust_short = $pelanggan_data['short'];
            $cust_kontak = $pelanggan_data['kontak'];
            if ($spk->reseller_id !== null) {
                $reseller_data = Pelanggan::data($spk->reseller_id);
                $reseller_alamat_id = $reseller_data['alamat_id'];
                $reseller_kontak_id = $reseller_data['kontak_id'];
                $reseller_long = $reseller_data['long'];
                $reseller_short = $reseller_data['short'];
                $reseller_kontak = $reseller_data['kontak'];
            }
            $nota = Nota::create([
                'pelanggan_id'=>$spk->pelanggan_id,
                'reseller_id'=>$spk->reseller_id,
                'pelanggan_nama'=>$spk->pelanggan_nama,
                'reseller_nama'=>$spk->reseller_nama,
                // 'jumlah_total'=>$jumlah_total,
                // 'harga_total'=>$spk_produk->harga * $jumlah_total,
                //
                'alamat_id'=>$alamat_id,
                'reseller_alamat_id'=>$reseller_alamat_id,
                'kontak_id'=>$kontak_id,
                'reseller_kontak_id'=>$reseller_kontak_id,
                'cust_long'=>$cust_long,
                'cust_short'=>$cust_short,
                'cust_kontak'=>$cust_kontak,
                'reseller_long'=>$reseller_long,
                'reseller_short'=>$reseller_short,
                'reseller_kontak'=>$reseller_kontak,
                'created_by'=>$user->username,
                'updated_by'=>$user->username,
            ]);
            $nota->no_nota = "N-$nota->id";
            $nota->save();

            SpkNota::create([
                'spk_id' => $spk->id,
                'nota_id' => $nota->id,
            ]);
            $success_ .= '- nota new, update no_nota, spk_nota -';
            // dump($nota);
        } else {
            $nota = Nota::find($post['nota_id']);
        }
        // dd($nota);
        $spk_produks = SpkProduk::where('spk_id', $spk->id)->get();
        foreach ($spk_produks as $spk_produk) {
            // CEK APAKAH ITEM SUDAH NOTA
            $spk_produk_notas = SpkProdukNota::where('spk_produk_id', $spk_produk->id)->get();
            $jumlah_sudah_nota = 0;
            foreach ($spk_produk_notas as $spk_produk_nota) {
                $jumlah_sudah_nota += $spk_produk_nota->jumlah;
            }
            $jumlah_belum_nota = $spk_produk->jumlah_selesai - $jumlah_sudah_nota;
            if ($jumlah_belum_nota > 0) {
                $produk = Produk::find($spk_produk->produk_id);
                $harga_produk = Produk::get_harga_pelanggan($produk->id, $spk->pelanggan_id);
                SpkProdukNota::create([
                    'spk_id'=>$spk->id,
                    'produk_id'=>$spk_produk->produk_id,
                    'spk_produk_id'=>$spk_produk->id,
                    'nota_id'=>$nota->id,
                    'jumlah'=>$jumlah_belum_nota,
                    'nama_nota'=>$produk->nama_nota,
                    'harga'=>$harga_produk,
                    'harga_t'=>$harga_produk * $jumlah_belum_nota,
                ]);
            }
            $spk_produk->jumlah_sudah_nota = $spk_produk->jumlah_selesai;
            $spk_produk->save();
            // END - CEK APAKAH ITEM SUDAH NOTA
        }
        $success_ .= '- loop spk_produks -';

        // UPDATE NOTA: jumlah_total dan harga_total
        $spk_produk_notas = SpkProdukNota::where('nota_id', $nota->id)->get();
        $jumlah_total = $harga_total = 0;
        foreach ($spk_produk_notas as $spk_produk_nota) {
            $jumlah_total += $spk_produk_nota->jumlah;
            $harga_total += $spk_produk_nota->harga_t;
        }
        $nota->jumlah_total = $jumlah_total;
        $nota->harga_total = $harga_total;
        $nota->save();
        $success_ .= '- update nota: jumlah_total, harga_total -';
        // END - UPDATE NOTA: jumlah_total dan harga_total
        return back()->with('success_', $success_);
    }

    function edit_tanggal(Nota $nota, Request $request) {
        $post = $request->post();
        // dump($post);
        // dd($nota);
        // VALIDASI
        if ($post['created_day'] === null || $post['created_month'] === null || $post['created_year'] === null) {
            $request->validate(['error'=>'required'],['error.required'=>'created_at?']);
        }
        if ($post['finished_day'] !== null) {
            if ($post['finished_month'] === null || $post['finished_year'] === null) {
                $request->validate(['error'=>'required'],['error.required'=>'finished_at?']);
            }
        } elseif ($post['finished_month'] !== null) {
            if ($post['finished_day'] === null || $post['finished_year'] === null) {
                $request->validate(['error'=>'required'],['error.required'=>'finished_at?']);
            }
        } elseif ($post['finished_year'] !== null) {
            if ($post['finished_day'] === null || $post['finished_month'] === null) {
                $request->validate(['error'=>'required'],['error.required'=>'finished_at?']);
            }
        }
        // END - VALIDASI
        $user = Auth::user();
        $created_at = date('Y-m-d', strtotime("$post[created_year]-$post[created_month]-$post[created_day]")) . " " . date("H:i:s");
        $finished_at = null;
        if ($post['finished_day'] !== null) {
            $finished_at = date('Y-m-d', strtotime("$post[finished_year]-$post[finished_month]-$post[finished_day]")) . " " . date("H:i:s");
        }

        $nota->created_at = $created_at;
        $nota->finished_at = $finished_at;
        $nota->updated_by = $user->username;
        $nota->save();
        $success_ = '-$nota->created_at, finished_at updated-';
        return back()->with('success_', $success_);
    }

    function delete_item(Spk $spk, SpkProdukNota $spk_produk_nota) {
        // dump($spk);
        // dd($spk_produk_nota);
        $danger_ = '';
        $success_ = '';
        $spk_produk_nota_srjalans = SpkProdukNotaSrjalan::where('spk_produk_nota_id',$spk_produk_nota->id)->get();
        foreach ($spk_produk_nota_srjalans as $spk_produk_nota_srjalan) {
            $spk_produk_nota_srjalan->delete();
            $danger_ .= '-spk_produk_nota_srjalan deleted-';
        }
        $spk_produk_nota->delete();
        $danger_ .= '-spk_produk_nota deleted-';
        Spk::update_data_SPK($spk);
        Nota::kaji_ulang_spk_dan_spk_produk($spk);
        Srjalan::kaji_ulang_spk_dan_spk_produk($spk);
        Nota::update_data_nota_srjalan($spk);
        // Update colly Srjalan
        $nota_srjalans = NotaSrjalan::where('nota_id', $spk_produk_nota->nota_id)->get();
        foreach ($nota_srjalans as $nota_srjalan) {
            $srjalan = Srjalan::find($spk_produk_nota_srjalan->srjalan_id);
            Srjalan::update_jumlah_packing_srjalan($srjalan);
        }
        // End - Update colly Srjalan
        $success_ .= '-Data SPK, Nota & Srjalan updated-';
        $feedback = [
            'danger_' => $danger_,
            'success_' => $success_,
        ];
        return back()->with($feedback);
    }

    function print_out(Nota $nota) {
        // dd($nota);
        $spk_produk_notas = SpkProdukNota::where('nota_id', $nota->id)->get();
        $rest_row = 16 - count($spk_produk_notas);
        $cust_kontak = null;
        if ($nota->cust_kontak) {
            $cust_kontak = json_decode($nota->cust_kontak, true);
        }
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'notas.print_out',
            'profile_menus' => Menu::get_profile_menus(),
            'nota' => $nota,
            'spk_produk_notas' => $spk_produk_notas,
            'rest_row' => $rest_row,
            'cust_kontak' => $cust_kontak,
        ];

        // dd($nota);
        return view('notas.print_out', $data);
    }

    function edit_alamat(Spk $spk, Request $request) {
        $post = $request->post();
        dump($post);
        dd($spk);
        $success_ = '';

        $feedback = [
            'success_' => $success_,
        ];
        return back()->with($feedback);
    }

    function edit_kontak(Spk $spk, Request $request) {
        $post = $request->post();
        dump($post);
        dd($spk);
        $success_ = '';

        $feedback = [
            'success_' => $success_,
        ];
        return back()->with($feedback);
    }

    function edit_harga_item(Spk $spk, Nota $nota, SpkProdukNota $spk_produk_nota, Request $request) {
        $post = $request->post();
        // dump($post);
        // dump($nota);
        // dd($spk_produk_nota);

        $request->validate(['harga' => 'required|numeric']);

        $success_ = '';
        if (isset($post['harga_khusus_pelanggan'])) {
            if ($post['harga_khusus_pelanggan'] === 'yes') {
                $pelanggan_produks = PelangganProduk::where('pelanggan_id', $spk->pelanggan_id)->where('produk_id', $spk_produk_nota->produk_id)->where('status', 'default')->get();
                foreach ($pelanggan_produks as $pelanggan_produk) {
                    $pelanggan_produk->status = 'lama';
                    $pelanggan_produk->save();
                }

                $produk_harga = ProdukHarga::where('produk_id', $spk_produk_nota->produk_id)->where('status', 'default')->latest()->first();

                PelangganProduk::create([
                    'pelanggan_id' => $spk->pelanggan_id,
                    'reseller_id' => $spk->reseller_id,
                    'produk_id' => $spk_produk_nota->produk_id,
                    'harga_price_list' => $produk_harga->harga,
                    'harga_khusus' => (int)$post['harga'],
                    'status' => 'default',
                ]);
                $success_ .= '-pelanggan_produk created-';
            }
        }

        $harga = (int)$post['harga'];
        $harga_t = $harga * $spk_produk_nota->jumlah;

        $spk_produk_nota->harga = $harga;
        $spk_produk_nota->harga_t = $harga_t;
        $spk_produk_nota->save();
        $success_ .= '-harga_nota_item updated-';

        // UPDATE HARGA TOTAL NOTA
        $spk_produk_notas = SpkProdukNota::where('nota_id', $nota->id)->get();

        $harga_total = 0;
        foreach ($spk_produk_notas as $spk_produk_nota) {
            $harga_total += $spk_produk_nota->harga_t;
        }

        $nota->harga_total = $harga_total;
        $nota->save();
        $success_ .= '-nota:harga_total updated-';

        return back()->with('success_', $success_);
    }
}
