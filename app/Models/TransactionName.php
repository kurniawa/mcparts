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
        $balance = null;
        $notas = null;
        if ($this->kategori_level_one === 'PENERIMAAN PIUTANG') {
            $balance = Overpayment::where('customer_id', $this->pelanggan_id)->latest()->first();
            
            $notas = Nota::where('pelanggan_id', $this->pelanggan_id)->where(function ($query) {
            $query->where('status_bayar', 'belum_lunas')
                  ->orWhere('status_bayar', 'sebagian');
            })
            ->get()->map(function ($nota) {
                $nota->invoice_id = $nota->id;
                return $nota;
            })
            ->toArray();
        } elseif ($this->kategori_level_one === 'BAYAR HUTANG BAHAN BAKU') {
            $balance = Overpayment::where('supplier_id', $this->supplier_id)->latest()->first();
            
            $notas = Pembelian::where('supplier_id', $this->supplier_id)->where(function ($query) {
            $query->where('status_bayar', 'BELUM')
                  ->orWhere('status_bayar', 'SEBAGIAN');
            })
            ->get()->map(function ($nota) {
                $nota->invoice_id = $nota->id;
                return $nota;
            })
            ->toArray();
        }

        if ($balance) {
            $balance = $balance->toArray();
        }

        
        $accountingInvoices = $notas;
        // Log::info($notas);
        
        if (!count($notas) ) {
            
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
        }
        
        return [$accountingInvoices, $balance];
    }
}
