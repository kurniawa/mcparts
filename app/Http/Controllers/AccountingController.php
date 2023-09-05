<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\TransactionName;
use App\Models\User;
use App\Models\UserInstance;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AccountingController extends Controller
{
    function index() {
        $user = Auth::user();

        $user_instance_this = UserInstance::where('user_id', $user->id)->get();
        $user_instances = UserInstance::all();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'accounting.index',
            'parent_route' => 'accounting.index',
            'profile_menus' => Menu::get_profile_menus(),
            'accounting_menus' => Menu::get_accounting_menus(),
            'user' => $user,
            'instance_types' => Accounting::get_instance_types(),
            'instance_names' => Accounting::get_instance_names(),
            'user_instance_this' => $user_instance_this,
            'user_instances' => $user_instances,
        ];
        // dump($user_instances);
        return view('accounting.index', $data);
    }

    function create_kas(Request $request) {
        $post = $request->post();
        // dd($post);

        $request->validate([
            // 'table_name' => 'required',
            'instance_type' => 'required',
            'instance_name' => 'required',
        ]);

        // dd(Schema::hasTable($post['table_name']));

        // if (Schema::hasTable($post['table_name'])) {
        //     $request->validate(['error'=>'required'],['error.required'=>'table name exist!']);
        // }

        // Schema::dropIfExists($post['table_name']);

        // Schema::create($post['table_name'], function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('spk_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('nota_id')->constrained()->onDelete('cascade');
        // });

        $user = Auth::user();

        $exist_user_instance = UserInstance::where('user_id', $user->id)->where('instance_type', $post['instance_type'])->where('instance_name', $post['instance_name'])->where('branch', $post['branch'])->first();
        if ($exist_user_instance) {
            $request->validate(['error'=>'required'],['error.required'=>'user_inst$exist_user_instance exist!']);
        }

        UserInstance::create([
            'user_id' => $user->id,
            'username' => $user->username,
            'instance_type' => $post['instance_type'],
            'instance_name' => $post['instance_name'],
            'branch' => $post['branch'],
            'account_number' => $post['account_number'],
        ]);

        return back()->with('success_', '-user_instance created-');
    }

    function show_transactions(UserInstance $user_instance) {
        // dd($user_instance);

        $accountings = collect();

        $from = null;
        $until = null;
        if ($user_instance->timerange === 'triwulan') {
            $month = (int)date('m');
            if ($month <= 3) {
                $from = date('Y') . "-01" . "-01";
                $t = date('t', strtotime(date('Y') . "-03-01"));
                $until = date('Y') . "-03" . "-$t" . " 23:59:59";
            } elseif ($month <= 6) {
                $from = date('Y') . "-04" . "-01";
                $t = date('t', strtotime(date('Y') . "-06-01"));
                $until = date('Y') . "-06" . "-$t" . " 23:59:59";
            } elseif ($month <= 9) {
                $from = date('Y') . "-07" . "-01";
                $t = date('t', strtotime(date('Y') . "-09-01"));
                $until = date('Y') . "-09" . "-$t" . " 23:59:59";
            } elseif ($month <= 12) {
                $from = date('Y') . "-10" . "-01";
                $t = date('t', strtotime(date('Y') . "-12-01"));
                $until = date('Y') . "-12" . "-$t" . " 23:59:59";
            }
        }
        $last_transaction = Accounting::where('created_at', '<', $from)->latest()->first();
        $saldo_awal = 0;
        if ($last_transaction !== null) {
            $saldo_awal = $last_transaction->saldo;
        }
        $accountings = Accounting::where('user_instance_id', $user_instance->id)->whereBetween('created_at',[$from, $until])->orderBy('created_at')->get();
        // dump((int)date('m'));
        // dump($accountings);
        $keluar_total = 0;
        $masuk_total = 0;
        foreach ($accountings as $accounting) {
            if ($accounting->transaction_type === 'pengeluaran') {
                $keluar_total += $accounting->jumlah;
            } elseif ($accounting->transaction_type === 'pemasukan') {
                $masuk_total += $accounting->jumlah;
            }
        }

        $user = Auth::user();

        $related_users = User::where('id', '!=', $user->id)->get();

        $label_deskripsi = TransactionName::select('id', 'desc as label', 'desc as value')->where('user_instance_id', $user_instance->id)->orderBy('desc')->get();
        // $label_kategori_level_one = Kategori::select('id', 'kategori_level_one as label', 'kategori_level_one as value')->get();
        // $label_kategori_level_two = Kategori::where('kategori_level_two', '!=', null)->select('id', 'kategori_level_two as label', 'kategori_level_two as value')->get();
        // $transaction_names = TransactionName::all();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'accounting.show_transaction',
            'parent_route' => 'accounting.index',
            'profile_menus' => Menu::get_profile_menus(),
            'accounting_menus' => Menu::get_accounting_menus(),
            'user' => $user,
            'instance_types' => Accounting::get_instance_types(),
            'instance_names' => Accounting::get_instance_names(),
            'accountings' => $accountings,
            'user_instance' => $user_instance,
            'keluar_total' => $keluar_total,
            'masuk_total' => $masuk_total,
            'related_users' => $related_users,
            'label_deskripsi' => $label_deskripsi,
            'saldo_awal' => $saldo_awal,
            // 'label_kategori_level_one' => $label_kategori_level_one,
            // 'label_kategori_level_two' => $label_kategori_level_two,
            // 'transaction_names' => $transaction_names,
        ];

        // dd($label_kategori_level_two);
        // dump($label_deskripsi);

        return view('accounting.show_transactions', $data);
    }

    function store_transactions(UserInstance $user_instance, Request $request) {
        $post = $request->post();
        // dump($user_instance);
        // if ($post['transaction_id'][0] !== null) {
        //     dump(TransactionName::find($post['transaction_id'][0]));
        // }
        // dd($post);

        $user = Auth::user();

        if ($user_instance->user_id !== $user->id) {
            $request->validate(['error'=>'required'],['error.required'=>'different user???']);
        }

        $success_ = '';
        for ($i=0; $i < count($post['transaction_desc']); $i++) {
            if ($post['created_at'][$i] !== null && $post['transaction_desc'][$i] !== null && ($post['keluar'][$i] !== null || $post['masuk'][$i] !== null) ) {
                $transaction_name = TransactionName::find($post['transaction_id'][$i]);
                if ($transaction_name === null) {
                    dump("transaction_name[$i]?");
                    dd($post);
                }
                $jumlah = null;
                $transaction_type = 'pengeluaran';
                if ($post['keluar'][$i] !== null) {
                    $jumlah = (int)$post['keluar'][$i];
                } elseif ($post['masuk'][$i] !== null) {
                    $jumlah = (int)$post['masuk'][$i];
                    $transaction_type = 'pemasukan';
                }

                $status = null;
                if ($transaction_name->related_user_id !== null) {
                    $status = 'not read yet';
                }
                $saldo = 0;
                // Cari apakah ada transaksi dengan tanggal yang setelahnya?
                $created_at = date('Y-m-d', strtotime($post['created_at'][$i])) . " " . date("H:i:s");
                $last_transactions = Accounting::where('user_instance_id', $user_instance->id)->where('created_at','>',$created_at)->orderBy('created_at')->get();
                if (count($last_transactions) !== 0) {
                    $before_last_transaction = Accounting::where('user_instance_id', $user_instance->id)->where('created_at','<',$last_transactions[0]->created_at)->latest()->first();
                    // dump('before_last_transaction: ', $before_last_transaction);
                    if ($before_last_transaction !== null) {
                        $saldo = $before_last_transaction->saldo;
                    }

                    if ($transaction_name->kategori_type === 'UANG KELUAR') {
                        $saldo = $saldo - (int)$post['keluar'][$i];
                    } elseif ($transaction_name->kategori_type === 'UANG MASUK') {
                        $saldo = $saldo + (int)$post['masuk'][$i];
                    }

                    Accounting::create([
                        'user_id'=>$user->id,
                        'username'=>$user->username,
                        'user_instance_id'=>$user_instance->id,
                        'instance_type'=>$user_instance->instance_type,
                        'instance_name'=>$user_instance->instance_name,
                        'branch'=>$user_instance->branch,
                        'account_number'=>$user_instance->account_number,
                        'kode'=>$post['kode'][$i],
                        'transaction_type'=>$transaction_type, // pemasukan, pengeluaran
                        'transaction_desc'=>$post['transaction_desc'][$i],
                        'kategori_type'=>$transaction_name->kategori_type,
                        'kategori_level_one'=>$transaction_name->kategori_level_one,
                        'kategori_level_two'=>$transaction_name->kategori_level_two,
                        'related_user_id'=>$transaction_name->related_user_id,
                        'related_username'=>$transaction_name->related_username,
                        'related_desc'=>$transaction_name->related_desc,
                        'related_user_instance_id'=>$transaction_name->related_user_instance_id,
                        'related_user_instance_type'=>$transaction_name->related_user_instance_type,
                        'related_user_instance_name'=>$transaction_name->related_user_instance_name,
                        'related_user_instance_branch'=>$transaction_name->related_user_instance_branch,
                        'pelanggan_id'=>$transaction_name->pelanggan_id,
                        'pelanggan_nama'=>$transaction_name->pelanggan_nama,
                        'keterangan'=>$post['keterangan'][$i], // keterangan tambahan akan ditulis dalam tanda kurung
                        'jumlah'=>$jumlah,
                        'saldo'=>$saldo,
                        'status'=>$status, // read or not read yet by other user
                        'created_at'=>$created_at
                    ]);

                    foreach ($last_transactions as $last_transaction) {
                        if ($last_transaction->transaction_type === 'pengeluaran') {
                            $saldo -= $last_transaction->jumlah;
                        } elseif ($last_transaction->transaction_type === 'pemasukan') {
                            $saldo += $last_transaction->jumlah;
                        }
                        $last_transaction->saldo = $saldo;
                        $last_transaction->save();
                    }
                    $success_ .= '-jumlah saldo editted-';

                } else {
                    $last_transaction = Accounting::where('user_instance_id', $user_instance->id)->latest()->first();
                    if ($last_transaction !== null) {
                        $saldo = $last_transaction->saldo;
                    }
                    if ($transaction_name->kategori_type === 'UANG KELUAR') {
                        $saldo = $saldo - (int)$post['keluar'][$i];
                    } elseif ($transaction_name->kategori_type === 'UANG MASUK') {
                        $saldo = $saldo + (int)$post['masuk'][$i];
                    }
                    Accounting::create([
                        'user_id'=>$user->id,
                        'username'=>$user->username,
                        'user_instance_id'=>$user_instance->id,
                        'instance_type'=>$user_instance->instance_type,
                        'instance_name'=>$user_instance->instance_name,
                        'branch'=>$user_instance->branch,
                        'account_number'=>$user_instance->account_number,
                        'kode'=>$post['kode'][$i],
                        'transaction_type'=>$transaction_type, // pemasukan, pengeluaran
                        'transaction_desc'=>$post['transaction_desc'][$i],
                        'kategori_type'=>$transaction_name->kategori_type,
                        'kategori_level_one'=>$transaction_name->kategori_level_one,
                        'kategori_level_two'=>$transaction_name->kategori_level_two,
                        'related_user_id'=>$transaction_name->related_user_id,
                        'related_username'=>$transaction_name->related_username,
                        'related_desc'=>$transaction_name->related_desc,
                        'related_user_instance_id'=>$transaction_name->related_user_instance_id,
                        'related_user_instance_type'=>$transaction_name->related_user_instance_type,
                        'related_user_instance_name'=>$transaction_name->related_user_instance_name,
                        'related_user_instance_branch'=>$transaction_name->related_user_instance_branch,
                        'pelanggan_id'=>$transaction_name->pelanggan_id,
                        'pelanggan_nama'=>$transaction_name->pelanggan_nama,
                        'keterangan'=>$post['keterangan'][$i], // keterangan tambahan akan ditulis dalam tanda kurung
                        'jumlah'=>$jumlah,
                        'saldo'=>$saldo,
                        'status'=>$status, // read or not read yet by other user
                        'created_at'=>$created_at
                    ]);
                }


            } else {
                if ($post['created_at'][$i] === null && $post['transaction_desc'][$i] !== null) {
                    dd('created_at: ', $post['created_at'][$i]);
                    dump('keluar: ', $post['keluar'][$i]);
                    dd('masuk: ', $post['masuk'][$i]);
                } elseif ($post['created_at'][$i] !== null && $post['transaction_desc'][$i] === null) {
                    dd('transaction_desc: ', $post['transaction_desc'][$i]);
                }
            }
        }

        $success_ = '-transactions inputted-';

        return back()->with('success_', $success_);
    }

    function transactions_relations() {
        $transaction_names = TransactionName::all();
        // dump($transaction_names);
        $users = User::all();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'accounting.transactions_relations',
            'parent_route' => 'accounting.index',
            'profile_menus' => Menu::get_profile_menus(),
            'accounting_menus' => Menu::get_accounting_menus(),
            'transaction_names' => $transaction_names,
            'users' => $users,
        ];

        return view('accounting.transactions_relations', $data);
    }
}
