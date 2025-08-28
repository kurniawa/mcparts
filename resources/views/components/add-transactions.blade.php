<div>
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
                            </td>
                        </tr>

                        
                        @endfor
                        {{-- <tr id="tr_add_transaction">
                            <td>
                                <button type="button" class="rounded bg-emerald-200 text-emerald-600" onclick="add_transaction('tr_add_transaction','table_add_transactions')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                            </td>
                        </tr> --}}
                    </table>
                    {{-- <div class="mt-3 text-xs border rounded p-1 inline-block border-yellow-500">
                        <p>*) Keterangan Tambahan akan tertulis dalam tanda kurung pada ringkasan/laporan.</p>
                    </div> --}}
                    <div class="mt-3 text-center text-xs">
                        <input id="loading_to_disable" type="submit" class="border-2 font-semibold rounded text-emerald-500 border-emerald-300 bg-emerald-200 px-2 hover:cursor-pointer" value="confirm" />
                    </div>
                    <div class="max-w-4xl"></div>
                </form>
            </div>
        </div>
        {{-- NOTIFIKASI --}}
        {{-- @if (Auth::user()->id === (int)$userInstance->user_id)
        <div class="">
            <div class="border rounded p-1">
                <h3 class="font-bold text-slate-500">Notifikasi</h3>
                <div class="w-52 h-52 overflow-auto">
                    @foreach ($notifications as $notification)
                    <div class="flex">
                        @if ($notification->status === 'not read yet')
                        <textarea readonly class="w-full text-xs p-1 border-red-300 border-2 text-red-500" rows="3">{{ $notification->username }} - {{ date('d-m-Y', strtotime($notification->created_at)) }} - input entry:"{{ $notification->transaction_desc }}"</textarea>
                        <div>
                            <form action="{{ route('accounting.mark_as_read_or_unread', [$userInstance->id, $notification->id]) }}" method="POST" onsubmit="return confirm('Mark as read?')">
                                @csrf
                                <button class="text-slate-400" type="submit" name="read" value="yes">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                    </svg>
                                </button>
                            </form>
                            <form action="{{ route('accounting.apply_entry', [$userInstance->id, $notification->id]) }}" method="POST" onsubmit="return confirm('Apply entry to your instance?')">
                                @csrf
                                <button class="text-slate-400" type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.5c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75A2.25 2.25 0 0116.5 4.5c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23H5.904M14.25 9h2.25M5.904 18.75c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 01-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 10.203 4.167 9.75 5 9.75h1.053c.472 0 .745.556.5.96a8.958 8.958 0 00-1.302 4.665c0 1.194.232 2.333.654 3.375z" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @else
                        <textarea readonly class="w-full text-xs p-1" rows="3">{{ $notification->username }} - {{ date('d-m-Y', strtotime($notification->created_at)) }} - input entry:"{{ $notification->transaction_desc }}"</textarea>
                        <div>
                            <form action="{{ route('accounting.mark_as_read_or_unread', [$userInstance->id, $notification->id]) }}" method="POST" onsubmit="return confirm('Mark as unread?')">
                                @csrf
                                <button class="text-emerald-500" type="submit" name="read" value="no">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif --}}
        {{-- END - NOTIFIKASI --}}
    </div>

    <script>
        const label_deskripsi = {!! json_encode($labelDeskripsi, JSON_HEX_TAG) !!};
        
        for (let i = 0; i < 15; i++) {
            autocomplete_deskripsi(i);
        }

        function autocomplete_deskripsi(index) {
            $(`#transaction_desc-${index}`).autocomplete({
                source: label_deskripsi,
                select: function (event, ui) {
                    // console.log(ui.item);
                    // document.getElementById(`transaction_desc-${index}`).value = ui.item.id;
                    document.getElementById(`transaction_desc-${index}`).value = ui.item.value;
                    document.getElementById(`transaction_id-${index}`).value = ui.item.id;
                    // autofill_transaction(index, ui.item.value);
                    // console.log("autocomplete_deskripsi: " + ui.item.id);
                    accountingGetRelatedInvoice(ui.item.id, index, ui.item.kategori_level_one, ui.item.kategori_type);
                }
            });

            /*
            Add event listener to transaction_desc input
            Kalau value transaction_desc == "", maka remove tr penerimaan piutang
            */
            document.getElementById(`transaction_desc-${index}`).addEventListener('change', function() {
                let transactionDescValue = this.value.trim();
                let trPenerimaanPiutang = document.getElementById(`tr-penerimaan-piutang-${index}`);
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
        
        function accountingGetRelatedInvoice(transactionNameId, trId, kategori_level_one, kategori_type) {
            let trAddTransaction = document.getElementById(`tr_add_transaction-${trId}`);
            let elementToAppend = "";
            // Reset tr penerimaan piutang jika sudah ada
            let resetElement = document.getElementById(`tr-penerimaan-piutang-${trId}`);
            if (resetElement) {
                resetElement.remove();
            }
            // Reset tr penerimaan piutang jika kategori_level_one bukan PENERIMAAN PIUTANG
            let trErrorFeedback = document.getElementById(`tr-error-feedback-${trId}`);
            if (trErrorFeedback) {
                trErrorFeedback.remove();
            }
            if (kategori_level_one == "PENERIMAAN PIUTANG") {
                // fetch(`/accounting/${transactionNameId}/get-related-invoice`)
                //     .then(response => {
                //         if (!response.ok) {
                //             throw new Error('Data not found');
                //         }
                //         return response.json();
                //     })
                //     .then(data => {
                //         console.log(data);
                //         // You can handle the data as needed, e.g., display it in a modal or alert
                //     })
                //     .catch(error => {
                //         console.error('Error:', error);
                //         alert(error.message);
                //     });
                
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
                            elementToAppend += `<tr id="tr-penerimaan-piutang-${trId}"><td colspan="6"><input id="input-kategori-level-one-${trId}" type="hidden" name="kategori_level_one[]" value="${kategori_level_one}"><div class="flex justify-center my-1"><div><table class="table-penerimaan-piutang"><tr><th></th><th>Nota</th><th>Harga Total</th><th>Sisa Bayar</th><th>Potongan Harga</th><th>Status Bayar</th><th>Total Bayar</th></tr>`;
                            let indexNota = 0;
                            let htmlRemainingBalanceMasuk = "";
                            data.notas.forEach(relatedInvoice => {
                                if (indexNota === 0) {
                                    htmlRemainingBalanceMasuk = `<td rowspan="${data.notas.length}">
                                        <div class="font-bold">Balance.M</div>
                                        <div id="remaining_balance_masuk-${trId}" class="text-xs p-1">0</div>
                                        <input type="hidden" id="remaining_balance_masuk-${trId}-real" name="remaining_balance_masuk[${trId}]" value="0">
                                        <div id="div-saldo-${trId}">
                                            <div class="font-bold">Saldo Awal</div>
                                            <div id="saldo-awal-${trId}" class="text-xs p-1">${data.customerBalance ? formatHargaIndo(data.customerBalance.amount) : 0}</div>
                                            <input type="hidden" id="saldo-awal-${trId}-real" name="saldo_awal[${trId}]" value="${data.customerBalance ? data.customerBalance.amount : 0}" readonly>
                                            <div class="font-bold">Sisa Saldo</div>
                                            <div id="sisa-saldo-${trId}" class="text-xs p-1 text-indigo-500">${data.customerBalance ? `=> ${formatHargaIndo(data.customerBalance.amount)}` : 0}</div>
                                            <input type="hidden" id="sisa-saldo-${trId}-real" name="sisa_saldo[${trId}]" value="${data.customerBalance ? data.customerBalance.amount : 0}">
                                        </div>
                                    </td>
                                    `;
                                } else {
                                    htmlRemainingBalanceMasuk = "";
                                }
                                elementToAppend += `
                                <tr>${htmlRemainingBalanceMasuk}
                                    <td>
                                        <label for="related_not_yet_paid_off_invoices[nota_id]" class="ml-1 hover:cursor-pointer">${relatedInvoice.no_nota}</label>
                                        <input type="hidden" id="related_not_yet_paid_off_invoices[nota_id]-${trId}-${relatedInvoice.invoice_id}" name="related_not_yet_paid_off_invoices[nota_id][${trId}][]" value="${relatedInvoice.invoice_id}">
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
                                        <div class="text-center">
                                            <input type="number" id="related_not_yet_paid_off_invoices[discount_percentage]-${trId}-${relatedInvoice.invoice_id}" name="related_not_yet_paid_off_invoices[discount_percentage][${trId}][]" value="0" class="text-xs p-1 w-12">%
                                        </div>
                                        <input type="text" id="related_not_yet_paid_off_invoices[total_discount]-${trId}-${relatedInvoice.invoice_id}" value="0" class="text-xs p-1 text-center">
                                        <input type="hidden" name="related_not_yet_paid_off_invoices[total_discount][${trId}][]" id="related_not_yet_paid_off_invoices[total_discount]-${trId}-${relatedInvoice.invoice_id}-real" value="0">
                                    </td>
                                    <td>
                                        <div class="text-xs p-1 text-center">${relatedInvoice.status_bayar}</div>
                                        <div class="text-xs text-center">
                                            <span class="text-emerald-400">=><input type="text" id="related_not_yet_paid_off_invoices[payment_status]-${trId}-${relatedInvoice.invoice_id}" name="related_not_yet_paid_off_invoices[payment_status][${trId}][]" class="text-xs p-0 border-none" value="${relatedInvoice.status_bayar}" readonly></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-bold text-xs">Dari Balance.M</div>
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
                                applyEvent(`related_not_yet_paid_off_invoices[discount_percentage]-${trId}-${relatedInvoice.invoice_id}`, trId);
                                applyFormatNumberAndEvent(`related_not_yet_paid_off_invoices[total_discount]-${trId}-${relatedInvoice.invoice_id}`, trId);
                                applyFormatNumberAndEvent(`related_not_yet_paid_off_invoices[amount_paid]-${trId}-${relatedInvoice.invoice_id}`, trId);
                                applyFormatNumberAndEvent(`related_not_yet_paid_off_invoices[balance_used]-${trId}-${relatedInvoice.invoice_id}`, trId);
                            });
    
                            // console.log('trId-masuk', trId);
                            applyEvent(`masuk-${trId}`, trId);
                            /*
                            Cek apakah array object listOfTrID memiliki trId yang sama.
                            Kalau sama, maka lakukan overwrite invoiceIDs pada object dengan index terkait.
                            Kalau tidak maka lakukan:
                            listOfTrID.push({trId:trId, invoiceIDs:listOfInvoiceID})
                            */
                            // Cek apakah sudah ada trId yang sama
                            // let indexTrId = listOfTrID.findIndex(item => item.trId === trId);
    
                            // if (indexTrId !== -1) {
                            //     // Kalau ada, overwrite invoiceIDs
                            //     listOfTrID[indexTrId].invoiceIDs = listOfInvoiceID;
                            // } else {
                            //     // Kalau tidak ada, tambahkan data baru
                            //     listOfTrID.push({ trId: trId, invoiceIDs: listOfInvoiceID });
                            // }
                        }
                    },
                    error: function(err) {
                        console.error('Error:', err);
                        console.error('message:', err.responseJSON?.message);
                        // Reset tr penerimaan piutang jika terjadi error
                        let trPenerimaanPiutang = document.getElementById(`tr-penerimaan-piutang-${trId}`);
                        if (trPenerimaanPiutang) {
                            trPenerimaanPiutang.remove();
                        }
                        elementToAppend += `<tr id="tr-penerimaan-piutang-${trId}" class="hidden"><td colspan="6">
                            <input id="input-kategori-level-one-${trId}" type="hidden" name="kategori_level_one[]" value="${kategori_level_one}">
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
                    <input id="input-kategori-level-one-${trId}" type="hidden" name="kategori_level_one[]" value="${kategori_level_one}">
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

        function applyFormatNumberAndEvent(elementId, trId) {
            let element = document.getElementById(`${elementId}`);
            try {
                element.addEventListener('change', function() {
                    formatNumber(element, `${elementId}-real`);
                    recalculateBalanceMasuk_TotalDue_TotalPaid(trId);
                });
            } catch (error) {
                console.log(error);
                console.log(elementId);
            }
        }

        function applyEvent(elementId, trId) {
            let element = document.getElementById(`${elementId}`);
            try {
                element.addEventListener('change', function() {
                    recalculateBalanceMasuk_TotalDue_TotalPaid(trId);
                });
            } catch (error) {
                console.log(error);
                console.log(elementId);
            }
        }

        function theChangeOfMasukChangeThePayment(trId) {
            console.log('theChangeOfMasukChangeThePayment', trId);
            let masuk = document.getElementById(`masuk-${trId}`);
            masuk.addEventListener('change', function() {
                recalculateBalanceMasuk_TotalDue_TotalPaid(trId);
            });
        }

        function recalculateBalanceMasuk_TotalDue_TotalPaid(trId) {
            // Set the initial value: remainingBalance
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
                    let discountPercentage = document.getElementById(`related_not_yet_paid_off_invoices[discount_percentage]-${trId}-${invoice.value}`);
                    let totalDiscount = document.getElementById(`related_not_yet_paid_off_invoices[total_discount]-${trId}-${invoice.value}`);
                    let totalDiscountReal = document.getElementById(`related_not_yet_paid_off_invoices[total_discount]-${trId}-${invoice.value}-real`);
                    let balanceUsed = document.getElementById(`related_not_yet_paid_off_invoices[balance_used]-${trId}-${invoice.value}`);
                    let balanceUsedReal = document.getElementById(`related_not_yet_paid_off_invoices[balance_used]-${trId}-${invoice.value}-real`);
                    let totalPrice = document.getElementById(`related_not_yet_paid_off_invoices[harga_total]-${trId}-${invoice.value}-real`);
                    
                    // parseFloat beberapa Value
                    let discountPercentageValue = parseFloat(discountPercentage.value);
                    let balanceUsedRealValue = parseFloat(balanceUsedReal.value);
                    let amountPaidRealValue = parseFloat(amountPaidReal.value);
                    let amountDueRealUnchangedValue = parseFloat(amountDueRealUnchanged.value);
                    let amountDueRealValue = parseFloat(amountDueReal.value);
                    let totalPriceValue = parseFloat(totalPrice.value);

                    // Hitung Potongan Harga
                    let totalDiscountRealValue = parseFloat(totalDiscountReal.value);
                    if (discountPercentageValue > 0) {
                        totalDiscountRealValue = (discountPercentageValue / 100) * amountDueRealValue;
                    }
                    totalDiscount.value = formatHargaIndo(totalDiscountRealValue);
                    totalDiscountReal.value = totalDiscountRealValue;
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
                            paymentStatus.value = 'lunas';
                        } else if (amountDueRealValue == (amountDueRealUnchangedValue-totalDiscountRealValue) && amountDueRealValue == totalPriceValue) {
                            paymentStatus.value = 'belum_lunas'; 
                        } else if (amountDueRealValue > 0 && (amountDueRealValue < (amountDueRealUnchangedValue-totalDiscountRealValue) || amountDueRealValue < totalPriceValue)) {
                            paymentStatus.value = 'sebagian';
                        } else {
                            paymentStatus.value = 'error';
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

                if (kategoriLevelOne && kategoriLevelOne.value == "PENERIMAAN PIUTANG") {
                    // remove class hidden pada tr penerimaan piutang
                    let trPenerimaanPiutang = document.getElementById(`tr-penerimaan-piutang-${i}`);
                    if (trPenerimaanPiutang.classList.contains('hidden')) {
                        trPenerimaanPiutang.classList.remove('hidden');
                    }
                    // apabila tidak ditemukan data nota, meskiput pun kategori_level_one adalah PENERIMAAN PIUTANG
                    let isDataFound = document.getElementById(`is-data-found-${i}`);
                    if (isDataFound && isDataFound.value == "no") {
                        adaError = true;
                        errorMessage += 'ERROR: [Data Nota tidak ditemukan!]';
                    } else {
                        let errorMessage2 = 'ERROR: ';
                        let notaIDs = document.getElementsByName(`related_not_yet_paid_off_invoices[nota_id][${i}][]`);
                        // console.log(notaIDs);
                        let remainingBalanceMasukRealValue = parseFloat(document.getElementById(`remaining_balance_masuk-${i}-real`).value);
                        let sisaSaldoRealValue = parseFloat(document.getElementById(`sisa-saldo-${i}-real`).value);
                        let saldoAwalRealValue = parseFloat(document.getElementById(`saldo-awal-${i}-real`).value);
                        
                        // console.log(masukRealValue);
                        
                        let totalSaldoUsed = 0;
                        notaIDs.forEach(notaID => {
                            let amountPaidRealValue = parseFloat(document.getElementById(`related_not_yet_paid_off_invoices[amount_paid]-${i}-${notaID.value}-real`).value);
                            let amountDueRealValue = parseFloat(document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${i}-${notaID.value}-real`).value);
                            let amountDueRealUnchangedValue = parseFloat(document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${i}-${notaID.value}-real-unchanged`).value);
                            let paymentStatusValue = document.getElementById(`related_not_yet_paid_off_invoices[payment_status]-${i}-${notaID.value}`).value;
                            let discountPercentageValue = parseFloat(document.getElementById(`related_not_yet_paid_off_invoices[discount_percentage]-${i}-${notaID.value}`).value);
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
    
                            // Validasi payment_status tidak error
                            if (paymentStatusValue == "error") {
                                errorMessage2 += '[Error pada status_bayar.]';
                                adaError = true;
                            }
                        });
    
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
                event.target.submit();
            }
        })
    </script>

    <style>
        .table-penerimaan-piutang, .table-penerimaan-piutang th, .table-penerimaan-piutang td {
            border: 1px solid darkorchid;
            border-collapse: collapse;
        }
    </style>
</div>