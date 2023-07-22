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
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ArtisanController extends Controller
{
    function index() {
        $data = [
            'route_now' => 'artisan.index',
            'menus' => Menu::get(),
            'profile_menus' => Menu::get_profile_menus(),
        ];
        return view('artisan.index', $data);
    }

    function change_column_name(Request $request) {
        $post = $request->post();
        Schema::table($post['table_name'], function (Blueprint $table) use ($post) {
            $table->renameColumn($post['column_name_old'], $post['column_name_new']);
        });
        return back();
    }

    function lower_case_role() {
        $users = User::all();
        foreach ($users as $user) {
            $user->role = strtolower($user->role);
            $user->save();
        }
        return back()->with('success_','lower_case_role all');
    }

    function create_spk_nota_relation() {
        Schema::dropIfExists('spk_notas');

        Schema::create('spk_notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_id')->constrained()->onDelete('cascade');
            $table->foreignId('nota_id')->constrained()->onDelete('cascade');
        });
        $spks = Spk::all();
        // $spks = Spk::where('id',28)->get();
        $grouped_spk_produk_notas = collect();
        foreach ($spks as $spk) {
            $spk_produk_notas = SpkProdukNota::where('spk_id', $spk->id)->get();
            $grouped = $spk_produk_notas->groupBy('nota_id');
            $grouped_spk_produk_notas->push($grouped);
        }
        foreach ($grouped_spk_produk_notas as $arr_spk_produk_notas) {
            foreach ($arr_spk_produk_notas as $spk_produk_notas) {
                SpkNota::create([
                    'spk_id' => $spk_produk_notas[0]->spk_id,
                    'nota_id' => $spk_produk_notas[0]->nota_id,
                ]);
            }
        }
        $spk_notas = SpkNota::limit(10)->get();
        dd($spk_notas);
    }

    function create_nota_srjalan_relation() {
        Schema::dropIfExists('nota_srjalans');

        Schema::create('nota_srjalans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_id')->constrained()->onDelete('cascade');
            $table->foreignId('srjalan_id')->constrained()->onDelete('cascade');
        });
        $notas = Nota::all();
        // $notas = Spk::where('id',28)->get();
        $grouped_spk_produk_nota_srjalans = collect();
        foreach ($notas as $nota) {
            $spk_produk_nota_srjalans = SpkProdukNotaSrjalan::where('nota_id', $nota->id)->get();
            $grouped = $spk_produk_nota_srjalans->groupBy('srjalan_id');
            $grouped_spk_produk_nota_srjalans->push($grouped);
        }
        foreach ($grouped_spk_produk_nota_srjalans as $arr_spk_produk_nota_srjalans) {
            foreach ($arr_spk_produk_nota_srjalans as $spk_produk_nota_srjalans) {
                NotaSrjalan::create([
                    'nota_id' => $spk_produk_nota_srjalans[0]->nota_id,
                    'srjalan_id' => $spk_produk_nota_srjalans[0]->srjalan_id,
                ]);
            }
        }
        $nota_srjalans = NotaSrjalan::limit(10)->get();
        dd($nota_srjalans);
    }

    function migrate_fresh_seed(Request $request) {
        if (Auth::user()->role !== 'developer') {
            $request->validate(['error'=>'required'],['error.required'=>'clearance?']);
        }
        Artisan::call('migrate:fresh --seed');
        dd(Artisan::output());
    }
    function symbolic_link(Request $request) {
        if (Auth::user()->role !== 'developer') {
            $request->validate(['error'=>'required'],['error.required'=>'clearance?']);
        }
        Artisan::call('storage:link');
        dd(Artisan::output());
    }
    function optimize_clear(Request $request) {
        if (Auth::user()->role !== 'developer') {
            $request->validate(['error'=>'required'],['error.required'=>'clearance?']);
        }
        Artisan::call('optimize:clear');
        dd(Artisan::output());
    }

    function spk_produk_fix_nama_produk() {
        $spk_produks = SpkProduk::all();
        foreach ($spk_produks as $spk_produk) {
            if ($spk_produk->nama_produk === null) {
                $produk = Produk::find($spk_produk->produk_id);
                $spk_produk->nama_produk = $produk->nama;
                $spk_produk->save();
            }
        }
        return back()->with('success_','spk_produks: nama_produk yang null sudah diisi!');
    }

    function srjalan_fix_jumlah_packing() {
        $srjalans = Srjalan::all();
        foreach ($srjalans as $srjalan) {
            $spk_produk_nota_srjalans = SpkProdukNotaSrjalan::where('srjalan_id', $srjalan->id)->get();
            $jml_dus = 0;
            $jml_dus_pcs = 0;
            $jml_colly = 0;
            $jml_colly_pcs = 0;
            $jml_rol = 0;
            $jml_rol_pcs = 0;
            $jml_bal = 0;
            $jml_bal_pcs = 0;
            foreach ($spk_produk_nota_srjalans as $spk_produk_nota_srjalan) {
                if ($spk_produk_nota_srjalan->tipe_packing === 'colly') {
                    $jml_colly_pcs += $spk_produk_nota_srjalan->jumlah;
                    $jml_colly = $srjalan->jml_colly;
                } elseif ($spk_produk_nota_srjalan->tipe_packing === 'dus') {
                    $jml_dus_pcs += $spk_produk_nota_srjalan->jumlah;
                    $jml_dus = $srjalan->jml_dus;
                } elseif ($spk_produk_nota_srjalan->tipe_packing === 'rol') {
                    $jml_rol_pcs += $spk_produk_nota_srjalan->jumlah;
                    $jml_rol = $srjalan->jml_rol;
                } elseif ($spk_produk_nota_srjalan->tipe_packing === 'bal') {
                    $jml_bal_pcs += $spk_produk_nota_srjalan->jumlah;
                    $jml_bal += $spk_produk_nota_srjalan->jumlah_packing;
                }
            }
            $jumlah_packing = array();
            if ($jml_colly !== 0) {
                $jumlah_packing[]=(["tipe_packing"=>"colly","jumlah"=>$jml_colly_pcs,"jumlah_packing"=>$jml_colly]);
            }
            if ($jml_dus !== 0) {
                $jumlah_packing[]=(["tipe_packing"=>"dus","jumlah"=>$jml_dus_pcs,"jumlah_packing"=>$jml_dus]);
            }
            if ($jml_rol !== 0) {
                $jumlah_packing[]=(["tipe_packing"=>"rol","jumlah"=>$jml_rol_pcs,"jumlah_packing"=>$jml_rol]);
            }
            if ($jml_bal !== 0) {
                $jumlah_packing[]=(["tipe_packing"=>"bal","jumlah"=>$jml_bal_pcs,"jumlah_packing"=>$jml_bal]);
            }
            $srjalan->jumlah_packing = json_encode($jumlah_packing);
            $srjalan->save();
        }

        return back()->with('success_','srjalan fix jumlah_packing');
    }

    function create_table_accounting() {
        Schema::dropIfExists('accounting_adis');
        Schema::dropIfExists('accounting_alberts');
        Schema::dropIfExists('accounting_dians');
        Schema::dropIfExists('accounting_demardis');

        Schema::create('accounting_adis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('tipe',['pengeluaran','pemasukan']);
            $table->string('keterangan');
            $table->string('kode',20);
            $table->bigInteger('jumlah');
            $table->bigInteger('saldo');
            $table->string('created_by');
            $table->string('updated_by');
            // Tanggal sudah diatur pada timestamps: created_at, updated_at
        });
        $spks = Spk::all();
        // $spks = Spk::where('id',28)->get();
        $grouped_spk_produk_notas = collect();
        foreach ($spks as $spk) {
            $spk_produk_notas = SpkProdukNota::where('spk_id', $spk->id)->get();
            $grouped = $spk_produk_notas->groupBy('nota_id');
            $grouped_spk_produk_notas->push($grouped);
        }
        foreach ($grouped_spk_produk_notas as $arr_spk_produk_notas) {
            foreach ($arr_spk_produk_notas as $spk_produk_notas) {
                SpkNota::create([
                    'spk_id' => $spk_produk_notas[0]->spk_id,
                    'nota_id' => $spk_produk_notas[0]->nota_id,
                ]);
            }
        }
        $spk_notas = SpkNota::limit(10)->get();
        dd($spk_notas);
    }

    function create_table_tipe_packing() {
        Schema::dropIfExists('tipe_packings');

        Schema::create('tipe_packings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        $tipe_packings = [
            ['name'=>'bal'],['name'=>'colly'],['name'=>'dus'],['name'=>'rol']
        ];

        foreach ($tipe_packings as $tipe_packing) {
            TipePacking::create(['name'=>$tipe_packing['name']]);
        }

        $tipe_packings = TipePacking::all();
        dd($tipe_packings);
    }
}
