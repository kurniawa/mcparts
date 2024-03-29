<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Pelanggan;
use App\Models\Supplier;
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

    function show_transactions(UserInstance $user_instance, Request $request) {
        // dd($user_instance);
        $get = $request->query();

        $fitur_up_down_transaction = true;
        $accountings = collect();

        $from = null;
        $until = null;
        if (count($get) === 0) {
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
            $accountings = Accounting::where('user_instance_id', $user_instance->id)->whereBetween('created_at',[$from, $until])->oldest()->get();
        } else {
            if ($get['desc'] === null) {
                if ($get['from_day'] && $get['from_month'] && $get['from_year'] && $get['to_day'] && $get['to_month'] && $get['to_year']) {
                    $from = "$get[from_year]-$get[from_month]-$get[from_day]";
                    $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
                } else {
                    dd('date?', $get);
                }
                $accountings = Accounting::where('user_instance_id', $user_instance->id)->whereBetween('created_at',[$from, $until])->oldest()->get();
            } else {
                if ($get['from_day'] && $get['from_month'] && $get['from_year'] && $get['to_day'] && $get['to_month'] && $get['to_year']) {
                    $from = "$get[from_year]-$get[from_month]-$get[from_day]";
                    $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
                    $accountings = Accounting::where('user_instance_id', $user_instance->id)->where('transaction_desc', 'like', "%$get[desc]%")->whereBetween('created_at',[$from, $until])->oldest()->get();
                } else {
                    $accountings = Accounting::where('user_instance_id', $user_instance->id)->where('transaction_desc', 'like', "%$get[desc]%")->oldest()->limit(500)->get();
                }
                $fitur_up_down_transaction = false;
            }
        }

        $last_transaction = null;
        if ($from) {
            $last_transaction = Accounting::where('user_instance_id', $user_instance->id)->where('created_at', '<', $from)->latest()->first();
        }
        $saldo_awal = 0;
        if ($last_transaction !== null) {
            $saldo_awal = $last_transaction->saldo;
        }
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

        $notifications = Accounting::where('related_user_instance_id', $user_instance->id)->latest()->limit(100)->get();

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
            'from' => $from,
            'notifications' => $notifications,
            'fitur_up_down_transaction' => $fitur_up_down_transaction,
            // 'label_kategori_level_one' => $label_kategori_level_one,
            // 'label_kategori_level_two' => $label_kategori_level_two,
            // 'transaction_names' => $transaction_names,
        ];

        // dd($label_kategori_level_two);
        // dump($label_deskripsi);
        // dump($accountings);
        return view('accounting.show_transactions', $data);
    }

    function store_transactions(UserInstance $user_instance, Request $request) {
        $post = $request->post();
        // dd($post);
        // dump($user_instance);
        // if ($post['transaction_id'][0] !== null) {
        //     dump(TransactionName::find($post['transaction_id'][0]));
        // }
        // dd((float)$post['masuk'][0]);
        // dump('loading...');

        $user = Auth::user();
        $success_ = '';
        $warnings_ = '';

        // VALIDASI
        if ((int)$user_instance->user_id !== $user->id) {
            $request->validate(['error'=>'required'],['error.required'=>'different user???']);
        }

        $working_index = count($post['transaction_desc']);

        for ($i=0; $i < count($post['transaction_desc']); $i++) {
            // dd('masuk loop');
            $created_at = null;
            if ($post['year'][$i] && $post['month'][$i] && $post['day'][$i]) {
                $created_at = date('Y-m-d', strtotime($post['year'][$i] . "-" . $post['month'][$i] . "-" . $post['day'][$i])) . " " . date("H:i:s");
            } else {
                $request->validate(['error'=>'required'],['error.required'=>$post['year'][$i] . "-" . $post['month'][$i] . "-" . $post['day'][$i]]);
            }

            // dump("post['keluar'][i] : ");
            // dump($post['keluar'][$i]);
            // dump("post['masuk'][i] : ");
            // dd($post['masuk'][$i]);
            $keluar = htmlspecialchars(trim($post['keluar'][$i]));
            $masuk = htmlspecialchars(trim($post['masuk'][$i]));

            // dump($keluar);
            // dump($masuk);
            // dump(is_numeric($keluar));
            // dump(is_numeric($masuk));

            if (!is_numeric($keluar)) {
                $keluar = null;
            }

            if (!is_numeric($masuk)) {
                $masuk = null;
            }

            if ($created_at !== null && $post['transaction_desc'][$i] !== null && ($keluar !== null || $masuk !== null) ) {
                // dd($post['transaction_desc'][$i]);
                $year_inputted = (int)date('Y', strtotime($created_at));
                $year_now = (int)date('Y');
                // dump($year_inputted);
                // dd($year_now);
                $year_diff = $year_now - $year_inputted;

                if ($year_diff >= 3 || $year_diff <= -3) {
                    $request->validate(['error'=>'required'],['error.required'=>"year_inputted[$i]: $year_inputted ?"]);
                }
                if ($post['transaction_id'][$i] === null) {
                    if ($post['transaction_desc'][$i] === null) {
                        // dump("transaction_name[$i] || transaction_desc[$i]?");
                        // dd($post);
                        $request->validate(['error'=>'required'],['error.required'=>"transaction_name[$i] || transaction_desc[$i]?"]);
                    } else {
                        $transaction_desc = strtoupper($post['transaction_desc'][$i]);
                        $transaction_name = TransactionName::where('user_instance_id', $user_instance->id)->where('desc', $transaction_desc)->first();
                        if ($transaction_name === null) {
                            $request->validate(['error'=>'required'],['error.required'=>"deskripsi[$i] tidak ditemukan"]);
                        }
                    }
                } else {
                    $transaction_name = TransactionName::find($post['transaction_id'][$i]);
                }

                if ($transaction_name === null) {
                    // dump("transaction_name[$i]");
                    // dd($post);
                    $request->validate(['error'=>'required'],['error.required'=>"transaction_name[$i]"]);
                }
                // dump($transaction_name);
                // dd($masuk);
                if ($transaction_name->kategori_type === 'UANG MASUK' && $masuk === null) {
                    // dump("index: $i - kategori_type = 'UANG MASUK' but post[masuk] = null?");
                    // dd($post);
                    $request->validate(['error'=>'required'],['error.required'=>"index: $i - kategori_type = 'UANG MASUK' but post[masuk] = null?"]);
                } elseif ($transaction_name->kategori_type === 'UANG KELUAR' && $keluar === null) {
                    // dump("index: $i - kategori_type = 'UANG KELUAR' but post[keluar] = null?");
                    // dd($post);
                    $request->validate(['error'=>'required'],['error.required'=>"index: $i - kategori_type = 'UANG KELUAR' but post[keluar] = null?"]);
                }

            } else {
                if ($i === 0) {
                    if ($created_at === null && $post['transaction_desc'][$i] !== null) {
                        // dd('created_at: ', $created_at);
                        // dump('keluar: ', $keluar);
                        // dd('masuk: ', $masuk);
                        $request->validate(['error'=>'required'],['error.required'=>"created_at[$i]: " . $created_at]);
                    } elseif ($created_at !== null && $post['transaction_desc'][$i] === null) {
                        // dd('transaction_desc: ', $post['transaction_desc'][$i]);
                        $request->validate(['error'=>'required'],['error.required'=>"transaction_desc[$i]: " . $post['transaction_desc'][$i]]);
                    } elseif ($keluar === null && $masuk === null) {
                        $request->validate(['error'=>'required'],['error.required'=>"jumlah keluar / masuk ?? [$i]"]);
                    }
                } else {
                    $working_index = $i;
                    if ($post['transaction_desc'][$i] !== null || ($keluar !== null || $masuk !== null)) {
                        $warnings_ .= "-mulai dari input ke-[$i] tidak diproses (data tidak lengkap)-";
                    }
                    break;
                }
            }
        }
        // dd($working_index);
        // END - VALIDASI

        for ($i=0; $i < $working_index ; $i++) {
            $keluar = htmlspecialchars(trim($post['keluar'][$i]));
            $masuk = htmlspecialchars(trim($post['masuk'][$i]));

            if (!is_numeric($keluar)) {
                $keluar = null;
            }

            if (!is_numeric($masuk)) {
                $masuk = null;
            }

            if ($post['transaction_id'][$i] === null) {
                $transaction_desc = strtoupper($post['transaction_desc'][$i]);
                $transaction_name = TransactionName::where('user_instance_id', $user_instance->id)->where('desc', $transaction_desc)->first();
            } else {
                $transaction_name = TransactionName::find($post['transaction_id'][$i]);
            }
            // dd($transaction_name);

            $jumlah = null;
            $transaction_type = 'pengeluaran';

            if ($transaction_name->kategori_type === 'UANG MASUK') {
                $transaction_type = 'pemasukan';
                $jumlah = (float)$masuk * 100;
                $keluar = null;
            } elseif ($transaction_name->kategori_type === 'UANG KELUAR') {
                $jumlah = (float)$keluar * 100;
                $masuk = null;
            }

            // if ($keluar !== null) {
            //     $jumlah = (float)$keluar * 100;
            // } elseif ($masuk !== null) {
            //     $jumlah = (float)$masuk * 100;
            //     $transaction_type = 'pemasukan';
            // }

            $status = null;
            if ($transaction_name->related_user_id !== null) {
                $status = 'not read yet';
            }
            $saldo = 0;
            // Cari apakah ada transaksi dengan tanggal yang setelahnya?
            // $created_at = date('Y-m-d', strtotime("$post[year][$i]-$post[month][$i]-$post[day][$i]")) . " " . date("H:i:s");
            $created_at = date('Y-m-d', strtotime($post['year'][$i] . "-" . $post['month'][$i] . "-" . $post['day'][$i])) . " " . date("H:i:s");
            // $created_at = date('Y-m-d', strtotime($post['created_at'][$i])) . " " . date("H:i:s");
            $last_transactions = Accounting::where('user_instance_id', $user_instance->id)->where('created_at','>',$created_at)->orderBy('created_at')->get();
            if (count($last_transactions) !== 0) {
                $before_last_transaction = Accounting::where('user_instance_id', $user_instance->id)->where('created_at','<',$created_at)->latest()->first();
                // dump('before_last_transaction: ', $before_last_transaction);
                if ($before_last_transaction !== null) {
                    $saldo = $before_last_transaction->saldo;
                }

                if ($transaction_name->kategori_type === 'UANG KELUAR') {
                    $saldo = $saldo - (float)$keluar * 100;
                } elseif ($transaction_name->kategori_type === 'UANG MASUK') {
                    $saldo = $saldo + (float)$masuk * 100;
                }

                $saldo_next = $saldo;
                foreach ($last_transactions as $last_transaction) {
                    if ($last_transaction->transaction_type === 'pengeluaran') {
                        $saldo_next -= $last_transaction->jumlah;
                    } elseif ($last_transaction->transaction_type === 'pemasukan') {
                        $saldo_next += $last_transaction->jumlah;
                    }
                    $last_transaction->saldo = $saldo_next;
                    $last_transaction->save();
                }
                $success_ .= '-jumlah saldo editted-';

            } else {
                $last_transaction = Accounting::where('user_instance_id', $user_instance->id)->latest()->first();
                if ($last_transaction !== null) {
                    $saldo = $last_transaction->saldo;
                }
                if ($transaction_name->kategori_type === 'UANG KELUAR') {
                    $saldo = $saldo - (float)$keluar * 100;
                } elseif ($transaction_name->kategori_type === 'UANG MASUK') {
                    $saldo = $saldo + (float)$masuk * 100;
                }
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
                'transaction_desc'=>$transaction_name->desc,
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
                'supplier_id'=>$transaction_name->supplier_id,
                'supplier_nama'=>$transaction_name->supplier_nama,
                'keterangan'=>$post['keterangan'][$i], // keterangan tambahan akan ditulis dalam tanda kurung
                'jumlah'=>$jumlah,
                'saldo'=>$saldo,
                'status'=>$status, // read or not read yet by other user
                'created_at'=>$created_at
            ]);
            sleep(1.5);
            // dump("$transaction_name->desc --> Rp " . number_format($jumlah,0,',','.') . ",- inputted");
        }

        $success_ .= '-transactions created-';

        $feedback = [
            'success_' => $success_,
            'warnings_' => $warnings_,
        ];
        // sleep(3);
        return back()->with($feedback);
    }

    function mark_as_read_or_unread(UserInstance $user_instance, Accounting $accounting, Request $request) {
        $post = $request->post();
        // dump($post);
        // dump($user_instance);
        // dd($accounting);
        $user = Auth::user();
        if ($user->id !== (int)$user_instance->user_id) {
            dump('user_instance->user: ', $user_instance->user_id);
            dd('auth_user: ', $user->id);
        }

        if ($post['read'] === 'yes') {
            $accounting->status = 'read';
            $accounting->save();
        } elseif ($post['read'] === 'no') {
            $accounting->status = 'not read yet';
            $accounting->save();
        }

        return back()->with('success_','-transaction_status changed-');
    }

    function apply_entry(UserInstance $user_instance, Accounting $accounting, Request $request) {
        $post = $request->post();
        // dump($post);
        // dump($user_instance);
        // dump($accounting);

        $success_ = '';
        $user = Auth::user();
        if ($user->id !== (int)$user_instance->user_id) {
            dump('user_instance->user: ', $user_instance->user_id);
            dd('auth_user: ', $user->id);
        }

        $transaction_name = TransactionName::where('user_id', $accounting->related_user_id)->where('desc', $accounting->related_desc)->where('user_instance_id', $accounting->related_user_instance_id)->first();
        // dd($transaction_name);
        $transaction_type = 'pengeluaran';
        if ($transaction_name->kategori_type === 'UANG MASUK') {
            $transaction_type = 'pemasukan';
        }

        $created_at = date('Y-m-d', strtotime($accounting->created_at)) . " " . date("H:i:s");
        $last_transactions = Accounting::where('user_instance_id', $user_instance->id)->where('created_at','>',$created_at)->orderBy('created_at')->get();
        // $last_transactions_desc = Accounting::where('user_instance_id', $user_instance->id)->where('created_at','>',$accounting->created_at)->orderByDesc('created_at')->get();
        // dump($last_transactions);
        // dd($last_transactions_desc);
        $saldo = 0;

        if (count($last_transactions) !== 0) {
            $before_last_transaction = Accounting::where('user_instance_id', $user_instance->id)->where('created_at','<',$created_at)->latest()->first();
            // dump('before_last_transaction: ', $before_last_transaction);
            if ($before_last_transaction !== null) {
                $saldo = (int)$before_last_transaction->saldo;
            }

            try {
                if ($transaction_name->kategori_type === 'UANG KELUAR') {
                    $saldo = $saldo - (int)$accounting->jumlah;
                } elseif ($transaction_name->kategori_type === 'UANG MASUK') {
                    $saldo = $saldo + (int)$accounting->jumlah;
                }
            } catch (\Throwable $th) {
                dump($accounting);
                dump(User::find($accounting->related_user_id));
                dump(UserInstance::find($accounting->related_user_instance_id));
                dd($transaction_name);
            }

            $saldo_next = $saldo;

            // dump($saldo);
            // dump($accounting);
            // dd($last_transactions);
            foreach ($last_transactions as $last_transaction) {
                if ($last_transaction->transaction_type === 'pengeluaran') {
                    $saldo_next -= (int)$last_transaction->jumlah;
                } elseif ($last_transaction->transaction_type === 'pemasukan') {
                    $saldo_next += (int)$last_transaction->jumlah;
                }
                $last_transaction->saldo = (string)$saldo_next;
                $last_transaction->save();
            }
            $success_ .= '-jumlah saldo editted-';

        } else {
            $last_transaction = Accounting::where('user_instance_id', $user_instance->id)->latest()->first();
            if ($last_transaction !== null) {
                // dump(date('d-m-Y H:i:s', strtotime($last_transaction->created_at)) . " - $last_transaction->transaction_type: $last_transaction->jumlah, saldo: $last_transaction->saldo");
                $saldo = (int)$last_transaction->saldo; // -5.000.000
            }
            try {
                if ($transaction_name->kategori_type === 'UANG KELUAR') {
                    $saldo = $saldo - (int)$accounting->jumlah;
                } elseif ($transaction_name->kategori_type === 'UANG MASUK') {
                    $saldo = $saldo + (int)$accounting->jumlah;
                }
            } catch (\Throwable $th) {
                dump("related to: " . User::find($accounting->related_user_id)->username . " - ID: $accounting->related_user_id, $accounting->related_desc - " . UserInstance::find($accounting->related_user_instance_id)->instance_type);
                dump($accounting);
                dump('transaction_name:', $transaction_name);
                dd('last_transaction:', $last_transaction);
            }
        }
        // dump($transaction_name);
        // dump("kategori_type: " . $transaction_name->kategori_type);
        // dd("transaction_type: " . $transaction_type);
        Accounting::create([
            'user_id'=>$user_instance->user_id,
            'username'=>$user_instance->username,
            'user_instance_id'=>$user_instance->id,
            'instance_type'=>$user_instance->instance_type,
            'instance_name'=>$user_instance->instance_name,
            'branch'=>$user_instance->instance_branch,
            'account_number'=>$user_instance->account_number,
            'kode'=>$user_instance->kode,
            'transaction_type'=>$transaction_type, // pemasukan, pengeluaran
            'transaction_desc'=>$accounting->related_desc,
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
            'supplier_id'=>$transaction_name->supplier_id,
            'supplier_nama'=>$transaction_name->supplier_nama,
            'keterangan'=>null, // keterangan tambahan akan ditulis dalam tanda kurung
            'jumlah'=>(string)$accounting->jumlah,
            'saldo'=>(string)$saldo,
            'status'=>'read', // read or not read yet by other user
            'created_at'=>$created_at
        ]);
        $success_ .= '-transaction created-';

        $accounting->status = 'read';
        $accounting->save();

        return back()->with('success_',$success_);
    }

    function edit_entry(UserInstance $user_instance, Accounting $accounting, Request $request) {
        $post = $request->post();
        // dump($post);
        // dump($user_instance);
        // dump($accounting);

        $user = Auth::user();

        if ((int)$user_instance->user_id !== $user->id) {
            $request->validate(['error'=>'required'],['error.required'=>'different user???']);
        }

        if ($post['created_at'] === null || $post['transaction_desc'] === null || ($post['keluar'] === null && $post['masuk'] === null)) {
            $request->validate(['error'=>'required'],['error.required'=>'tanggal || transaction_desc || jumlah_keluar || jumlah_masuk?']);
        }

        $success_ = '';

        // 1. NILAI CREATED AT
        $created_at_old = date('d-m-Y', strtotime($accounting->created_at));
        // dump($created_at_old);
        $created_at_new = $created_at_old;
        // dump($post['created_at']);
        // dd($created_at_old);
        if (is_string($created_at_old)) {
            if ($post['created_at'] !== $created_at_old) {
                $created_at_new = date('Y-m-d', strtotime($post['created_at'])) . " " . date("H:i:s");
                // mencegah error, apabila jam, menit dan detik, kebetulan sama dengan yang ingin ditukar posisinya
                $same_created_at = Accounting::where('user_instance_id', $user_instance->id)->where('created_at', $created_at_new)->first();
                if ($same_created_at !== null) {
                    $detik_0 = date('Y-m-d H:i:00', strtotime($created_at_new));
                    $detik_0 = strtotime($detik_0);
                    // dd($detik_0);
                    $detik_digunakan = $detik_0;
                    for ($i=0; $i < 60; $i++) {
                        $detik_digunakan += $i;
                        $created_at_new = date('Y-m-d H:i:s', $detik_digunakan);
                        $same_created_at = Accounting::where('user_instance_id', $user_instance->id)->where('created_at', $created_at_new)->first();
                        if ($same_created_at === null) {
                            break;
                        }
                        if ($i === 59) {
                            dd('-very rare error: detik sama-');
                        }
                    }
                }
                // END - mencegah error, apabila jam, menit dan detik, kebetulan sama dengan yang ingin ditukar posisinya
            }
        } else {
            dump('created_at_old is not string type');
            dd($created_at_old);
        }
        // END - NILAI CREATED AT
        // TENTUKAN APAKAH TRANSAKI MUNDUR ATAU MAJU?
        $mode = null;
        $date_1 = strtotime($created_at_old);
        $date_2 = strtotime($post['created_at']);
        // dump($created_at_old, $date_1);
        // $date_1_seharian = strtotime("$created_at_old 23:59:59");
        // dump($date_1_seharian - $date_1);
        // dump(gmdate("H:i:s", $date_1_seharian - $date_1));
        // dump($post['created_at'], $date_2);
        // dump(date('Y-m-d H:i:00', strtotime($created_at_new)));
        // dd($date_2 - $date_1);
        // dd(date('Y-m-d H:i:s', $date_1));

        if ($date_1 === $date_2) {
            $mode = 'tanggal_sama';
        } elseif ($date_2 < $date_1) {
            $mode = 'tanggal_mundur';
        } elseif ($date_2 > $date_1) {
            $mode = 'tanggal_maju';
        }

        // Format created_at_old kembali seperti seharusnya
        $created_at_old = $accounting->created_at;

        $transaction_name = TransactionName::where('user_instance_id', $user_instance->id)->where('desc', $post['transaction_desc'])->first();
        if ($transaction_name === null) {
            dump("transaction_name?");
            dd($post);
        }
        $jumlah = null;
        $transaction_type = 'pengeluaran';

        $keluar = htmlspecialchars(trim($post['keluar']));
        $masuk = htmlspecialchars(trim($post['masuk']));

        if (!is_numeric($keluar)) {
            $keluar = null;
        }

        if (!is_numeric($masuk)) {
            $masuk = null;
        }

        if ($transaction_name->kategori_type === 'UANG MASUK') {
            if (!$masuk) {
                $request->validate(['error'=>'required'],['error.required'=>'UANG MASUK, tapi kolom masuk === null']);
            }
        } elseif ($transaction_name->kategori_type === 'UANG KELUAR') {
            if (!$keluar) {
                $request->validate(['error'=>'required'],['error.required'=>'UANG KELUAR, tapi kolom keluar === null']);
            }
        }

        if ($transaction_name->kategori_type === 'UANG MASUK') {
            $transaction_type = 'pemasukan';
            $jumlah = (float)$masuk * 100;
            $keluar = null;
        } elseif ($transaction_name->kategori_type === 'UANG KELUAR') {
            $jumlah = (float)$keluar * 100;
            $masuk = null;
        }

        // dump($keluar);

        $status = null;
        if ($transaction_name->related_user_id !== null) {
            $status = 'not read yet';
        }

        // dd($accounting);
        $saldo_to_update = (int)$accounting->saldo;

        if ($mode === 'tanggal_mundur') {
            // MODE TRANSAKSI MUNDUR
            $transactions_between = Accounting::where('user_instance_id', $user_instance->id)->whereBetween('created_at', [$created_at_new, $created_at_old])->where('id', '!=', $accounting->id)->orderBy('created_at')->get();
            $saldo_akhir = 0;
            // dump($created_at_old);
            // dump($created_at_new);
            // dd($transactions_between);
            if (count($transactions_between) > 0) {
                $transaction_batas_atas = Accounting::where('user_instance_id', $user_instance->id)->where('created_at', '<' , $created_at_new)->latest()->first();
                if ($transaction_batas_atas) {
                    $saldo_akhir = (int)$transaction_batas_atas->saldo;
                }
            }

            if ($transaction_name->kategori_type === 'UANG KELUAR') {
                $saldo_akhir -= $jumlah;
            } elseif ($transaction_name->kategori_type === 'UANG MASUK') {
                $saldo_akhir += $jumlah;
            }

            $saldo_to_update = $saldo_akhir;

            foreach ($transactions_between as $transaction_between) {
                if ($transaction_between->transaction_type === 'pengeluaran') {
                    $saldo_akhir -= (int)$transaction_between->jumlah;
                } elseif ($transaction_between->transaction_type === 'pemasukan') {
                    $saldo_akhir += (int)$transaction_between->jumlah;
                }
                $transaction_between->saldo = (string)$saldo_akhir;
                $transaction_between->save();
            }

            $success_ .= '-tanggal_mundur, transactions_between updated-';

        } elseif ($mode === 'tanggal_maju') {
            // MODE TRANSAKSI MAJU
            $transactions_between = Accounting::where('user_instance_id', $user_instance->id)->whereBetween('created_at', [$created_at_old, $created_at_new])->where('id', '!=', $accounting->id)->orderBy('created_at')->get();
            // dump($created_at_old);
            // dump($created_at_new);
            // dd($transactions_between);
            $saldo_akhir = 0;
            if (count($transactions_between) > 0) {
                $transaction_batas_atas = Accounting::where('user_instance_id', $user_instance->id)->where('created_at', '<' , $created_at_old)->latest()->first();
                if ($transaction_batas_atas) {
                    $saldo_akhir = (int)$transaction_batas_atas->saldo;
                }
            }

            foreach ($transactions_between as $transaction_between) {
                if ($transaction_between->transaction_type === 'pengeluaran') {
                    $saldo_akhir -= (int)$transaction_between->jumlah;
                } elseif ($transaction_between->transaction_type === 'pemasukan') {
                    $saldo_akhir += (int)$transaction_between->jumlah;
                }
                $transaction_between->saldo = (string)$saldo_akhir;
                $transaction_between->save();
            }

            if ($transaction_name->kategori_type === 'UANG KELUAR') {
                $saldo_akhir -= $jumlah;
            } elseif ($transaction_name->kategori_type === 'UANG MASUK') {
                $saldo_akhir += $jumlah;
            }

            $saldo_to_update = $saldo_akhir;
            $success_ .= '-tanggal_maju, transactions_between updated-';
        } else {
            $created_at_new = $created_at_old;
            $saldo_akhir = 0;

            $transaction_batas_atas = Accounting::where('user_instance_id', $user_instance->id)->where('created_at', '<' , $created_at_old)->latest()->first();
            if ($transaction_batas_atas) {
                $saldo_akhir = (int)$transaction_batas_atas->saldo;
            }

            if ($transaction_name->kategori_type === 'UANG KELUAR') {
                $saldo_akhir -= $jumlah;
            } elseif ($transaction_name->kategori_type === 'UANG MASUK') {
                $saldo_akhir += $jumlah;
            }

            $saldo_to_update = $saldo_akhir;

            $transactions_after = Accounting::where('user_instance_id', $user_instance->id)->where('created_at', '>' , $created_at_old)->orderBy('created_at')->get();

            foreach ($transactions_after as $transaction_after) {
                if ($transaction_after->transaction_type === 'pengeluaran') {
                    $saldo_akhir -= (int)$transaction_after->jumlah;
                } elseif ($transaction_after->transaction_type === 'pemasukan') {
                    $saldo_akhir += (int)$transaction_after->jumlah;
                }
                $transaction_after->saldo = (string)$saldo_akhir;
                $transaction_after->save();
            }

            $success_ .= '-tanggal_sama, transactions_between none, transactions_after updated-';
        }

        $accounting->update([
            'user_id'=>$user->id,
            'username'=>$user->username,
            'user_instance_id'=>$user_instance->id,
            'instance_type'=>$user_instance->instance_type,
            'instance_name'=>$user_instance->instance_name,
            'branch'=>$user_instance->branch,
            'account_number'=>$user_instance->account_number,
            'kode'=>$post['kode'],
            'transaction_type'=>$transaction_type, // pemasukan, pengeluaran
            'transaction_desc'=>$post['transaction_desc'],
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
            'supplier_id'=>$transaction_name->supplier_id,
            'supplier_nama'=>$transaction_name->supplier_nama,
            'keterangan'=>$post['keterangan'], // keterangan tambahan akan ditulis dalam tanda kurung
            'jumlah'=>$jumlah,
            'saldo'=>(string)$saldo_to_update,
            'status'=>$status, // read or not read yet by other user
            'created_at'=>$created_at_new
        ]);

        $success_ .= '-transactions updated-';
        // dump('updated!');
        return back()->with('success_', $success_);

    }

    function delete_entry(UserInstance $user_instance, Accounting $accounting) {
        // dump($user_instance);
        // dd($accounting);

        $user = Auth::user();
        if ((int)$user_instance->user_id !== $user->id) {
            dd('user?');
        }

        $warnings_ = '';

        $saldo = 0;
        // Cari apakah ada transaksi dengan tanggal yang setelahnya?
        $last_transactions = Accounting::where('user_instance_id', $user_instance->id)->where('created_at','>',$accounting->created_at)->orderBy('created_at')->get();

        if (count($last_transactions) !== 0) {
            $before_last_transaction = Accounting::where('user_instance_id', $user_instance->id)->where('created_at','<',$accounting->created_at)->latest()->first();
            // dump('before_last_transaction: ', $before_last_transaction);
            if ($before_last_transaction !== null) {
                $saldo = $before_last_transaction->saldo;
            }

            $saldo_next = $saldo;
            foreach ($last_transactions as $last_transaction) {
                if ($last_transaction->transaction_type === 'pengeluaran') {
                    $saldo_next -= $last_transaction->jumlah;
                } elseif ($last_transaction->transaction_type === 'pemasukan') {
                    $saldo_next += $last_transaction->jumlah;
                }
                $last_transaction->saldo = $saldo_next;
                $last_transaction->save();
            }
            $warnings_ .= '-jumlah saldo editted-';

        }

        $accounting->delete();
        $warnings_ .= '-transaction deleted-';

        return back()->with('warnings_', $warnings_);
    }

    function jurnal(Request $request) {
        // dd($user_instance);
        $get = $request->query();

        $accountings = collect();

        $from = null;
        $until = null;
        if (count($get) === 0) {
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
            $accountings = Accounting::orderBy('user_instance_id')->whereBetween('created_at',[$from, $until])->oldest()->get();
        } else {
            // dd($get);
            if (!$get['kode'] && !$get['desc']) {
                if ($get['from_day'] && $get['from_month'] && $get['from_year'] && $get['to_day'] && $get['to_month'] && $get['to_year']) {
                    $from = "$get[from_year]-$get[from_month]-$get[from_day]";
                    $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
                    $accountings = Accounting::orderBy('user_instance_id')->whereBetween('created_at',[$from, $until])->oldest()->get();
                } else {
                    dd('date?', $get);
                }
            } elseif ($get['kode'] && !$get['desc']) {
                if ($get['from_day'] && $get['from_month'] && $get['from_year'] && $get['to_day'] && $get['to_month'] && $get['to_year']) {
                    $from = "$get[from_year]-$get[from_month]-$get[from_day]";
                    $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
                    $accountings = Accounting::orderBy('user_instance_id')->where('kode', 'like', "%$get[kode]%")->whereBetween('created_at',[$from, $until])->oldest()->get();
                } else {
                    $accountings = Accounting::orderBy('user_instance_id')->where('kode', 'like', "%$get[kode]%")->oldest()->limit(500)->get();
                }
            } elseif (!$get['kode'] && $get['desc']) {
                if ($get['from_day'] && $get['from_month'] && $get['from_year'] && $get['to_day'] && $get['to_month'] && $get['to_year']) {
                    $from = "$get[from_year]-$get[from_month]-$get[from_day]";
                    $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
                    $accountings = Accounting::orderBy('user_instance_id')->where('transaction_desc', 'like', "%$get[desc]%")->whereBetween('created_at',[$from, $until])->oldest()->get();
                } else {
                    $accountings = Accounting::orderBy('user_instance_id')->where('transaction_desc', 'like', "%$get[desc]%")->oldest()->limit(500)->get();
                }
            } elseif ($get['kode'] && $get['desc']) {
                if ($get['from_day'] && $get['from_month'] && $get['from_year'] && $get['to_day'] && $get['to_month'] && $get['to_year']) {
                    $from = "$get[from_year]-$get[from_month]-$get[from_day]";
                    $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
                    $accountings = Accounting::orderBy('user_instance_id')->where('kode', 'like', "%$get[kode]%")->where('transaction_desc', 'like', "%$get[desc]%")->whereBetween('created_at',[$from, $until])->oldest()->get();
                } else {
                    $accountings = Accounting::orderBy('user_instance_id')->where('kode', 'like', "%$get[kode]%")->where('transaction_desc', 'like', "%$get[desc]%")->oldest()->limit(500)->get();
                }
            }
        }

        // $last_transaction = null;
        // if ($from) {
        //     $last_transaction = Accounting::orderBy('user_instance_id')->where('created_at', '<', $from)->latest()->first();
        // }

        // $saldo_awal = 0;
        // if ($last_transaction !== null) {
        //     $saldo_awal = $last_transaction->saldo;
        // }

        // dump((int)date('m'));
        // dump($accountings);
        $keluar_total = 0;
        $masuk_total = 0;
        $balance_total = 0;

        foreach ($accountings as $accounting) {
            if ($accounting->transaction_type === 'pengeluaran') {
                $keluar_total += $accounting->jumlah;
            } elseif ($accounting->transaction_type === 'pemasukan') {
                $masuk_total += $accounting->jumlah;
            }
        }
        $diff_total = $masuk_total - $keluar_total;

        $accountings_grouped = $accountings->groupBy('user_instance_id');
        $keluar = $masuk = $balance = $saldo_awals = $diff = array();

        // dd($accountings_grouped);
        $i_accounting_grouped = 0;
        foreach ($accountings_grouped as $key_accounting_grouped => $accounting_grouped) {

            $keluar[$key_accounting_grouped] = 0;
            $masuk[$key_accounting_grouped] = 0;
            foreach ($accounting_grouped as $key_accounting => $accounting) {
                if ($key_accounting == 0) {
                    $accounting_awal = Accounting::where('user_instance_id', $accounting->user_instance_id)->where('created_at', '<', $from)->latest()->first();
                    if ($accounting_awal) {
                        $saldo_awals[$key_accounting_grouped] = $accounting_awal->saldo;
                    } else {
                        $saldo_awals[$key_accounting_grouped] = 0;
                    }
                }
                if ($accounting->transaction_type === 'pengeluaran') {
                    $keluar[$key_accounting_grouped] += $accounting->jumlah;
                } elseif ($accounting->transaction_type === 'pemasukan') {
                    $masuk[$key_accounting_grouped] += $accounting->jumlah;
                }
            }
            $diff[$key_accounting_grouped] = $masuk[$key_accounting_grouped] - $keluar[$key_accounting_grouped];
            // dd($saldo_awals[$key_accounting_grouped]);
            // dd($diff[$key_accounting_grouped]);
            $balance[$key_accounting_grouped] = $diff[$key_accounting_grouped] + $saldo_awals[$key_accounting_grouped];
            $balance_total += $balance[$key_accounting_grouped];
            // $i_accounting_grouped++;
        }

        $user = Auth::user();

        $related_users = User::where('id', '!=', $user->id)->get();

        $label_deskripsi = TransactionName::select('desc as label', 'desc as value')->groupBy('desc')->orderBy('desc')->get();
        // $label_kategori_level_one = Kategori::select('id', 'kategori_level_one as label', 'kategori_level_one as value')->get();
        // $label_kategori_level_two = Kategori::where('kategori_level_two', '!=', null)->select('id', 'kategori_level_two as label', 'kategori_level_two as value')->get();
        // $transaction_names = TransactionName::all();

        // $notifications = Accounting::where('related_user_instance_id', $user_instance->id)->latest()->limit(100)->get();
        // dd($saldo_awals);
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
            'keluar_total' => $keluar_total,
            'masuk_total' => $masuk_total,
            'related_users' => $related_users,
            'saldo_awals' => $saldo_awals,
            'balance_total' => $balance_total,
            'diff_total' => $diff_total,
            'from' => $from,
            'keluar' => $keluar,
            'masuk' => $masuk,
            'diff' => $diff,
            'balance' => $balance,
            // 'user_instance' => $user_instance,
            'label_deskripsi' => $label_deskripsi,
            // 'notifications' => $notifications,
            // 'label_kategori_level_one' => $label_kategori_level_one,
            // 'label_kategori_level_two' => $label_kategori_level_two,
            // 'transaction_names' => $transaction_names,
        ];

        // dd($label_kategori_level_two);
        // dump($label_deskripsi);
        // dump($accountings);

        // dump($accountings);
        // dd($accountings->groupBy('user_instance_id'));

        return view('accounting.jurnal', $data);
    }

    function ringkasan(Request $request) {
        $get = $request->query();

        $kategories = Kategori::list_of_kategoris();
        $ringkasans = array();

        $from = null;
        $until = null;

        $keluar_total = 0;
        $masuk_total = 0;

        if (count($get) !== 0) {
            // dd($get);
            if ($get['from_day'] && $get['from_month'] && $get['from_year'] && $get['to_day'] && $get['to_month'] && $get['to_year']) {
                $from = "$get[from_year]-$get[from_month]-$get[from_day]";
                $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
            } else {
                dd('date?', $get);
            }
        } else {
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

        foreach ($kategories as $key_type => $kategori) {
            // $kategori_types = array();
            $kategori_level_ones = array();
            foreach ($kategori['kategori_level_one'] as $key_level_one => $kategori_level_one) {
                if (isset($kategori_level_one['kategori_level_two'])) {
                    $kategori_level_twos = array();
                    foreach ($kategori_level_one['kategori_level_two'] as $key_level_two => $kategori_level_two) {
                        // dd($kategori_level_two);
                        $transactions = Accounting::whereBetween('created_at', [$from, $until])->where('kategori_type', $kategori['type'])->where('kategori_level_one', $kategori_level_one['name'])->where('kategori_level_two', $kategori_level_two['name'])->get();
                        $jumlah = 0;
                        foreach ($transactions as $transaction) {
                            $jumlah += $transaction->jumlah;
                        }
                        $kategori_level_twos[] = [
                            'name' => $kategori_level_two['name'],
                            'jumlah' => $jumlah,
                        ];
                        if ($kategori['type'] === 'UANG KELUAR') {
                            $keluar_total += $jumlah;
                        } elseif ($kategori['type'] === 'UANG MASUK') {
                            $masuk_total += $jumlah;
                        }
                    }
                    $kategori_level_ones[] = [
                        'name' => $kategori_level_one['name'],
                        'kategori_level_two' => $kategori_level_twos,
                    ];
                } else {
                    $transactions = Accounting::whereBetween('created_at', [$from, $until])->where('kategori_type', $kategori['type'])->where('kategori_level_one', $kategori_level_one['name'])->get();
                    // dump("$kategori[type] - $kategori_level_one[name]");
                    // dump($transactions);
                    $jumlah = 0;
                    foreach ($transactions as $transaction) {
                        $jumlah += $transaction->jumlah;
                    }
                    $kategori_level_ones[] = [
                        'name' => $kategori_level_one['name'],
                        'jumlah' => $jumlah,
                    ];
                    if ($kategori['type'] === 'UANG KELUAR') {
                        $keluar_total += $jumlah;
                    } elseif ($kategori['type'] === 'UANG MASUK') {
                        $masuk_total += $jumlah;
                    }
                }
                // $kategori_types[] = [
                //     'type' => $kategori['type'],
                //     'kategori_level_one' => $kategori_level_ones
                // ];
            }
            $ringkasans[] = [
                'type' => $kategori['type'],
                'kategori_level_one' => $kategori_level_ones
            ];
        }

        // dump($kategories);
        // dd($ringkasans);

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'accounting.ringkasan',
            'parent_route' => 'accounting.index',
            'profile_menus' => Menu::get_profile_menus(),
            'accounting_menus' => Menu::get_accounting_menus(),
            'ringkasans' => $ringkasans,
            'from' => $from,
            'until' => $until,
            'keluar_total' => $keluar_total,
            'masuk_total' => $masuk_total,
            'kategories' => $kategories,

        ];
        return view('accounting.ringkasan', $data);
    }

    function transactions_relations(Request $request) {
        $get = $request->query();
        // dump($user_instances);
        $user_instances_all = UserInstance::all();
        $user_instances = collect();
        $transaction_names = collect();
        if (count($get) !== 0) {
            // dd($get);
            if ($get['user_instance_id'] === 'all') {
                $user_instances = UserInstance::all();
            } else {
                $user_instances = UserInstance::where('id', $get['user_instance_id'])->get();
            }
        } else {
            $user_instances = UserInstance::all();
        }

        foreach ($user_instances as $user_instance) {
            $tr_names = collect();
            $uang_masuks = collect();
            $uang_keluars = collect();
            if (count($get) !== 0) {
                if ($get['desc'] && $get['kategori_level_one'] && $get['kategori_level_two']) {
                    if ($get['type'] === 'ALL') {
                        $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->where('desc','like', "%$get[desc]%")->where('kategori_level_one', 'like', "%$get[kategori_level_one]%")->where('kategori_level_two', 'like', "%%get[kategori_level_two]%")->orderBy('desc')->get();
                        $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->where('desc','like', "%$get[desc]%")->where('kategori_level_one', 'like', "%$get[kategori_level_one]%")->where('kategori_level_two', 'like', "%%get[kategori_level_two]%")->orderBy('desc')->get();
                    } elseif ($get['type'] === 'UANG KELUAR') {
                        $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->where('desc','like', "%$get[desc]%")->where('kategori_level_one', 'like', "%$get[kategori_level_one]%")->where('kategori_level_two', 'like', "%%get[kategori_level_two]%")->orderBy('desc')->get();
                    } elseif ($get['type'] === 'UANG MASUK') {
                        $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->where('desc','like', "%$get[desc]%")->where('kategori_level_one', 'like', "%$get[kategori_level_one]%")->where('kategori_level_two', 'like', "%%get[kategori_level_two]%")->orderBy('desc')->get();
                    }
                } elseif ($get['desc'] && $get['kategori_level_one'] && !$get['kategori_level_two']) {
                    if ($get['type'] === 'ALL') {
                        $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->where('desc','like', "%$get[desc]%")->where('kategori_level_one', 'like', "%$get[kategori_level_one]%")->orderBy('desc')->get();
                        $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->where('desc','like', "%$get[desc]%")->where('kategori_level_one', 'like', "%$get[kategori_level_one]%")->orderBy('desc')->get();
                    } elseif ($get['type'] === 'UANG KELUAR') {
                        $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->where('desc','like', "%$get[desc]%")->where('kategori_level_one', 'like', "%$get[kategori_level_one]%")->orderBy('desc')->get();
                    } elseif ($get['type'] === 'UANG MASUK') {
                        $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->where('desc','like', "%$get[desc]%")->where('kategori_level_one', 'like', "%$get[kategori_level_one]%")->orderBy('desc')->get();
                    }
                } elseif ($get['desc'] && !$get['kategori_level_one'] && $get['kategori_level_two']) {
                    if ($get['type'] === 'ALL') {
                        $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->where('desc','like', "%$get[desc]%")->where('kategori_level_two', 'like', "%%get[kategori_level_two]%")->orderBy('desc')->get();
                        $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->where('desc','like', "%$get[desc]%")->where('kategori_level_two', 'like', "%%get[kategori_level_two]%")->orderBy('desc')->get();
                    } elseif ($get['type'] === 'UANG KELUAR') {
                        $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->where('desc','like', "%$get[desc]%")->where('kategori_level_two', 'like', "%%get[kategori_level_two]%")->orderBy('desc')->get();
                    } elseif ($get['type'] === 'UANG MASUK') {
                        $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->where('desc','like', "%$get[desc]%")->where('kategori_level_two', 'like', "%%get[kategori_level_two]%")->orderBy('desc')->get();
                    }
                } elseif ($get['desc'] && !$get['kategori_level_one'] && !$get['kategori_level_two']) {
                    if ($get['type'] === 'ALL') {
                        $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->where('desc','like', "%$get[desc]%")->orderBy('desc')->get();
                        $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->where('desc','like', "%$get[desc]%")->orderBy('desc')->get();
                    } elseif ($get['type'] === 'UANG KELUAR') {
                        $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->where('desc','like', "%$get[desc]%")->orderBy('desc')->get();
                    } elseif ($get['type'] === 'UANG MASUK') {
                        $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->where('desc','like', "%$get[desc]%")->orderBy('desc')->get();
                    }
                }  elseif (!$get['desc'] && $get['kategori_level_one'] && $get['kategori_level_two']) {
                    if ($get['type'] === 'ALL') {
                        $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->where('kategori_level_one', 'like', "%$get[kategori_level_one]%")->where('kategori_level_two', 'like', "%%get[kategori_level_two]%")->orderBy('desc')->get();
                        $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->where('kategori_level_one', 'like', "%$get[kategori_level_one]%")->where('kategori_level_two', 'like', "%%get[kategori_level_two]%")->orderBy('desc')->get();
                    } elseif ($get['type'] === 'UANG KELUAR') {
                        $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->where('kategori_level_one', 'like', "%$get[kategori_level_one]%")->where('kategori_level_two', 'like', "%%get[kategori_level_two]%")->orderBy('desc')->get();
                    } elseif ($get['type'] === 'UANG MASUK') {
                        $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->where('kategori_level_one', 'like', "%$get[kategori_level_one]%")->where('kategori_level_two', 'like', "%%get[kategori_level_two]%")->orderBy('desc')->get();
                    }
                } elseif (!$get['desc'] && $get['kategori_level_one'] && !$get['kategori_level_two']) {
                    if ($get['type'] === 'ALL') {
                        $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->where('kategori_level_one', 'like', "%$get[kategori_level_one]%")->orderBy('desc')->get();
                        $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->where('kategori_level_one', 'like', "%$get[kategori_level_one]%")->orderBy('desc')->get();
                    } elseif ($get['type'] === 'UANG KELUAR') {
                        $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->where('kategori_level_one', 'like', "%$get[kategori_level_one]%")->orderBy('desc')->get();
                    } elseif ($get['type'] === 'UANG MASUK') {
                        $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->where('kategori_level_one', 'like', "%$get[kategori_level_one]%")->orderBy('desc')->get();
                    }
                } elseif (!$get['desc'] && !$get['kategori_level_one'] && $get['kategori_level_two']) {
                    if ($get['type'] === 'ALL') {
                        $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->where('kategori_level_two', 'like', "%$get[kategori_level_two]%")->orderBy('desc')->get();
                        $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->where('kategori_level_two', 'like', "%$get[kategori_level_two]%")->orderBy('desc')->get();
                    } elseif ($get['type'] === 'UANG KELUAR') {
                        $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->where('kategori_level_two', 'like', "%$get[kategori_level_two]%")->orderBy('desc')->get();
                    } elseif ($get['type'] === 'UANG MASUK') {
                        $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->where('kategori_level_two', 'like', "%$get[kategori_level_two]%")->orderBy('desc')->get();
                    }
                } elseif (!$get['desc'] && !$get['kategori_level_one'] && !$get['kategori_level_two']) {
                    if ($get['type'] === 'ALL') {
                        $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->orderBy('desc')->get();
                        $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->orderBy('desc')->get();
                    } elseif ($get['type'] === 'UANG KELUAR') {
                        $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->orderBy('desc')->get();
                    } elseif ($get['type'] === 'UANG MASUK') {
                        $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->orderBy('desc')->get();
                    }
                }
            } else {
                $uang_keluars = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG KELUAR')->orderBy('desc')->get();
                $uang_masuks = TransactionName::where('user_instance_id', $user_instance->id)->where('kategori_type','UANG MASUK')->orderBy('desc')->get();
            }
            $tr_names->push($uang_keluars);
            $tr_names->push($uang_masuks);
            $transaction_names->push($tr_names);
        }
        // dump($transaction_names);
        // dd($transaction_names[0]);
        $users = User::all();
        $label_suppliers = Supplier::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
        $label_pelanggans = Pelanggan::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
        $label_deskripsi_masuk = TransactionName::where('kategori_type', 'UANG MASUK')->select('id', 'desc as label', 'desc as value')->groupBy('id', 'desc')->orderBy('desc')->get();
        $label_deskripsi_keluar = TransactionName::where('kategori_type', 'UANG KELUAR')->select('id', 'desc as label', 'desc as value')->groupBy('id', 'desc')->orderBy('desc')->get();
        $label_deskripsi = TransactionName::select('id', 'desc as label', 'desc as value')->groupBy('id', 'desc')->orderBy('desc')->get();
        // dump($label_deskripsi_keluar);
        // dd($label_deskripsi_masuk);
        $kategoris = Kategori::all();
        // $label_kategori_level_one = Kategori::select('kategori_level_one as label', 'kategori_level_one as value')->groupBy('kategori_level_one')->orderBy('kategori_level_one')->get();
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'accounting.transactions_relations',
            'parent_route' => 'accounting.index',
            'profile_menus' => Menu::get_profile_menus(),
            'accounting_menus' => Menu::get_accounting_menus(),
            'transaction_names' => $transaction_names,
            'user_instances' => $user_instances,
            'users' => $users,
            'label_suppliers' => $label_suppliers,
            'label_pelanggans' => $label_pelanggans,
            'user_instances_all' => $user_instances_all,
            'label_deskripsi' => $label_deskripsi,
            'label_deskripsi_keluar' => $label_deskripsi_keluar,
            'label_deskripsi_masuk' => $label_deskripsi_masuk,
            'kategoris' => $kategoris,
            // 'label_kategori_level_one' => $label_kategori_level_one,
        ];

        return view('accounting.transactions_relations', $data);
    }

    function store_transactions_relations(Request $request) {
        $post = $request->post();
        // dd($post);
        $request->validate([
            'user_instance_id' => 'required',
            'type' => 'required',
            'desc' => 'required',
            'kategori_level_one' => 'required',
        ]);

        $success_ = '';
        $user_instance = UserInstance::find($post['user_instance_id']);
        $user = Auth::user();

        if ((int)$user_instance->user_id !== $user->id) {
            dd('user_id');
        }

        $exist_kategori = null;

        if ($post['kategori_level_two']) {
            $exist_kategori = Kategori::where('type', $post['type'])->where('kategori_level_one', $post['kategori_level_one'])->where('kategori_level_two', $post['kategori_level_two'])->first();
        } else {
            $exist_kategori = Kategori::where('type', $post['type'])->where('kategori_level_one', $post['kategori_level_one'])->first();
            if ($exist_kategori->kategori_level_two !== null) {
                dump($exist_kategori);
                dd('kategori_level_two?');
            }
        }

        if (!$exist_kategori) {
            dump($post);
            dd('exist kategori?');
        }

        $pelanggan_id = null;
        $pelanggan_nama = null;

        if ($post['pelanggan_id']) {
            $pelanggan = Pelanggan::find($post['pelanggan_id']);
            if (!$pelanggan) {
                dd('isset($post["pelanggan_id"]) but pelanggan?');
            }
            $pelanggan_id = $pelanggan->id;
            $pelanggan_nama = $pelanggan->nama;
        }

        $supplier_id = null;
        $supplier_nama = null;

        if ($post['supplier_id']) {
            $supplier = Supplier::find($post['supplier_id']);
            $supplier_id = $supplier->id;
            $supplier_nama = $supplier->nama;
        }

        $related_user_id = null;
        $related_username = null;
        $related_desc = null;
        $related_user_instance_id = null;
        $related_user_instance_type = null;
        $related_user_instance_name = null;
        $related_user_instance_branch = null;

        if ($post['related_user_instance_id']) {
            if ($post['related_user_instance_id'] === $post['user_instance_id']) {
                dd('user_instance = related_user_instance ?');
            }
            if (!$post['related_desc']) {
                dd('related_desc?');
            }
            $related_user_instance = UserInstance::find($post['related_user_instance_id']);
            $related_user_instance_id = $related_user_instance->id;
            $related_user_instance_type = $related_user_instance->instance_type;
            $related_user_instance_name = $related_user_instance->instance_name;
            $related_user_instance_branch = $related_user_instance->branch;
            $related_user_id = $related_user_instance->user_id;
            $related_username = $related_user_instance->username;
            $related_desc = $post['related_desc'];
        }

        TransactionName::create([
            'user_id'=>$user->id,
            'username'=>$user->username,
            'user_instance_id'=>$user_instance->id,
            'user_instance_type'=>$user_instance->instance_type,
            'user_instance_name'=>$user_instance->instance_name,
            'user_instance_branch'=>$user_instance->branch,
            'related_user_id'=>$related_user_id,
            'related_username'=>$related_username,
            'desc'=>$post['desc'],
            'kategori_type'=>$post['type'],
            'kategori_level_one'=>$post['kategori_level_one'],
            'kategori_level_two'=>$post['kategori_level_two'],
            'related_desc'=>$related_desc,
            'pelanggan_id'=>$pelanggan_id,
            'pelanggan_nama'=>$pelanggan_nama,
            'supplier_id'=>$supplier_id,
            'supplier_nama'=>$supplier_nama,
            'related_user_instance_id'=>$related_user_instance_id,
            'related_user_instance_type'=>$related_user_instance_type,
            'related_user_instance_name'=>$related_user_instance_name,
            'related_user_instance_branch'=>$related_user_instance_branch,
        ]);

        $success_ .= '-transaction_relation created-';

        return back()->with('success_', $success_);
    }

    function delete_transaction_relation(TransactionName $transaction_name) {
        // dd($transaction_name);
        $transaction_name->delete();
        return back()->with('danger_', '-transaction_relation deleted!-');
    }

    function up_down_transaction(UserInstance $user_instance, Accounting $accounting, Request $request) {
        $post = $request->post();
        // dump($post);
        // dump($user_instance);
        // dd($accounting);

        $accounting_to_compare = collect();
        $saldo_1 = null;
        $saldo_2 = null;
        if ($post['up_down_transaction'] === 'up') {
            $accounting_to_compare = Accounting::where('user_instance_id', $user_instance->id)->where('created_at', '<', $accounting->created_at)->latest()->first();
            dump($accounting_to_compare);
            if ($accounting_to_compare === null) {
                $request->validate(['error'=>'required'],['error.required'=>'no transaction to compare']);
            }
            $saldo_1 = (int)$accounting->saldo;
            if ($accounting_to_compare->transaction_type === 'pengeluaran') {
                $saldo_1 += (int)$accounting_to_compare->jumlah;
                $saldo_2 = $saldo_1;
                $saldo_2 -= (int)$accounting_to_compare->jumlah;
            } elseif ($accounting_to_compare->transaction_type === 'pemasukan') {
                $saldo_1 -= (int)$accounting_to_compare->jumlah;
                $saldo_2 = $saldo_1;
                $saldo_2 += (int)$accounting_to_compare->jumlah;
            }
            // dump($saldo_1);
            // dd($saldo_2);
        } elseif ($post['up_down_transaction'] === 'down') {
            $accounting_to_compare = Accounting::where('user_instance_id', $user_instance->id)->where('created_at', '>', $accounting->created_at)->first();
            if ($accounting_to_compare === null) {
                $request->validate(['error'=>'required'],['error.required'=>'no transaction to compare']);
            }
            $saldo_2 = $accounting->saldo;
            if ($accounting->transaction_type === 'pengeluaran') {
                $saldo_2 += (int)$accounting->jumlah;
            } elseif ($accounting->transaction_type === 'pemasukan') {
                $saldo_2 -= (int)$accounting->jumlah;
            }

            if ($accounting_to_compare->transaction_type === 'pengeluaran') {
                $saldo_2 -= (int)$accounting_to_compare->jumlah;
            } elseif ($accounting_to_compare->transaction_type === 'pemasukan') {
                $saldo_2 += (int)$accounting_to_compare->jumlah;
            }

            $saldo_1 = $saldo_2;

            if ($accounting->transaction_type === 'pengeluaran') {
                $saldo_1 -= (int)$accounting->jumlah;
            } elseif ($accounting->transaction_type === 'pemasukan') {
                $saldo_1 += (int)$accounting->jumlah;
            }

        }

        // dd($accounting_to_compare);

        $created_at_1 = $accounting_to_compare->created_at;
        $created_at_2 = $accounting->created_at;

        // dump('saldo_1: ' . $saldo_1);
        // dd('saldo_2: ' . $saldo_2);

        $accounting->update([
            "created_at" => $created_at_1,
            "saldo" => (string)$saldo_1,
        ]);
        // $accounting->created_at = $created_at_1;
        // $accounting->saldo = $saldo_1;
        // $accounting->save();

        $accounting_to_compare->update([
            "created_at" => $created_at_2,
            "saldo" => (string)$saldo_2,
        ]);

        // $accounting_to_compare->created_at = $created_at_2;
        // $accounting_to_compare->saldo = $saldo_2;
        // $accounting_to_compare->save();

        return back()->with('success_', "-accounting's time swapped-");
    }
}
