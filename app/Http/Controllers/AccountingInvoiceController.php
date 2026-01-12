<?php

namespace App\Http\Controllers;

use App\Models\AccountingInvoice;
use App\Models\Nota;
use App\Models\Overpayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                $nota->status_bayar = 'belum_lunas';
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
}
