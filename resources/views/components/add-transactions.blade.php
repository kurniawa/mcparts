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
                <form action="{{ route('accounting.store_transactions', $userInstance->id) }}" onsubmit="return set_scroll_here()" method="POST" class="mt-1 inline-block min-w-max">
                    @csrf
                    <table class="text-xs min-w-max" id="table_add_transactions">
                        <tr class="text-slate-600">
                            <th>tanggal</th><th>kode</th><th>deskripsi/keterangan</th><th>keterangan tambahan</th><th>keluar</th><th>masuk</th>
                            {{-- <th>saldo</th> --}}
                        </tr>
                        @for ($i = 0; $i < 7; $i++)
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
                            </td>
                        </tr>

                        
                        @endfor
                        <tr id="tr_add_transaction">
                            <td>
                                <button type="button" class="rounded bg-emerald-200 text-emerald-600" onclick="add_transaction('tr_add_transaction','table_add_transactions')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                                {{-- <input type="hidden" name="user_instance_id" value="{{ $userInstance->id }}"> --}}
                            </td>
                        </tr>
                    </table>
                    {{-- <div class="mt-3 text-xs border rounded p-1 inline-block border-yellow-500">
                        <p>*) Keterangan Tambahan akan tertulis dalam tanda kurung pada ringkasan/laporan.</p>
                    </div> --}}
                    <div class="mt-3 text-center text-xs">
                        <input id="loading_to_disable" type="submit" class="border-2 font-semibold rounded text-emerald-500 border-emerald-300 bg-emerald-200 px-2 hover:cursor-pointer" value="confirm" />
                    </div>
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

        function applyFormatNumberForMasuk(trId) {
            document.querySelectorAll('[id^="masuk-"]').forEach(input => {
                if (!input.id.includes('-real')) {
                    input.addEventListener('change', function() {
                        formatNumber(input, `masuk-${trId}-real`);
                    });
                }
            });
        }
        function accountingGetRelatedInvoice(transactionNameId, trId) {
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
                    console.log(data.message);
                    console.log(data.notas);
                    console.log(data.customerBalance);
                    if (data.notas.length > 0) {
                        let trAddTransaction = document.getElementById(`tr_add_transaction-${trId}`);
                        let elementToAppend = `<tr id="tr-penerimaan-piutang-${trId}"><td colspan="6"><div class="flex justify-center my-1"><div class="border p-2"><table><tr><th></th><th>Nota</th><th>Harga Total</th><th>Sisa Bayar</th><th>Status Bayar</th><th>Total Bayar</th></tr>`;
                        let indexNota = 0;
                        let htmlRemainingBalanceMasuk = "";
                        data.notas.forEach(relatedInvoice => {
                            if (indexNota === 0) {
                                htmlRemainingBalanceMasuk = `<td rowspan="${data.notas.length}">
                                    <div class="font-bold">Balance.M</div>
                                    <div id="remaining_balance_masuk-${trId}" class="text-xs p-1"></div>
                                    <input type="hidden" id="remaining_balance_masuk-${trId}-real" name="remaining_balance_masuk[${trId}]">
                                    <div id="div-saldo-${trId}">
                                        <div class="font-bold"><input type="checkbox" id="checkbox-saldo-${trId}" name="checkbox-saldo[${trId}]" value="yes">Saldo</div>
                                        <div id="saldo-${trId}" class="text-xs p-1">${data.customerBalance ? formatHargaIndo(data.customerBalance.amount) : 0}</div>
                                        <div id="saldo-${trId}-recalculate" class="text-xs p-1 text-indigo-500">${data.customerBalance ? `=> ${formatHargaIndo(data.customerBalance.amount)}` : ""}</div>
                                        <input type="hidden" id="saldo-${trId}-real" name="saldo[${trId}]" value="${data.customerBalance ? data.customerBalance.amount : 0}">
                                        <input type="hidden" id="saldo-${trId}-real-unchanged" name="saldo[${trId}]" value="${data.customerBalance ? data.customerBalance.amount : 0}" readonly>
                                    </div>
                                </td>
                                `;
                            } else {
                                htmlRemainingBalanceMasuk = "";
                            }
                            elementToAppend += `
                            <tr>${htmlRemainingBalanceMasuk}
                                <td>
                                    <input type="checkbox" name="related_not_yet_paid_off_invoices[nota_id][${trId}][]" id="related_not_yet_paid_off_invoices[nota_id]-${trId}-${relatedInvoice.id}" value="${relatedInvoice.id}" class="checkbox-${trId} hover:cursor-pointer">
                                    <label for="related_not_yet_paid_off_invoices[nota_id]" class="ml-1 hover:cursor-pointer">${relatedInvoice.no_nota}</label>
                                </td>
                                <td>
                                    <input type="text" value="${formatHargaIndo(relatedInvoice.harga_total)}" class="text-xs p-0 border-none text-center" readonly>
                                    <input type="hidden" name="related_not_yet_paid_off_invoices[harga_total][${trId}][]" id="related_not_yet_paid_off_invoices[harga_total]-${trId}-${relatedInvoice.id}-real" value="${relatedInvoice.harga_total}">
                                </td>
                                <td>
                                    <div class="text-xs p-0 border-none text-center">${formatHargaIndo(relatedInvoice.amount_due)}</div>
                                    <div>
                                        <span class="text-orange-400">=><input type="text" id="related_not_yet_paid_off_invoices[amount_due]-${trId}-${relatedInvoice.id}" value="${formatHargaIndo(relatedInvoice.amount_due)}" class="text-xs p-0 border-none text-center" readonly></span>
                                        <input type="hidden" name="related_not_yet_paid_off_invoices[amount_due][${trId}][]" id="related_not_yet_paid_off_invoices[amount_due]-${trId}-${relatedInvoice.id}-real" value="${relatedInvoice.amount_due}">
                                        <input type="hidden" id="related_not_yet_paid_off_invoices[amount_due]-${trId}-${relatedInvoice.id}-real-unchanged" value="${relatedInvoice.amount_due}">
                                    </div>
                                </td>
                                <td>
                                    <div class="text-xs p-1">${relatedInvoice.status_bayar}</div>
                                    <span class="text-emerald-400">=><input type="text" id="related_not_yet_paid_off_invoices[payment_status]-${trId}-${relatedInvoice.id}" name="related_not_yet_paid_off_invoices[payment_status][${trId}][${relatedInvoice.id}]" class="text-xs p-0 border-none" value="${relatedInvoice.status_bayar}"></span>
                                </td>
                                <td id="td-related_not_yet_paid_off_invoices[amount_paid]-${trId}-${relatedInvoice.id}" class="hidden">
                                    <input type="text" id="related_not_yet_paid_off_invoices[amount_paid]-${trId}-${relatedInvoice.id}" value="${formatHargaIndoTanpaDesimal(relatedInvoice.amount_paid)}" class="text-xs p-1">
                                    <input type="hidden" id="related_not_yet_paid_off_invoices[amount_paid]-${trId}-${relatedInvoice.id}-real" name="related_not_yet_paid_off_invoices[amount_paid][${trId}][]" value="${relatedInvoice.amount_paid}">
                                </td>
                            </tr>`;

                            indexNota++;
                        });
                        let htmlTotalDuePaidOverpayment = `<tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>`;
                        elementToAppend += `</table></div></div></td></tr>`;

                        trAddTransaction.insertAdjacentHTML('afterend', elementToAppend);

                        data.notas.forEach(relatedInvoice => {
                            applyFormatNumber(`related_not_yet_paid_off_invoices[amount_due]-${trId}-${relatedInvoice.id}`);
                            applyFormatNumberAndCountAmountDue(trId, relatedInvoice.id);
                            toggleCheckboxRecalculate_BalanceMasuk_TotalDue_TotalPaid(trId, relatedInvoice.id);
                        });

                        // Set remainingBalanceMasuk value
                        let masukRealValue = document.getElementById(`masuk-${trId}-real`).value;
                        let remainingBalanceMasuk = document.getElementById(`remaining_balance_masuk-${trId}`);
                        let remainingBalanceMasukReal = document.getElementById(`remaining_balance_masuk-${trId}-real`);
                        // console.log('element remainingBalanceMasuk:', remainingBalanceMasuk);
                        if (masukRealValue) {
                            remainingBalanceMasuk.innerHTML = formatHargaIndo(masukRealValue);
                            remainingBalanceMasukReal.value = masukRealValue;
                        } else {
                            remainingBalanceMasuk.innerHTML = 0;
                            remainingBalanceMasukReal.value = 0;
                        }
                        theChangeOfMasukChangeThePayment(trId);
                    }
                },
                error: function(err) {
                    console.error('Error:', err);
                    console.error('message:', err.responseJSON?.message);
                    let trPenerimaanPiutang = document.getElementById(`tr-penerimaan-piutang-${trId}`);
                    if (trPenerimaanPiutang) {
                        trPenerimaanPiutang.remove();
                    }
                    // alert(err.responseJSON?.message ?? 'Terjadi kesalahan');
                }
            });
        }

        function applyFormatNumber(elementId) {
            let element = document.getElementById(`${elementId}`);
            element.addEventListener('change', function() {
                formatNumber(element, `${elementId}-real`);
            });
        }

        function applyFormatNumberAndCountAmountDue(trId, invoiceId) {
            let amountPaid = document.getElementById(`related_not_yet_paid_off_invoices[amount_paid]-${trId}-${invoiceId}`);
            amountPaid.addEventListener('change', function() {
                formatNumber(amountPaid, `related_not_yet_paid_off_invoices[amount_paid]-${trId}-${invoiceId}-real`);
                let realTotalAmount = document.getElementById(`related_not_yet_paid_off_invoices[harga_total]-${trId}-${invoiceId}-real`).value;
                let realAmountPaid = document.getElementById(`related_not_yet_paid_off_invoices[amount_paid]-${trId}-${invoiceId}-real`).value;
                let realAmountDue = realTotalAmount - realAmountPaid;
                let amountDue = document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${trId}-${invoiceId}`);
                amountDue.value = formatHargaIndo(realAmountDue.toString());
                let amountDueReal = document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${trId}-${invoiceId}-real`);
                amountDueReal.value = realAmountDue;
            });
        }

        function theChangeOfMasukChangeThePayment(trId) {
            let masuk = document.getElementById(`masuk-${trId}`);
            masuk.addEventListener('change', function() {
                console.log('theChangeOfMasukChangeThePayment');
                recalculateBalanceMasuk_TotalDue_TotalPaid(trId);
            });
        }

        function toggleCheckboxRecalculate_BalanceMasuk_TotalDue_TotalPaid(trId, invoiceId) {
            let checkbox = document.getElementById(`related_not_yet_paid_off_invoices[nota_id]-${trId}-${invoiceId}`);
            let checkboxSaldo = document.getElementById(`checkbox-saldo-${trId}`);
            checkbox.addEventListener('change', function() {
                recalculateBalanceMasuk_TotalDue_TotalPaid(trId);
            });
            checkboxSaldo.addEventListener('change', function () {
                recalculateBalanceMasuk_TotalDue_TotalPaid(trId);
            })
        }

        function recalculateBalanceMasuk_TotalDue_TotalPaid(trId) {
            let remainingBalanceMasuk = document.getElementById(`remaining_balance_masuk-${trId}`);
            let remainingBalanceMasukReal = document.getElementById(`remaining_balance_masuk-${trId}-real`);
            // Set the initial value of masukReal
            let masukReal = document.getElementById(`masuk-${trId}-real`);
            if (!masukReal.value) {
                masukReal.value = 0;
            }
            // console.log('masukReal.value:', masukReal.value);
            remainingBalanceMasuk.innerHTML = formatHargaIndo(masukReal.value);
            remainingBalanceMasukReal.value = masukReal.value;

            /*
            Melakukan perhitungan saldo terlebih dahulu.
            Apabila pelanggan memiliki saldo, maka perlu dikurangkan dulu dari saldo yang dia punya.
            */
            recalculateSaldoAndOverpayment(trId);
            
            let relatedNotYetPaidOffInvoices = document.querySelectorAll(`input[name="related_not_yet_paid_off_invoices[nota_id][${trId}][]"]`);
            
            // Mulai Perhitungan
            if (relatedNotYetPaidOffInvoices.length > 0) {
                relatedNotYetPaidOffInvoices.forEach(invoice => {
                    let tdAmountPaid = document.getElementById(`td-related_not_yet_paid_off_invoices[amount_paid]-${trId}-${invoice.value}`);
                    let amountPaid = document.getElementById(`related_not_yet_paid_off_invoices[amount_paid]-${trId}-${invoice.value}`);
                    let amountPaidReal = document.getElementById(`related_not_yet_paid_off_invoices[amount_paid]-${trId}-${invoice.value}-real`);
                    let amountDue = document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${trId}-${invoice.value}`);
                    let amountDueReal = document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${trId}-${invoice.value}-real`);
                    let amountDueRealUnchanged = document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${trId}-${invoice.value}-real-unchanged`);
                    let paymentStatus = document.getElementById(`related_not_yet_paid_off_invoices[payment_status]-${trId}-${invoice.value}`);
                    let amountPaidRealValue = parseFloat(amountPaidReal.value);
                    let amountDueRealUnchangedValue = parseFloat(amountDueRealUnchanged.value);
                    let amountDueRealValue = parseFloat(amountDueReal.value);
                    
                    if (invoice.checked) {
                        // console.log('checked')
                        if (tdAmountPaid.classList.contains('hidden')) {
                            tdAmountPaid.classList.remove('hidden');
                        }
                        // console.log(`amountPaidReal.value = ${amountPaidReal.value}`);
                        if (amountPaidReal.value == 0) {
                            // console.log('amountPaidReal.value == 0');
                            if (remainingBalanceMasukReal.value >= amountDueRealValue) {
                                // console.log('remainingBalanceMasukReal.value >= amountDueRealValue');
                                amountPaidReal.value = amountDueReal.value;
                                remainingBalanceMasukReal.value -= amountDueRealValue;
                            } else {
                                // console.log('remainingBalanceMasukReal.value < amountDueRealValue');
                                amountPaidReal.value = remainingBalanceMasukReal.value;
                                remainingBalanceMasukReal.value = 0;
                            }
                        } else {
                            // console.log('amountPaidRealValue !== 0')
                            if (remainingBalanceMasukReal.value >= amountPaidRealValue) {
                                remainingBalanceMasukReal.value -= amountPaidRealValue;
                            } else {
                                amountPaidRealValue = remainingBalanceMasukReal.value;
                                remainingBalanceMasukReal.value = 0;
                            }
                        }
                    } else {
                        if (!tdAmountPaid.classList.contains('hidden')) {
                            tdAmountPaid.classList.add('hidden');
                        }
                        amountPaid.value = 0;
                        amountPaidReal.value = 0;
                    }
                    amountDueReal.value = amountDueRealUnchanged.value - amountPaidReal.value;
                    amountPaid.value = formatHargaIndo(amountPaidReal.value);
                    amountDue.value = formatHargaIndo(amountDueReal.value);
                    remainingBalanceMasuk.innerHTML = formatHargaIndo(remainingBalanceMasukReal.value);
                    // console.log('remainingBalanceMasukReal:', remainingBalanceMasukReal.value);
                    // Update payment_status
                    // all numbers to be compared in floating number for the true results
                    setTimeout(() => {
                        console.log(amountPaidRealValue, amountDueRealValue);
                        // console.log(amountDueRealValue);
                        if (amountPaidRealValue >= amountDueRealValue) {
                            paymentStatus.value = 'lunas';
                        } else if (amountPaidRealValue == 0) {
                            paymentStatus.value = 'belum_lunas';
                        } else {
                            paymentStatus.value = 'sebagian'
                        }
                        console.log(paymentStatus.value);
                    }, 1000);
                });
            }
        }

        function recalculateSaldoAndOverpayment(trId) {
            let checkboxSaldo = document.getElementById(`checkbox-saldo-${trId}`);
            let customerBalanceRecalculate = document.getElementById(`saldo-${trId}-recalculate`);
            let customerBalanceReal = document.getElementById(`saldo-${trId}-real`);
            let customerBalanceRealUnchanged = document.getElementById(`saldo-${trId}-real-unchanged`);
            // set nilai awal dikembalikan ke semula
            customerBalanceReal.value = customerBalanceRealUnchanged.value;
            let customerBalanceRealValue = parseFloat(customerBalanceReal.value);

            if (!checkboxSaldo.checked || customerBalanceRealValue == 0) {
                return false;
            }
            
            let relatedNotYetPaidOffInvoices = document.querySelectorAll(`input[name="related_not_yet_paid_off_invoices[nota_id][${trId}][]"]`);
            if (relatedNotYetPaidOffInvoices.length > 0) {
                // looping untuk set nilai awal kembali ke semula
                relatedNotYetPaidOffInvoices.forEach(invoice => {
                    let amountDueReal = document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${trId}-${invoice.value}-real`);
                    let amountDueRealUnchanged = document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${trId}-${invoice.value}-real-unchanged`);
                    amountDueReal.value = amountDueRealUnchanged.value;
                });
                
                // looping untuk mulai perhitungan
                relatedNotYetPaidOffInvoices.forEach(invoice => {
                    let amountDueReal = document.getElementById(`related_not_yet_paid_off_invoices[amount_due]-${trId}-${invoice.value}-real`);
                    let amountDueRealValue = parseFloat(amountDueReal.value);
                    
                    if (invoice.checked) {
                        if (tdAmountPaid.classList.contains('hidden')) {
                            tdAmountPaid.classList.remove('hidden');
                        }
                        if (customerBalanceRealValue <= amountDueRealValue) {
                            customerBalanceReal.value = 0
                            amountDueReal.value = amountDueRealValue - customerBalanceRealValue;
                        } else {
                            customerBalanceReal.value = customerBalanceRealValue - amountDueRealValue;
                            amountDueReal.value = 0;
                        }
                    } else {
                        if (!tdAmountPaid.classList.contains('hidden')) {
                            tdAmountPaid.classList.add('hidden');
                        }
                    }
                    
                });
            }
        }
    </script>
</div>