<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Nota extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public static function create_from_spk_produk($spk, $spk_produk, $jumlah_total) {
        $alamat_id = null;
        $kontak_id = null;
        $cust_long = null;
        $cust_short = null;
        $cust_kontak = null;
        $reseller_alamat_id = null;
        $reseller_kontak_id = null;
        $reseller_long = null;
        $reseller_short = null;
        $reseller_kontak = null;
        $spk_notas = SpkNota::where('spk_id',$spk->id)->get();
        $nota_acuan = null;
        if (count($spk_notas) !== 0) {
            $nota_acuan = Nota::find($spk_notas[0]->nota_id);
            $alamat_id = $nota_acuan->alamat_id;
            $kontak_id = $nota_acuan->kontak_id;
            $cust_long = $nota_acuan->cust_long;
            $cust_short = $nota_acuan->cust_short;
            $cust_kontak = $nota_acuan->cust_kontak;
            $reseller_alamat_id = $nota_acuan->reseller_alamat_id;
            $reseller_kontak_id = $nota_acuan->reseller_kontak_id;
            $reseller_long = $nota_acuan->reseller_long;
            $reseller_short = $nota_acuan->reseller_short;
            $reseller_kontak = $nota_acuan->reseller_kontak;
        } else {
            $pelanggan_data = Pelanggan::data($spk->pelanggan_id);
            $alamat_id = $pelanggan_data['alamat_id'];
            $kontak_id = $pelanggan_data['kontak_id'];
            $cust_long = $pelanggan_data['long'];
            $cust_short = $pelanggan_data['short'];
            $cust_kontak = $pelanggan_data['kontak'];
            if ($spk->reseller_id !== null) {
                $reseller_data = Pelanggan::data($spk->reseller_id);
                $reseller_alamat_id = $reseller_data['alamat_id'];
                $reseller_kontak_id = $reseller_data['kontak_id'];
                $reseller_long = $reseller_data['long'];
                $reseller_short = $reseller_data['short'];
                $reseller_kontak = $reseller_data['kontak'];
            }
        }
        $produk = Produk::find($spk_produk->produk_id);
        $harga_produk = Produk::get_harga_pelanggan($produk->id, $spk->pelanggan_id);
        // dump($harga_produk);
        // $harga_total =
        $user = Auth::user();
        $nota = Nota::create([
            'pelanggan_id'=>$spk->pelanggan_id,
            'reseller_id'=>$spk->reseller_id,
            'pelanggan_nama'=>$spk->pelanggan_nama,
            'reseller_nama'=>$spk->reseller_nama,
            'jumlah_total'=>$jumlah_total,
            'harga_total'=>$harga_produk * $jumlah_total,
            //
            'alamat_id'=>$alamat_id,
            'reseller_alamat_id'=>$reseller_alamat_id,
            'kontak_id'=>$kontak_id,
            'reseller_kontak_id'=>$reseller_kontak_id,
            'cust_long'=>$cust_long,
            'cust_short'=>$cust_short,
            'cust_kontak'=>$cust_kontak,
            'reseller_long'=>$reseller_long,
            'reseller_short'=>$reseller_short,
            'reseller_kontak'=>$reseller_kontak,
            'created_by'=>$user->username,
            'updated_by'=>$user->username,
            'copy'=>$spk->copy,
        ]);
        // UPDATE NO_NOTA
        $nota->no_nota = "N-$nota->id";
        $nota->save();
        // CREATE SPK_NOTA
        $spk_nota = SpkNota::create([
            'spk_id' => $spk->id,
            'nota_id' => $nota->id,
        ]);
        // CREATE SPK_PRODUK_NOTA

        $spk_produk_nota = SpkProdukNota::create([
            'spk_id'=>$spk->id,
            'produk_id'=>$spk_produk->produk_id,
            'spk_produk_id'=>$spk_produk->id,
            'nota_id'=>$nota->id,
            'jumlah'=>$jumlah_total,
            'nama_nota'=>$produk->nama_nota,
            'harga'=>$harga_produk,
            'harga_t'=>$harga_produk * $jumlah_total,
        ]);
    }

    public static function kaji_ulang_spk_dan_spk_produk($spk) {
        $spk_produks = SpkProduk::where('spk_id', $spk->id)->get();
        $jumlah_sudah_nota_gabungan = 0;
        foreach ($spk_produks as $spk_produk) {
            $spk_produk_notas = SpkProdukNota::where('spk_produk_id', $spk_produk->id)->get();
            $jumlah_sudah_nota = 0;
            foreach ($spk_produk_notas as $spk_produk_nota) {
                $jumlah_sudah_nota += $spk_produk_nota->jumlah;
            }
            $spk_produk->jumlah_sudah_nota = $jumlah_sudah_nota;
            $spk_produk->save();

            $jumlah_sudah_nota_gabungan += $jumlah_sudah_nota;
        }

        $status_nota = 'BELUM';
        if ($spk->jumlah_total === $jumlah_sudah_nota_gabungan) {
            $status_nota = 'SEMUA';
        } elseif ($jumlah_sudah_nota_gabungan > 0) {
            $status_nota = 'SEBAGIAN';
        } elseif ($jumlah_sudah_nota_gabungan <= 0) {
            $status_nota = 'BELUM';
        }

        $spk->status_nota = $status_nota;
        $spk->jumlah_sudah_nota = $jumlah_sudah_nota_gabungan;
        $spk->save();
    }

    public static function update_data_nota_srjalan($spk) {
        $spk_notas = SpkNota::where('spk_id', $spk->id)->get();
        foreach ($spk_notas as $spk_nota) {
            $nota = Nota::find($spk_nota->nota_id);
            $spk_produk_notas = SpkProdukNota::where('nota_id', $nota->id)->get();
            $harga_total = 0;
            $jumlah_total = 0;
            foreach ($spk_produk_notas as $spk_produk_nota) {
                $harga_total += $spk_produk_nota->harga_t;
                $jumlah_total += $spk_produk_nota->jumlah;
            }
            $nota->jumlah_total = $jumlah_total;
            $nota->harga_total = $harga_total;
            $nota->save();

            $nota_srjalans = NotaSrjalan::where('nota_id', $nota->id)->get();
            foreach ($nota_srjalans as $nota_srjalan) {
                $srjalan = Srjalan::find($nota_srjalan->srjalan_id);
                Srjalan::update_jumlah_packing_srjalan($srjalan);
            }
        }
    }

    public function updatePaymentAndAccountingInvoice_NewInvoice() : void {
        /**
         * Ketika nota pertama kali dibuat, maka belum ada transaksi pembayaran nota,
         * maka tidak ada AccountingInvoice terkait nota baru tersebut.
         * 
         * Perhatikan pada kasus ini, accounting_id => null,
         * karena belum ada transaksi pembayaran terkait dengan invoice ini,
         * karena invoice baru saja dibuat.
         */

        // Update the status_bayar pada nota
        if ($this->amount_paid == 0) {
            $this->status_bayar = 'belum_lunas';
        } elseif ($this->amount_paid < $this->harga_total) {
            $this->status_bayar = 'sebagian';
        } elseif ($this->amount_paid >= $this->harga_total) {
            $this->status_bayar = 'lunas';
            $this->finished_at = now();
        }

        $this->amount_due = $this->harga_total - $this->amount_paid;

        // Save the changes to the nota
        $this->updated_by = Auth::user()->username;
        $this->save();

        
        AccountingInvoice::create([
            'time_key' => strtotime($this->created_at),
            'invoice_id' => $this->id,
            'invoice_table' => 'notas',
            'invoice_number' => $this->no_nota,
            // 'transaction_name_id' => $related_transaction_name->id,
            // 'transaction_name_desc' => $related_transaction_name->desc,
            'customer_id' => $this->pelanggan_id,
            'customer_name' => $this->pelanggan_nama,
            'payment_status' => $this->status_bayar,
            'amount_due' => $this->harga_total,
            'amount_paid' => 0,
            'total_amount' => $this->harga_total,
        ]);
    }
    public function updatePaymentAndAccountingInvoice_AccountingInvoiceIsExist($accounting, $accounting_invoice, $transaction_name, $amount_due, $amount_paid, $payment_status) {
        /**
         * Validasi $amount_due, $amount_paid, $payment_status
         */
        $amount_due_to_compare = $accounting_invoice->amount_due - $amount_paid;
        if ($amount_due_to_compare < 0) {
            $amount_due_to_compare = 0;
        }
        $payment_status_to_compare = "belum_lunas";
        if ($amount_due_to_compare == 0) {
            $payment_status_to_compare = 'lunas';
        } elseif ($amount_due_to_compare > 0 && $amount_due_to_compare < $this->harga_total) {
            $payment_status_to_compare = 'sebagian';
        }

        if ($amount_due_to_compare != $amount_due || $payment_status_to_compare !== $payment_status) {
            throw new Exception("ERR - updatePaymentAndAccountingInvoice_AccountingInvoiceIsExist:\n$transaction_name->desc\n$amount_due_to_compare != $amount_due || $payment_status_to_compare !== $payment_status");
        }
        /**
         * Apabila ditemukan adanya tranksaksi/accounting yang terkait dengan invoice ini,
         * yakni ketika accounting_id !== null,
         * tidak boleh melakukan update data record tersebut.
         * Maka perlu untuk membuat record baru di tabel accounting_invoices.
         */
        if ($accounting_invoice->accounting_id == null) {
            $accounting_invoice->accounting_id = $accounting->id;
            $accounting_invoice->transaction_id = $transaction_name->id;
            $accounting_invoice->transaction_desc = $transaction_name->desc;
            $accounting_invoice->payment_status = $payment_status;
            $accounting_invoice->amount_paid = $amount_paid;
            $accounting_invoice->amount_due = $amount_due;
            $accounting_invoice->save();
        } elseif ($accounting_invoice->accounting_id != null) {
            // dd('Telah terjadi transaksi pembayaran pada nota terkait');
            /**
             * Telah terjadi transaksi pembayaran sebelumnya pada nota ini.
             * maka perlu dihitung berapa pembayaran yang telah dilakukan sebelumnya dan
             * berapa sisa pembayaran yang perlu dilunasi.
             */
            AccountingInvoice::create([
                'time_key' => strtotime($this->created_at),
                'invoice_id' => $this->id,
                'invoice_table' => 'notas',
                'invoice_number' => $this->no_nota,
                'transaction_name_id' => $transaction_name->id,
                'transaction_name_desc' => $transaction_name->desc,
                'customer_id' => $this->pelanggan_id,
                'customer_name' => $this->pelanggan_nama,
                'payment_status' => $payment_status,
                'amount_due' => $amount_due,
                'amount_paid' => $amount_paid,
                'total_amount' => $this->harga_total,
            ]);
        } else {
        }
        // Update the status_bayar pada nota
        $this->amount_paid = $amount_paid;
        $this->amount_due = $amount_due;
        $this->status_bayar = $payment_status;
        $this->updated_by = Auth::user()->username;
        $this->save();
    }

    public function UpdatePaymentStatus() {
        // Validasi Payment Status
        $payment_status = 'error';
        if ($this->amount_due == 0) {
            $payment_status = 'lunas';
        } else if (($this->amount_paid + $this->balance_used) == 0 && ($this->amount_due == ($this->harga_total - $this->total_discount) || $this->amount_due == $this->harga_total)) {
            $payment_status = 'belum_lunas'; 
        } else if (($this->amount_paid + $this->balance_used) > 0 && ($this->amount_due < ($this->harga_total - $this->total_discount) && $this->amount_due < $this->harga_total)) {
            $payment_status = 'sebagian';
        }
        return $payment_status;
    }
}
