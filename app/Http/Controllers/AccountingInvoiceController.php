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
        $after_this = AccountingInvoice::where('nota_id', $accountingInvoice->nota_id)
            ->where('created_at', '>', $accountingInvoice->created_at)
            ->exists();

        if ($after_this) {
            return redirect()->back()->with('error', 'Hanya bisa menghapus pembayaran terakhir saja');
        }

        // Apabila ini merupakan satu-satunya history pembayaran, maka tidak boleh dihapus
        $only_one = AccountingInvoice::where('nota_id', $accountingInvoice->nota_id)->count();
        if ($only_one <= 1) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus pembayaran terakhir, karena ini merupakan satu-satunya pembayaran.');
        }

        DB::beginTransaction();
        try {
            /**
             * UPDATE / DELETE Overpayment kalau ada
             */
            if ($accountingInvoice->overpayment > 0) {
                $overpayment = Overpayment::where("customer_id", $accountingInvoice->customer_id)->first();
                $overpayment->amount -= $accountingInvoice->overpayment;
                if ($overpayment->amount > 0) {
                    $overpayment->save();
                    $success_ .= "overpayment dikurangi sebesar $accountingInvoice->overpayment. ";
                } else {
                    $overpayment->delete();
                    $success_ .= "overpayment dihapus karena menjadi 0. ";
                }
            }

            // UPDATE Nota
            $nota->amount_paid -= $accountingInvoice->amount_paid;
            $nota->balance_used -= $accountingInvoice->balance_used;
            $nota->amount_due += ($accountingInvoice->amount_paid + $accountingInvoice->balance_used);
            $nota->overpayment -= $accountingInvoice->overpayment;
            $nota->status_bayar = $nota->UpdatePaymentStatus();
            $nota->save();
            $success_ .= "nota diupdate. ";

            // UPDATE entry Accounting / Transaksi terkait
            if ($accountingInvoice->accounting_id) {
                $accounting = $accountingInvoice->accounting;
                $accounting->jumlah -= $accountingInvoice->amount_paid;
                $accounting->save();
                $accounting->updateAccountingAfter();
                $success_ .= "accounting diupdate. ";
            }

            $accountingInvoice->delete();
            $success_ .= "accounting invoice dihapus. ";
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error_', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
        
        return redirect()->back()->with('success_', $success_);
    }
}
