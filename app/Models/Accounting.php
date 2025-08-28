<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accounting extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    static function get_instance_types() {
        return [
            'safe',
            'bank',
            'e-wallet',
        ];
    }

    static function get_instance_names() {
        return [
            'BCA',
            'BRI',
            'BNI',
            'Mandiri',
            'Danamon',
            'Maybank',
            'GoPay',
            'OVO',
            'ShopeePay',
            'storage',
        ];
    }

    static function validasi_data_untuk_penerimaan_piutang($request, $i) {
        $request->validate([
            // "remaining_balance_masuk.$i" => "required|numeric",
            "related_not_yet_paid_off_invoices.nota_id.$i" => "required|array",
            "related_not_yet_paid_off_invoices.nota_id.$i.*" => "numeric",
            "related_not_yet_paid_off_invoices.harga_total.$i" => "required|array",
            "related_not_yet_paid_off_invoices.harga_total.$i.*" => "numeric",
            "related_not_yet_paid_off_invoices.amount_due.$i" => "required|array",
            "related_not_yet_paid_off_invoices.amount_due.$i.*" => "numeric",
            "related_not_yet_paid_off_invoices.amount_paid.$i" => "required|array",
            "related_not_yet_paid_off_invoices.amount_paid.$i.*" => "numeric",
        ]);

        // Validasi perbandingan nilai-nilai yang di post dengan yang ada di database, apakah sudah sesuai?
        $post = $request->post();
        $total_amount_paid_posted = 0;
        $total_saldo_used = 0;
        $customer_id = null;
        for ($j=0; $j < count($post['related_not_yet_paid_off_invoices']['nota_id'][$i]); $j++) { 
            $related_nota = Nota::find($post['related_not_yet_paid_off_invoices']['nota_id'][$i][$j]);

            if (!$related_nota) {
                $request->validate(['error' => 'required'], [
                    'error.required' => "Nota $post[related_not_yet_paid_off_invoices][nota_id][$i][$j] tidak ditemukan"
                ]);
            }

            $related_transaction_name = TransactionName::where('kategori_level_one', 'PENERIMAAN PIUTANG')->where('desc', $post['transaction_desc'][$i])->first();

            if (!$related_transaction_name) {
                $request->validate(['error' => 'required'], [
                    'error.required' => "TransactionName $post[related_not_yet_paid_off_invoices][nota_id][$i][$j] tidak ditemukan"
                ]);
            }
            
            $amount_due_old = $related_nota->amount_due;

            $amount_paid = (float)$post['related_not_yet_paid_off_invoices']['amount_paid'][$i][$j];
            $amount_due = (float)$post['related_not_yet_paid_off_invoices']['amount_due'][$i][$j];
            $discount_percentage = (float)$post['related_not_yet_paid_off_invoices']['discount_percentage'][$i][$j];
            $total_discount = (float)$post['related_not_yet_paid_off_invoices']['total_discount'][$i][$j];
            $payment_status = $post['related_not_yet_paid_off_invoices']['payment_status'][$i][$j];
            $balance_used = (float)$post['related_not_yet_paid_off_invoices']['balance_used'][$i][$j];

            // Validasi Potongan Harga
            $total_discount_new = $total_discount;
            if ($discount_percentage > 0) {
                $total_discount_new = ($discount_percentage / 100) * $amount_due_old;
            }

            if ($total_discount_new != $total_discount) {
                $request->validate(['error' => 'required'], [
                    'error.required' => "total_discount_new != total_discount --> $total_discount_new != $total_discount"
                ]);
            }

            // Validasi Amount Due / Sisa Bayar
            // dump($related_nota);
            // dump($amount_due_old, $total_discount_new, $amount_paid, $balance_used);
            $amount_due_new = $amount_due_old - $total_discount_new - $amount_paid - $balance_used;
            // dd($amount_due_new, $amount_due);
            if ($amount_due_new != $amount_due) {
                $request->validate(['error' => 'required'], [
                    'error.required' => "amount_due_new != amount_due --> $amount_due_new != $amount_due"
                ]);
            }

            // Validasi nilai negatif pada amount_paid dan nilai negatif pada saldo dan sisa bayar
            if ($amount_paid < 0) {
                $request->validate(['error' => 'required'], [
                    'error.required' => "[Nilai tidak sesuai pada balance masuk yang digunakan]"
                ]);
            }
            if ($balance_used < 0) {
                $request->validate(['error' => 'required'], [
                    'error.required' => "[Nilai tidak sesuai pada saldo yang digunakan.]"
                ]);
            }
            if ($amount_due_new < 0) {
                $request->validate(['error' => 'required'], [
                    'error.required' => "[Nilai tidak sesuai pada sisa bayar.]"
                ]);
            }

            // Validasi Payment Status
            $payment_status_new = 'error';
            if ($amount_due_new <= 0) {
                $payment_status_new = 'lunas';
            } else if ($amount_due_new == ($amount_due_old - $total_discount_new)) {
                $payment_status_new = 'belum_lunas'; 
            } else if ($amount_due_new > 0 && $amount_due_new < ($amount_due_old - $total_discount_new)) {
                $payment_status_new = 'sebagian';
            }
            if ($payment_status_new == 'error') {
                $request->validate(['error' => 'required'], [
                    'error.required' => "payment_status == error --> $payment_status == error"
                ]);
            } elseif ($payment_status_new != $payment_status) {
                $request->validate(['error' => 'required'], [
                    'error.required' => "payment_status == post[payment_status][$i][$j] --> $payment_status == $post[payment_status][$i][$j]"
                ]);
            }

            $total_amount_paid_posted += $amount_paid; // Akumulasi total_amount_paid_posted
            $total_saldo_used += $balance_used; // Akumulasi total_saldo_used/total_balance_used

            if ($customer_id === null) {
                $customer_id = $related_nota->pelanggan_id;
            }
        }
        // Validasi Saldo dan Sisa Saldo
        $overpayment = Overpayment::where('customer_id', $customer_id)->latest()->first();
        $saldo_awal_old = 0;
        if ($overpayment) {
            $saldo_awal_old = $overpayment->amount;
        }
        // dump($post);
        // dd($post['saldo_awal'][$i], $saldo_awal_old);
        if ($saldo_awal_old != $post['saldo_awal'][$i]) {
            $request->validate(['error' => 'required'], [
                'error.required' => "saldo_awal_old != post[saldo_awal][$i] --> $saldo_awal_old != " . $post['saldo_awal'][$i]
            ]);
        }

        $sisa_saldo_new = $saldo_awal_old - $total_saldo_used;
        if ($sisa_saldo_new != $post['sisa_saldo'][$i]) {
            $request->validate(['error' => 'required'], [
                'error.required' => "sisa_saldo_new != post[sisa_saldo][$i] --> $sisa_saldo_new != " . $post['sisa_saldo'][$i]
            ]);
        }

        // Validasi Remaining Balance Masuk - Uang Masuk tidak boleh kosong atau kurang dari 0
        $masuk = $post['masuk'][$i] ?? null;
        $balance_used = null;
        if (isset($post['related_not_yet_paid_off_invoices']['balance_used'][$i])) {
            $balance_used = 0;
            foreach ($post['related_not_yet_paid_off_invoices']['balance_used'][$i] as $key => $value) {
                if (!is_numeric(trim($value)) || (float)trim($value) < 0) {
                    $request->validate(['error' => 'required'], [
                        'error.required' => "Nilai saldo yang digunakan tidak sesuai pada baris ke-$i"
                    ]);
                }
                $balance_used += (float)$value;
            }
        }
        if ($masuk && !$balance_used) {
            if ((float)$masuk > 0) {
                $remaining_balance_new = (float)$masuk - $total_amount_paid_posted;
                if ($remaining_balance_new != $post['remaining_balance_masuk'][$i]) {
                    $request->validate(['error' => 'required'], [
                        'error.required' => "remaining_balance_new != post[related_not_yet_paid_off_invoices][payment_status][$i][$j] --> $remaining_balance_new != " . $post['related_not_yet_paid_off_invoices']['payment_status'][$i][$j]
                    ]);
                }
            } elseif ((float)$masuk <= 0) {
                $request->validate(['error' => 'required'], [
                    'error.required' => "input uang masuk[$i][$j] --> " . $post['masuk'][$i]
                ]);
            }
        } elseif (!$masuk && $balance_used) {
            $amount_due_to_validate = $amount_due_old - (float)$balance_used;
            if ($amount_due_new != $amount_due_to_validate) {
                $request->validate(['error' => 'required'], [
                    'error.required' => "amount_due_new != amount_due_to_validate --> $amount_due_new != $amount_due_to_validate"
                ]);
            }
        } elseif ($masuk && $balance_used) {
            $amount_due_to_validate = $amount_due_old ($amount_paid + (float)$balance_used);
            if ($amount_due_new != $amount_due_to_validate) {
                $request->validate(['error' => 'required'], [
                    'error.required' => "amount_due_new != amount_due_to_validate --> $amount_due_new != $amount_due_to_validate"
                ]);
            }
        } elseif (!$masuk && !$balance_used) {
            $request->validate(['error' => 'required'], [
                'error.required' => "input uang masuk dan saldo yang digunakan tidak sesuai"
            ]);
        }

        // Validasi total saldo yang digunakan tidak melebih saldo awal, karena tidak make sense.
        // if ($total_saldo_used > saldoAwalRealValue) {
        //     $request->validate(['error' => 'required'], [
        //         'error.required' => "input uang masuk[$i][$j] --> $post[masuk][$i]"
        //     ]);
        // }
    }

    public function updateAccountingAfter() {
        // Hitung saldo
        $saldo = 0;
        $after_trans = Accounting::where('user_instance_id', $this->user_instance_id)
            ->where('created_at', '>', $this->created_at)
            ->orderBy('created_at')
            ->get();

        $before_trans = Accounting::where('user_instance_id', $this->user_instance_id)
            ->where('created_at', '<', $this->created_at)
            ->latest()
            ->first();

        if ($before_trans) {
            $saldo = $before_trans->saldo;
        } else {
            $last_existing = Accounting::where('user_instance_id', $this->user_instance_id)->latest()->first();
            if ($last_existing) {
                $saldo = $last_existing->saldo;
            }
        }

        // Perbarui saldo
        $saldo += ($this->transaction_type === 'pemasukan') ? $this->jumlah : -$this->jumlah;

        // Update saldo untuk semua transaksi setelahnya
        if ($after_trans->isNotEmpty()) {
            $saldo_next = $saldo;
            foreach ($after_trans as $aft) {
                $saldo_next += ($aft->transaction_type === 'pemasukan') ? $aft->jumlah : -$aft->jumlah;
                $aft->saldo = $saldo_next;
                $aft->save();
            }
            // $success_ .= "-saldo setelahnya diperbarui-";
        }
    }
}
