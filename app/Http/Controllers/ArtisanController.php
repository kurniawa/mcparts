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
use App\Models\TransactionName;
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
            $table->timestamp('tanggal_lunas')->nullable();
            $table->timestamps();
        });

        dump('create tables: pembelian_barangs');
    }

    function filling_pembelian_barang() {
        // Pengelompokkan berdasarkan supplier terlebih dahulu
        $suppliers = Supplier::all();
        $number_processed = 0;
        foreach ($suppliers as $key_supplier => $supplier) {
            if ($supplier->nama === 'Bpk. ISMAIL') {
                $pembelian_temps = PembelianTemp::where('supplier', 'like', "%ismail%")->orderBy('created_at')->get();
            } else {
                $pembelian_temps = PembelianTemp::where('supplier', 'like', "%$supplier->nama%")->orderBy('created_at')->get();
            }
            // pusing, coba sekarang lakukan pemisahan tanggal terlebih dahulu!
            $pembelian_temps_grouped_by_supplier_date = array();
            $arr_temp = array();

            if ($supplier->nama === 'TOKO BARU') {
                $arr_nomor_nota = array();
                $date_1 = null;
                foreach ($pembelian_temps as $key_pembelian_temp => $pembelian_temp) {
                    $suku_kata_nama_supplier = explode(' ', trim($pembelian_temp->supplier));
                    if (isset($suku_kata_nama_supplier[2])) {
                        // dump($suku_kata_nama_supplier[2]);
                        $exist_nomor_nota = false;
                        foreach ($arr_nomor_nota as $nomor_nota) {
                            if ($nomor_nota === $suku_kata_nama_supplier[2]) {
                                $exist_nomor_nota = true;
                            }
                        }
                        if (!$exist_nomor_nota) {
                            $nomor_nota_now = $suku_kata_nama_supplier[2];
                            $pembelian_temps_nomor_nota = PembelianTemp::where('supplier', "$supplier->nama $nomor_nota_now")->get();
                            $pembelian_temps_grouped_by_supplier_date[] = $pembelian_temps_nomor_nota;
                            $nomor_nota = $nomor_nota_now;
                            $arr_nomor_nota[] = $nomor_nota_now;
                        }
                        // else {
                        //     $nomor_nota_now = $suku_kata_nama_supplier[1];
                        //     $pembelian_temps_nomor_nota = PembelianTemp::where('supplier', "$supplier->nama $nomor_nota_now")->get();
                        // }
                    } else {
                        $date_2 = date('Y-m-d', strtotime($pembelian_temp->created_at));
                        if ($date_1 !== $date_2) {
                            $pembelian_temps_this = PembelianTemp::where('supplier', $supplier->nama)->whereDate('created_at', date('Y-m-d', strtotime($pembelian_temp->created_at)))->get();
                            $pembelian_temps_grouped_by_supplier_date[] = $pembelian_temps_this;
                            $date_1 = $date_2;
                        }
                    }
                    // $nomor_nota = explode(' ', trim($pembelian_temp->supplier))[2];
                    // dump($nomor_nota);
                }
            } elseif ($supplier->nama === 'ROYAL' || $supplier->nama === 'MAX') {
                $arr_nomor_nota = array();
                $date_1 = null;
                foreach ($pembelian_temps as $key_pembelian_temp => $pembelian_temp) {
                    $suku_kata_nama_supplier = explode(' ', trim($pembelian_temp->supplier));
                    if (isset($suku_kata_nama_supplier[1])) {
                        // dump($suku_kata_nama_supplier[1]);
                        $exist_nomor_nota = false;
                        foreach ($arr_nomor_nota as $nomor_nota) {
                            if ($nomor_nota === $suku_kata_nama_supplier[1]) {
                                $exist_nomor_nota = true;
                            }
                        }
                        if (!$exist_nomor_nota) {
                            $nomor_nota_now = $suku_kata_nama_supplier[1];
                            $pembelian_temps_nomor_nota = PembelianTemp::where('supplier', "$supplier->nama $nomor_nota_now")->get();
                            $pembelian_temps_grouped_by_supplier_date[] = $pembelian_temps_nomor_nota;
                            $nomor_nota = $nomor_nota_now;
                            $arr_nomor_nota[] = $nomor_nota_now;
                        }
                        // else {
                        //     $nomor_nota_now = $suku_kata_nama_supplier[1];
                        //     $pembelian_temps_nomor_nota = PembelianTemp::where('supplier', "$supplier->nama $nomor_nota_now")->get();
                        // }
                    } else {
                        $date_2 = date('Y-m-d', strtotime($pembelian_temp->created_at));
                        if ($date_1 !== $date_2) {
                            $pembelian_temps_this = PembelianTemp::where('supplier', $supplier->nama)->whereDate('created_at', date('Y-m-d', strtotime($pembelian_temp->created_at)))->get();
                            $pembelian_temps_grouped_by_supplier_date[] = $pembelian_temps_this;
                            $date_1 = $date_2;
                        }
                    }
                }
            } else {
                $date_1 = date('Y-m-d', strtotime($pembelian_temps[0]->created_at));
                foreach ($pembelian_temps as $key_pembelian_temp => $pembelian_temp) {
                    if ($key_pembelian_temp === 0) {
                        // dump("date_1: $date_1");
                        // $arr_temp[] = $key_pembelian_temp;
                        $arr_temp[] = $pembelian_temp;
                    } elseif ($key_pembelian_temp > 0) {
                        $date_2 = date('Y-m-d', strtotime($pembelian_temp->created_at));
                        if ($date_2 > $date_1) {
                            // dump("date_1: $date_1 > date_2: $date_2");
                            // kalau beda tanggal, arr_temp yang sudah ada masuk dulu ke arr_key_same_date
                            // lalu bikin arr_temp yang baru, yaitu kosongkan arr_temp dan tambah key sekarang.
                            $pembelian_temps_grouped_by_supplier_date[] = $arr_temp;
                            $arr_temp = array();
                            // $arr_temp[] = $key_pembelian_temp;
                            $arr_temp[] = $pembelian_temp;
                            $date_1 = $date_2;

                            if (count($pembelian_temps) === ($key_pembelian_temp + 1)) {
                                $pembelian_temps_grouped_by_supplier_date[] = $arr_temp;
                            }
                        } elseif ($date_2 === $date_1) {
                            // dump("date_1: $date_1 === date_2: $date_2");
                            // $arr_temp[] = $key_pembelian_temp;
                            $arr_temp[] = $pembelian_temp;
                            if (count($pembelian_temps) === ($key_pembelian_temp + 1)) {
                                $pembelian_temps_grouped_by_supplier_date[] = $arr_temp;
                            }
                        }
                    }
                }
            }


            // dump(count($pembelian_temps));
            // dump($arr_key_same_date);

            // dump($supplier->nama);
            // foreach ($pembelian_temps_grouped_by_supplier_date as $pembelian_temps) {
            //     dump($pembelian_temps);
            // }
            // DARI SINI
            foreach ($pembelian_temps_grouped_by_supplier_date as $pembelian_temps_grouped_by_date) {

                $pembelian = Pembelian::create([
                    'supplier_id' => $supplier->id,
                    'supplier_nama' => $supplier->nama,
                    'created_at' => $pembelian_temps_grouped_by_date[0]->created_at,
                ]);

                foreach ($pembelian_temps_grouped_by_date as $pembelian_temp) {
                    $number_processed++;
                    $barang = Barang::where('nama', $pembelian_temp->nama_barang)->first();
                    $jumlah_sub = null;
                    if ($pembelian_temp->jumlah_rol !== null) {
                        $jumlah_sub = (int)($pembelian_temp->jumlah_rol * 100);
                    }
                    PembelianBarang::create([
                        'pembelian_id' => $pembelian->id,
                        'barang_id' => $barang->id,
                        'barang_nama' => $barang->nama,
                        'satuan_main' => $pembelian_temp->satuan_meter,
                        'jumlah_main' => (int)($pembelian_temp->jumlah_meter * 100),
                        'harga_main' => (int)$pembelian_temp->harga_meter,
                        'satuan_sub' => $pembelian_temp->satuan_rol,
                        'jumlah_sub' => $jumlah_sub,
                        'harga_sub' => null,
                        'harga_t' => (int)$pembelian_temp->harga_total,
                        'status_bayar' => $pembelian_temp->status_pembayaran,
                        'keterangan_bayar' => $pembelian_temp->keterangan_pembayaran,
                        'tanggal_lunas' => $pembelian_temp->tanggal_lunas,
                        'created_at' => $pembelian_temp->created_at,
                        'updated_at' => $pembelian_temp->updated_at,
                        'creator' => $pembelian_temp->created_by,
                        'updater' => $pembelian_temp->updated_by,
                    ]);
                }

                $pembelian_barangs = PembelianBarang::where('pembelian_id', $pembelian->id)->orderBy('created_at')->get();

                $isi = array();
                $harga_total = 0;
                $status_bayar = 'BELUM';
                $jumlah_lunas = 0;
                $keterangan_bayar = '';
                $tanggal_lunas = null;

                foreach ($pembelian_barangs as $key_pembelian_barang => $pembelian_barang) {
                    $harga_total += (int)$pembelian_barang->harga_t;
                    $exist_satuan_main = false;
                    $exist_satuan_sub = false;
                    if (count($isi) !== 0) {
                        for ($i=0; $i < count($isi); $i++) {
                            if ($isi[$i]['satuan'] === $pembelian_barang->satuan_main) {
                                $isi[$i]['jumlah'] += (int)($pembelian_barang->jumlah_main);
                                $exist_satuan_main = true;
                            }
                            if ($isi[$i]['satuan'] === $pembelian_barang->satuan_sub) {
                                $isi[$i]['jumlah'] += (int)($pembelian_barang->jumlah_sub);
                                $exist_satuan_sub = true;
                            }
                        }
                    }
                    if (!$exist_satuan_main) {
                        $isi[] = [
                            'satuan' => $pembelian_barang->satuan_main,
                            'jumlah' => (int)($pembelian_barang->jumlah_main),
                        ];
                    }
                    if (!$exist_satuan_sub) {
                        if ($pembelian_barang->satuan_sub !== null) {
                            $isi[] = [
                                'satuan' => $pembelian_barang->satuan_sub,
                                'jumlah' => (int)($pembelian_barang->jumlah_sub),
                            ];
                        }
                    }
                    if ($pembelian_barang->status_bayar === 'LUNAS') {
                        $jumlah_lunas++;
                    }
                    $keterangan_bayar = $pembelian_barang->keterangan_bayar;
                    if ($tanggal_lunas === null) {
                        $tanggal_lunas = $pembelian_barang->tanggal_lunas;
                    } else {
                        if ($pembelian_barang->tanggal_lunas !== null) {
                            if (date('Y-m-d H:i:s', strtotime($tanggal_lunas)) < date('Y-m-d H:i:s', strtotime($pembelian_barang->tanggal_lunas))) {
                                $tanggal_lunas = $pembelian_barang->tanggal_lunas;
                            }
                        }
                    }
                }

                if ($jumlah_lunas === count($pembelian_barangs)) {
                    $status_bayar = 'LUNAS';
                } elseif ($jumlah_lunas < count($pembelian_barangs) && $jumlah_lunas > 0) {
                    $status_bayar = 'SEBAGIAN';
                }

                // if ($status_bayar === 'BELUM') {
                //     $tanggal_lunas = null;
                // }

                $nomor_nota = null;
                if ($supplier->nama === 'MAX' || $supplier->nama === 'ROYAL') {
                    $suku_kata_nama_supplier = explode(' ', trim($pembelian_temp->supplier));
                    if (isset($suku_kata_nama_supplier[1])) {
                        $nomor_nota = $suku_kata_nama_supplier[1];
                    }
                } elseif ($supplier->nama === 'TOKO BARU') {
                    $suku_kata_nama_supplier = explode(' ', trim($pembelian_temp->supplier));
                    if (isset($suku_kata_nama_supplier[2])) {
                        $nomor_nota = $suku_kata_nama_supplier[2];
                    } else {
                        $nomor_nota = "N-$pembelian->id";
                    }
                } else {
                    $nomor_nota = "N-$pembelian->id";
                }

                if (!$nomor_nota) {
                    dump("nomor nota? pembelian->supplier_nama: $pembelian->supplier_nama -- pembelian_temp->supplier: $pembelian_temp->supplier");
                }

                $pembelian->update([
                    'nomor_nota' => $nomor_nota,
                    'isi' => json_encode($isi),
                    'harga_total' => $harga_total,
                    'status_bayar' => $status_bayar,
                    'keterangan_bayar' => $keterangan_bayar,
                    'tanggal_lunas' => $tanggal_lunas,
                ]);

            }
            // SAMPE SINI

        }

        dump($number_processed);
        dump('filling_pembelian_barang');
        $pembelian_barangs = PembelianBarang::all();
        dump(count($pembelian_barangs));
        dump(count(PembelianTemp::all()));
    }
    // END - SUPPLIER

    // FUNGSI - ACCOUNTING
    function create_tables_for_accounting() {
        Schema::dropIfExists('kategoris');
        Schema::dropIfExists('transaction_names');
        Schema::dropIfExists('accountings');
        Schema::dropIfExists('user_instances');

        Schema::create('user_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('username', 50);
            $table->string('instance_type', 50); // safe, bank, e-wallet
            $table->string('instance_name', 50);
            $table->string('branch', 50)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('kode', 20)->nullable();
            $table->string('timerange', 50)->default('triwulan')->nullable(); // bulan, triwulan, caturwulan, semester
            $table->timestamps();
        });

        Schema::create('accountings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('username', 50);
            $table->foreignId('user_instance_id')->nullable()->constrained()->onDelete('set null');
            $table->string('instance_type', 50);
            $table->string('instance_name', 50);
            $table->string('branch', 50)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('kode', 20)->nullable();
            $table->string('transaction_type', 50); // pemasukan, pengeluaran
            $table->string('transaction_desc');
            $table->string('kategori_type',50)->nullable();
            $table->string('kategori_level_one',100)->nullable();
            $table->string('kategori_level_two',100)->nullable();
            $table->foreignId('related_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('related_username', 50)->nullable();
            $table->string('related_desc')->nullable();
            $table->foreignId('related_user_instance_id')->nullable()->constrained('user_instances')->onDelete('set null');
            $table->string('related_user_instance_type', 50)->nullable();
            $table->string('related_user_instance_name', 50)->nullable();
            $table->string('related_user_instance_branch', 50)->nullable();
            $table->foreignId('pelanggan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('pelanggan_nama')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_nama')->nullable();
            $table->string('keterangan')->nullable(); // keterangan tambahan akan ditulis dalam tanda kurung
            $table->bigInteger('jumlah');
            $table->bigInteger('saldo');
            $table->string('status', 20)->nullable(); // read or not read yet by other user
            $table->timestamps();
        });

        $user_instances = UserInstance::list_of_user_instances();

        foreach ($user_instances as $user_instance) {
            UserInstance::create([
                'user_id'=>$user_instance['user_id'],
                'username'=>$user_instance['username'],
                'instance_type'=>$user_instance['instance_type'],
                'instance_name'=>$user_instance['instance_name'],
                'branch'=>$user_instance['branch'],
                'account_number'=>$user_instance['account_number'],
                'kode'=>$user_instance['kode'],
                'timerange'=>$user_instance['timerange'],
            ]);
        }

        dump(Accounting::limit(100)->get());
        dump(UserInstance::all());
    }

    function create_table_for_transaction_names() {
        Schema::dropIfExists('kategoris');
        Schema::dropIfExists('transaction_names');

        Schema::create('transaction_names', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('username', 50);
            $table->foreignId('user_instance_id')->nullable()->constrained()->onDelete('set null');
            $table->string('user_instance_type', 50);
            $table->string('user_instance_name', 50);
            $table->string('user_instance_branch', 50);
            $table->string('desc');
            $table->string('kategori_type',50)->nullable();
            $table->string('kategori_level_one',100)->nullable();
            $table->string('kategori_level_two',100)->nullable();
            $table->foreignId('pelanggan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('pelanggan_nama')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_nama')->nullable();
            $table->foreignId('related_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('related_username', 50)->nullable();
            $table->string('related_desc')->nullable();
            $table->foreignId('related_user_instance_id')->nullable()->constrained('user_instances')->onDelete('set null');
            $table->string('related_user_instance_type', 50)->nullable();
            $table->string('related_user_instance_name', 50)->nullable();
            $table->string('related_user_instance_branch', 50)->nullable();
        });

        Schema::create('kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('kategori_level_one', 100);
            $table->string('kategori_level_two', 100)->nullable();
        });

        $kategoris = Kategori::list_of_kategoris();

        foreach ($kategoris as $kategori_types) {
            foreach ($kategori_types['kategori_level_one'] as $kategori_level_one) {
                // dump($kategori_types);
                if (isset($kategori_level_one['kategori_level_two'])) {
                    // dump($kategori_types['kategori_level_two']);
                    foreach ($kategori_level_one['kategori_level_two'] as $kategori_level_two) {
                        try {
                            Kategori::create([
                                'type'=>$kategori_types['type'],
                                'kategori_level_one'=>$kategori_level_one['name'],
                                'kategori_level_two' => $kategori_level_two['name']
                            ]);
                        } catch (\Throwable $th) {
                            dump('error');
                            dump($kategori_types);
                            dump($kategori_level_one['kategori_level_two']);
                            dd($kategori_level_two);
                        }
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

        list(
            $list_of_transaction_names_kas_ktr_1_albert,
            $list_of_transaction_names_kas_ktr_akhun,
            $list_of_transaction_names_kas_ktr_dian,
            $list_of_transaction_names_bca_mcp,
            $list_of_transaction_names_bca_dmd,
            $list_of_transaction_names_bri_dmd,
            $list_of_transaction_names_danamon_mcp,
            $list_of_transaction_names_kas_bg,
        ) = TransactionName::list_of_transaction_names();

        foreach ($list_of_transaction_names_kas_ktr_1_albert as $transaction_name_albert) {
            try {
                TransactionName::create([
                    'user_id'=>$transaction_name_albert['user_id'],
                    'username'=>$transaction_name_albert['username'],
                    'user_instance_id'=>$transaction_name_albert['user_instance_id'],
                    'user_instance_type'=>$transaction_name_albert['user_instance_type'],
                    'user_instance_name'=>$transaction_name_albert['user_instance_name'],
                    'user_instance_branch'=>$transaction_name_albert['user_instance_branch'],
                    'related_user_id'=>$transaction_name_albert['related_user_id'],
                    'related_username'=>$transaction_name_albert['related_username'],
                    'desc'=>$transaction_name_albert['desc'],
                    'kategori_type'=>$transaction_name_albert['kategori_type'],
                    'kategori_level_one'=>$transaction_name_albert['kategori_level_one'],
                    'kategori_level_two'=>$transaction_name_albert['kategori_level_two'],
                    'related_desc'=>$transaction_name_albert['related_desc'],
                    'pelanggan_id'=>$transaction_name_albert['pelanggan_id'],
                    'pelanggan_nama'=>$transaction_name_albert['pelanggan_nama'],
                    'supplier_id'=>$transaction_name_albert['supplier_id'],
                    'supplier_nama'=>$transaction_name_albert['supplier_nama'],
                    'related_user_instance_id'=>$transaction_name_albert['related_user_instance_id'],
                    'related_user_instance_type'=>$transaction_name_albert['related_user_instance_type'],
                    'related_user_instance_name'=>$transaction_name_albert['related_user_instance_name'],
                    'related_user_instance_branch'=>$transaction_name_albert['related_user_instance_branch'],
                ]);
            } catch (\Throwable $th) {
                dump($th);
                dd($transaction_name_albert);
            }
        }

        foreach ($list_of_transaction_names_kas_ktr_akhun as $transaction_name_akhun) {
            TransactionName::create([
                'user_id'=>$transaction_name_akhun['user_id'],
                'username'=>$transaction_name_akhun['username'],
                'user_instance_id'=>$transaction_name_akhun['user_instance_id'],
                'user_instance_type'=>$transaction_name_akhun['user_instance_type'],
                'user_instance_name'=>$transaction_name_akhun['user_instance_name'],
                'user_instance_branch'=>$transaction_name_akhun['user_instance_branch'],
                'related_user_id'=>$transaction_name_akhun['related_user_id'],
                'related_username'=>$transaction_name_akhun['related_username'],
                'desc'=>$transaction_name_akhun['desc'],
                'kategori_type'=>$transaction_name_akhun['kategori_type'],
                'kategori_level_one'=>$transaction_name_akhun['kategori_level_one'],
                'kategori_level_two'=>$transaction_name_akhun['kategori_level_two'],
                'related_desc'=>$transaction_name_akhun['related_desc'],
                'pelanggan_id'=>$transaction_name_akhun['pelanggan_id'],
                'pelanggan_nama'=>$transaction_name_akhun['pelanggan_nama'],
                'supplier_id'=>$transaction_name_akhun['supplier_id'],
                'supplier_nama'=>$transaction_name_akhun['supplier_nama'],
                'related_user_instance_id'=>$transaction_name_akhun['related_user_instance_id'],
                'related_user_instance_type'=>$transaction_name_akhun['related_user_instance_type'],
                'related_user_instance_name'=>$transaction_name_akhun['related_user_instance_name'],
                'related_user_instance_branch'=>$transaction_name_akhun['related_user_instance_branch'],
            ]);
        }

        foreach ($list_of_transaction_names_kas_ktr_dian as $transaction_name_dian) {
            TransactionName::create([
                'user_id'=>$transaction_name_dian['user_id'],
                'username'=>$transaction_name_dian['username'],
                'user_instance_id'=>$transaction_name_dian['user_instance_id'],
                'user_instance_type'=>$transaction_name_dian['user_instance_type'],
                'user_instance_name'=>$transaction_name_dian['user_instance_name'],
                'user_instance_branch'=>$transaction_name_dian['user_instance_branch'],
                'related_user_id'=>$transaction_name_dian['related_user_id'],
                'related_username'=>$transaction_name_dian['related_username'],
                'desc'=>$transaction_name_dian['desc'],
                'kategori_type'=>$transaction_name_dian['kategori_type'],
                'kategori_level_one'=>$transaction_name_dian['kategori_level_one'],
                'kategori_level_two'=>$transaction_name_dian['kategori_level_two'],
                'related_desc'=>$transaction_name_dian['related_desc'],
                'pelanggan_id'=>$transaction_name_dian['pelanggan_id'],
                'pelanggan_nama'=>$transaction_name_dian['pelanggan_nama'],
                'supplier_id'=>$transaction_name_dian['supplier_id'],
                'supplier_nama'=>$transaction_name_dian['supplier_nama'],
                'related_user_instance_id'=>$transaction_name_dian['related_user_instance_id'],
                'related_user_instance_type'=>$transaction_name_dian['related_user_instance_type'],
                'related_user_instance_name'=>$transaction_name_dian['related_user_instance_name'],
                'related_user_instance_branch'=>$transaction_name_dian['related_user_instance_branch'],
            ]);
        }

        foreach ($list_of_transaction_names_bca_mcp as $transaction_name_bca_mcp) {
            try {
                TransactionName::create([
                    'user_id'=>$transaction_name_bca_mcp['user_id'],
                    'username'=>$transaction_name_bca_mcp['username'],
                    'user_instance_id'=>$transaction_name_bca_mcp['user_instance_id'],
                    'user_instance_type'=>$transaction_name_bca_mcp['user_instance_type'],
                    'user_instance_name'=>$transaction_name_bca_mcp['user_instance_name'],
                    'user_instance_branch'=>$transaction_name_bca_mcp['user_instance_branch'],
                    'related_user_id'=>$transaction_name_bca_mcp['related_user_id'],
                    'related_username'=>$transaction_name_bca_mcp['related_username'],
                    'desc'=>$transaction_name_bca_mcp['desc'],
                    'kategori_type'=>$transaction_name_bca_mcp['kategori_type'],
                    'kategori_level_one'=>$transaction_name_bca_mcp['kategori_level_one'],
                    'kategori_level_two'=>$transaction_name_bca_mcp['kategori_level_two'],
                    'related_desc'=>$transaction_name_bca_mcp['related_desc'],
                    'pelanggan_id'=>$transaction_name_bca_mcp['pelanggan_id'],
                    'pelanggan_nama'=>$transaction_name_bca_mcp['pelanggan_nama'],
                    'supplier_id'=>$transaction_name_bca_mcp['supplier_id'],
                    'supplier_nama'=>$transaction_name_bca_mcp['supplier_nama'],
                    'related_user_instance_id'=>$transaction_name_bca_mcp['related_user_instance_id'],
                    'related_user_instance_type'=>$transaction_name_bca_mcp['related_user_instance_type'],
                    'related_user_instance_name'=>$transaction_name_bca_mcp['related_user_instance_name'],
                    'related_user_instance_branch'=>$transaction_name_bca_mcp['related_user_instance_branch'],
                ]);
            } catch (\Throwable $th) {
                dd($transaction_name_bca_mcp);
            }
        }

        foreach ($list_of_transaction_names_bca_dmd as $transaction_name_bca_dmd) {
            TransactionName::create([
                'user_id'=>$transaction_name_bca_dmd['user_id'],
                'username'=>$transaction_name_bca_dmd['username'],
                'user_instance_id'=>$transaction_name_bca_dmd['user_instance_id'],
                'user_instance_type'=>$transaction_name_bca_dmd['user_instance_type'],
                'user_instance_name'=>$transaction_name_bca_dmd['user_instance_name'],
                'user_instance_branch'=>$transaction_name_bca_dmd['user_instance_branch'],
                'related_user_id'=>$transaction_name_bca_dmd['related_user_id'],
                'related_username'=>$transaction_name_bca_dmd['related_username'],
                'desc'=>$transaction_name_bca_dmd['desc'],
                'kategori_type'=>$transaction_name_bca_dmd['kategori_type'],
                'kategori_level_one'=>$transaction_name_bca_dmd['kategori_level_one'],
                'kategori_level_two'=>$transaction_name_bca_dmd['kategori_level_two'],
                'related_desc'=>$transaction_name_bca_dmd['related_desc'],
                'pelanggan_id'=>$transaction_name_bca_dmd['pelanggan_id'],
                'pelanggan_nama'=>$transaction_name_bca_dmd['pelanggan_nama'],
                'supplier_id'=>$transaction_name_bca_dmd['supplier_id'],
                'supplier_nama'=>$transaction_name_bca_dmd['supplier_nama'],
                'related_user_instance_id'=>$transaction_name_bca_dmd['related_user_instance_id'],
                'related_user_instance_type'=>$transaction_name_bca_dmd['related_user_instance_type'],
                'related_user_instance_name'=>$transaction_name_bca_dmd['related_user_instance_name'],
                'related_user_instance_branch'=>$transaction_name_bca_dmd['related_user_instance_branch'],
            ]);
        }

        foreach ($list_of_transaction_names_bri_dmd as $transaction_name_bri_dmd) {
            TransactionName::create([
                'user_id'=>$transaction_name_bri_dmd['user_id'],
                'username'=>$transaction_name_bri_dmd['username'],
                'user_instance_id'=>$transaction_name_bri_dmd['user_instance_id'],
                'user_instance_type'=>$transaction_name_bri_dmd['user_instance_type'],
                'user_instance_name'=>$transaction_name_bri_dmd['user_instance_name'],
                'user_instance_branch'=>$transaction_name_bri_dmd['user_instance_branch'],
                'related_user_id'=>$transaction_name_bri_dmd['related_user_id'],
                'related_username'=>$transaction_name_bri_dmd['related_username'],
                'desc'=>$transaction_name_bri_dmd['desc'],
                'kategori_type'=>$transaction_name_bri_dmd['kategori_type'],
                'kategori_level_one'=>$transaction_name_bri_dmd['kategori_level_one'],
                'kategori_level_two'=>$transaction_name_bri_dmd['kategori_level_two'],
                'related_desc'=>$transaction_name_bri_dmd['related_desc'],
                'pelanggan_id'=>$transaction_name_bri_dmd['pelanggan_id'],
                'pelanggan_nama'=>$transaction_name_bri_dmd['pelanggan_nama'],
                'supplier_id'=>$transaction_name_bri_dmd['supplier_id'],
                'supplier_nama'=>$transaction_name_bri_dmd['supplier_nama'],
                'related_user_instance_id'=>$transaction_name_bri_dmd['related_user_instance_id'],
                'related_user_instance_type'=>$transaction_name_bri_dmd['related_user_instance_type'],
                'related_user_instance_name'=>$transaction_name_bri_dmd['related_user_instance_name'],
                'related_user_instance_branch'=>$transaction_name_bri_dmd['related_user_instance_branch'],
            ]);
        }

        foreach ($list_of_transaction_names_danamon_mcp as $transaction_name_danamon_mcp) {
            TransactionName::create([
                'user_id'=>$transaction_name_danamon_mcp['user_id'],
                'username'=>$transaction_name_danamon_mcp['username'],
                'user_instance_id'=>$transaction_name_danamon_mcp['user_instance_id'],
                'user_instance_type'=>$transaction_name_danamon_mcp['user_instance_type'],
                'user_instance_name'=>$transaction_name_danamon_mcp['user_instance_name'],
                'user_instance_branch'=>$transaction_name_danamon_mcp['user_instance_branch'],
                'related_user_id'=>$transaction_name_danamon_mcp['related_user_id'],
                'related_username'=>$transaction_name_danamon_mcp['related_username'],
                'desc'=>$transaction_name_danamon_mcp['desc'],
                'kategori_type'=>$transaction_name_danamon_mcp['kategori_type'],
                'kategori_level_one'=>$transaction_name_danamon_mcp['kategori_level_one'],
                'kategori_level_two'=>$transaction_name_danamon_mcp['kategori_level_two'],
                'related_desc'=>$transaction_name_danamon_mcp['related_desc'],
                'pelanggan_id'=>$transaction_name_danamon_mcp['pelanggan_id'],
                'pelanggan_nama'=>$transaction_name_danamon_mcp['pelanggan_nama'],
                'supplier_id'=>$transaction_name_danamon_mcp['supplier_id'],
                'supplier_nama'=>$transaction_name_danamon_mcp['supplier_nama'],
                'related_user_instance_id'=>$transaction_name_danamon_mcp['related_user_instance_id'],
                'related_user_instance_type'=>$transaction_name_danamon_mcp['related_user_instance_type'],
                'related_user_instance_name'=>$transaction_name_danamon_mcp['related_user_instance_name'],
                'related_user_instance_branch'=>$transaction_name_danamon_mcp['related_user_instance_branch'],
            ]);
        }

        foreach ($list_of_transaction_names_kas_bg as $transaction_name_kas_bg) {
            TransactionName::create([
                'user_id'=>$transaction_name_kas_bg['user_id'],
                'username'=>$transaction_name_kas_bg['username'],
                'user_instance_id'=>$transaction_name_kas_bg['user_instance_id'],
                'user_instance_type'=>$transaction_name_kas_bg['user_instance_type'],
                'user_instance_name'=>$transaction_name_kas_bg['user_instance_name'],
                'user_instance_branch'=>$transaction_name_kas_bg['user_instance_branch'],
                'related_user_id'=>$transaction_name_kas_bg['related_user_id'],
                'related_username'=>$transaction_name_kas_bg['related_username'],
                'desc'=>$transaction_name_kas_bg['desc'],
                'kategori_type'=>$transaction_name_kas_bg['kategori_type'],
                'kategori_level_one'=>$transaction_name_kas_bg['kategori_level_one'],
                'kategori_level_two'=>$transaction_name_kas_bg['kategori_level_two'],
                'related_desc'=>$transaction_name_kas_bg['related_desc'],
                'pelanggan_id'=>$transaction_name_kas_bg['pelanggan_id'],
                'pelanggan_nama'=>$transaction_name_kas_bg['pelanggan_nama'],
                'supplier_id'=>$transaction_name_kas_bg['supplier_id'],
                'supplier_nama'=>$transaction_name_kas_bg['supplier_nama'],
                'related_user_instance_id'=>$transaction_name_kas_bg['related_user_instance_id'],
                'related_user_instance_type'=>$transaction_name_kas_bg['related_user_instance_type'],
                'related_user_instance_name'=>$transaction_name_kas_bg['related_user_instance_name'],
                'related_user_instance_branch'=>$transaction_name_kas_bg['related_user_instance_branch'],
            ]);
        }

        $jumlah_transaction_names =
        count($list_of_transaction_names_kas_ktr_1_albert) +
        count($list_of_transaction_names_kas_ktr_akhun) +
        count($list_of_transaction_names_kas_ktr_dian) +
        count($list_of_transaction_names_bca_mcp) +
        count($list_of_transaction_names_bca_dmd) +
        count($list_of_transaction_names_bri_dmd) +
        count($list_of_transaction_names_danamon_mcp) +
        count($list_of_transaction_names_kas_bg);

        dump($jumlah_transaction_names);
        dump(TransactionName::all());
    }
    // END - FUNGSI - ACCOUNTING
    function filling_suppliers_dan_barangs() {
        $suppliers = PembelianTemp::select('supplier')->orderBy('supplier')->groupBy('supplier')->get();
        // dd($suppliers);
        // foreach ($suppliers as $supplier) {
        //     dump($supplier->supplier);
        // }
        // dd('finish');
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
            } elseif (str_contains($supplier->supplier, 'TOKO BARU')) {
                $nama = 'TOKO BARU';
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
        // $suppliers = Supplier::all();
        // dump(count($suppliers));
        // foreach ($suppliers as $supplier) {
        //     dump($supplier->nama);
        // }
        // dd('check');
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
                } elseif (str_contains($barang->supplier, 'TOKO BARU')) {
                    $supplier = Supplier::where('nama', 'TOKO BARU')->first();
                    // if (explode(' ', trim($barang->supplier))[0] === 'TOKO' && explode(' ', trim($barang->supplier))[1] === 'BARU') {
                    //     $supplier = Supplier::where('nama', 'TOKO BARU')->first();
                    // }
                } elseif (str_contains($barang->supplier, 'ISMAIL')) {
                    $supplier = Supplier::where('nama', 'Bpk. ISMAIL')->first();
                }
            }

            if ($supplier === null) {
                dump($barang->supplier);
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

    function accounting_update_data_rupiah() {
        // ACCOUNTING
        $accountings = Accounting::all();
        foreach ($accountings as $accounting) {
            $accounting->update([
                'jumlah' => (string)((int)$accounting->jumlah * 100),
                'saldo' => (string)((int)$accounting->saldo * 100)
            ]);
            // $accounting->jumlah = (int)$accounting->jumlah * 100;
            // $accounting->saldo = (int)$accounting->saldo * 100;
            // $accounting->save();
        }
        dump('data rupiah pada accounting diupdate.');
        // END - ACCOUNTING
    }

    function keterangan_untuk_spk_produk_nota() {
        $spk_produks = SpkProduk::all();
        foreach ($spk_produks as $spk_produk) {
            $spk_produk_notas = SpkProdukNota::where('spk_produk_id', $spk_produk->id)->get();
            foreach ($spk_produk_notas as $spk_produk_nota) {
                $spk_produk_nota->keterangan = $spk_produk->keterangan;
                $spk_produk_nota->save();
            }
        }
        return back()->with('success_', '-keterangan spk_produk_notas created-');
    }

    function data_pelanggan_id_pada_spk_produk_nota() {
        $spk_produk_notas = SpkProdukNota::all();
        foreach ($spk_produk_notas as $spk_produk_nota) {
            $spk = Spk::find($spk_produk_nota->spk_id);
            $spk_produk_nota->pelanggan_id = $spk->pelanggan_id;
            $spk_produk_nota->save();
        }
        return back()->with('success_', '-data pelanggan_id pada table spk_produk_notas berhasil ditambahkan-');
    }
}
