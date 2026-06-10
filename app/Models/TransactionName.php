<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TransactionName extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * Get notas, where status_bayar is 'BELUM_LUNAS' or 'SEBAGIAN'
     */
    public function getRelatedNotYetPaidOffInvoices()
    {
        $balance = null;
        $notas = [];
        $invoice_table = 'notas';
        if ($this->kategori_type === 'UANG MASUK') {
            $balance = Overpayment::where('customer_id', $this->pelanggan_id)->latest()->first();
            
            $notas = Nota::where('pelanggan_id', $this->pelanggan_id)->where(function ($query) {
            $query->where('status_bayar', 'BELUM_LUNAS')
                  ->orWhere('status_bayar', 'SEBAGIAN');
            })
            ->limit(10)->get()->map(function ($nota) {
                $nota->invoice_table = 'notas';
                $nota->invoice_id = $nota->id;
                return $nota;
            })
            ->toArray();
        } elseif ($this->kategori_type === 'UANG KELUAR') {
            $invoice_table = 'pembelians';
            $balance = Overpayment::where('supplier_id', $this->supplier_id)->latest()->first();
            
            $notas = Pembelian::where('supplier_id', $this->supplier_id)->where(function ($query) {
            $query->where('status_bayar', 'BELUM_LUNAS')
                  ->orWhere('status_bayar', 'SEBAGIAN');
            })
            ->limit(10)->get()->map(function ($nota) {
                $nota->invoice_table = 'pembelians';
                $nota->invoice_id = $nota->id;
                return $nota;
            })
            ->toArray();
            // dump('supplier_id: ' . $this->supplier_id);
            // dd($notas);
        }

        if ($balance) {
            $balance = $balance->toArray();
        }

        
        $accountingInvoices = $notas;
        // Log::info($notas);
        
        if (!count($notas) ) {
            $accountingInvoices = AccountingInvoice::where('invoice_table', $invoice_table)
                ->where($invoice_table === 'notas' ? 'customer_id' : 'supplier_id', $this->pelanggan_id ?? $this->supplier_id)
                ->whereIn('payment_status', ['BELUM_LUNAS', 'SEBAGIAN'])
                ->where('status', 'active')
                ->get()
                ->map(function ($invoice) use ($invoice_table) {
                    $invoice->nomor_nota = $invoice->invoice_number;
                    $invoice->pelanggan_id = $invoice->customer_id;
                    $invoice->supplier_id = $invoice->supplier_id;
                    $invoice->harga_total = $invoice->total_amount;
                    $invoice->status_bayar = $invoice->payment_status;
                    $invoice->invoice_table = $invoice_table;
                    return $invoice;
                })
                ->toArray();
            // Log::info($accountingInvoices);
        }
        
        return [$accountingInvoices, $balance];
    }

    // ambil data relasi dengan pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'id');
    }

    // ambil data relasi dengan supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

}
