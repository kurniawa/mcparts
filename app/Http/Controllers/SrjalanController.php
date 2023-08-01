<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Nota;
use App\Models\NotaSrjalan;
use App\Models\Produk;
use App\Models\Spk;
use App\Models\SpkNota;
use App\Models\SpkProduk;
use App\Models\SpkProdukNota;
use App\Models\SpkProdukNotaSrjalan;
use App\Models\Srjalan;
use App\Models\TipePacking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SrjalanController extends Controller
{
    function create_or_edit_jumlah_spk_produk_nota_srjalan(Spk $spk, Nota $nota, SpkProduk $spk_produk, SpkProdukNota $spk_produk_nota, Request $request) {
        $post = $request->post();
        // dd($post);
        // VALIDASI
        // JUMLAH PADA NOTA YANG SUDAH ADA, MASIH BOLEH 0 TAPI TIDAK BOLEH < 0
        // kalau 0 masih gapapa, semisal sempet salah input, sebenarnya item ini belum selesai, maka reset ke 0.
        $jumlah_input = 0;
        // DI DALAM LOOPING VALIDASI SEKALIGUS CONVERT MENJADI INTEGER
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
        // JUMLAH TIDAK BOLEH LEBIH DARI JUMLAH SPK_PRODUK_NOTA
        if ($jumlah_input > $spk_produk_nota->jumlah) {
            $request->validate(['error'=>'required'],['error.required'=>'jumlah_input > spk_produk_nota->jumlah']);
        }
        // END - VALIDASI
        // MULAI CREATE/EDIT SRJALAN
        $success_ = '';
        $user = Auth::user();
        $produk = Produk::find($spk_produk->produk_id);
        for ($i=0; $i < count($post['srjalan_id']); $i++) {
            if ($post['jumlah'][$i] > 0) {
                $srjalan = null;
                if ($post['srjalan_id'][$i] === 'new') {
                    $srjalan = Srjalan::create_from_spk_produk_nota($spk, $nota, $produk, $spk_produk, $spk_produk_nota, $post['jumlah'][$i]);
                    $success_ .= '- new sj -';
                } else {
                    // Strateginya adalah, apabila jumlah lebih dari 0 baru dia akan input ke surat jalan terkait
                    $srjalan = Srjalan::find($post['srjalan_id'][$i]);
                    $spk_produk_nota_srjalan = SpkProdukNotaSrjalan::where('spk_produk_nota_id',$spk_produk_nota->id)->where('srjalan_id',$post['srjalan_id'][$i])->first();
                    // KALAU NULL BERARTI EMANG BELUM ADA YANG DIINPUT KE NOTA TERKAIT
                    // BERARTI BIKIN SPK_PRODUK_NOTA_SRJALAN BARU
                    if ($spk_produk_nota_srjalan === null) {
                        // dump($post['jumlah'][$i]);
                        // dump($produk->aturan_packing);
                        // dd(round($post['jumlah'][$i] / $produk->aturan_packing));
                        $spk_produk_nota_srjalan = SpkProdukNotaSrjalan::create([
                            'spk_id' => $spk->id,
                            'produk_id' => $spk_produk->produk_id,
                            'nota_id' => $nota->id,
                            'srjalan_id' => $srjalan->id,
                            'spk_produk_id' => $spk_produk->id,
                            'spk_produk_nota_id' => $spk_produk_nota->id,
                            'tipe_packing' => $produk->tipe_packing,
                            'jumlah' => $post['jumlah'][$i],
                            'jumlah_packing' => round($post['jumlah'][$i] / $produk->aturan_packing),
                        ]);
                        $success_ .= '- new spk_produk_nota_srjalan -';
                    // END - BERARTI BIKIN SPK_PRODUK_NOTA_SRJALAN BARU
                    } else {
                        $spk_produk_nota_srjalan->jumlah = $post['jumlah'][$i];
                        $spk_produk_nota_srjalan->jumlah_packing = round($post['jumlah'][$i] / $produk->aturan_packing);
                        $spk_produk_nota_srjalan->save();
                        $success_ .= '- update spk_produk_nota_srjalan -';
                    }
                    // MEMASTIKAN DATA SRJALAN TERUPDATE MELALUI LOOPING SPK_PRODUK_NOTA_SRJALANS
                    Srjalan::update_jumlah_packing_srjalan($srjalan);
                    $success_ .= '- update srjalan -';
                    // END - MEMASTIKAN DATA SRJALAN TERUPDATE MELALUI LOOPING SPK_PRODUK_NOTA_SRJALANS
                }
            }
        }
        // END - MULAI CREATE/EDIT SRJALAN
        return back()->with('success_', $success_);
    }

    function edit_tanggal(Srjalan $srjalan, Request $request) {
        $post = $request->post();
        // dump($post);
        // dd($srjalan);
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

        $srjalan->created_at = $created_at;
        $srjalan->finished_at = $finished_at;
        $srjalan->updated_by = $user->username;
        $srjalan->save();
        $success_ = '-$srjalan->created_at, finished_at updated-';
        return back()->with('success_', $success_);
    }

    function edit_jumlah_packing(Srjalan $srjalan, SpkProdukNotaSrjalan $spk_produk_nota_srjalan, Request $request) {
        $post = $request->post();
        // dump($post);
        // dump($srjalan);
        // dd($spk_produk_nota_srjalan);
        $success_ = '';
        $spk_produk_nota_srjalan->jumlah_packing = $post['jumlah_packing'];
        $spk_produk_nota_srjalan->save();
        $success_ .= '-spk_produk_nota_srjalan updated-';

        // UPDATE SRJALAN: JUMLAH_PACKING
        Srjalan::update_jumlah_packing_srjalan($srjalan);
        $success_ .= '-srjalan: jumlah_packing updated-';
        // END - UPDATE SRJALAN: JUMLAH_PACKING
        $feedback = [
            'success_' => $success_
        ];
        return back()->with($feedback);
    }

    function delete(Spk $spk, Srjalan $srjalan) {
        // dump($spk);
        // dd($srjalan);
        $danger_ = '';
        $success_ = '';
        $srjalan->delete();
        $danger_ .= '-srjalan deleted-';
        // SPK: kaji ulang jumlah_sudah_srjalan
        Srjalan::kaji_ulang_spk_dan_spk_produk($spk);
        $success_ .= '-spk: jumlah_sudah_srjalan, status - kaji ulang-';
        // END - SPK: kaji ulang jumlah_sudah_srjalan
        $feedback = [
            'danger_' => $danger_,
            'success_' => $success_,
        ];
        return back()->with($feedback);
    }

    function delete_item(Spk $spk, SpkProdukNotaSrjalan $spk_produk_nota_srjalan) {
        // dump($spk);
        // dd($spk_produk_nota_srjalan);
        $danger_ = '';
        $success_ = '';
        $spk_produk_nota_srjalan->delete();
        $danger_ .= '-spk_produk_nota_srjalan deleted-';
        Spk::update_data_SPK($spk);
        Nota::kaji_ulang_spk_dan_spk_produk($spk);
        Srjalan::kaji_ulang_spk_dan_spk_produk($spk);
        Nota::update_data_nota_srjalan($spk);
        $srjalan = Srjalan::find($spk_produk_nota_srjalan->srjalan_id);
        Srjalan::update_jumlah_packing_srjalan($srjalan);
        $success_ .= '-Data SPK, Nota & Srjalan updated-';
        $feedback = [
            'danger_' => $danger_,
            'success_' => $success_,
        ];
        return back()->with($feedback);
    }

    function update_packing(Srjalan $srjalan, Request $request) {
        $post = $request->post();
        // dump($post);
        // dd($srjalan);
        $data_packing = collect();
        for ($i=0; $i < count($post['tipe_packing']); $i++) {
            if ($post['jumlah_packing'][$i] > 0) {
                $data_packing->push([
                    'tipe_packing' => $post['tipe_packing'][$i],
                    'jumlah' => $post['jumlah'][$i],
                    'jumlah_packing' => $post['jumlah_packing'][$i],
                ]);
            }
        }
        $srjalan->jumlah_packing = json_encode($data_packing);
        $srjalan->save();
        $success_ = '-data_packing updated-';
        $feedback = [
            'success_' => $success_
        ];
        return back()->with($feedback);
    }

    function print_out(Srjalan $srjalan) {
        $spk_produk_nota_srjalans = SpkProdukNotaSrjalan::where('srjalan_id', $srjalan->id)->get();

        if ($srjalan->cust_kontak !== null) {
            $srjalan->cust_kontak = json_decode($srjalan->cust_kontak);
        }
        if ($srjalan->reseller_kontak !== null) {
            $srjalan->reseller_kontak = json_decode($srjalan->reseller_kontak);
        }
        if ($srjalan->ekspedisi_kontak !== null) {
            $srjalan->ekspedisi_kontak = json_decode($srjalan->ekspedisi_kontak);
        }
        if ($srjalan->transit_kontak !== null) {
            $srjalan->transit_kontak = json_decode($srjalan->transit_kontak);
        }

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'sjs.print_out',
            'profile_menus' => Menu::get_profile_menus(),
            'srjalan' => $srjalan,
            'spk_produk_nota_srjalans' => $spk_produk_nota_srjalans,
        ];

        // dd($srjalan->cust_kontak['nomor']);
        return view('srjalans.print_out', $data);
    }

    function edit_jenis_barang(Srjalan $srjalan, Request $request) {
        $post = $request->post();
        $success_ = '';
        $srjalan->jenis_barang = $post['jenis_barang'];
        $srjalan->save();
        $success_ .= '-srjalan: jenis_barang updated-';
        $feedback = [
            'success_' => $success_,
        ];
        return back()->with($feedback);
    }
}
