<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Produk;
use App\Models\Spk;
use App\Models\SpkNota;
use App\Models\SpkProduk;
use App\Models\SpkProdukNota;
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
            if ($post['nota_id'][$i] === 'new') {
                Nota::create_from_spk_produk($spk, $spk_produk, $post['jumlah'][$i]);
                $success_ .= '- new nota -';
            } else {
                $spk_produk_nota = SpkProdukNota::where('spk_produk_id',$spk_produk->id)->where('nota_id',$post['nota_id'][$i])->first();
                // KALAU NULL BERARTI EMANG BELUM ADA YANG DIINPUT KE NOTA TERKAIT
                // BERARTI BIKIN SPK_PRODUK_NOTA BARU
                if ($spk_produk_nota === null) {
                    $spk_produk_nota = SpkProdukNota::create([
                        'spk_id'=>$spk->id,
                        'produk_id'=>$spk_produk->produk_id,
                        'spk_produk_id'=>$spk_produk->id,
                        'nota_id'=>$post['nota_id'][$i],
                        'jumlah'=>$post['jumlah'][$i],
                        'nama_nota'=>$produk->nama_nota,
                        'harga'=>$spk_produk->harga,
                        'harga_t'=>$spk_produk->harga * $post['jumlah'][$i],
                    ]);
                    $success_ .= '- new spk_produk_nota -';
                // END - BERARTI BIKIN SPK_PRODUK_NOTA BARU
                } else {
                    $spk_produk_nota->jumlah = $post['jumlah'][$i];
                    $spk_produk_nota->harga_t = $post['jumlah'][$i] * $spk_produk->harga;
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
        // END - MULAI CREATE/EDIT NOTA
        return back()->with('success_', $success_);
    }

    function delete(Nota $nota) {
        // dd($nota);
        // UPDATE DATA SPK_PRODUK TERKAIT DENGAN SPK_PRODUK_NOTA
        $spk_produk_notas = SpkProdukNota::where('nota_id', $nota->id)->get();
        foreach ($spk_produk_notas as $spk_produk_nota) {
            $spk_produk = SpkProduk::find($spk_produk_nota->spk_produk_id);
            $spk_produk->jumlah_sudah_nota = $spk_produk->jumlah_sudah_nota - $spk_produk_nota->jumlah;
            $spk_produk->jumlah_sudah_srjalan = $spk_produk->jumlah_sudah_srjalan - $spk_produk_nota->jumlah;
            $spk_produk->save();
        }
        // END - UPDATE DATA SPK_PRODUK TERKAIT DENGAN SPK_PRODUK_NOTA
        // UPDATE DATA SPK TERKAIT DENGAN NOTA
        $spk_notas = SpkNota::where('nota_id', $nota->id)->get();
        foreach ($spk_notas as $spk_nota) {
            $spk = Spk::find($spk_nota->spk_id);
            $spk_produks = SpkProduk::where('spk_id', $spk->id)->get();
            $jumlah_sudah_nota = 0;
            foreach ($spk_produks as $spk_produk) {
                $jumlah_sudah_nota += $spk_produk->jumlah_sudah_nota;
            }
            $spk->jumlah_sudah_nota = $jumlah_sudah_nota;
            $spk->save();
        }
        // END - UPDATE DATA SPK TERKAIT DENGAN NOTA
        // MULAI DELETE NOTA
        $nota->delete();
        return back()->with('warnings_','- nota deleted -');
    }
}
