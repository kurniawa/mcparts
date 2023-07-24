<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Produk;
use App\Models\Spk;
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
        // MULAI CERATE/EDIT SRJALAN
        $success_ = '';
        $user = Auth::user();
        $produk = Produk::find($spk_produk->produk_id);
        for ($i=0; $i < count($post['srjalan_id']); $i++) {
            $srjalan = null;
            if ($post['srjalan_id'][$i] === 'new') {
                $srjalan = Srjalan::create_from_spk_produk_nota($spk, $nota, $produk, $spk_produk, $spk_produk_nota, $post['jumlah'][$i]);
                $success_ .= '- new sj -';
            } else {
                $srjalan = Srjalan::find($post['srjalan_id'][$i]);
                $spk_produk_nota_srjalan = SpkProdukNotaSrjalan::where('spk_produk_nota_id',$spk_produk_nota->id)->where('srjalan_id',$post['srjalan_id'][$i])->first();
                // KALAU NULL BERARTI EMANG BELUM ADA YANG DIINPUT KE NOTA TERKAIT
                // BERARTI BIKIN SPK_PRODUK_NOTA_SRJALAN BARU
                if ($spk_produk_nota_srjalan === null) {
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
                $spk_produk_nota_srjalans = SpkProdukNotaSrjalan::where('srjalan_id', $srjalan->id)->get();
                $jumlah_packing = array();
                $tipe_packings = TipePacking::all();
                foreach ($spk_produk_nota_srjalans as $spk_produk_nota_srjalan) {
                    if (count($jumlah_packing) === 0) {
                        $jumlah_packing[] = ["tipe_packing"=>$spk_produk_nota_srjalan->tipe_packing, "jumlah"=>$spk_produk_nota_srjalan->jumlah, "jumlah_packing"=>$spk_produk_nota_srjalan->jumlah_packing];
                    } else {
                        $is_tipe_packing_exist = false;
                        foreach ($jumlah_packing as $jml_packing) {
                            foreach ($tipe_packings as $tipe_packing) {
                                if ($tipe_packing->name === $spk_produk_nota_srjalan->tipe_packing) {
                                    if ($jml_packing['tipe_packing'] === $spk_produk_nota_srjalan->tipe_packing) {
                                        $jml_packing['jumlah'] += $spk_produk_nota_srjalan->jumlah;
                                        $jml_packing['jumlah_packing'] += $spk_produk_nota_srjalan->jumlah_packing;
                                        $is_tipe_packing_exist = true;
                                    }
                                }
                            }
                        }
                        if (!$is_tipe_packing_exist) {
                            $jumlah_packing[] = ["tipe_packing"=>$spk_produk_nota_srjalan->tipe_packing, "jumlah"=>$spk_produk_nota_srjalan->jumlah, "jumlah_packing"=>$spk_produk_nota_srjalan->jumlah_packing];
                        }
                    }
                }
                if (count($jumlah_packing) === 0) {
                    $jumlah_packing = null;
                } else {
                    $jumlah_packing = json_encode($jumlah_packing);
                }
                // dump($srjalan);
                // dd($jumlah_packing);
                $srjalan->jumlah_packing = $jumlah_packing;
                $srjalan->updated_by = $user->username;
                $srjalan->save();
                $success_ .= '- update srjalan -';
                // END - MEMASTIKAN DATA SRJALAN TERUPDATE MELALUI LOOPING SPK_PRODUK_NOTA_SRJALANS
            }
        }
        // END - MULAI CREATE/EDIT SRJALAN
        return back()->with('success_', $success_);
    }
}
