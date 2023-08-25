<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Nota;
use App\Models\NotaSrjalan;
use App\Models\Pembelian;
use App\Models\PembelianBarang;
use App\Models\PembelianTemp;
use App\Models\Produk;
use App\Models\Spk;
use App\Models\SpkNota;
use App\Models\SpkProduk;
use App\Models\SpkProdukNota;
use App\Models\SpkProdukNotaSrjalan;
use App\Models\Srjalan;
use App\Models\Supplier;
use App\Models\TipePacking;
use App\Models\User;
use App\Models\UserInstance;
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

    // function create_table_accounting() {
    //     Schema::dropIfExists('accounting_adis');
    //     Schema::dropIfExists('accounting_alberts');
    //     Schema::dropIfExists('accounting_dians');
    //     Schema::dropIfExists('accounting_demardis');

    //     Schema::create('accounting_adis', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreignId('user_id')->constrained()->onDelete('cascade');
    //         $table->enum('tipe',['pengeluaran','pemasukan']);
    //         $table->string('keterangan');
    //         $table->string('kode',20);
    //         $table->bigInteger('jumlah');
    //         $table->bigInteger('saldo');
    //         $table->string('created_by');
    //         $table->string('updated_by');
    //         // Tanggal sudah diatur pada timestamps: created_at, updated_at
    //     });
    //     $spks = Spk::all();
    //     // $spks = Spk::where('id',28)->get();
    //     $grouped_spk_produk_notas = collect();
    //     foreach ($spks as $spk) {
    //         $spk_produk_notas = SpkProdukNota::where('spk_id', $spk->id)->get();
    //         $grouped = $spk_produk_notas->groupBy('nota_id');
    //         $grouped_spk_produk_notas->push($grouped);
    //     }
    //     foreach ($grouped_spk_produk_notas as $arr_spk_produk_notas) {
    //         foreach ($arr_spk_produk_notas as $spk_produk_notas) {
    //             SpkNota::create([
    //                 'spk_id' => $spk_produk_notas[0]->spk_id,
    //                 'nota_id' => $spk_produk_notas[0]->nota_id,
    //             ]);
    //         }
    //     }
    //     $spk_notas = SpkNota::limit(10)->get();
    //     dd($spk_notas);
    // }

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

    // SUPPLIER
    function duplicate_pembelian_temps() {
        Schema::dropIfExists('pembelian_temps');

        Schema::create('pembelian_temps', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_nota', 20)->nullable();
            $table->string('supplier')->nullable();
            $table->string('nama_barang');
            $table->string('keterangan')->nullable();
            $table->string('satuan_rol', 50)->nullable();
            $table->string('satuan_meter', 50);
            $table->decimal('jumlah_rol', 6, 2)->nullable();
            $table->decimal('jumlah_meter', 10, 2);
            $table->decimal('harga_meter', 21, 2);
            $table->decimal('harga_total', 21, 2);
            $table->enum('status_pembayaran', ['BELUM', 'SEBAGIAN', 'LUNAS'])->nullable();
            $table->string('keterangan_pembayaran')->nullable();
            $table->timestamp('tanggal_lunas')->nullable();
            $table->timestamps();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
        });

        $pembelians = Pembelian::all()->each(function ($pembelian) {
            $pembelian_temp = $pembelian->replicate();
            $pembelian_temp->setTable('pembelian_temps');
            $pembelian_temp->save();
        });

        $pembelian_temps = PembelianTemp::all();
        for ($i=0; $i < count($pembelians); $i++) {
            $pembelian_temps[$i]->created_at = $pembelians[$i]->created_at;
            $pembelian_temps[$i]->updated_at = $pembelians[$i]->updated_at;
            $pembelian_temps[$i]->save();
        }
        dump($pembelian_temps);
    }

    function create_table_supplier_barang() {
        Schema::dropIfExists('pembelian_barangs');
        Schema::dropIfExists('pembelians');
        Schema::dropIfExists('supplier_kontaks');
        Schema::dropIfExists('supplier_alamats');
        Schema::dropIfExists('pembelian_barangs');
        Schema::dropIfExists('barangs');
        Schema::dropIfExists('suppliers');

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string("bentuk", 10)->nullable(); // PT, CV, Yayasan, Sekolah, dll.
            $table->string("nama", 100);
            $table->string("nama_pemilik", 100)->nullable();
            $table->string("initial", 10)->nullable();
            $table->string("keterangan")->nullable();
            $table->string('creator', 50)->nullable();
            $table->string('updater', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('supplier_alamats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('CASCADE');// hapus mah hapus aja, supaya ga menuh2 in database
            $table->foreignId('alamat_id')->constrained()->onDelete('CASCADE');
            $table->enum('tipe',['UTAMA','CADANGAN'])->default('CADANGAN');
            $table->timestamps();
        });

        Schema::create('supplier_kontaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('CASCADE');
            $table->string('tipe', 20)->nullable(); // kantor, rumah, hp
            $table->string('kodearea', 10)->nullable();
            $table->string('nomor', 20)->nullable();
            $table->enum('is_aktual',['yes','no'])->nullable()->default('no'); // ini untuk tujuan bila nomor terakhir belum tentu itu yang seharusnya otomatis tercantum di nota
            $table->string('lokasi',20)->nullable();// keterangan lokasi apabila di perlukan, terutama apabila nomor ini ber relasi dengan alamat.
            $table->timestamps();
        });

        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_nama');
            $table->string('nama');
            $table->string('satuan_main')->nullable();
            $table->string('satuan_sub')->nullable();
            $table->bigInteger('harga_main')->nullable();
            $table->bigInteger('harga_sub')->nullable();
            $table->integer('jumlah_main')->nullable();
            $table->integer('jumlah_sub')->nullable();
            $table->integer('harga_total_main')->nullable();
            $table->bigInteger('harga_total_sub')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        dump('-created table: suppliers, supplier_alamats, supplier_kontaks, barangs-');

        // Pengambilan data supplier dari semua pembelian yang sudah dibuat
        // Pengisian Table suppliers
        $suppliers = PembelianTemp::select('supplier')->orderBy('supplier')->groupBy('supplier')->get();
        // dd($suppliers);
        $user = Auth::user();
        foreach ($suppliers as $supplier) {
            $nama = $supplier->supplier;
            if (str_contains($supplier->supplier, 'MAX')) {
                // if (strtok($supplier->supplier) === 'MAX') {
                //     $nama = 'MAX';
                // }
                if (explode(' ', trim($supplier->supplier))[0] === 'MAX') {
                    $nama = 'MAX';
                }
            } elseif (str_contains($supplier->supplier, 'ROYAL')) {
                // if (strtok($supplier->supplier) === 'ROYAL') {
                //     $nama = 'ROYAL';
                // }
                if (explode(' ', trim($supplier->supplier))[0] === 'ROYAL') {
                    $nama = 'ROYAL';
                }
            } elseif (str_contains($supplier->supplier, 'ISMAIL')) {
                $nama = 'Bpk. ISMAIL';
            }
            $exist_supplier = Supplier::where('nama', $nama)->first();
            if (!$exist_supplier) {
                Supplier::create([
                    'nama' => $nama,
                    'creator' => $user->username,
                    'updater' => $user->username,
                ]);
            }
        }
        // END - Supplier

        // Pengisian Table barangs
        $barangs = PembelianTemp::orderBy('nama_barang')->get();
        // dd($barangs->groupBy('nama_barang')['AMPLAS (RY) 137']);
        $barangs_grouped = $barangs->groupBy('nama_barang');
        $barangs_grouped_filtered = collect();
        foreach ($barangs_grouped as $barang) {
            // dd($barang);
            $barangs_grouped_filtered->push($barang[0]);
        }
        // dd($barangs_grouped_filtered);
        foreach ($barangs_grouped_filtered as $barang) {
            // satuan_main, harga_main adalah acuan yang digunakan nantinya untuk menghitung harga_total
            // misal satuan_sub: rol, jumlah_sub: 5, satuan_main: meter, 1 rol berapa meter? -> jumlah_main: 20 meter, harga_main: 20000
            // jumlah_main dan harga_main adalah harga acuan/utama untuk penentu harga_total/ harga_total_main
            //
            $satuan_main = null;
            $satuan_sub = null;
            $harga_main = null;
            $harga_sub = null;
            $jumlah_main = null;
            $jumlah_sub = null;
            $harga_total_main = null;
            $harga_total_sub = null;
            if ($barang->satuan_rol !== null && $barang->satuan_meter !== null) {
                $satuan_main = $barang->satuan_meter;
                $satuan_sub = $barang->satuan_rol;
                $jumlah_sub = 100;
                $harga_main = (int)$barang->harga_meter;
                $jumlah_main = (int)($barang->jumlah_meter * 100);
                $harga_total_main = $barang->harga_meter * $barang->jumlah_meter;
                $harga_sub = $harga_total_main;
                $harga_total_sub = $harga_sub;
            } elseif ($barang->satuan_rol === null && $barang->satuan_meter !== null) {
                $satuan_main = $barang->satuan_meter;
                $harga_main = (int)$barang->harga_meter;
                $jumlah_main = (int)($barang->jumlah_meter * 100);
                $harga_total_main = $barang->harga_meter * $barang->jumlah_meter;
            } elseif ($barang->satuan_rol !== null && $barang->satuan_meter === null) {
                $satuan_main = $barang->satuan_rol;
                $harga_main = (int)($barang->harga_total / $barang->jumlah_rol);
                $jumlah_main = 1;
                $harga_total_main = $harga_main;
            }
            $supplier = Supplier::where('nama', $barang->supplier)->first();
            if ($supplier === null) {
                if (str_contains($barang->supplier, 'MAX')) {
                    if (explode(' ', trim($barang->supplier))[0] === 'MAX') {
                        $supplier = Supplier::where('nama', 'MAX')->first();
                    }
                } elseif (str_contains($barang->supplier, 'ROYAL')) {
                    if (explode(' ', trim($barang->supplier))[0] === 'ROYAL') {
                        $supplier = Supplier::where('nama', 'ROYAL')->first();
                    }
                } elseif (str_contains($barang->supplier, 'ISMAIL')) {
                    $supplier = Supplier::where('nama', 'Bpk. ISMAIL')->first();
                }
            }
            Barang::create([
                'supplier_id' => $supplier->id,
                'supplier_nama' => $supplier->nama,
                'nama' => $barang->nama_barang,
                'satuan_main' => $satuan_main,
                'satuan_sub' => $satuan_sub,
                'harga_main' => $harga_main,
                'harga_sub' => $harga_sub,
                'jumlah_sub' => $jumlah_sub,
                'jumlah_main' => $jumlah_main,
                'harga_total_main' => $harga_total_main,
                'harga_total_sub' => $harga_total_sub,
            ]);
        }
        // END - Pengisian Table barangs

        dump('filling records to barangs and suppliers');
    }

    function reset_schema_table_pembelian() {
        Schema::dropIfExists('pembelians');

        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_nota', 20)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_nama');
            $table->foreignId('supplier_alamat_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_long')->nullable();
            $table->string('supplier_short')->nullable();
            $table->foreignId('supplier_kontak_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_kontak')->nullable();
            // $table->foreignId('barang_id')->nullable()->constrained()->onDelete('set null'); // ga bisa taro sini, taronya di pembelian_barangs
            // $table->string('barang_nama');
            $table->string('keterangan')->nullable();
            $table->string('isi')->nullable();
            $table->bigInteger('harga_total')->nullable();
            $table->string('status_bayar', 20)->default('BELUM'); // ['BELUM', 'SEBAGIAN', 'LUNAS']
            $table->string('keterangan_bayar')->nullable();
            $table->timestamp('tanggal_lunas')->nullable();
            $table->timestamps();
            $table->string('creator', 50)->nullable();
            $table->string('updater', 50)->nullable();
        });

        dump('-table pembelians resetted-');
    }

    function create_table_pembelian_barangs() {
        Schema::dropIfExists('pembelian_barangs');

        Schema::create('pembelian_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained()->onDelete('cascade');
            // $table->foreignId('supplier_id')->nullable(); // tidak butuh data ini, karena sudah ada pada table pembelians
            // $table->string('supplier_nama');
            $table->foreignId('barang_id')->nullable()->constrained()->onDelete('set null');
            $table->string('barang_nama');
            $table->string('satuan_main');
            $table->integer('jumlah_main');
            $table->bigInteger('harga_main');
            $table->string('satuan_sub')->nullable();
            $table->integer('jumlah_sub')->nullable();
            $table->bigInteger('harga_sub')->nullable();
            $table->bigInteger('harga_t');
            $table->string('keterangan')->nullable();
            $table->string('status_bayar', 20)->default('BELUM'); // ['BELUM', 'SEBAGIAN', 'LUNAS']
            $table->string('keterangan_bayar')->nullable();
            $table->timestamps();
        });

        dump('create tables: pembelian_barangs');
    }

    function filling_pembelian_barang() {
        // Pengelompokkan Pembelian yang sudah ada
        // $pembelian = Pembelian::first();
        // dd($pembelian);
        $from = null;
        $until = null;
        $pembelians = PembelianTemp::orderBy('created_at')->get();
        $a = 0;
        // dump(count($pembelians));
        while ($a < count($pembelians)) {
            // dump($a);
            if ($a === 0) {
                $pembelian = PembelianTemp::first();
                $from = date('Y-m-d', strtotime($pembelian->created_at));
                $from .= ' 23:59:59';
                // dump($from);
                $until = $from;
                $pembelian_tanggal_terkaits = PembelianTemp::where('created_at', '<=', $from)->get();
            } else {
                // dump($from);
                $pembelian_tanggal_terkaits = PembelianTemp::where('created_at', '>', $from)->where('created_at', '<=', $until)->orderBy('created_at')->get();
            }
            // dump($pembelian_tanggal_terkaits);
            $pembelian_grouped_suppliers = $pembelian_tanggal_terkaits->groupBy('supplier');
            foreach ($pembelian_grouped_suppliers as $pembelian_grouped_supplier) {
                // if ($key === 0) {
                //     dump(date('Y-m-d', strtotime($pembelian_tanggal_terkait->created_at)));
                // }
                // dump($count_pembelians++);
                $supplier = Supplier::where('nama', $pembelian_grouped_supplier[0]->supplier)->first();
                $nomor_nota = null;
                if ($supplier === null) {
                    if (str_contains($pembelian_grouped_supplier[0]->supplier, 'MAX')) {
                        // if (strtok($pembelian_grouped_supplier[0]->supplier) === 'MAX') {
                        //     $supplier = Supplier::where('nama', 'MAX')->first();
                        //     $nomor_nota = explode(' ', trim($pembelian_grouped_supplier[0]->supplier))[1];
                        // }
                        if (explode(' ', trim($pembelian_grouped_supplier[0]->supplier))[0] === 'MAX') {
                            $supplier = Supplier::where('nama', 'MAX')->first();
                            $nomor_nota = explode(' ', trim($pembelian_grouped_supplier[0]->supplier))[1];
                        }
                    } elseif (str_contains($pembelian_grouped_supplier[0]->supplier, 'ROYAL')) {
                        // if (strtok($pembelian_grouped_supplier[0]->supplier) === 'ROYAL') {
                        //     $supplier = Supplier::where('nama', 'ROYAL')->first();
                        //     $nomor_nota = explode(' ', trim($pembelian_grouped_supplier[0]->supplier))[1];
                        // }
                        if (explode(' ', trim($pembelian_grouped_supplier[0]->supplier))[0] === 'ROYAL') {
                            $supplier = Supplier::where('nama', 'ROYAL')->first();
                            $nomor_nota = explode(' ', trim($pembelian_grouped_supplier[0]->supplier))[1];
                        }
                    } elseif (str_contains($pembelian_grouped_supplier[0]->supplier, 'ISMAIL')) {
                        $supplier = Supplier::where('nama', 'Bpk. ISMAIL')->first();
                    }
                }
                $pembelian_new = Pembelian::create([
                    'supplier_id' => $supplier->id,
                    'supplier_nama' => $supplier->nama,
                ]);

                $isi = array();
                $harga_total = 0;
                $status_bayar = 'BELUM';
                $jumlah_lunas = 0;
                $keterangan_bayar = '';
                $tanggal_lunas = null;
                $created_at = null;

                foreach ($pembelian_grouped_supplier as $pembelian_barang) {
                    $barang = Barang::where('nama', $pembelian_barang->nama_barang)->first();
                    $jumlah_sub = null;
                    if ($pembelian_barang->jumlah_rol !== null) {
                        $jumlah_sub = (int)($pembelian_barang->jumlah_rol * 100);
                    }
                    $pembelian_barang_new = PembelianBarang::create([
                        'pembelian_id' => $pembelian_new->id,
                        'barang_id' => $barang->id,
                        'barang_nama' => $barang->nama,
                        'satuan_main' => $pembelian_barang->satuan_meter,
                        'jumlah_main' => (int)($pembelian_barang->jumlah_meter * 100),
                        'harga_main' => (int)$pembelian_barang->harga_meter,
                        'satuan_sub' => $pembelian_barang->satuan_rol,
                        'jumlah_sub' => $jumlah_sub,
                        'harga_sub' => null,
                        'harga_t' => (int)$pembelian_barang->harga_total,
                        'status_bayar' => $pembelian_barang->status_pembayaran,
                        'keterangan_bayar' => $pembelian_barang->keterangan_pembayaran,
                        'tanggal_lunas' => $pembelian_barang->tanggal_lunas,
                        'created_at' => $pembelian_barang->created_at,
                        'updated_at' => $pembelian_barang->updated_at,
                        'creator' => $pembelian_barang->created_by,
                        'updater' => $pembelian_barang->updated_by,
                    ]);
                    $harga_total += (int)$pembelian_barang_new->harga_t;
                    $exist_satuan_main = false;
                    $exist_satuan_sub = false;
                    if (count($isi) !== 0) {
                        for ($i=0; $i < count($isi); $i++) {
                            if ($isi[$i]['satuan'] === $pembelian_barang->satuan_meter) {
                                $isi[$i]['jumlah'] += (int)($pembelian_barang->jumlah_meter * 100);
                                $exist_satuan_main = true;
                            }
                            if ($isi[$i]['satuan'] === $pembelian_barang->satuan_rol) {
                                $isi[$i]['jumlah'] += (int)($pembelian_barang->jumlah_rol * 100);
                                $exist_satuan_sub = true;
                            }
                        }
                        // foreach ($isi as $isi_item) {
                        //     if ($isi_item['satuan'] === $pembelian_barang->satuan_meter) {
                        //         $isi_item['jumlah'] += (int)($pembelian_barang->jumlah_meter);
                        //         $exist_satuan_main = true;
                        //     }
                        //     if ($isi_item['satuan'] === $pembelian_barang->satuan_rol) {
                        //         $isi_item['jumlah'] += (int)($pembelian_barang->jumlah_rol);
                        //         $exist_satuan_sub = true;
                        //     }
                        // }
                    }
                    if (!$exist_satuan_main) {
                        // $isi->push([
                        //     'satuan' => $pembelian_barang->satuan_meter,
                        //     'jumlah' => (int)($pembelian_barang->jumlah_meter),
                        // ]);
                        $isi[] = [
                            'satuan' => $pembelian_barang->satuan_meter,
                            'jumlah' => (int)($pembelian_barang->jumlah_meter * 100),
                        ];
                    }
                    if (!$exist_satuan_sub) {
                        if ($pembelian_barang->satuan_rol !== null) {
                            $isi[] = [
                                'satuan' => $pembelian_barang->satuan_rol,
                                'jumlah' => (int)($pembelian_barang->jumlah_rol * 100),
                            ];
                            // $isi->push([
                            //     'satuan' => $pembelian_barang->satuan_rol,
                            //     'jumlah' => (int)($pembelian_barang->jumlah_rol),
                            // ]);
                        }
                    }
                    if ($pembelian_barang->status_pembayaran === 'LUNAS') {
                        $jumlah_lunas++;
                    }
                    $keterangan_bayar = $pembelian_barang->keterangan_pembayaran;
                    if ($tanggal_lunas === null) {
                        $tanggal_lunas = $pembelian_barang->tanggal_lunas;
                    } else {
                        if ($pembelian_barang->tanggal_lunas !== null) {
                            if (date('Y-m-d H:i:s', strtotime($tanggal_lunas)) < date('Y-m-d H:i:s', strtotime($pembelian_barang->tanggal_lunas))) {
                                $tanggal_lunas = $pembelian_barang->tanggal_lunas;
                            }
                        }
                    }

                    if ($created_at === null) {
                        $created_at = $pembelian_barang->created_at;
                    } else {
                        if (date('Y-m-d H:i:s', strtotime($created_at)) > date('Y-m-d H:i:s', strtotime($pembelian_barang->created_at))) {
                            $created_at = $pembelian_barang->created_at;
                        }
                    }
                }
                // Perlu diupdate: nomor_nota, isi, status_bayar, keterangan_bayar, tanggal_lunas

                if ($jumlah_lunas === count($pembelian_grouped_supplier)) {
                    $status_bayar = 'LUNAS';
                } elseif ($jumlah_lunas < count($pembelian_grouped_supplier) && $jumlah_lunas > 0) {
                    $status_bayar = 'SEBAGIAN';
                }

                if ($nomor_nota === null) {
                    $nomor_nota = "N-$pembelian_new->id";
                }

                $pembelian_new->update([
                    'nomor_nota' => $nomor_nota,
                    'isi' => json_encode($isi),
                    'harga_total' => $harga_total,
                    'status_bayar' => $status_bayar,
                    'keterangan_bayar' => $keterangan_bayar,
                    'tanggal_lunas' => $tanggal_lunas,
                    'created_at' => $tanggal_lunas,
                ]);
            }
            $pembelian_tanggal_selanjutnya = PembelianTemp::where('created_at', '>', $until)->orderBy('created_at')->first();
            // if ($until > date('Y-m-d H:i:s', strtotime('2023-05-08 23:59:59'))) {
            //     dump('anomali');
            //     dump($until);
            //     dump($pembelian_tanggal_selanjutnya);
            // }
            if ($pembelian_tanggal_selanjutnya === null) {
                // dd($a);
                break;
            }
            // dump($pembelian_tanggal_selanjutnya->created_at);
            $until = date('Y-m-d', strtotime($pembelian_tanggal_selanjutnya->created_at));
            $until .= ' 23:59:59';
            $from = date('Y-m-d', strtotime($pembelian_tanggal_selanjutnya->created_at));
            $from .= ' 00:00:00';
            // $pembelian_tanggal_terkaits = Pembelian::where('created_at', '>', $from)->where('created_at', '<=', $until)->get();
            // dd($pembelian_tanggal_selanjutnya);
            // dump(count($pembelian_tanggal_terkaits));
            $a += count($pembelian_tanggal_terkaits);
        }

        // END - Pengelompokkan Pembelian yang sudah ada
        dump('filling_pembelian_barang');
        $pembelian_barangs = PembelianBarang::all();
        dump(count($pembelian_barangs));
    }
    // END - SUPPLIER

    // FUNGSI PRODUK
    // function duplicate_produk() {
    //     Schema::dropIfExists('produks_duplicate');

    //     Schema::create('produks_duplicate', function (Blueprint $table) {
    //         $table->id();
    //         $table->string('tipe', 50);
    //         $table->string('nama');
    //         $table->string('nama_nota');
    //         $table->string('tipe_packing', 20)->nullable();
    //         $table->smallInteger('aturan_packing')->nullable();
    //         $table->string('keterangan')->nullable();
    //         $table->timestamps();
    //         $table->string('creator', 50)->nullable();
    //         $table->string('updater', 50)->nullable();
    //     });

    //     $produks = Produk::all()->each(function ($produk) {
    //         $produk_temp = $produk->replicate();
    //         $produk_temp->setTable('produks_duplicate');
    //         $produk_temp->save();
    //     });

    //     $produks_duplicate = ProdukDuplicate::all();
    //     for ($i=0; $i < count($produks); $i++) {
    //         $produks_duplicate[$i]->created_at = $produks[$i]->created_at;
    //         $produks_duplicate[$i]->updated_at = $produks[$i]->updated_at;
    //         $produks_duplicate[$i]->save();
    //     }
    //     dump($produks_duplicate);
    // }

    // END - FUNGSI PRODUK

    // FUNGSI - ACCOUNTING
    function create_tables_for_accounting() {
        Schema::dropIfExists('accountings');
        Schema::dropIfExists('user_instances');

        Schema::create('user_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('username', 50);
            $table->string('instance_type', 50);
            $table->string('instance_name', 50);
            $table->string('branch', 50)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('timerange', 50)->default('triwulan')->nullable(); // bulan, triwulan, caturwulan, semester
            $table->timestamps();
        });

        Schema::create('accountings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('username', 50);
            $table->foreignId('related_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('related_username', 50)->nullable();
            $table->foreignId('user_instance_id')->nullable()->constrained()->onDelete('set null');
            $table->string('instance_type', 50);
            $table->string('instance_name', 50);
            $table->string('branch', 50)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('kode', 20)->nullable();
            $table->string('transaction_type', 50); // pemasukan, pengeluaran
            $table->string('transaction_desc');
            $table->string('related_desc')->nullable();
            $table->string('kategori_type',50)->nullable();
            $table->string('kategori_level_one',100)->nullable();
            $table->string('kategori_level_two',100)->nullable();
            $table->foreignId('pelanggan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('pelanggan_nama')->nullable();
            $table->string('keterangan')->nullable(); // keterangan tambahan akan ditulis dalam tanda kurung
            $table->bigInteger('jumlah');
            $table->bigInteger('saldo');
            $table->string('status', 20); // read or not read yet by other user
            $table->timestamps();
        });

        dump(Accounting::limit(100)->get());
        dump(UserInstance::all());
    }

    function create_table_transaction_names() {
        Schema::dropIfExists('kategoris');
        Schema::dropIfExists('transaction_names');

        Schema::create('transaction_names', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('username', 50);
            $table->foreignId('related_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('related_username', 50)->nullable();
            $table->string('desc');
            $table->string('kategori_type',50)->nullable();
            $table->string('kategori_level_one',100)->nullable();
            $table->string('kategori_level_two',100)->nullable();
            $table->string('related_desc')->nullable();
            $table->foreignId('pelanggan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('pelanggan_nama')->nullable();
        });

        Schema::create('kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('kategori_level_one', 100);
            $table->string('kategori_level_two', 100);
        });

        $kategoris = [
            [
                'type' => 'UANG MASUK', 'kategori_level_one'=>
                    [
                        ['name'=>'PENJUALAN CASH'],
                        ['name'=>'PENERIMAAN PIUTANG'],
                        ['name'=>'MUTASI DARI KAS KANTOR 1'],
                        ['name'=>'MUTASI DARI KAS KANTOR AKHUN'],
                        ['name'=>'MUTASI DARI KAS BCA MCP'],
                        ['name'=>'MUTASI DARI KAS BCA DMD'],
                        ['name'=>'MUTASI DARI KAS DANAMON MCP'],
                        ['name'=>'MUTASI DARI KAS BRI DMD'],
                        ['name'=>'MUTASI DARI KAS BG'],
                        ['name'=>'BUNGA TABUNGAN'],
                        ['name'=>'TITIPAN TRF'],
                        ['name'=>'PENGGANTI GIRO KOSONG/TOLAKAN KLIRING'],
                        ['name'=>'PENERIMAAN LAIN-LAIN'],
                        ['name'=>'UTANG LAIN-LAIN']
                    ],
            ],
            [
                'type' => 'UANG KELUAR', 'kategori_level_one'=>
                    [
                        ['name'=>'BAYAR HUTANG BAHAN BAKU'],
                        ['name'=>'BELI BAHAN BAKU CASH'],
                        ['name'=>'BAYAR HUTANG BAHAN PENDUKUNG'],
                        ['name'=>'BELI BAHAN PENDUKUNG CASH'],
                        ['name'=>'MUTASI KE KAS KANTOR 1'],
                        ['name'=>'MUTASI KE KAS KANTOR DMD'],
                        ['name'=>'MUTASI KE KAS KANTOR DIAN'],
                        ['name'=>'MUTASI KE KAS KANTOR AKHUN'],
                        ['name'=>'MUTASI KE KAS BCA MCP'],
                        ['name'=>'MUTASI KE KAS BCA DMD'],
                        ['name'=>'MUTASI KE KAS DANAMON MCP'],
                        ['name'=>'MUTASI KE KAS BRI DMD'],
                        ['name'=>'PRIVE'],
                        ['name'=>'CASHBON KARYAWAN'],
                        ['name'=>'REFUND - KELEBIHAN PEMBAYARAN'],
                        ['name'=>'TITIP SIMPAN GAJI KARYAWAN'],
                        ['name'=>'AMBIL TITIPAN TRF'],
                        ['name'=>'PAJAK BUNGA TABUNGAN'],
                        ['name'=>'BIAYA TENAGA KERJA LANGSUNG', 'kategori_level_two'=>
                            ['name'=>'GAJI DAN UPAH'],
                            ['name'=>'LEMBUR'],
                            ['name'=>'TUNJANGAN KARYAWAN'],
                            ['name'=>'KOMISI PENJUALAN'],
                        ],
                        ['name'=>'BIAYA BAHAN PENDUKUNG', 'kategori_level_two'=>
                            ['name'=>'PERLENGKAPAN SABLON'],
                            ['name'=>'PERLENGKAPAN JAHIT'],
                            ['name'=>'POLIMAS'],
                            ['name'=>'PACKING'],
                            ['name'=>'JASA BORDIR'],
                        ],
                        ['name'=>'BIAYA PENGIRIMAN BARANG', 'kategori_level_two'=>
                            ['name'=>'OPERASIONAL PENGIRIMAN'],
                        ],
                        ['name'=>'BIAYA UTILITAS', 'kategori_level_two'=>
                            ['name'=>'LISTRIK'],
                            ['name'=>'TELEPON'],
                            ['name'=>'INTERNET'],
                        ],
                        ['name'=>'PAJAK', 'kategori_level_two'=>
                            ['name'=>'PPH PASAL 21'],
                            ['name'=>'PPH PASAL 25 DAN 29'],
                        ],
                        ['name'=>'BIAYA INVENTARIS (PERALATAN DAN PERLENGKAPAN)', 'kategori_level_two'=>
                            ['name'=>'ATK'],
                            ['name'=>'PERALATAN DAN PERLENGKAPAN PRODUKSI'],
                            ['name'=>'CICILAN MOBIL TRAGA'],
                        ],
                        ['name'=>'BIAYA MAINTENANCE', 'kategori_level_two'=>
                            ['name'=>'PERAWATAN KENDARAAN'],
                            ['name'=>'PERAWATAN MESIN'],
                            ['name'=>'PERAWATAN INVENTARIS'],
                        ],
                        ['name'=>'BIAYA LAIN-LAIN', 'kategori_level_two'=>
                            ['name'=>'ADMINISTRASI BANK'],
                            ['name'=>'ENTERTAIN PELANGGAN'],
                            ['name'=>'KUNJUNGAN KE DAERAH'],
                            ['name'=>'LAIN-LAIN'],
                        ],
                    ],
            ],
        ];

        foreach ($kategoris as $kategori_types) {
            foreach ($kategori_types['kategori_level_one'] as $kategori_level_one) {
                if (isset($kategori_level_one['kategori_level_two'])) {
                    foreach ($kategori_level_one['kategori_level_two'] as $kategori_level_two) {
                        Kategori::create([
                            'type'=>$kategori_types['type'],
                            'kategori_level_one'=>$kategori_level_one['name'],
                            'kategori_level_two' => $kategori_level_two['name']
                        ]);
                    }
                } else {
                    Kategori::create([
                        'type'=>$kategori_types['type'],
                        'kategori_level_one'=>$kategori_level_one['name'],
                        'kategori_level_two' => null
                    ]);
                }
            }
        }

        $list_of_transaction_names_albert = [
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'CASHBON MINGGUAN',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'GAJI DAN UPAH',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'UPAH MINGGUAN',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'GAJI DAN UPAH',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'DLL',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA LAIN-LAIN',
                'kategori_level_two'=>'LAIN-LAIN',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'MUTASI DARI KAS KANTOR AK',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'BIAYA LAIN-LAIN',
                'kategori_level_two'=>'LAIN-LAIN',
                'related_user_id'=>2,
                'related_username'=>'kuruniawa',
                'related_desc'=>'MUTASI KE KAS KANTOR ALBERT'
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'PBK TLP',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA UTILITAS',
                'kategori_level_two'=>'TELEPON',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'name'=>'PRIVE DMD',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'PRIVE',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'REIMBURSE U.MKN',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'TUNJANGAN KARYAWAN',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'REIMBURSE U.BEROBAT',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'TUNJANGAN KARYAWAN',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'REIMBURSE U.KESEHATAN',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'TUNJANGAN KARYAWAN',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'REIMBURSE U.TRANSPORT',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'TUNJANGAN KARYAWAN',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'SISA GAJI',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'GAJI DAN UPAH',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'UPAH MINGGUAN AKHIR',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'GAJI DAN UPAH',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'UPAH BULANAN',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'GAJI DAN UPAH',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'PPH PS 21',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'PAJAK',
                'kategori_level_two'=>'PPH PASAL 21',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'PPH PS 25',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'PAJAK',
                'kategori_level_two'=>'PPH PASAL 25 DAN 29',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'MAKAN SIANG',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'GAJI DAN UPAH',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'INDIHOME MCP',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA UTILITAS',
                'kategori_level_two'=>'INTERNET',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
            ],
        ];

        $list_of_transaction_names_bca_dmd = [
            // PIUTANG
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG 3M - SURABAYA',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'pelanggan_id'=>2,
                'pelanggan_nama'=>'3M',
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'BERJAYA MOTOR',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'pelanggan_id'=>17,
                'pelanggan_nama'=>'Berjaya Motor',
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG BUDI STIKER - MEDAN',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'pelanggan_id'=>20,
                'pelanggan_nama'=>'Budi Stiker',
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG DARWIS MOTOR',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'pelanggan_id'=>26,
                'pelanggan_nama'=>'Darwis Motor',
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG GLOBAL STIKER',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'pelanggan_id'=>33,
                'pelanggan_nama'=>'Global Stiker',
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG HOKKY MOTOR - MANADO',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'pelanggan_id'=>37,
                'pelanggan_nama'=>'Hokky Motor',
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG IPM',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'pelanggan_id'=>42,
                'pelanggan_nama'=>'Indo Putra Mandiri',
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG JAYA MOTOR - MANADO',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'pelanggan_id'=>45,
                'pelanggan_nama'=>'Jaya Motor',
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG KMS Motor',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'pelanggan_id'=>55,
                'pelanggan_nama'=>'KMS Motor',
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG MILLENIUM MOTOR - PALEMBANG',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'pelanggan_id'=>58,
                'pelanggan_nama'=>'Millenium Motor',
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG NAGATA - SINTANG',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'pelanggan_id'=>64,
                'pelanggan_nama'=>'Nagata Motor',
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG NGK MOTOR - JAMBI',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'pelanggan_id'=>65,
                'pelanggan_nama'=>'NGK Motor',
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG WA - MAKASSAR',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'pelanggan_id'=>100,
                'pelanggan_nama'=>'WA',
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG WK MOTOR - SEI PINYUH',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'pelanggan_id'=>101,
                'pelanggan_nama'=>'WK Motor',
            ],
            // END - PIUTANG
        ];

        $list_of_transaction_names_bri_dmd = [
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG JHON MOTOR - MAKASSAR',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'pelanggan_id'=>48,
                'pelanggan_nama'=>'Jhon Motor',
            ],[
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG ZONA MOTOR',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'pelanggan_id'=>104,
                'pelanggan_nama'=>'Zona Motor',
            ],
        ];
    }
    // END - FUNGSI - ACCOUNTING
}
