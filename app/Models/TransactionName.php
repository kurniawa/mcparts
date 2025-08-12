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
        $customerBalance = Overpayment::where('customer_id', $this->pelanggan_id)->latest()->first();

        if ($customerBalance) {
            $customerBalance = $customerBalance->toArray();
        }

        $accountingInvoices = AccountingInvoice::where('invoice_table', 'notas')
            ->where('customer_id', $this->pelanggan_id)
            ->whereIn('payment_status', ['belum_lunas', 'sebagian'])
            ->where('status', 'active')
            ->get()
            ->map(function ($invoice) {
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
                ->get()->map(function ($nota) {
                    $nota->invoice_id = $nota->id;
                    return $nota;
                })
                ->toArray();
            $accountingInvoices = $notas;
        }
        
        return [$accountingInvoices, $customerBalance];
    }
}
