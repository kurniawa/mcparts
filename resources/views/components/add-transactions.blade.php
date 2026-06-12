<div>
    @if ($errors->any())
        <div class="alert alert-danger text-xs">
            @foreach ($errors->all() as $message)
                <div>{{ $message }}</div>
            @endforeach
        </div>
    @endif
    <div class="flex flex-col lg:flex-row lg:gap-2 mt-3">
        <div class="border rounded p-1">
            {{-- LOADING ANIMATION --}}
            <div id="spinner" class="flex justify-center items-center">
                <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
            </div>
            {{-- END - LOADING ANIMATION --}}
            <div id="loading_to_hide">
                <h2 class="font-bold text-slate-500">Tambah Transaksi :</h2>
                <form id="form-add-transactions" action="{{ route('accounting.store_transactions', $userInstance->id) }}" method="POST" class="mt-1 inline-block min-w-max">
                    @csrf
                    <table class="text-xs min-w-max" id="table_add_transactions">
                        <tr class="text-slate-600">
                            <th>tanggal</th><th>kode</th><th>deskripsi/keterangan</th><th>keterangan tambahan</th><th>keluar</th><th>masuk</th>
                            {{-- <th>saldo</th> --}}
                        </tr>
                        @for ($i = 0; $i < 15; $i++)
                        <tr id="tr_add_transaction-{{ $i }}">
                            <td>
                                {{-- <input type="text" name="created_at[]" id="created_at-{{ $i }}" class="border p-1 text-xs w-28" placeholder="dd-mm-yyyy" value="{{ old('created_at.' . $i) }}"> --}}
                                <div class="flex items-center">
                                    <input type="text" name="day[]" id="day-{{ $i }}" class="border p-1 text-xs w-8" placeholder="dd" value="{{ old('day.' . $i) ? old('day.' . $i) : date('d') }}">
                                    <span>-</span>
                                    <input type="text" name="month[]" id="month-{{ $i }}" class="border p-1 text-xs w-8" placeholder="mm" value="{{ old('month.' . $i) ? old('month.' . $i) : date('m') }}">
                                    <span>-</span>
                                    <input type="text" name="year[]" id="year-{{ $i }}" class="border p-1 text-xs w-10" placeholder="yyyy" value="{{ old('year.' . $i) ? old('year.' . $i) : date('Y') }}">
                                </div>
                            </td>
                            <td><input type="text" name="kode[]" id="kode-{{ $i }}" class="border p-1 text-xs w-20" value="{{ old('kode.' . $i) ? old('kode.' . $i) : $userInstance->kode }}"></td>
                            <td><input type="text" name="transaction_desc[]" id="transaction_desc-{{ $i }}" class="border p-1 text-xs w-60" value="{{ old('transaction_desc.' . $i) }}"></td>
                            <td><input type="text" name="keterangan[]" id="keterangan-{{ $i }}" class="border p-1 text-xs w-full" value="{{ old('keterangan.' . $i) }}"></td>
                            <td>
                                <input type="text" id="keluar-{{ $i }}" class="border p-1 text-xs w-36" onchange="formatNumber(this, 'keluar-{{ $i }}-real')" value="{{ old('keluar.' . $i) ? number_format((int)old('keluar.' . $i),0,',','.') : "" }}">
                                <input type="hidden" name="keluar[]" id="keluar-{{ $i }}-real" value="{{ old('keluar.' . $i) }}">
                            </td>
                            <td>
                                <input type="text" id="masuk-{{ $i }}" class="border p-1 text-xs w-36" value="{{ old('masuk.' . $i) ? number_format((int)old('masuk.' . $i),0,',','.') : "" }}">
                                <input type="hidden" name="masuk[]" id="masuk-{{ $i }}-real" value="{{ old('masuk.' . $i) }}">
                                <input type="hidden" name="transaction_id[]" id="transaction_id-{{ $i }}" value="{{ old('transaction_id.' . $i) }}">
                            </td>
                            <td>
                                <input type="hidden" name="trId[]" value="{{ $i }}">
                                <input type="hidden" name="kategori_level_one[]" id="kategori_level_one-{{ $i }}" value="">
                            </td>
                        </tr>

                        
                        @endfor
                    </table>
                    <div class="mt-3 text-center text-xs">
                        <input id="loading_to_disable" type="submit" class="border-2 font-semibold rounded text-emerald-500 border-emerald-300 bg-emerald-200 px-2 hover:cursor-pointer" value="confirm" />
                    </div>
                    <div class="max-w-4xl"></div>
                </form>
            </div>
        </div>
        
    </div>

    <script>
        const label_deskripsi = {!! json_encode($labelDeskripsi, JSON_HEX_TAG) !!};
        
        // run function after page loaded
        for (let i = 0; i < 15; i++) {
            autocomplete_deskripsi(i);
        }

        function autocomplete_deskripsi(index) {
            $(`#transaction_desc-${index}`).autocomplete({
                source: label_deskripsi,
                select: function (event, ui) {
                    document.getElementById(`transaction_desc-${index}`).value = ui.item.value;
                    document.getElementById(`transaction_id-${index}`).value = ui.item.id;
                    accountingGetRelatedInvoiceReceivables(ui.item.id, index, ui.item.kategori_type, ui.item.kategori_level_one, ui.item.pelanggan_id, ui.item.supplier_id);
                    // console.log(ui.item.id);
                }
            });

            /*
            Add event listener to transaction_desc input
            Kalau value transaction_desc == "", maka remove tr penerimaan piutang
            */
            document.getElementById(`transaction_desc-${index}`).addEventListener('change', function() {
                let transactionDescValue = this.value.trim();
                let trPenerimaanPiutang = document.getElementById(`tr-pemasukan-pengeluaran-dengan-nota-${index}`);
                let trErrorFeedback = document.getElementById(`tr-error-feedback-${index}`);
                if (transactionDescValue === "" && trPenerimaanPiutang) {
                    trPenerimaanPiutang.remove();
                }
                if (transactionDescValue === "" && trErrorFeedback) {
                    trErrorFeedback.remove();
                }
            });
        }
        // console.log("21" == "21.00");
        // console.log((21).toString() == (21.00).toString());
        // console.log(21 == 21.00);
        // console.log(parseFloat(21) == parseFloat(21.00));
        document.querySelectorAll('[id^="masuk-"]').forEach(input => {
            if (!input.id.includes('-real')) {
                input.addEventListener('change', function() {
                    formatNumber(input, `${input.id}-real`);
                });
            }
        });
        
        function accountingGetRelatedInvoiceReceivables(transactionNameId, trId, kategori_type, kategori_level_one, pelanggan_id, supplier_id) {
            let trAddTransaction = document.getElementById(`tr_add_transaction-${trId}`);
            let elementToAppend = "";
            // Reset tr penerimaan piutang jika sudah ada
            let resetElement = document.getElementById(`tr-pemasukan-pengeluaran-dengan-nota-${trId}`);
            if (resetElement) {
                resetElement.remove();
            }

            let trErrorFeedback = document.getElementById(`tr-error-feedback-${trId}`);
            if (trErrorFeedback) {
                trErrorFeedback.remove();
            }
            if (pelanggan_id || supplier_id) {
                $.ajax({
                    url: `/accounting/${transactionNameId}/get-related-not-yet-paid-off-invoices`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // console.log(data.message);
                        // console.log(data.notas);
                        // console.log(data.customerBalance);
                        if (data.notas.length > 0) {
                            // let listOfInvoiceID = []; // untuk digunakan nanti pada saat validasi submit
                            elementToAppend += `<tr id="tr-pemasukan-pengeluaran-dengan-nota-${trId}"><td colspan="6">
                                <input id="input-kategori-type-${trId}" type="hidden" name="kategori_type[]" value="${kategori_type}">
                                <input id="input-kategori-level-one-${trId}" type="hidden" name="kategori_level_one[]" value="${kategori_level_one}">
                                <input id="input-pelanggan-id-${trId}" type="hidden" name="pelanggan_id[]" value="${pelanggan_id}">
                                <input id="input-supplier-id-${trId}" type="hidden" name="supplier_id[]" value="${supplier_id}">
                                <div class="flex justify-center my-1"><div>
                                <table class="table-penerimaan-piutang">
                                    <tr><th></th><th>Nota</th><th>Harga Total</th><th>Sisa Bayar</th><th>Potongan Harga</th><th>Status Bayar</th><th>Total Bayar</th></tr>`;
                            let indexNota = 0;
                            data.notas.forEach(relatedInvoice => {
                                const [htmlRemainingBalance, labelBalance] = getHtmlRemainingBalance(kategori_type, data, indexNota, trId);
                                
                                let linkUrl = relatedInvoice.invoice_table === 'notas' ? `/notas/${relatedInvoice.id}/show` : `/pembelians/${relatedInvoice.id}/show`;
                                elementToAppend += `
                                <tr>${htmlRemainingBalance}
                                    <td class="font-bold">
                                        <label for="related_not_yet_paid_off_invoices[nota_id]" class="ml-1 text-sky-500 hover:cursor-pointer"><a href="${linkUrl}" target="_blank">${relatedInvoice.nomor_nota}</a></label>
                                        <input type="hidden" id="related_not_yet_paid_off_invoices[nota_id]-${trId}-${relatedInvoice.invoice_id}" name="related_not_yet_paid_off_invoices[nota_id][${trId}][]" value="${relatedInvoice.invoice_id}">
                                        <div class="text-center">${formatDate(relatedInvoice.created_at)}</div>
                                    </td>
                                    <td>
                                        <input type="text" value="${formatHargaIndo(relatedInvoice.harga_total)}" class="text-xs p-0 border-none text-center" readonly>
                                        <input type="hidden" name="related_not_yet_paid_off_invoices[harga_total][${trId}][]" id="related_not_yet_paid_off_invoices[harga_total]-${trId}-${relatedInvoice.invoice_id}-real" value="${relatedInvoice.harga_total}">
                                    </td>
                                    <td>
                                        <div class="text-xs p-0 border-none text-center">${formatHargaIndo(relatedInvoice.amount_due)}</div>
                                        <div>
                                            <span class="text-orange-400">=><input type="text" id="related_not_yet_paid_off_invoices[amount_due]-${trId}-${relatedInvoice.invoice_id}" value="${formatHargaIndo(relatedInvoice.amount_due)}" class="text-xs p-0 border-none text-center" readonly></span>
                                            <input type="hidden" name="related_not_yet_paid_off_invoices[amount_due][${trId}][]" id="related_not_yet_paid_off_invoices[amount_due]-${trId}-${relatedInvoice.invoice_id}-real" value="${relatedInvoice.amount_due}">
                                            <input type="hidden" id="related_not_yet_paid_off_invoices[amount_due]-${trId}-${relatedInvoice.invoice_id}-real-unchanged" value="${relatedInvoice.amount_due}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex w-full">
                                            <input type="number" id="related_not_yet_paid_off_invoices[discount_percent]-${trId}-${relatedInvoice.invoice_id}" name="related_not_yet_paid_off_invoices[discount_percent][${trId}][]" value="0" class="text-xs p-0 pl-1 w-max-30">
                                            <span>%</span>
                                            <input type="text" id="related_not_yet_paid_off_invoices[discount_amount]-${trId}-${relatedInvoice.invoice_id}" value="0" class="text-xs p-0 pl-1 w-max-30 bg-slate-200" readonly>
                                        </div>
                                        <div class="flex w-full">
                                            <input type="text" id="related_not_yet_paid_off_invoices[other_discount]-${trId}-${relatedInvoice.invoice_id}" value="0" class="text-xs p-0 pl-1 col-span-2 w-max-30">
                                            <input type="text" id="related_not_yet_paid_off_invoices[total_discount]-${trId}-${relatedInvoice.invoice_id}" value="0" class="text-xs p-0 pl-1 col-span-3 bg-slate-200 w-max-30" readonly>
                                        </div>
                                        <input type="text" name="related_not_yet_paid_off_invoices[discount_description][${trId}][]" placeholder="keterangan diskon" class="text-xs p-0 pl-1">
                                        <input type="hidden" name="related_not_yet_paid_off_invoices[discount_amount][${trId}][]" id="related_not_yet_paid_off_invoices[discount_amount]-${trId}-${relatedInvoice.invoice_id}-real" value="0">
                                        <input type="hidden" name="related_not_yet_paid_off_invoices[other_discount][${trId}][]" id="related_not_yet_paid_off_invoices[other_discount]-${trId}-${relatedInvoice.invoice_id}-real" value="0">
                                        <input type="hidden" name="related_not_yet_paid_off_invoices[total_discount][${trId}][]" id="related_not_yet_paid_off_invoices[total_discount]-${trId}-${relatedInvoice.invoice_id}-real" value="0">
                                    </td>
                                    <td>
                                        <div class="text-xs p-1 text-center">${relatedInvoice.status_bayar}</div>
                                        <input type="hidden" id="related_not_yet_paid_off_invoices[payment_status_before]-${trId}-${relatedInvoice.invoice_id}" value="${relatedInvoice.status_bayar}" readonly>
                                        <div class="text-xs text-center">
                                            <span class="text-emerald-400">=><input type="text" id="related_not_yet_paid_off_invoices[payment_status]-${trId}-${relatedInvoice.invoice_id}" name="related_not_yet_paid_off_invoices[payment_status][${trId}][]" class="text-xs p-0 border-none" value="${relatedInvoice.status_bayar}" readonly></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-bold text-xs">${labelBalance}</div>
                                        <input type="text" id="related_not_yet_paid_off_invoices[amount_paid]-${trId}-${relatedInvoice.invoice_id}" value="0" class="text-xs p-1">
                                        <input type="hidden" id="related_not_yet_paid_off_invoices[amount_paid]-${trId}-${relatedInvoice.invoice_id}-real" name="related_not_yet_paid_off_invoices[amount_paid][${trId}][]" value="0">
                                        <div class="font-bold text-xs">Dari Saldo</div>
                                        <input type="text" id="related_not_yet_paid_off_invoices[balance_used]-${trId}-${relatedInvoice.invoice_id}" value="0" class="text-xs p-1">
                                        <input type="hidden" id="related_not_yet_paid_off_invoices[balance_used]-${trId}-${relatedInvoice.invoice_id}-real" name="related_not_yet_paid_off_invoices[balance_used][${trId}][]" value="0">
                                        <input type="hidden" name="invoiceID[${trId}][]" value="${relatedInvoice.invoice_id}">
                                    </td>
                                </tr>`;
    
                                indexNota++;
                                // listOfInvoiceID.push(relatedInvoice.invoice_id);
                            });
                            let htmlTotalDuePaidOverpayment = `<tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>`;
                            let htmlErrorFeedback = `<tr id="tr-error-feedback-${trId}" class="hidden"><td colspan=7><div class="text-center max-w-4xl"><p id="p-error-feedback-${trId}" class="text-red-500 font-bold"></p></div></td></tr>`;
                            elementToAppend += `${htmlErrorFeedback}</table></div></div></td></tr>`;
    
                            trAddTransaction.insertAdjacentHTML('afterend', elementToAppend);
    
                            data.notas.forEach(relatedInvoice => {
                                // console.log('trId', trId);
                                applyFormatNumber(`related_not_yet_paid_off_invoices[amount_due]-${trId}-${relatedInvoice.invoice_id}`);
                                applyEvent(`related_not_yet_paid_off_invoices[discount_percent]-${trId}-${relatedInvoice.invoice_id}`, trId, kategori_type);
                                applyFormatNumberAndEvent(`related_not_yet_paid_off_invoices[other_discount]-${trId}-${relatedInvoice.invoice_id}`, trId, kategori_type);
                                applyFormatNumberAndEvent(`related_not_yet_paid_off_invoices[amount_paid]-${trId}-${relatedInvoice.invoice_id}`, trId, kategori_type);
                                applyFormatNumberAndEvent(`related_not_yet_paid_off_invoices[balance_used]-${trId}-${relatedInvoice.invoice_id}`, trId, kategori_type);
                            });
    
                            // console.log('trId-masuk', trId);
                            if (kategori_type === "UANG MASUK") {
                                applyEvent(`masuk-${trId}`, trId, kategori_type);
                            } else if (kategori_type === "UANG KELUAR") {
                                applyEvent(`keluar-${trId}`, trId, kategori_type);
                            }
                        }
                    },
                    error: function(err) {
                        console.error('Error:', err);
                        console.error('message:', err.responseJSON?.message);
                        // Reset tr penerimaan piutang jika terjadi error
                        let trPenerimaanPiutang = document.getElementById(`tr-pemasukan-pengeluaran-dengan-nota-${trId}`);
                        if (trPenerimaanPiutang) {
                            trPenerimaanPiutang.remove();
                        }
                        elementToAppend += `<tr id="tr-pemasukan-pengeluaran-dengan-nota-${trId}" class="hidden"><td colspan="6">
                            <input id="is-data-found-${trId}" type="hidden" value="no">
                            <div class="flex justify-center my-1"><div>`;
                        // Tambahkan elemen error feedback
                        let htmlErrorFeedback = `<tr id="tr-error-feedback-${trId}" class="hidden"><td colspan=6><div class="text-center max-w-4xl border border-red-4000"><p id="p-error-feedback-${trId}" class="text-red-500 font-bold"></p></div></td></tr>`;
                        elementToAppend += `${htmlErrorFeedback}</div></div></td></tr>`;
                        
                        trAddTransaction.insertAdjacentHTML('afterend', elementToAppend);
                        // alert(err.responseJSON?.message ?? 'Terjadi kesalahan');
                    }
                });
                // console.log(listOfTrID);
            } else {
                elementToAppend = `<tr id="tr-error-feedback-${trId}" class="hidden"><td colspan=6>
                    <input id="input-kategori-type-${trId}" type="hidden" name="kategori_type[]" value="${kategori_type}">
                    <div class="text-center max-w-4xl"><p id="p-error-feedback-${trId}" class="text-red-500 font-bold"></p></div>
                </td></tr>`;
                trAddTransaction.insertAdjacentHTML('afterend', elementToAppend);
            }
        }

        function applyFormatNumber(elementId) {
            let element = document.getElementById(`${elementId}`);
            try {
                element.addEventListener('change', function() {
                    formatNumber(element, `${elementId}-real`);
                });
                
            } catch (error) {
                console.log(error);
                console.log(elementId);
            }
        }

        function applyFormatNumberAndEvent(elementId, trId, type) {
            let element = document.getElementById(`${elementId}`);
            try {
                element.addEventListener('change', function() {
                    formatNumber(element, `${elementId}-real`);
                    if (type === "UANG MASUK") {
                        recalculateBalanceMasuk_TotalDue_TotalPaid(trId);
                    } else if (type === "UANG KELUAR") {
                        recalculateBalanceKeluar_TotalDue_TotalPaid_ForBayarBahanBaku(trId);
                    }
                });
            } catch (error) {
                console.log(error);
                console.log(elementId);
            }
        }

        function applyEvent(elementId, trId, type) {
            let element = document.getElementById(`${elementId}`);
            try {
                element.addEventListener('change', function() {
                    if (type === "UANG MASUK") {
                        recalculateBalanceMasuk_TotalDue_TotalPaid(trId);
                    } else if (type === "UANG KELUAR") {
                        // console.log('applyEvent UANG KELUAR');
                        recalculateBalanceKeluar_TotalDue_TotalPaid_ForBayarBahanBaku(trId);
                    }
                });
            } catch (error) {
                console.log(error);
                console.log(elementId);
            }
        }

        function theChangeOfMasukChangeThePayment(trId) {
            // console.log('theChangeOfMasukChangeThePayment', trId);
            let masuk = document.getElementById(`masuk-${trId}`);
            masuk.addEventListener('change', function() {
                recalculateBalanceMasuk_TotalDue_TotalPaid(trId);
            });
        }

        function recalculateBalanceMasuk_TotalDue_TotalPaid(trId) {
            // Set the initial value: remainingBalance
            let kategoriLevelOne = document.getElementById(`input-kategori-level-one-${trId}`).value;
            // PIUTANG MASUK
            let remainingBalanceMasuk = document.getElementById(`remaining_balance_masuk-${trId}`);
            let remainingBalanceMasukReal = document.getElementById(`remaining_balance_masuk-${trId}-real`);

            let masukReal = document.getElementById(`masuk-${trId}-real`);
            let masukRealValue = 0;
            if (masukReal) {
                masukRealValue = masukReal.value;
            }
            remainingBalanceMasuk.innerHTML = formatHargaIndo(masukRealValue);
            remainingBalanceMasukReal.value = masukRealValue;
            let remainingBalanceMasukRealValue = parseFloat(remainingBalanceMasukReal.value);

            // PEMBAYARAN HUTANG BAHAN BAKU
            let remainingBalanceKeluar = document.getElementById(`remaining_balance_keluar-${trId}`);
            let remainingBalanceKeluarReal = document.getElementById(`remaining_balance_keluar-${trId}-real`);

            let keluarReal = document.getElementById(`keluar-${trId}-real`);
            let keluarRealValue = 0;
            if (keluarReal) {
                keluarRealValue = keluarReal.value;
            }

            let sisaSaldo = document.getElementById(`sisa-saldo-${trId}`);
            let sisaSaldoReal = document.getElementById(`sisa-saldo-${trId}-real`);
            let saldoAwal = document.getElementById(`saldo-awal-${trId}-real`);
            let sisaSaldoRealValue = parseFloat(saldoAwal.value);

            // console.log('masukReal.value:', masukReal.value);
            // console.log('masukReal', masukReal);
            // console.log('remainingBalanceMasuk', remainingBalanceMasuk);
            // console.log('trId', trId);
            
            let relatedNotYetPaidOffInvoices = document.querySelectorAll(`input[name="related_not_yet_paid_off_invoices[nota_id][${trId}][]"]`);
            // console.log(relatedNotYetPaidOffInvoices);
            // Mulai Perhitungan
            if (relatedNotYetPaidOffInvoices.length > 0) {
                // looping untuk set nilai awal kembali ke semula
                relatedNotYetPaidOffInvoices.forEach(invoice => {
                    let amountDueReal = document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${trId}-${invoice.value}-real`);
                    let amountDueRealUnchanged = document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${trId}-${invoice.value}-real-unchanged`);
                    amountDueReal.value = amountDueRealUnchanged.value;
                });

                relatedNotYetPaidOffInvoices.forEach(invoice => {
                    let amountPaid = document.getElementById(`related_not_yet_paid_off_invoices[amount_paid]-${trId}-${invoice.value}`);
                    let amountPaidReal = document.getElementById(`related_not_yet_paid_off_invoices[amount_paid]-${trId}-${invoice.value}-real`);
                    let amountDue = document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${trId}-${invoice.value}`);
                    let amountDueReal = document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${trId}-${invoice.value}-real`);
                    let amountDueRealUnchanged = document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${trId}-${invoice.value}-real-unchanged`);
                    let paymentStatus = document.getElementById(`related_not_yet_paid_off_invoices[payment_status]-${trId}-${invoice.value}`);
                    let paymentStatusBefore = document.getElementById(`related_not_yet_paid_off_invoices[payment_status_before]-${trId}-${invoice.value}`);
                    let discountPercentage = document.getElementById(`related_not_yet_paid_off_invoices[discount_percent]-${trId}-${invoice.value}`);
                    let percentDiscount = document.getElementById(`related_not_yet_paid_off_invoices[discount_amount]-${trId}-${invoice.value}`);
                    let percentDiscountReal = document.getElementById(`related_not_yet_paid_off_invoices[discount_amount]-${trId}-${invoice.value}-real`);
                    let otherDiscount = document.getElementById(`related_not_yet_paid_off_invoices[other_discount]-${trId}-${invoice.value}`);
                    let otherDiscountReal = document.getElementById(`related_not_yet_paid_off_invoices[other_discount]-${trId}-${invoice.value}-real`);
                    let totalDiscount = document.getElementById(`related_not_yet_paid_off_invoices[total_discount]-${trId}-${invoice.value}`);
                    let totalDiscountReal = document.getElementById(`related_not_yet_paid_off_invoices[total_discount]-${trId}-${invoice.value}-real`);
                    let balanceUsed = document.getElementById(`related_not_yet_paid_off_invoices[balance_used]-${trId}-${invoice.value}`);
                    let balanceUsedReal = document.getElementById(`related_not_yet_paid_off_invoices[balance_used]-${trId}-${invoice.value}-real`);
                    let totalPrice = document.getElementById(`related_not_yet_paid_off_invoices[harga_total]-${trId}-${invoice.value}-real`);
                    // console.log(trId, invoice.value);
                    // console.log(otherDiscountReal);
                    // parseFloat beberapa Value
                    let discountPercentageValue = parseFloat(discountPercentage.value);
                    let otherDiscountRealValue = parseFloat(otherDiscountReal.value);
                    let balanceUsedRealValue = parseFloat(balanceUsedReal.value);
                    let amountPaidRealValue = parseFloat(amountPaidReal.value);
                    let amountDueRealUnchangedValue = parseFloat(amountDueRealUnchanged.value);
                    let amountDueRealValue = parseFloat(amountDueReal.value);
                    let totalPriceValue = parseFloat(totalPrice.value);

                    // Hitung Potongan Harga
                    let percentDiscountRealValue = (discountPercentageValue / 100) * amountDueRealValue;
                    percentDiscountReal.value = percentDiscountRealValue;
                    percentDiscount.value = formatHargaIndo(percentDiscountRealValue);
                    let totalDiscountRealValue = percentDiscountRealValue + otherDiscountRealValue;
                    otherDiscount.value = formatHargaIndo(otherDiscountRealValue);
                    totalDiscountReal.value = totalDiscountRealValue;
                    totalDiscount.value = formatHargaIndo(totalDiscountRealValue);
                    // console.log("discountPercentageValue", discountPercentageValue);
                    // console.log("totalDiscountRealValue", totalDiscountRealValue);

                    // Hitung Sisa Saldo
                    sisaSaldoRealValue = sisaSaldoRealValue - balanceUsedRealValue;
                    sisaSaldoReal.value = sisaSaldoRealValue;
                    sisaSaldo.innerHTML = formatHargaIndo(sisaSaldoRealValue); // Format angka yang ditampilkan
                    // console.log("balanceUsedRealValue", balanceUsedRealValue);
                    // console.log("sisaSaldoRealValue", sisaSaldoRealValue);

                    // Hitung Sisa Bayar
                    amountDueRealValue = amountDueRealValue - totalDiscountRealValue - amountPaidRealValue - balanceUsedRealValue;
                    amountDueReal.value = amountDueRealValue;
                    amountDue.value = formatHargaIndo(amountDueRealValue); // Format angka yang ditampilkan
                    
                    // Hitung Sisa Balance
                    remainingBalanceMasukRealValue = remainingBalanceMasukRealValue - amountPaidRealValue;
                    remainingBalanceMasukReal.value = remainingBalanceMasukRealValue;
                    remainingBalanceMasuk.innerHTML = formatHargaIndo(remainingBalanceMasukRealValue);

                    // Menentukan status_bayar
                    setTimeout(() => {
                        // console.log(amountPaidRealValue, amountDueRealValue);
                        // console.log(amountDueRealValue);
                        if (amountDueRealValue <= 0) {
                            paymentStatus.value = 'LUNAS';
                        } else if (amountDueRealValue == (amountDueRealUnchangedValue-totalDiscountRealValue) || amountDueRealValue == totalPriceValue) {
                            // console.log(paymentStatusBefore.value);
                            paymentStatus.value = 'BELUM_LUNAS';
                            if (paymentStatusBefore.value == 'SEBAGIAN') {
                                paymentStatus.value = 'SEBAGIAN';
                            }
                        } else if (amountDueRealValue > 0 && (amountDueRealValue < (amountDueRealUnchangedValue-totalDiscountRealValue) || amountDueRealValue < totalPriceValue)) {
                            paymentStatus.value = 'SEBAGIAN';
                        }
                        // console.log(paymentStatus.value);
                    }, 1000);
                });
            }
        }

        // Simpan posisi scroll sebelum form disubmit
        document.querySelectorAll("form").forEach(form => {
            form.addEventListener("submit", () => {
                sessionStorage.setItem("scrollY", window.scrollY);
            });
        });

        // Kembalikan posisi scroll saat halaman dimuat
        window.addEventListener("load", () => {
            const scrollY = sessionStorage.getItem("scrollY");
            if (scrollY !== null) {
                window.scrollTo(0, parseInt(scrollY));
                sessionStorage.removeItem("scrollY"); // Hapus agar tidak mengganggu navigasi normal
            }
        });

        // LOADING SPINNER
        $spinner = $('#spinner');
        $spinner.hide(500);
        const loading_animation = () => {
            $loading_to_disable = $('#loading_to_disable')
            $loading_to_disable.prop('disabled', true);
            $loading_to_hide = $('#loading_to_hide');
            $loading_to_hide.hide()
            // console.log('loading_animation');
            $spinner = $('#spinner');
            $spinner.show();
        }

        // VALIDASI SECARA FRONTEND
        document.getElementById('form-add-transactions').addEventListener('submit', (event) => {
            event.preventDefault();
            $spinner.show(500);
            setTimeout(() => {
                $spinner.hide(500);
            }, 3000); // Sembunyikan spinner setelah 2 detik (2000 ms)
            // console.log('submit');
            /*
            Filter array object listOfTrID, apabila ditemukan duplicate dari listOfInvoiceID,
            maka submit akan dibatalkan
            */
            // const seen = new Set();
            // const hasDuplicate = listOfTrID.some(item => {
            //     if (seen.has(item.listOfInvoiceID)) return true;
            //     seen.add(item.listOfInvoiceID);
            //     return false;
            // });

            // if (hasDuplicate) {
            //     alert('Terdapat duplikat Invoice ID, submit dibatalkan.');
            //     return;
            // }

            let adaError = false;
            let trIDs = document.getElementsByName(`trId[]`);
            // console.log(trIDs);
            // console.log(trIDs.length);
            for (let i = 0; i < trIDs.length; i++) {
                // console.log(`input-kategori-level-one-${i}`);
                let errorMessage = '';
                let kategoriLevelOne = document.getElementById(`input-kategori-level-one-${i}`);
                let kategoriType = document.getElementById(`input-kategori-type-${i}`);
                let pelangganId = document.getElementById(`input-pelanggan-id-${i}`);
                let supplierId = document.getElementById(`input-supplier-id-${i}`);
                let masuk = document.getElementById(`masuk-${i}`);
                let keluar = document.getElementById(`keluar-${i}`);
                let masukReal = document.getElementById(`masuk-${i}-real`);
                let keluarReal = document.getElementById(`keluar-${i}-real`);

                // Reset element feedback to hidden
                // Kalau pada baris terkait, input deskripsi belum diisi apapun, maka element tr tidak akan di create
                let trErrorFeedback = document.getElementById(`tr-error-feedback-${i}`);

                if (!trErrorFeedback && !masuk.value.trim() && !keluar.value.trim()) {
                    continue; // Skip to the next iteration if trErrorFeedback does not exist
                }

                let pErrorFeedback = document.getElementById(`p-error-feedback-${i}`);
                if (!trErrorFeedback.classList.contains('hidden')) {
                    trErrorFeedback.classList.add('hidden');
                    pErrorFeedback.textContent = "";
                }

                if (pelangganId.value || supplierId.value) {
                    // remove class hidden pada tr penerimaan piutang
                    let trPenerimaanPiutang = document.getElementById(`tr-pemasukan-pengeluaran-dengan-nota-${i}`);
                    if (trPenerimaanPiutang && trPenerimaanPiutang.classList.contains('hidden')) {
                        trPenerimaanPiutang.classList.remove('hidden');
                    }
                    let isDataFound = document.getElementById(`is-data-found-${i}`);
                    if (isDataFound && isDataFound.value == "no") {
                        adaError = true;
                        errorMessage += 'ERROR: [Data Nota tidak ditemukan!]';
                    } else {
                        let errorMessage2 = 'ERROR: ';
                        let notaIDs = document.getElementsByName(`related_not_yet_paid_off_invoices[nota_id][${i}][]`);
                        // console.log(notaIDs);
                        // let remainingBalanceMasukRealValue = parseFloat(document.getElementById(`remaining_balance_masuk-${i}-real`).value);
                        let sisaSaldoRealValue = parseFloat(document.getElementById(`sisa-saldo-${i}-real`).value);
                        let saldoAwalRealValue = parseFloat(document.getElementById(`saldo-awal-${i}-real`).value);
                        
                        // console.log(masukRealValue);
                        
                        let totalSaldoUsed = 0;
                        notaIDs.forEach(notaID => {
                            let amountPaidRealValue = parseFloat(document.getElementById(`related_not_yet_paid_off_invoices[amount_paid]-${i}-${notaID.value}-real`).value);
                            let amountDueRealValue = parseFloat(document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${i}-${notaID.value}-real`).value);
                            let amountDueRealUnchangedValue = parseFloat(document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${i}-${notaID.value}-real-unchanged`).value);
                            let paymentStatusValue = document.getElementById(`related_not_yet_paid_off_invoices[payment_status]-${i}-${notaID.value}`).value;
                            let discountPercentageValue = parseFloat(document.getElementById(`related_not_yet_paid_off_invoices[discount_percent]-${i}-${notaID.value}`).value);
                            let totalDiscountRealValue = parseFloat(document.getElementById(`related_not_yet_paid_off_invoices[total_discount]-${i}-${notaID.value}-real`).value);
                            let balanceUsedRealValue = parseFloat(document.getElementById(`related_not_yet_paid_off_invoices[balance_used]-${i}-${notaID.value}-real`).value);
                            totalSaldoUsed += balanceUsedRealValue;
    
                            // Validasi nilai negatif pada amount_paid dan nilai negatif pada saldo dan pada amount_due(sisa bayar)
                            // Validasi nilai 0 pada amount_paid dan saldo
                            if (amountPaidRealValue < 0) {
                                errorMessage2 += '[Nilai tidak sesuai pada saldo yang digunakan.]';
                                adaError = true;
                            }
                            if (balanceUsedRealValue < 0) {
                                errorMessage2 += '[Nilai tidak sesuai pada balance masuk yang digunakan]';
                                adaError = true;
                            }
                            if (amountDueRealValue < 0) {
                                errorMessage2 += '[Nilai tidak sesuai pada sisa bayar.]';
                                adaError = true;
                            }
                            // if (amountPaidRealValue <= 0 && balanceUsedRealValue <= 0) {
                            //     errorMessage2 += '[tidak ada pembayaran yang dilakukan, baik dari uang masuk maupun dari saldo yang digunakan.]';
                            //     adaError = true;
                            // }
    
                            // Validasi payment_status tidak error
                            if (paymentStatusValue == "error") {
                                errorMessage2 += '[Error pada status_bayar.]';
                                adaError = true;
                            }
                        });
    
                        if (kategoriType === 'UANG MASUK') {
                            // Validasi nilai uang masuk
                            if (isNaN(masukReal.value) || masukReal.value < 0) {
                                errorMessage2 += '[Input nilai masuk tidak sesuai.]';
                                adaError = true;
                            }
    
                            if (masukReal.value == 0) {
                                if (totalSaldoUsed == 0) {
                                    errorMessage2 += '[Apabila uang masuk 0, maka saldo yang digunakan tidak boleh 0.]';
                                    adaError = true;
                                }
                            }
                        } else if (kategoriType === 'UANG KELUAR') {
                            // Validasi nilai uang keluar
                            if (isNaN(keluarReal.value) || keluarReal.value < 0) {
                                errorMessage2 += '[Input nilai keluar tidak sesuai.]';
                                adaError = true;
                            }

                            if (keluarReal.value == 0) {
                                if (totalSaldoUsed == 0) {
                                    errorMessage2 += '[Apabila uang keluar 0, maka saldo yang digunakan tidak boleh 0.]';
                                    adaError = true;
                                }
                            }
                        }
    
                        // Validasi total saldo yang digunakan tidak melebih saldo awal, karena tidak make sense.
                        if (totalSaldoUsed > saldoAwalRealValue) {
                            errorMessage2 += '[Total saldo yang digunakan melebihi saldo awal.]';
                            adaError = true;
                        }

                        if (adaError) {
                            errorMessage += errorMessage2;
                        }
                    }
                } else {
                    // console.log(kategoriType);
                    if (kategoriType.value == "UANG MASUK") {
                        if (!masukReal || masukReal.value <= 0) {
                            errorMessage += '[UANG MASUK?]';
                            adaError = true;
                        }
                    } else {
                        if (!keluarReal || keluarReal.value <= 0) {
                            errorMessage += '[UANG KELUAR?]';
                            adaError = true;
                        }
                    }
                }

                if (adaError) {
                    if (trErrorFeedback.classList.contains('hidden')) {
                        trErrorFeedback.classList.remove('hidden');
                        pErrorFeedback.textContent = errorMessage;
                    }
                }
            }

            if (adaError) {
                return false; // Batalkan submit
            } else {
                // Jika tidak ada error, lanjutkan submit
                // return false;
                event.target.submit();
            }
        })

        function formatDate(params) {
            let createdAt = new Date(params);

            // Format manual (contoh: 16-09-2025 14:25)
            let formatted = '<div class="border rounded border-red-400">' + createdAt.getDate().toString().padStart(2, '0') + '/' +
                            (createdAt.getMonth() + 1).toString().padStart(2, '0') + '<br>' +
                            createdAt.getFullYear() + '</div>';
                            // + ' ' +
                            // createdAt.getHours().toString().padStart(2, '0') + ':' +
                            // createdAt.getMinutes().toString().padStart(2, '0');

            // console.log(formatted);

            return formatted;
        }
    </script>

    <style>
        .table-penerimaan-piutang, .table-penerimaan-piutang th, .table-penerimaan-piutang td {
            border: 1px solid darkorchid;
            border-collapse: collapse;
        }
    </style>

    <script src="{{ asset('js/addTransactions.js') }}"></script>
</div>