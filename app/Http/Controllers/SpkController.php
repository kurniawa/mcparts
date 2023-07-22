<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Menu;
use App\Models\Pelanggan;
use App\Models\PelangganAlamat;
use App\Models\PelangganKontak;
use App\Models\PelangganProduk;
use App\Models\Produk;
use App\Models\ProdukHarga;
use App\Models\Spk;
use App\Models\SpkProduk;
use App\Models\SpkProdukNota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpkController extends Controller
{
    function create() {
        $label_pelanggans = Pelanggan::label_pelanggans();
        $label_produks = Produk::select('id', 'nama as label', 'nama as value')->get();

        $produk_hargas = collect();
        foreach ($label_produks as $produk) {
            $produk_harga = ProdukHarga::where('produk_id', $produk->id)->latest()->first();
            $produk_hargas->push($produk_harga);
        }
        $data = [
            // 'goback' => 'home',
            // 'user_role' => $user_role,
            'menus' => Menu::get(),
            'route_now' => 'spks.create',
            'profile_menus' => Menu::get_profile_menus(),
            'label_pelanggans' => $label_pelanggans,
            'label_produks' => $label_produks,
        ];
        return view('spks.create', $data);
    }

    function store(Request $request) {
        $post = $request->post();
        dump($post);
        // VALIDASI DATA
        $request->validate(['pelanggan_id'=>'required','produk_id'=>'required','produk_jumlah'=>'required']);
        if (in_array(null,$post['produk_id'],true)) {
            $request->validate(['error'=>'required'],['error.required'=>'produk_id']);
        }
        if (in_array(null,$post['produk_jumlah'],true) || in_array(0,$post['produk_jumlah'])) {
            $request->validate(['error'=>'required'],['error.required'=>'jumlah']);
        }
        // END - VALIDASI DATA
        $success_ = "";
        $user = Auth::user();
        // SPK - CREATE dulu, supaya dapet ID nya
        $pelanggan = Pelanggan::find($post['pelanggan_id']);
        // Data Pelanggan - Alamat
        $cust_long=$cust_short=null;
        $pelanggan_alamat=PelangganAlamat::where('pelanggan_id',$pelanggan['id'])->where('tipe','UTAMA')->first();
        if ($pelanggan_alamat!==null) {
            $alamat=Alamat::find($pelanggan_alamat['alamat_id']);
            $cust_long=$alamat['long'];
            $cust_short=$alamat['short'];
        }
        // Data Pelanggan - Kontak
        $cust_kontak=PelangganKontak::where('pelanggan_id',$pelanggan['id'])->where('is_aktual','yes')->first();

        // Data Reseller
        $reseller=$reseller_id=$reseller_nama=$reseller_long=$reseller_short=$reseller_kontak=null;

        if ($post['reseller_id']!==null) {
            $reseller=Pelanggan::find($post['reseller_id']);
            $reseller_id=$reseller['id'];
            $reseller_nama=$reseller['nama'];

            // Data Reseller - Alamat
            $reseller_alamat=PelangganAlamat::where('pelanggan_id',$reseller_id)->where('tipe','UTAMA')->first();
            if ($reseller_alamat!==null) {
                $alamat_reseller=Alamat::find($reseller_alamat['alamat_id']);
                $reseller_long=$alamat_reseller['long'];
                $reseller_short=$alamat_reseller['short'];
            }
            // Data Reseller - Kontak
            $reseller_kontak=PelangganKontak::where('pelanggan_id',$reseller_id)->where('is_aktual','yes')->first();
        }
        $created_at = date('Y-m-d', strtotime("$post[year]-$post[month]-$post[day]")) . " " . date("H:i:s");
        $new_spk = Spk::create([
            'pelanggan_id'=>$post['pelanggan_id'],
            'reseller_id'=>$post['reseller_id'],
            'judul'=>$post['judul'],
            'created_by'=>$user['username'],
            'updated_by'=>$user['username'],
            'created_at'=>$created_at,
            'pelanggan_nama'=>$pelanggan->nama,
            'cust_long'=>$cust_long,
            'cust_short'=>$cust_short,
            'cust_kontak'=>$cust_kontak,
            'reseller_nama'=>$reseller_nama,
            'reseller_long'=>$reseller_long,
            'reseller_short'=>$reseller_short,
            'reseller_kontak'=>$reseller_kontak,
        ]);
        // LANGSUNG UPDATE NO_SPK
        $new_spk->no_spk = "SPK-" . $new_spk->id;
        $new_spk->save();
        $success_ = "new_spk, updated";
        // END - SPK - CREATE dulu, supaya dapet ID nya

        // SPK PRODUKS
        foreach ($post['produk_id'] as $key => $produk_id) {
            $pelanggan_produk = PelangganProduk::where('pelanggan_id', $pelanggan->id)->where('produk_id', $produk_id)->latest()->first();
            $harga = null;
            if ($pelanggan_produk !== null) {
                $harga = $pelanggan_produk->harga_khusus;
            } else {
                $produk_harga = ProdukHarga::where('produk_id', $produk_id)->latest()->first();
                $harga = $produk_harga->harga;
            }
            // dd($post['produk_nama'][$key]);
            SpkProduk::create([
                'spk_id' => $new_spk->id,
                'produk_id' => $produk_id,
                'jumlah' => $post['produk_jumlah'][$key],
                'jml_blm_sls' => $post['produk_jumlah'][$key],
                'jumlah_total' => $post['produk_jumlah'][$key],
                'harga' => $harga,
                'keterangan' => $post['produk_keterangan'][$key],
                'status' => 'PROSES',
                'nama_produk' => $post['produk_nama'][$key],
            ]);
        }
        $success_ .= "- spk_produks";
        // END - SPK PRODUKS
        $feedback = [
            'success_' => $success_
        ];
        return back()->with($feedback);
    }

    function show(Spk $spk) {
        // dd($spk);
        // $test_array = [["tipe_packing"=>"colly","jumlah"=>2406,"jumlah_packing"=>16],];
        // $encoded_test_array = json_encode($test_array);
        // $test_array2 = [["tipe_packing"=>"colly","jumlah"=>300,"jumlah_packing"=>2],];
        // $encoded_test_array2 = json_encode($test_array2);
        // dump($test_array);
        // dump($encoded_test_array);
        // dump($test_array2);
        // dd($encoded_test_array2);
        $data_spk_nota_srjalans = Spk::Data_SPK_Nota_Srjalan($spk);
        // dd($data_spk_nota_srjalans);
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'spks.create',
            'profile_menus' => Menu::get_profile_menus(),
            'spk' => $spk,
            'nama_pelanggan' => $data_spk_nota_srjalans['nama_pelanggan'],
            'spk_produks' => $data_spk_nota_srjalans['spk_produks'],
            'notas' => $data_spk_nota_srjalans['notas'],
            'cust_kontaks' => $data_spk_nota_srjalans['cust_kontaks'],
            'col_spk_produk_notas' => $data_spk_nota_srjalans['col_spk_produk_notas'],
            'col_srjalans' => $data_spk_nota_srjalans['col_srjalans'],
            'col_ekspedisi_kontaks' => $data_spk_nota_srjalans['col_ekspedisi_kontaks'],
            'col_col_spk_produk_nota_srjalans' => $data_spk_nota_srjalans['col_col_spk_produk_nota_srjalans'],
            'data_spk_produks' => $data_spk_nota_srjalans['data_spk_produks'],
            'data_spk_produk_notas' => $data_spk_nota_srjalans['data_spk_produk_notas'],
        ];
        return view('spks.show', $data);
    }

    function spk_item_tetapkan_selesai(SpkProduk $spk_produk, Request $request) {
        $post = $request->post();
        // dump($post);
        // dd($spk_produk);
        // VALIDASI
        // JUMLAH TIDAK BOLEH KURANG DARI 0
        // kalau 0 masih gapapa, semisal sempet salah input, sebenarnya item ini belum selesai, maka reset ke 0.
        if ((int)$post['jumlah'] < 0) {
            $request->validate(['error'=>'required'],['error.required'=>'jumlah < 0']);
        }
        // JUMLAH TIDAK BOLEH LEBIH DARI JUMLAH TOTAL SPK_PRODUK
        if ((int)$post['jumlah'] > $spk_produk->jumlah_total) {
            $request->validate(['error'=>'required'],['error.required'=>'jumlah > spk_produk->jumlah_total']);
        }
        // CEK APAKAH SUDAH NOTA?
        // Kalau sudah, cek juga berapa yang masuk ke nota, karena untuk pengeditan, jumlah selesai tidak boleh kurang dari jumlah yang sudah Nota
        $spk_produk_notas = SpkProdukNota::where('spk_produk_id', $spk_produk->id)->get();
        if (count($spk_produk_notas) !== 0) {
            $jumlah_sudah_nota = 0;
            foreach ($spk_produk_notas as $spk_produk_nota) {
                $jumlah_sudah_nota+=$spk_produk_nota->jumlah;
            }
            if ((int)$post['jumlah'] < $jumlah_sudah_nota) {
                $request->validate(['error'=>'required'],['error.required'=>'jumlah < jumlah_sudah_nota -> hapus/edit nota_item terlebih dahulu!']);
            }
        }
        // END - VALIDASI
        $spk_produk->jumlah_selesai = $post['jumlah'];
        $spk_produk->save();

        return back()->with('success_','jumlah_selesai updated!');
    }

    function delete(Spk $spk) {
        dd($spk);
    }
}
