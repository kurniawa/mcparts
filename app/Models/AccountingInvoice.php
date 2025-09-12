<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingInvoice extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function accounting()
    {
        return $this->belongsTo(Accounting::class);
    }

    public function userInstance()
    {
        return $this->belongsTo(UserInstance::class);
    }

    public function isExistAccountingInvoiceAfter() {
        $accounting_invoice_after = AccountingInvoice::where('invoice_table', 'notas')
            ->where('invoice_id', $this->invoice_id)
            ->where('created_at', '>', $this->created_at)
            ->orderBy('created_at')->get();
        
        // dump($this);
        // dump($this->invoice_id);
        // dump($this->created_at);
        // dd($accounting_invoice_after);
        if ($accounting_invoice_after->isEmpty()) {
            return false;
        }
        
        $amountDueBuffer = 0;
        foreach ($accounting_invoice_after as $index => $accounting_invoice) {
            // Yang mempengaruhi amount_due adalah total_discount, amount_paid, balance_used
            dump("index = $index");
            if ($index === 0) {
                $amount_due_before = $accounting_invoice->amount_due + $accounting_invoice->amount_paid + $accounting_invoice->total_discount + $accounting_invoice->balance_used;
                dump("amount_due_before = $accounting_invoice->amount_due + $accounting_invoice->amount_paid + $accounting_invoice->total_discount + $accounting_invoice->balance_used = $amount_due_before");
                $amount_due_after = $amount_due_before - $this->amount_paid - $this->total_discount - $this->balance_used;
                dump("amount_due_after = $amount_due_before - $this->amount_paid - $this->total_discount - $this->balance_used = $amount_due_after");
                $this->update([
                    'amount_due' => $amount_due_after,
                ]);
                $amountDueBuffer = $amount_due_after - $accounting_invoice->amount_paid - $accounting_invoice->total_discount - $accounting_invoice->balance_used;
                dump("amountDueBuffer = $amount_due_after - $accounting_invoice->amount_paid - $accounting_invoice->total_discount - $accounting_invoice->balance_used = $amountDueBuffer");
                $accounting_invoice->update([
                    'amount_due' => $amountDueBuffer,
                ]);
            } else {
                $amount_due_after = $amountDueBuffer - $accounting_invoice->amount_paid - $accounting_invoice->total_discount - $accounting_invoice->balance_used;
                dump("amount_due_after = $amountDueBuffer - $this->amount_paid - $this->total_discount - $this->balance_used = $amount_due_after");
                $accounting_invoice->update([
                    'amount_due' => $amount_due_after,
                ]);
                $amountDueBuffer = $amount_due_after;
                dump("amount_due_after = amount_due_after = $amount_due_after");
            }
        }
        dd('stop');
        return true;
    }
}
