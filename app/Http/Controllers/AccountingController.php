<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use App\Models\Menu;
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

        $user_instance = UserInstance::where('user_id', $user->id)->get();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'produks.index',
            'parent_route' => 'home',
            'profile_menus' => Menu::get_profile_menus(),
            'spk_menus' => Menu::get_spk_menus(),
            'user' => $user,
            'instance_types' => Accounting::get_instance_types(),
            'instance_names' => Accounting::get_instance_names(),
            'user_instance' => $user_instance,
        ];

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
        $accountings = Accounting::whereBetween('created_at',[$from, $until])->orderBy('created_at')->get();
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

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'produks.index',
            'parent_route' => 'home',
            'profile_menus' => Menu::get_profile_menus(),
            'spk_menus' => Menu::get_spk_menus(),
            'user' => $user,
            'instance_types' => Accounting::get_instance_types(),
            'instance_names' => Accounting::get_instance_names(),
            'accountings' => $accountings,
            'user_instance' => $user_instance,
            'keluar_total' => $keluar_total,
            'masuk_total' => $masuk_total,
            'related_users' => $related_users,
        ];

        return view('accounting.show_transactions', $data);
    }

    function store_transactions(Request $request) {
        $post = $request->post();
        dd($post);
    }
}
