<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionName extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * Get notas, where status_bayar is 'belum_lunas' or 'sebagian'
     */
    public function getRelatedNotYetPaidOffInvoices()
    {
        $accountingInvoiceOverpayment = AccountingInvoice::where('invoice_table', 'notas')
            ->where('customer_id', $this->pelanggan_id)
            ->where('is_overpayment_exist', true)
            ->latest()->first();

        $accountingInvoices = AccountingInvoice::where('invoice_table', 'notas')
            ->where('customer_id', $this->pelanggan_id)
            ->whereIn('payment_status', ['belum_lunas', 'sebagian'])
            ->get()
            ->map(function ($invoice) use ($accountingInvoiceOverpayment) {
                if ($accountingInvoiceOverpayment) {
                    $invoice->overpayment = $accountingInvoiceOverpayment->overpayment;
                    $invoice->overpayment_time = $accountingInvoiceOverpayment->overpayment_time;
                    $invoice->is_overpayment_exist = $accountingInvoiceOverpayment->is_overpayment_exist;
                }
                $invoice->no_nota = $invoice->invoice_number;
                $invoice->pelanggan_id = $invoice->customer_id;
                $invoice->harga_total = $invoice->total_amount;
                $invoice->status_bayar = $invoice->payment_status;
                return $invoice;
            })
            ->toArray();
            
        if (!count($accountingInvoices) ) {
            $notas = Nota::where('pelanggan_id', $this->pelanggan_id)->where('status_bayar', 'belum_lunas')
                ->orWhere('status_bayar', 'sebagian')
                ->get()->toArray();
            if (count($notas) > 0) {
                return $notas;
            } else {
                return [];
            }
        } else {
            return $accountingInvoices;
        }
    }
}
