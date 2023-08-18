<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Ekspedisi;
use App\Models\EkspedisiAlamat;
use App\Models\Menu;
use App\Models\Nota;
use App\Models\NotaSrjalan;
use App\Models\Pelanggan;
use App\Models\PelangganAlamat;
use App\Models\PelangganEkspedisi;
use App\Models\PelangganKontak;
use App\Models\PelangganProduk;
use App\Models\Produk;
use App\Models\ProdukHarga;
use App\Models\Spk;
use App\Models\SpkNota;
use App\Models\SpkProduk;
use App\Models\SpkProdukNota;
use App\Models\SpkProdukNotaSrjalan;
use App\Models\Srjalan;
use App\Models\TipePacking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpkController extends Controller
{
    function create() {
        $label_pelanggans = Pelanggan::label_pelanggans();
        $label_produks = Produk::select('id', 'nama as label', 'nama as value')->get();

        // $produk_hargas = collect();
        // foreach ($label_produks as $produk) {
        //     $produk_harga = ProdukHarga::where('produk_id', $produk->id)->latest()->first();
        //     $produk_hargas->push($produk_harga);
        // }
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
        // dump($post);
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
            'keterangan'=>$post['keterangan'],
            'created_by'=>$user['username'],
            'updated_by'=>$user['username'],
            'created_at'=>$created_at,
            'pelanggan_nama'=>$pelanggan->nama,
            // 'cust_long'=>$cust_long,
            'cust_short'=>$cust_short,
            // 'cust_kontak'=>$cust_kontak,
            'reseller_nama'=>$reseller_nama,
            // 'reseller_long'=>$reseller_long,
            'reseller_short'=>$reseller_short,
            // 'reseller_kontak'=>$reseller_kontak,
        ]);
        // LANGSUNG UPDATE NO_SPK
        $new_spk->no_spk = "SPK-" . $new_spk->id;
        $new_spk->save();
        $success_ = "new_spk, updated";
        // END - SPK - CREATE dulu, supaya dapet ID nya

        // SPK PRODUKS
        $jumlah_total = 0;
        foreach ($post['produk_id'] as $key => $produk_id) {
            // $pelanggan_produk = PelangganProduk::where('pelanggan_id', $pelanggan->id)->where('produk_id', $produk_id)->latest()->first();
            // $harga = null;
            // if ($pelanggan_produk !== null) {
            //     $harga = $pelanggan_produk->harga_khusus;
            // } else {
            //     $produk_harga = ProdukHarga::where('produk_id', $produk_id)->latest()->first();
            //     $harga = $produk_harga->harga;
            // }
            // dd($post['produk_nama'][$key]);
            SpkProduk::create([
                'spk_id' => $new_spk->id,
                'produk_id' => $produk_id,
                'jumlah' => $post['produk_jumlah'][$key],
                // 'jml_blm_sls' => $post['produk_jumlah'][$key],
                'jumlah_total' => $post['produk_jumlah'][$key],
                // 'harga' => $harga,
                'keterangan' => $post['produk_keterangan'][$key],
                'status' => 'PROSES',
                'nama_produk' => $post['produk_nama'][$key],
            ]);
            $jumlah_total += (int)$post['produk_jumlah'][$key];
        }
        $success_ .= "- spk_produks";
        // END - SPK PRODUKS
        // UPDATE SPK: jumlah_total
        $new_spk->jumlah_total = $jumlah_total;
        $new_spk->save();
        $success_ .= "-spk: update jumlah_total-";
        // END - UPDATE SPK: jumlah_total
        $feedback = [
            'success_' => $success_
        ];
        return back()->with($feedback);
        // return redirect()->route('spks.show', $new_spk->id)->with($feedback);
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
        $label_pelanggans = Pelanggan::label_pelanggans();
        $label_produks = Produk::select('id', 'nama as label', 'nama as value')->get();
        // PILIHAN ALAMAT PELANGGAN
        $pelanggan_alamats = PelangganAlamat::where('pelanggan_id', $spk->pelanggan_id)->get();
        $pilihan_alamat = collect();
        $alamat_id_terpilih = null;
        $kontak_id_terpilih = null;
        if (count($data_spk_nota_srjalans['notas']) !== 0) {
            $alamat_id_terpilih = $data_spk_nota_srjalans['notas'][0]->alamat_id;
            $kontak_id_terpilih = $data_spk_nota_srjalans['notas'][0]->kontak_id;
        }
        foreach ($pelanggan_alamats as $pelanggan_alamat) {
            $alamat = Alamat::find($pelanggan_alamat->alamat_id);
            $pilihan_alamat->push($alamat);
        }
        // END - PILIHAN ALAMAT PELANGGAN
        // PILIHAN_KONTAK_PELANGGAN
        $pilihan_kontak = PelangganKontak::where('pelanggan_id', $spk->pelanggan_id)->get();
        // dd($pilihan_kontak);
        // END - PILIHAN_KONTAK_PELANGGAN
        // PILIHAN_EKSPEDISI
        $pelanggan_ekspedisis = PelangganEkspedisi::where('pelanggan_id', $spk->pelanggan_id)->where('is_transit','no')->get();
        $pilihan_ekspedisi = collect();
        foreach ($pelanggan_ekspedisis as $pelanggan_ekspedisi) {
            $ekspedisi = Ekspedisi::find($pelanggan_ekspedisi->ekspedisi_id);
            $ekspedisi_alamat = EkspedisiAlamat::where('ekspedisi_id', $ekspedisi->id)->where('tipe','UTAMA')->first();
            $alamat = Alamat::find($ekspedisi_alamat->alamat_id);
            $pilihan_ekspedisi->push([
                'id' => $ekspedisi->id,
                'nama' => $ekspedisi->nama,
                'alamat' => $alamat,
                'tipe' => $pelanggan_ekspedisi->tipe,
            ]);
        }
        $pelanggan_transits = PelangganEkspedisi::where('pelanggan_id', $spk->pelanggan_id)->where('is_transit','yes')->get();
        $pilihan_transit = collect();
        foreach ($pelanggan_transits as $pelanggan_transit) {
            $transit = Ekspedisi::find($pelanggan_transit->ekspedisi_id);
            $transit_alamat = EkspedisiAlamat::where('ekspedisi_id', $transit->id)->where('tipe','UTAMA')->first();
            $alamat = Alamat::find($transit_alamat->alamat_id);
            $pilihan_transit->push([
                'id' => $transit->id,
                'nama' => $transit->nama,
                'alamat' => $alamat,
                'tipe' => $pelanggan_transit->tipe,
            ]);
        }

        // dump($pilihan_ekspedisi);
        // dd($pilihan_transit);
        // END - PILIHAN_EKSPEDISI

        // DATA PACKING
        // $pilihan_srjalan = SpkProdukNotaSrjalan::where('spk_id', $spk->id)->select('srjalan_id as id')->groupBy('srjalan_id')->get();
        $pilihan_srjalan = collect();
        $data_packings = collect();
        $spk_notas = SpkNota::where('spk_id', $spk->id)->get();
        foreach ($spk_notas as $spk_nota) {
            $nota_srjalans = NotaSrjalan::where('nota_id', $spk_nota->nota_id)->get();
            foreach ($nota_srjalans as $nota_srjalan) {
                $srjalan = Srjalan::find($nota_srjalan->srjalan_id);
                $data_packings->push(Srjalan::get_data_packing($srjalan->jumlah_packing));
                $pilihan_srjalan->push(['id'=>$srjalan->id]);
            }
        }
        $pilihan_srjalan = $pilihan_srjalan->unique('id');
        // dump($spk_notas);
        // END - DATA PACKING
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
            'label_pelanggans' => $label_pelanggans,
            'label_produks' => $label_produks,
            'data_packings' => $data_packings,
            'alamat_id_terpilih' => $alamat_id_terpilih,
            'pilihan_alamat' => $pilihan_alamat,
            'pilihan_kontak' => $pilihan_kontak,
            'kontak_id_terpilih' => $kontak_id_terpilih,
            'pilihan_ekspedisi' => $pilihan_ekspedisi,
            'pilihan_transit' => $pilihan_transit,
            'pilihan_srjalan' => $pilihan_srjalan,
        ];
        // dump($data_spk_nota_srjalans['notas']);
        // dd($data_spk_nota_srjalans['col_srjalans']);
        // dd($data_spk_nota_srjalans['notas'][0]);
        // dd($data_packings);
        return view('spks.show', $data);
    }

    function spk_item_tetapkan_selesai(SpkProduk $spk_produk, Request $request) {
        $post = $request->post();
        // dump($post);
        // dd($spk_produk);
        // VALIDASI
        // JUMLAH TIDAK BOLEH KURANG DARI 0
        // kalau 0 masih gapapa, semisal sempet salah input, sebenarnya item ini belum selesai, maka reset ke 0.
        $jumlah = (int)$post['jumlah'];
        if ($jumlah < 0) {
            $request->validate(['error'=>'required'],['error.required'=>'jumlah < 0']);
        }
        // JUMLAH TIDAK BOLEH LEBIH DARI JUMLAH TOTAL SPK_PRODUK
        if ($jumlah > $spk_produk->jumlah_total) {
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
            if ($jumlah < $jumlah_sudah_nota) {
                $request->validate(['error'=>'required'],['error.required'=>'jumlah < jumlah_sudah_nota -> hapus/edit nota_item terlebih dahulu!']);
            }
        }
        // END - VALIDASI
        $status = 'BELUM';
        if ($jumlah === $spk_produk->jumlah_total) {
            $status = 'SELESAI';
        } elseif ($jumlah <= $spk_produk->jumlah_total && $jumlah > 0) {
            $status = 'SEBAGIAN';
        }
        $spk_produk->jumlah_selesai = $post['jumlah'];
        $spk_produk->status = $status;
        $spk_produk->save();

        return back()->with('success_','jumlah_selesai updated!');
    }

    function delete(Spk $spk) {
        // dd($spk);
        $danger_ = '';
        // CEK NOTA DAN SRJALAN
        // Nota dan Srjalan apabila sudah dibuat, tidak terhapus otomatis, oleh karena itu hapus nota dan srjalan
        $spk_notas = SpkNota::where('spk_id', $spk->id)->get();
        foreach ($spk_notas as $spk_nota) {
            $nota = Nota::find($spk_nota->nota_id);
            $nota_srjalans = NotaSrjalan::where('nota_id', $nota->id)->get();
            foreach ($nota_srjalans as $nota_srjalan) {
                $srjalan = Srjalan::find($nota_srjalan->srjalan_id);
                $srjalan->delete();
                $danger_ .= '-srjalan deleted-';
            }
            $nota->delete();
            $danger_ .= '-nota deleted-';
        }
        // END - CEK NOTA DAN SRJALAN
        $spk->delete();
        $danger_ .= '-spk deleted-';

        $feedback = [
            'danger_' => $danger_
        ];
        return redirect()->route('home')->with($feedback);
    }

    function selesai_all(Spk $spk) {
        // dd($spk);
        $spk_produks = SpkProduk::where('spk_id', $spk->id)->get();
        foreach ($spk_produks as $spk_produk) {
            $spk_produk->jumlah_selesai = $spk_produk->jumlah_total;
            $spk_produk->status = 'SELESAI';
            $spk_produk->save();
        }
        // UPDATE SPK - jumlah_selesai, status dan finished_at
        $user = Auth::user();
        $spk->jumlah_selesai = $spk->jumlah_total;
        $spk->status = 'SELESAI';
        $spk->finished_at = date("Y-m-d H:i:s");
        $spk->updated_by = $user->username;
        $spk->save();
        // END - UPDATE SPK - jumlah_selesai, status dan finished_at
        return back()->with('success_','-selesai_all-');
    }

    function edit_pelanggan(Spk $spk, Request $request) {
        $post = $request->post();
        // dump($post);
        // dd($spk);
        $success_ = '';
        $pelanggan_data = Pelanggan::data($post['pelanggan_id']);
        $pelanggan_nama = $pelanggan_data['nama'];
        $alamat_id = $pelanggan_data['alamat_id'];
        $kontak_id = $pelanggan_data['kontak_id'];
        $cust_long = $pelanggan_data['long'];
        $cust_short = $pelanggan_data['short'];
        $cust_kontak = $pelanggan_data['kontak'];
        $reseller_nama = null;
        $reseller_alamat_id = null;
        $reseller_kontak_id = null;
        $reseller_long = null;
        $reseller_short = null;
        $reseller_kontak = null;
        if ($post['reseller_id'] !== null) {
            $reseller_data = Pelanggan::data($post['reseller_id']);
            $reseller_nama = $reseller_data['nama'];
            $reseller_alamat_id = $reseller_data['alamat_id'];
            $reseller_kontak_id = $reseller_data['kontak_id'];
            $reseller_long = $reseller_data['long'];
            $reseller_short = $reseller_data['short'];
            $reseller_kontak = $reseller_data['kontak'];
        }
        // UPDATE SPK
        $user = Auth::user();
        $spk->update([
            'pelanggan_id'=>$post['pelanggan_id'],
            'reseller_id'=>$post['reseller_id'],
            // 'judul'=>$post['judul'],
            // 'created_by'=>$user['username'],
            'pelanggan_nama'=>$pelanggan_nama,
            'cust_long'=>$cust_long,
            'cust_short'=>$cust_short,
            'cust_kontak'=>$cust_kontak,
            'reseller_nama'=>$reseller_nama,
            'reseller_long'=>$reseller_long,
            'reseller_short'=>$reseller_short,
            'reseller_kontak'=>$reseller_kontak,
            'updated_by'=>$user->username,
        ]);
        $success_ .= '-spk updated-';
        // END - UPDATE SPK
        // APAKAH SPK SUDAH ADA NOTA DAN SURAT JALAN?
        // Kalau sudah maka data pelanggan dari nota dan srjalan terkait pun akan diedit
        $spk_notas = SpkNota::where('spk_id', $spk->id)->get();
        foreach ($spk_notas as $spk_nota) {
            $nota = Nota::find($spk_nota->nota_id);
            $nota->update([
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
                'updated_by'=>$user->username,
            ]);
            $success_ .= '-nota updated-';
            $nota_srjalans = NotaSrjalan::where('nota_id', $nota->id)->get();
            foreach ($nota_srjalans as $nota_srjalan) {
                // EKSPEDISI
                $ekspedisi_id = null;
                $ekspedisi_nama = null;
                $ekspedisi_alamat_id = null;
                $ekspedisi_kontak_id = null;
                $ekspedisi_long = null;
                $ekspedisi_short = null;
                $ekspedisi_kontak = null;
                // TRANSIT
                $transit_id = null;
                $transit_nama = null;
                $transit_alamat_id = null;
                $transit_kontak_id = null;
                $transit_long = null;
                $transit_short = null;
                $transit_kontak = null;
                $ekspedisi_data = PelangganEkspedisi::data($nota->pelanggan_id, false);
                // EKSPEDISI
                $ekspedisi_id = $ekspedisi_data['ekspedisi_id'];
                $ekspedisi_nama = $ekspedisi_data['ekspedisi_nama'];
                $ekspedisi_alamat_id = $ekspedisi_data['ekspedisi_alamat_id'];
                $ekspedisi_kontak_id = $ekspedisi_data['ekspedisi_kontak_id'];
                $ekspedisi_long = $ekspedisi_data['ekspedisi_long'];
                $ekspedisi_short = $ekspedisi_data['ekspedisi_short'];
                $ekspedisi_kontak = $ekspedisi_data['ekspedisi_kontak'];
                // TRANSIT
                $transit_data = PelangganEkspedisi::data($nota->pelanggan_id, true);
                $transit_id = $transit_data['transit_id'];
                $transit_nama = $transit_data['transit_nama'];
                $transit_alamat_id = $transit_data['transit_alamat_id'];
                $transit_kontak_id = $transit_data['transit_kontak_id'];
                $transit_long = $transit_data['transit_long'];
                $transit_short = $transit_data['transit_short'];
                $transit_kontak = $transit_data['transit_kontak'];
                $srjalan = Srjalan::find($nota_srjalan->srjalan_id);
                $srjalan->update([
                    // PELANGGAN
                    'pelanggan_id'=>$nota->pelanggan_id,
                    'pelanggan_nama'=>$nota->pelanggan_nama,
                    'alamat_id'=>$alamat_id,
                    'kontak_id'=>$kontak_id,
                    'cust_long'=>$cust_long,
                    'cust_short'=>$cust_short,
                    'cust_kontak'=>$cust_kontak,
                    // RESELLER
                    'reseller_id'=>$nota->reseller_id,
                    'reseller_nama'=>$nota->reseller_nama,
                    'reseller_alamat_id'=>$reseller_alamat_id,
                    'reseller_kontak_id'=>$reseller_kontak_id,
                    'reseller_long'=>$reseller_long,
                    'reseller_short'=>$reseller_short,
                    'reseller_kontak'=>$reseller_kontak,
                    // EKSPEDISI
                    'ekspedisi_id' => $ekspedisi_id,
                    'ekspedisi_nama' => $ekspedisi_nama,
                    'ekspedisi_alamat_id' => $ekspedisi_alamat_id,
                    'ekspedisi_kontak_id' => $ekspedisi_kontak_id,
                    'ekspedisi_long' => $ekspedisi_long,
                    'ekspedisi_short' => $ekspedisi_short,
                    'ekspedisi_kontak' => $ekspedisi_kontak,
                    // TRANSIT
                    'transit_id' => $transit_id,
                    'transit_nama' => $transit_nama,
                    'transit_alamat_id' => $transit_alamat_id,
                    'transit_kontak_id' => $transit_kontak_id,
                    'transit_long' => $transit_long,
                    'transit_short' => $transit_short,
                    'transit_kontak' => $transit_kontak,
                    //
                    // 'jumlah_packing'=>$jumlah_packing,
                    // 'created_by'=>$user->username,
                    'updated_by'=>$user->username,
                ]);
                $success_ .= '-srjalan updated-';
            }
        }
        // END - APAKAH SPK SUDAH ADA NOTA DAN SURAT JALAN?
        return back()->with('success_', $success_);
    }

    function edit_keterangan(Spk $spk, Request $request) {
        $post = $request->post();
        // dump($post);
        // dd($spk);
        $user = Auth::user();
        $spk->keterangan = $post['keterangan'];
        $spk->updated_by = $user->username;
        $spk->save();
        $success_ = '-$spk->keterangan updated-';
        return back()->with('success_', $success_);
    }

    function edit_tanggal(Spk $spk, Request $request) {
        $post = $request->post();
        // dump($post);
        // dd($spk);
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

        $spk->created_at = $created_at;
        $spk->finished_at = $finished_at;
        $spk->updated_by = $user->username;
        $spk->save();
        $success_ = '-$spk->created_at, finished_at updated-';
        return back()->with('success_', $success_);
    }

    function print_out(Spk $spk) {
        // dd($spk);
        $spk_produks = SpkProduk::where('spk_id', $spk->id)->get();
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'spks.print_out',
            'profile_menus' => Menu::get_profile_menus(),
            'spk' => $spk,
            'spk_produks' => $spk_produks,
        ];

        return view('spks.print_out', $data);
    }

    function add_item(Spk $spk, Request $request) {
        $post = $request->post();
        // dd($post);
        // dd($spk);
        $success_ = '';
        for ($i=0; $i < count($post['produk_id']); $i++) {
            // VALIDASI
            if ($post['produk_id'][$i] !== null) {
                if ($post['produk_jumlah'][$i] === null || $post['produk_jumlah'][$i] <= 0) {
                    $request->validate(['error'=>'required'],['error.required'=>'produk_jumlah?']);
                }
            }
            // END - VALIDASI
            SpkProduk::create([
                'spk_id' => $spk->id,
                'produk_id' => $post['produk_id'][$i],
                'jumlah' => $post['produk_jumlah'][$i],
                // 'jml_blm_sls' => $post['produk_jumlah'][$i],
                'jumlah_total' => $post['produk_jumlah'][$i],
                // 'harga' => $harga,
                'keterangan' => $post['produk_keterangan'][$i],
                'status' => 'PROSES',
                'nama_produk' => $post['produk_nama'][$i],
            ]);
            $success_ .= '-new spk_produk-';
        }
        Spk::update_data_SPK($spk);
        Nota::kaji_ulang_spk_dan_spk_produk($spk);
        Srjalan::kaji_ulang_spk_dan_spk_produk($spk);
        Nota::update_data_nota_srjalan($spk);
        $success_ .= '-Data SPK, Nota & Srjalan updated-';
        $feedback = [
            'success_' => $success_
        ];
        return back()->with($feedback);
    }

    function delete_item(Spk $spk, SpkProduk $spk_produk) {
        // dump($spk);
        // dd($spk_produk);
        $danger_ = '';
        $success_ = '';
        $spk_produk_notas = SpkProdukNota::where('spk_produk_id', $spk_produk->id)->get();
        foreach ($spk_produk_notas as $spk_produk_nota) {
            $spk_produk_nota->delete();
            $danger_ .= '-spk_produk_nota deleted-';
        }
        $spk_produk_nota_srjalans = SpkProdukNotaSrjalan::where('spk_produk_id', $spk_produk->id)->get();
        foreach ($spk_produk_nota_srjalans as $spk_produk_nota_srjalan) {
            $spk_produk_nota_srjalan->delete();
            $danger_ .= '-spk_produk_nota_srjalan deleted-';
        }
        $spk_produk->delete();
        $danger_ .= '-spk_produk deleted-';
        Spk::update_data_SPK($spk);
        Nota::kaji_ulang_spk_dan_spk_produk($spk);
        Srjalan::kaji_ulang_spk_dan_spk_produk($spk);
        Nota::update_data_nota_srjalan($spk);
        $srjalan_ids = SpkProdukNotaSrjalan::select('srjalan_id')->where('spk_id', $spk->id)->groupBy('srjalan_id')->get();
        foreach ($srjalan_ids as $srjalan) {
            $srjalan = Srjalan::find($srjalan->srjalan_id);
            Srjalan::update_jumlah_packing_srjalan($srjalan);
        }
        $success_ .= '-Data SPK, Nota dan Srjalan updated-';
        $feedback = ['danger_' => $danger_, 'success_' => $success_];
        return back()->with($feedback);
    }

    function edit_jumlah_deviasi(Spk $spk, SpkProduk $spk_produk, Request $request) {
        $post = $request->post();
        $jumlah = (int)$post['jumlah'];
        $deviasi = (int)$post['deviasi'];
        $jumlah_total = $deviasi + $jumlah;

        if ($jumlah_total < $spk_produk->jumlah_sudah_nota) {
            $request->validate(['error'=>'required'],['error.required'=>'jumlah_total < jumlah_sudah_nota -> hapus nota item terkait/edit jumlah nota item terkait']);
        }

        $jumlah_selesai = $spk_produk->jumlah_selesai;
        if ($jumlah_total < $jumlah_selesai) {
            $request->validate(['error'=>'required'],['error.required'=>'jumlah_total < jumlah_selesai']);
        }

        $spk_produk->jumlah = $jumlah;
        $spk_produk->deviasi_jumlah = $deviasi;
        $spk_produk->jumlah_total = $jumlah_total;
        $spk_produk->save();
        $success_ = '-spk_produk: jumlah, deviasi_jumlah, jumlah_total updated-';

        Spk::update_data_SPK($spk);
        $success_ .= '-SPK: data updated-';

        $feedback = [
            'success_' => $success_,
        ];
        return back()->with($feedback);
    }
}
