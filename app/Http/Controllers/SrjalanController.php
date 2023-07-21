<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use App\Models\SpkProduk;
use App\Models\SpkProdukNota;
use App\Models\Srjalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SrjalanController extends Controller
{
    function create_or_edit_jumlah_spk_produk_nota_srjalan(Spk $spk, SpkProduk $spk_produk, SpkProdukNota $spk_produk_nota, Request $request) {
        $post = $request->post();
        dd($post);
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
        // MULAI CERATE/EDIT NOTA
        $success_ = '';
        $user = Auth::user();
        for ($i=0; $i < count($post['srjalan_id']); $i++) {
            if ($post['srjalan_id'][$i] === 'new') {
                Srjalan::create_from_spk_produk_nota($spk, $spk_produk, $spk_produk_nota, $post['jumlah'][$i]);
                $success_ .= '- new sj -';
            } else {
                $spk_produk_nota = SpkProdukNota::where('spk_produk_id',$spk_produk->id)->where('srjalan_id',$post['srjalan_id'][$i])->first();
                $spk_produk_nota->jumlah = $post['jumlah'][$i];
                $spk_produk_nota->harga_t = $post['jumlah'][$i] * $spk_produk->harga;
                $spk_produk_nota->save();
                $success_ .= '- update spk_produk_nota -';
                // MEMASTIKAN DATA NOTA TERUPDATE
                $spk_produk_notas = SpkProdukNota::where('srjalan_id', $post['srjalan_id'][$i])->get();
                $jumlah_total = 0;
                $harga_total = 0;
                foreach ($spk_produk_notas as $spk_produk_nota) {
                    $jumlah_total += $spk_produk_nota->jumlah;
                    $harga_total += $spk_produk_nota->harga_t;
                }
                $nota = Srjalan::find($post['srjalan_id'][$i]);
                $nota->jumlah_total = $jumlah_total;
                $nota->harga_total = $harga_total;
                $nota->updated_by = $user->username;
                $nota->save();
                $success_ .= '- update nota -';
                // END - MEMASTIKAN DATA NOTA TERUPDATE
            }
        }
        // END - MULAI CREATE/EDIT NOTA
        return back()->with('success_', $success_);
    }
}
