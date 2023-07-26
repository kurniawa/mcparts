<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Pelanggan;
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
                SpkProdukNota::create([
                    'spk_id'=>$spk->id,
                    'produk_id'=>$spk_produk->produk_id,
                    'spk_produk_id'=>$spk_produk->id,
                    'nota_id'=>$nota->id,
                    'jumlah'=>$jumlah_belum_nota,
                    'nama_nota'=>$produk->nama_nota,
                    'harga'=>$spk_produk->harga,
                    'harga_t'=>$spk_produk->harga * $jumlah_belum_nota,
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
}
