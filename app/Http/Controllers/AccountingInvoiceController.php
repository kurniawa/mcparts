<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use App\Models\AccountingInvoice;
use App\Models\Menu;
use App\Models\Nota;
use App\Models\Overpayment;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class AccountingInvoiceController extends Controller
{
    public function delete_last_payment_customer(Nota $nota, AccountingInvoice $accountingInvoice)
    {
        $success_ = '';
        /**
         * Menghapus accounting invoice adalah seperti menghapus history pembayaran
         * Oleh karena itu, penghapusan harus dilakukan dari accounting invoice yang terakhir
         */
        $after_this = AccountingInvoice::where('invoice_id', $accountingInvoice->invoice_id)
            ->where('created_at', '>', $accountingInvoice->created_at)
            ->exists();

        if ($after_this) {
            return redirect()->back()->with('errors_', 'Penghapusan harus dimulai dari history pembayaran terakhir.');
        }

        // Apabila ini merupakan satu-satunya history pembayaran, maka tidak boleh dihapus
        $only_one = AccountingInvoice::where('invoice_id', $accountingInvoice->invoice_id)->count();
        $reset_nota = false;
        if ($only_one <= 1) {
            // return redirect()->back()->with('errors_', 'Tidak bisa menghapus pembayaran terakhir, karena ini merupakan satu-satunya pembayaran.');
            $reset_nota = true;
        }

        DB::beginTransaction();
        try {
            /**
             * UPDATE / DELETE Overpayment kalau ada
             */
            // dd($accountingInvoice);
            if ($accountingInvoice->balance_used > 0 || $accountingInvoice->overpayment > 0) {
                $overpayment = Overpayment::where("customer_id", $accountingInvoice->customer_id)->first();
                $overpayment->amount -= $accountingInvoice->overpayment;
                $overpayment->amount += $accountingInvoice->balance_used;
                // dd($overpayment->amount);
                if ($overpayment->amount > 0) {
                    $overpayment->save();
                    $success_ .= "overpayment->amount diupdate";
                } elseif ($overpayment->amount == 0) {
                    // dd($overpayment);
                    $overpayment->delete();
                    $success_ .= "overpayment dihapus karena menjadi 0. ";
                } elseif ($overpayment->amount < 0) {
                    // Ini seharusnya tidak mungkin terjadi, tapi untuk jaga-jaga saja
                    dd($overpayment->amount);
                    // return redirect()->back()->with('error', 'Terjadi kesalahan pada data overpayment.');
                }
            }
            // dump(Overpayment::where("customer_id", $accountingInvoice->customer_id)->first());

            // UPDATE Nota
            $log = "nota->amount_paid -= accountingInvoice->amount_paid | $nota->amount_paid, $accountingInvoice->amount_paid";
            $log .= "\nnota->balance_used -= accountingInvoice->balance_used | $nota->balance_used, $accountingInvoice->balance_used";
            $log .= "\nnota->amount_due += (accountingInvoice->amount_paid + accountingInvoice->balance_used) | $nota->amount_due, ($accountingInvoice->amount_paid + $accountingInvoice->balance_used)";
            $log .= "\nnota->status_bayar | $nota->status_bayar";
            /**
             * Data overpayment pada nota, tidak perlu diupdate, itu hanya untuk menandakan,
             * kapan dan pada nota yang mana pelanggan melakukan pembayaran yang lebih daripada seharusnya
             */

            if ($reset_nota) {
                // Reset semua data pembayaran pada nota
                $nota->amount_paid = 0;
                $nota->balance_used = 0;
                $nota->amount_due = $nota->harga_total;
                $nota->status_bayar = 'BELUM_LUNAS';
                $nota->overpayment = 0;
                $log .= "\n\nRESET nota karena ini merupakan satu-satunya pembayaran.";
                $log .= "\nnota->amount_paid = $nota->amount_paid";
                $log .= "\nnota->balance_used = $nota->balance_used";
                $log .= "\nnota->amount_due = $nota->amount_due";
                $log .= "\nnota->status_bayar = $nota->status_bayar";
            } else {
                $nota->amount_paid -= $accountingInvoice->amount_paid;
                $nota->balance_used -= $accountingInvoice->balance_used;
                $nota->amount_due += ($accountingInvoice->amount_paid + $accountingInvoice->balance_used);
                // $nota->overpayment -= $accountingInvoice->balance_used;
                $nota->status_bayar = $nota->UpdatePaymentStatus();
                $log .= "\n\nnota->amount_paid = $nota->amount_paid";
                $log .= "\nnota->balance_used = $nota->balance_used";
                $log .= "\nnota->amount_due = $nota->amount_due";
                $log .= "\nnota->status_bayar = $nota->status_bayar";
            }
            // dd($log);
            $nota->save();
            $success_ .= "nota diupdate. ";

            // UPDATE entry Accounting / Transaksi terkait
            if ($accountingInvoice->accounting_id) {
                $accounting = $accountingInvoice->accounting;
                $accounting->jumlah -= (($accountingInvoice->amount_paid + $accountingInvoice->overpayment));
                $accounting->save();
                $accounting->updateAccountingAfter();
                $success_ .= "accounting diupdate. ";
                if ($accounting->jumlah == 0) {
                    $accounting->delete();
                    $success_ .= "accounting deleted. ";
                }
            }

            $accountingInvoice->delete();
            $success_ .= "accounting invoice dihapus. ";
            // dd(Overpayment::where("customer_id", $accountingInvoice->customer_id)->first());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            $message = "Error: " . $e->getMessage()
                . "\n\nFile: " . $e->getFile()
                . "\n\nFile: " . $e->getLine()
                . "\n\nTrace: " . $e->getTraceAsString();
            dd($message);

            return redirect()->back()->with('errors_', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
        
        return redirect()->back()->with('success_', $success_);
    }

    public function edit_payment_history(Nota $nota)
    {
        $accountingInvoices = AccountingInvoice::where('invoice_id', $nota->id)->where('invoice_table', 'notas')->orderBy('created_at', 'asc')->get();
        $available_accounting_invoices = AccountingInvoice::whereNull('invoice_id')->whereNull('accounting_id')->where('invoice_table', 'notas')->where('customer_id', $nota->pelanggan_id)->get();
        $available_accountings = Accounting::where('pelanggan_id', $nota->pelanggan_id)->where('keterangan', 'LIKE', '%sisa%')->orderBy('created_at', 'asc')->get();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'pembelians.show',
            'profile_menus' => Menu::get_profile_menus(),
            'nota' => $nota,
            'accountingInvoices' => $accountingInvoices,
            'available_accounting_invoices' => $available_accounting_invoices,
            'available_accountings' => $available_accountings,
        ];

        // dd($available_accounting_invoices);

        return view('accounting-invoices.edit-payment-history', $data);
    }

    public function delete_payment_history(AccountingInvoice $accountingInvoice)
    {
        DB::beginTransaction();
        try {
            $accounting = $accountingInvoice->accounting;
            $nota = $accountingInvoice->nota;

            // Update AccountingInvoice
            $accountingInvoice->update([
                'accounting_time_key' => null,
                'accounting_id' => null,
                'invoice_id' => null,
                'invoice_number' => null,
            ]);

            // Update Keterangan Accounting
            $accounting->keterangan = $accounting->updateKeteranganAccounting($accounting);
            $accounting->save();

            // Update Nota, hitung ulang dari semua accounting invoice yang terkait dengan nota ini
            $nota->updateNotaAndAllRelatedAccountingInvoices();

            DB::commit();

            return redirect()->back()->with('success_', 'Data history pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            $message = "Error: " . $e->getMessage()
                . "\n\nFile: " . $e->getFile()
                . "\n\nFile: " . $e->getLine()
                . "\n\nTrace: " . $e->getTraceAsString();
            dd($message);

            return redirect()->back()->with('errors_', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
        
    }

    public function add_payment_history(Request $request, Nota $nota)
    {
        $validated = $request::validate([
            'accounting_invoice_id' => 'required|exists:accounting_invoices,id',
            'accounting_id' => 'required|exists:accountings,id',
        ]);

        DB::beginTransaction();
        try {
            $accountingInvoice = AccountingInvoice::findOrFail($validated['accounting_invoice_id']);
            $accounting = Accounting::findOrFail($validated['accounting_id']);

            // Update AccountingInvoice
            $accountingInvoice->update([
                'accounting_time_key' => $accounting->time_key,
                'accounting_id' => $accounting->id,
                'invoice_id' => $nota->id,
                'invoice_number' => $nota->nomor_nota,
            ]);

            // Update Keterangan Accounting
            $accounting->keterangan = $accounting->updateKeteranganAccounting($accounting);
            $accounting->save();

            // Update Nota, hitung ulang dari semua accounting invoice yang terkait dengan nota ini
            $nota->updateNotaAndAllRelatedAccountingInvoices();

            DB::commit();
            return back()->with('success_', 'Data history pembayaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            $message = "Error: " . $e->getMessage()
                . "\n\nFile: " . $e->getFile()
                . "\n\nFile: " . $e->getLine()
                . "\n\nTrace: " . $e->getTraceAsString();
            dd($message);

            return redirect()->back()->with('errors_', 'Terjadi kesalahan saat menambahkan data: ' . $e->getMessage());
        }

    }
}
