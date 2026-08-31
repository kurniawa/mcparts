<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use App\Models\AccountingInvoice;
use App\Models\BilyetGiro;
use App\Models\Menu;
use App\Models\Nota;
use App\Models\Pembelian;
use App\Models\TransactionName;
use App\Models\UserInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountingController2 extends Controller
{
    public function laba_rugi(Request $request)
    {
        $get = $request->query();
        $from = date('Y-m-d', strtotime('-1 month'));
        $until = date('Y-m-d');
        $timerange = isset($get['timerange']) ? $get['timerange'] : null;
        $from_day = isset($get['from_day']) ? $get['from_day'] : null;
        $from_month = isset($get['from_month']) ? $get['from_month'] : null;
        $from_year = isset($get['from_year']) ? $get['from_year'] : null;
        $to_day = isset($get['to_day']) ? $get['to_day'] : null;
        $to_month = isset($get['to_month']) ? $get['to_month'] : null;
        $to_year = isset($get['to_year']) ? $get['to_year'] : null;
        if (isset($get['from_day']) && isset($get['from_month']) && isset($get['from_year']) && isset($get['to_day']) && isset($get['to_month']) && isset($get['to_year'])) {
            $request->validate([
                'from_day' => 'required|integer|min:1|max:31',
                'from_month' => 'required|integer|min:1|max:12',
                'from_year' => 'required|integer|min:1900|max:2100',
                'to_day' => 'required|integer|min:1|max:31',
                'to_month' => 'required|integer|min:1|max:12',
                'to_year' => 'required|integer|min:1900|max:2100',
            ], [
                'from_day.required' => 'From day is required.',
                'from_day.integer' => 'From day must be an integer.',
                'from_day.min' => 'From day must be at least 1.',
                'from_day.max' => 'From day must be at most 31.',
                'from_month.required' => 'From month is required.',
                'from_month.integer' => 'From month must be an integer.',
                'from_month.min' => 'From month must be at least 1.',
                'from_month.max' => 'From month must be at most 12.',
                'from_year.required' => 'From year is required.',
                'from_year.integer' => 'From year must be an integer.',
                'from_year.min' => 'From year must be at least 1900.',
                'from_year.max' => 'From year must be at most 2100.',
                'to_day.required' => 'Until day is required.',
                'to_day.integer' => 'Until day must be an integer.',
                'to_day.min' => 'Until day must be at least 1.',
                'to_day.max' => 'Until day must be at most 31.',
                'to_month.required' => 'Until month is required.',
                'to_month.integer' => 'Until month must be an integer.',
                'to_month.min' => 'Until month must be at least 1.',
                'to_month.max' => 'Until month must be at most 12.',
                'to_year.required' => 'Until year is required.',
                'to_year.integer' => 'Until year must be an integer.',
                'to_year.min' => 'Until year must be at least 1900.',
                'to_year.max' => 'Until year must be at most 2100.', 
            ]);
            $from = date('Y-m-d', strtotime($get['from_year'] . '-' . $get['from_month'] . '-' . $get['from_day']));
            $until = date('Y-m-d', strtotime($get['to_year'] . '-' . $get['to_month'] . '-' . $get['to_day']));
        }

        // Laporan Laba Rugi   
        // PENDAPATAN
        $penjualan_barang_dan_jasa = Nota::whereBetween('created_at', [$from, $until])
        ->sum(DB::raw("
            CASE 
                WHEN status_bayar = 'LUNAS' THEN harga_total 
                WHEN status_bayar = 'SEBAGIAN' THEN amount_paid 
                ELSE 0 
            END
        "));

        $total_discount = Nota::where(function ($query) {
                $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
            })
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $until)
            ->sum('total_discount');
        $bunga = Accounting::where('transaction_desc', 'BUNGA')->where('created_at', '>=', $from)
            ->where('created_at', '<=', $until)
            ->sum('jumlah');
        $pajak_bunga = Accounting::where('transaction_desc', 'PAJAK BUNGA')->where('created_at', '>=', $from)
            ->where('created_at', '<=', $until)
            ->sum('jumlah');
        $pendapatan_bunga_dipotong_pajak_bunga = $bunga - $pajak_bunga; 
        $total_pendapatan = $penjualan_barang_dan_jasa - $total_discount + $pendapatan_bunga_dipotong_pajak_bunga;
        $income_statement = [
            [
                'name' => 'PENDAPATAN',
                'childs' => [
                    ['name' => 'PENJUALAN BARANG DAN JASA', 'amount' => $penjualan_barang_dan_jasa],
                    ['name' => 'POTONGAN HARGA', 'amount' => $total_discount],
                    ['name' => 'PENDAPATAN BUNGA DIPOTONG PAJAK BUNGA', 'amount' => $pendapatan_bunga_dipotong_pajak_bunga],
                    ['name' => 'PENDAPATAN LAIN-LAIN', 'amount' => 0],
                    ['name' => 'TOTAL PENDAPATAN', 'amount' => $total_pendapatan],
                ]
            ],
        ];
        // dd($income_statement);

        /**
         * HARGA POKOK PENJUALAN
         */
        $biaya_bahan_baku = Pembelian::whereBetween('created_at', [$from, $until])
            ->sum(DB::raw("
                CASE 
                    WHEN status_bayar = 'LUNAS' THEN harga_total 
                    WHEN status_bayar = 'SEBAGIAN' THEN amount_paid 
                    ELSE 0 
                END
            "));
        $income_statement[] = [
            'name' => 'HARGA POKOK PENJUALAN',
            'childs' => [
                ['name' => 'BIAYA BAHAN BAKU', 'amount' => $biaya_bahan_baku],
                ['name' => 'BIAYA BAHAN BAKU RETURAN', 'amount' => $total_discount],
                ['name' => 'BIAYA BAHAN PENDUKUNG', 'amount' => $pendapatan_bunga_dipotong_pajak_bunga],
                ['name' => 'BIAYA TENAGA KERJA', 'amount' => 0],
                ['name' => 'BIAYA UTILITAS', 'amount' => $total_pendapatan],
                ['name' => 'TOTAL HARGA POKOK PENJUALAN', 'amount' => $total_pendapatan],
            ]
        ];
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'accounting.laba_rugi',
            'parent_route' => 'accounting.index',
            'profile_menus' => Menu::get_profile_menus(),
            'accounting_menus' => Menu::get_accounting_menus(),
            'from' => $from,
            'until' => $until,
            'income_statement' => $income_statement,
            'from_day' => $from_day,
            'from_month' => $from_month,
            'from_year' => $from_year,
            'to_day' => $to_day,
            'to_month' => $to_month,
            'to_year' => $to_year,
            'timerange' => $timerange,
        ];
        // dd($data);
        return view('accounting.laba_rugi', $data);
    }

    public function getRelatedNotYetPaidOffInvoices(TransactionName $transactionName) {
        // Get the related invoice for the transaction name
        // return response()->json(['message' => "Transaction name: $transactionName"], 404);
        // Log::info("Transaction name: $transactionName");
        if (!isset($transactionName)) {
            return response()->json(['message' => "Transaction name not define: $transactionName"], 404);
        } elseif (!$transactionName) {
            return response()->json(['message' => "Transaction name not define: $transactionName"], 404);
        } else {
            if ($transactionName->pelanggan_id) {
                [$notYetPaidOffInvoices, $customerBalance] = $transactionName->getRelatedNotYetPaidOffInvoices();
                if (!$notYetPaidOffInvoices) {
                    return response()->json(['message' => 'Data not found'], 404);
                }
        
                return response()->json(['message' => 'Data found', 'notas' => $notYetPaidOffInvoices, 'customerBalance' => $customerBalance], 200);
            } elseif ($transactionName->supplier_id) {
                // Log::info("Transaction name has supplier_id: $transactionName->supplier_id");
                // dump('supplier');
                [$notYetPaidOffInvoices, $supplierBalance] = $transactionName->getRelatedNotYetPaidOffInvoices();
                if (!$notYetPaidOffInvoices) {
                    return response()->json(['message' => 'Data not found'], 404);
                }
        
                return response()->json(['message' => 'Data found', 'notas' => $notYetPaidOffInvoices, 'supplierBalance' => $supplierBalance], 200);
            } else {
                return response()->json(['message' => 'Transaction name does not have a related customer or supplier'], 400);
            }
        }
    }

    function search_related_accounting(Nota $nota) {
        $possible_related_accountings = $nota->possible_related_accountings();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'accounting.laba_rugi',
            'parent_route' => 'accounting.index',
            'profile_menus' => Menu::get_profile_menus(),
            'accounting_menus' => Menu::get_accounting_menus(),
            'nota' => $nota,
            'possible_related_accountings' => $possible_related_accountings,
        ];
        return view('spks.search_related_accounting', $data);
    }

    function link_nota_accounting(Request $request, Nota $nota, Accounting $accounting) {
        $post = $request->post();
        // dd($post);
        $payment_status = $post['payment_status'];
        if ($payment_status !== 'LUNAS') {
            $request->validate(['error'=>'required'],['error.required'=>'Payment status must be "lunas" when linking nota to accounting.']);
        }
        $spk_id = $nota->spk->first()->id;
        $transaction_name = TransactionName::where('user_instance_id', $accounting->user_instance_id)->where('desc', $accounting->transaction_desc)->first();
        
        $accounting_invoice_status = 'active';
        if ($payment_status == 'LUNAS') {
            $accounting_invoice_status = 'inactive';
        }
        // Create AccountingInvoice
        AccountingInvoice::create([
            'accounting_time_key' => $accounting->time_key,
            'time_key' => time(),
            'accounting_id' => $accounting->id,
            'user_instance_id' => $accounting->user_instance_id,
            'invoice_id' => $nota->id,
            'invoice_table' => 'notas',
            'invoice_number' => $nota->nomor_nota,
            'transaction_name_id' => $transaction_name->id,
            'transaction_name_desc' => $transaction_name->desc,
            'customer_id' => $nota->pelanggan_id,
            'customer_name' => $nota->pelanggan_nama,
            'payment_status' => $post['payment_status'],
            'discount_percentage' => $post['discount_percent'],
            'discount_amount' => $post['discount_amount'],
            'other_discount' => $post['other_discount'],
            'total_discount' => $post['total_discount'],
            'discount_description' => $post['discount_description'],
            'amount_due' => $post['amount_due_new'],
            'amount_paid' => $post['amount_paid'],
            'balance_used' => 0.00,
            'total_amount' => $nota->harga_total,
            'remaining_funds' => $post['remaining_balance'],
            'balance' => $post['balance_start'],
            'overpayment' => 0.00,
            'status' => $accounting_invoice_status,
            'created_at' => $accounting->created_at,
        ]);
        // Update Nota payment status
        $nota->status_bayar = $payment_status;
        $nota->discount_percent = $post['discount_percent'];
        $nota->discount_amount = $post['discount_amount'];
        $nota->other_discount = $post['other_discount'];
        $nota->total_discount = $post['total_discount'];
        $nota->discount_description = $post['discount_description'];
        $nota->amount_due = $post['amount_due_new'];
        $nota->amount_paid = $post['amount_paid'];
        $nota->balance_used = 0.00;
        $nota->overpayment = 0.00;
        $nota->finished_at = $accounting->created_at;
        $nota->save();

        return redirect()->route('spks.show', ['spk' => $spk_id])->with('success_', 'Nota linked to Accounting successfully. AccountingInvoice created. Nota updated.');
    }

    function change_date(Request $request,Accounting $accounting) {
        $post = $request->post();
        // dump($post);
        // dump($accounting);
        // dd($accounting->accounting_invoices);

        // Validate new date
        $request->validate([
            'new_date' => 'required|date',
        ], [
            'new_date.required' => 'New date is required.',
            'new_date.date' => 'New date must be a valid date.',
        ]);

        // If new date is same as old date, do nothing
        $old_date = date('Y-m-d', strtotime($accounting->created_at));
        if ($old_date == $post['new_date']) {
            return;
        }

        DB::beginTransaction();
        try {
            $success_ = '';
            // Update related AccountingInvoices
            $new_created_at = date('Y-m-d', strtotime($post['new_date'])) . ' ' . date('H:i:s');
            foreach ($accounting->accounting_invoices as $accounting_invoice) {
                $accounting_invoice->created_at = $new_created_at;
                $accounting_invoice->save();
                $success_ .= "AccountingInvoice $accounting_invoice->customer_name - $accounting_invoice->invoice_number date updated.";
                /**
                 * If $accounting->kategori_level_one == 'Penerimaan Piutang' &&
                 * $accounting_invoice->payment_status == 'LUNAS'
                 * then also update the related Nota's finished_at date
                 */
                if ($accounting->kategori_level_one == 'Penerimaan Piutang' && $accounting_invoice->payment_status == 'LUNAS') {
                    $nota = Nota::find($accounting_invoice->invoice_id);
                    if ($nota) {
                        $nota->finished_at = $new_created_at;
                        $nota->save();
                        $success_ .= " Nota $nota->nomor_nota finished_at date updated.";
                    }
                }
            }

            /**
             * Updating Accounting's created_at need to recalculate the balance(saldo) of related user_instance_id
             * So first we need to get the balance(saldo) before this accounting's old created_at
             * then recalculate the balance(saldo) from this accounting's new created_at to the latest accounting
             */
            $old_created_at = $accounting->created_at;
            if($old_created_at > $new_created_at) { // tanggal lebih awal
                // Get balance before new_created_at
                $accounting_before = Accounting::where('user_instance_id', $accounting->user_instance_id)
                    ->where('created_at', '<', $new_created_at)
                    ->orderBy('created_at', 'desc')
                    ->first();
                $starting_balance = $accounting_before ? $accounting_before->saldo : 0.00;
                $last_balance = $starting_balance;
                
                // Update $last_balance for this accounting terlebih dahulu
                // Setelah itu update accountings yang ada diantara new_created_at dan old_created_at
                if($accounting->transaction_type == 'pemasukan') {
                    $last_balance += $accounting->jumlah;
                } elseif($accounting->transaction_type == 'pengeluaran') {
                    $last_balance -= $accounting->jumlah;
                }
                $accounting->saldo = $last_balance;
                $accounting->created_at = $new_created_at;
                $accounting->save();
                $success_ .= " Accounting date updated.";

                $accounting_betweens = Accounting::where('user_instance_id', $accounting->user_instance_id)
                    ->where('created_at', '>', $new_created_at)
                    ->where('created_at', '<', $old_created_at)
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                // Recalculate balance from new_created_at to old_created_at
                foreach ($accounting_betweens as $acc) {
                    if($acc->transaction_type == 'pemasukan') {
                        $last_balance += $acc->jumlah;
                    } elseif($acc->transaction_type == 'pengeluaran') {
                        $last_balance -= $acc->jumlah;
                    }
                    $acc->saldo = $last_balance;
                    $acc->save();
                }
                $success_ .= " Related accountings' balances recalculated.";
                
            } elseif($old_created_at < $new_created_at) { // tanggal lebih akhir
                // Get balance before old_created_at
                $accounting_before = Accounting::where('user_instance_id', $accounting->user_instance_id)
                    ->where('created_at', '<', $old_created_at)
                    ->orderBy('created_at', 'desc')
                    ->first();
                $starting_balance = $accounting_before ? $accounting_before->saldo : 0.00;
                $last_balance = $starting_balance;

                // Update accountings yang ada diantara old_created_at dan new_created_at terlebih dahulu
                // Setelah itu update $last_balance for this accounting
                $accounting_betweens = Accounting::where('user_instance_id', $accounting->user_instance_id)
                    ->where('created_at', '>', $old_created_at)
                    ->where('created_at', '<', $new_created_at)
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                foreach ($accounting_betweens as $acc) {
                    if($acc->transaction_type == 'pemasukan') {
                        $last_balance += $acc->jumlah;
                    } elseif($acc->transaction_type == 'pengeluaran') {
                        $last_balance -= $acc->jumlah;
                    }
                    $acc->saldo = $last_balance;
                    $acc->save();
                }
                $success_ .= " Related accountings' balances recalculated.";

                if($accounting->transaction_type == 'pemasukan') {
                    $last_balance += $accounting->jumlah;
                } elseif($accounting->transaction_type == 'pengeluaran') {
                    $last_balance -= $accounting->jumlah;
                }
                $accounting->saldo = $last_balance;
                $accounting->created_at = $new_created_at;
                $accounting->save();
                $success_ .= " Accounting date updated.";
            }
            DB::commit();
            return back()->with('success_', $success_);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    function kliring_bg(UserInstance $user_instance) {
        $bilyetGiros = BilyetGiro::orderBy('created_at', 'desc')->get();
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'accounting.kliring_bg',
            'parent_route' => 'accounting.index',
            'profile_menus' => Menu::get_profile_menus(),
            'accounting_menus' => Menu::get_accounting_menus(),
            'user_instance' => $user_instance,
            'bilyetGiros' => $bilyetGiros,
        ];
        // dd($data);
        return view('accounting.kliring_bg', $data);
    }

    function store_kliring_bg(UserInstance $user_instance, Request $request) {
        dump($user_instance);
        dd($request->post());
    }
}
