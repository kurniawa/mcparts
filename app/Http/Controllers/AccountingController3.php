<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use App\Models\BilyetGiro;
use App\Models\UserInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingController3 extends Controller
{
    public function destroy_accounting_bg(Request $request, Accounting $accounting, UserInstance $user_instance, BilyetGiro $bilyet_giro)
    {
        // dd($request->all(), $accounting, $user_instance, $bilyet_giro);
        if ((int)$user_instance->user_id !== Auth::user()->id) {
            $request->validate(['error'=>'required'],['error.required'=>'different user???']);
        }
        $warnings_ = '';
        // dd('test');
        DB::beginTransaction();
        try {
            $balance = 0;
            // Cari apakah ada transaksi dengan tanggal yang setelahnya?
            $last_transactions = Accounting::where('user_instance_id', $user_instance->id)->where('created_at','>',$accounting->created_at)->orderBy('created_at')->get();

            if (count($last_transactions) !== 0) {
                $before_last_transaction = Accounting::where('user_instance_id', $user_instance->id)->where('created_at','<',$accounting->created_at)->latest()->first();
                // dump('before_last_transaction: ', $before_last_transaction);
                if ($before_last_transaction !== null) {
                    $balance = $before_last_transaction->saldo;
                }

                $balance_next = $balance;
                foreach ($last_transactions as $last_transaction) {
                    if ($last_transaction->transaction_type === 'pengeluaran') {
                        $balance_next -= $last_transaction->jumlah;
                    } elseif ($last_transaction->transaction_type === 'pemasukan') {
                        $balance_next += $last_transaction->jumlah;
                    }
                    $last_transaction->saldo = $balance_next;
                    $last_transaction->save();
                }
                $warnings_ .= '-balance saldo edited-';
            }
            $accounting->delete();
            $bilyet_giro->status = 'pending';
            $bilyet_giro->clearing_date = null;
            $bilyet_giro->save();
            $warnings_ .= '-Accounting entry destroyed successfully.-';

            DB::commit();
            // dump($warnings_);
            // dd($request->all(), $accounting, $user_instance, $bilyet_giro);
            return back()->with('warnings_', $warnings_);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            // return response()->json(['error' => 'Failed to destroy accounting entry: ' . $e->getMessage()], 500);
        }
    }
}
