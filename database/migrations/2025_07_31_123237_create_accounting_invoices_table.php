<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel accounting_invoices ini dibutuhkan, karena terkadang satu transaksi yang tercatat pada accountings,
     * terkait dengan beberapa nota/invoices. Misal Jhon Motor membayar 3 nota sekaligus.
     * Demikian juga sebaliknya, satu nota/invoice bisa terkait dengan beberapa transaksi.
     * Misal Jhon Motor membayar 3 kali untuk satu nota/invoice.
     * Maka kita perlu tabel accounting_invoices untuk menyimpan informasi ini.
     * 
     * Kolom accounting_id boleh null. Artinya invoice terkait baru saja dibuat,
     * belum ada transaksi untuk pembayaran invoice tersebut.
     * Apabila sudah ada transaksi/accounting yang terkait dengan invoice ini,
     * maka kolom accounting_id akan diupdate, yakni diisi dengan id dari transaksi/accounting tersebut.
     * 
     * Lalu apabila ada transaksi/accounting tambahan yang terkait dengan invoice yang sama,
     * maka perlu untuk membuat record baru di tabel accounting_invoices.
     * Dengan demikian, satu invoice bisa terkait dengan beberapa transaksi/accounting, begitu pula sebaliknya.
     */

    public function up(): void
    {
        Schema::create('accounting_invoices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('accounting_time_key')->nullable(); // untuk koneksi ke accounting
            $table->bigInteger('time_key')->nullable()->unique(); // untuk sorting
            $table->foreignId('accounting_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('invoice_id')->nullable();
            $table->string('invoice_table', 50)->nullable();
            $table->string('invoice_number', 50)->nullable();
            $table->foreignId('transaction_name_id')->nullable()->constrained('transaction_names')->onDelete('set null');
            $table->string('transaction_name_desc')->nullable(); // e.g., 'Pembayaran Nota', 'Pembayaran Hutang'
            $table->foreignId('customer_id')->nullable()->constrained('pelanggans')->onDelete('set null');
            $table->string('customer_name', 100)->nullable(); // e.g., 'Jhon Motor'
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->string('supplier_name', 100)->nullable(); //
            $table->string('payment_status', 50)->default('belum_lunas'); // e.g., paid, unpaid, partial
            $table->string('payment_method', 50)->nullable(); // e.g., cash, bank transfer, credit card
            $table->decimal('total_amount', 15, 2)->default(0.00); // Total amount of the invoice

            // Data Pembayaran
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('total_discount', 15, 2)->default(0.00);
            $table->decimal('discount_description', 15, 2)->default(0.00);
            $table->decimal('amount_due', 15, 2)->default(0.00); // Amount still due for payment
            $table->decimal('amount_paid', 15, 2)->default(0.00); // Amount already paid
            $table->decimal('balance_used', 15, 2)->default(0.00);
            $table->decimal('remaining_funds', 15, 2)->default(0.00);
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->decimal('overpayment', 15, 2)->default(0.00); // Overpayment
            $table->enum('status', ['active', 'inactive'])->default('active');

            // $table->decimal('discount_percentage_old', 5, 2)->default(0.00);
            // $table->decimal('total_discount_old', 15, 2)->default(0.00);
            // $table->decimal('discount_description_old', 15, 2)->default(0.00);
            // $table->decimal('amount_due_old', 15, 2)->default(0.00);
            // $table->decimal('amount_paid_old', 15, 2)->default(0.00);
            // $table->decimal('overpayment_old', 15, 2)->default(0.00);
            // $table->decimal('balance_old', 15, 2)->default(0.00);

            $table->date('due_date')->nullable(); // Due date for payment
            $table->string('currency', 10)->default('IDR'); // Currency of the invoice, defaulting to IDR
            $table->string('notes', 255)->nullable();
            $table->string('created_by', 50)->nullable(); // username of the user who created the record
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable(); // Soft delete
            $table->string('deleted_by', 50)->nullable(); // User who deleted the record, if applicable
            $table->string('deleted_reason', 255)->nullable(); //
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_invoices');
    }
};
