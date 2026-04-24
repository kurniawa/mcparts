<div>
    <script>
        // TEST
        function accountingGetRelatedInvoiceRawMaterials(transactionNameId, trId, kategori_level_one, kategori_type) {
            let trAddTransaction = document.getElementById(`tr_add_transaction-${trId}`);
            let elementToAppend = "";
            // Reset tr bahan baku jika sudah ada
            let resetElement = document.getElementById(`tr-bayar-bahan-baku-${trId}`);
            if (resetElement) {
                resetElement.remove();
            }
            // Reset tr bahan baku jika kategori_level_one bukan BAYAR HUTANG BAHAN BAKU
            let trErrorFeedback = document.getElementById(`tr-error-feedback-${trId}`);
            if (trErrorFeedback) {
                trErrorFeedback.remove();
            }
            if (kategori_level_one === "BAYAR HUTANG BAHAN BAKU") {
                $.ajax({
                    url: `/accounting/${transactionNameId}/get-related-not-yet-paid-off-invoices`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // console.log(data.message);
                        // console.log(data.notas);
                        // console.log(data.supplierBalance);
                        if (data.notas.length > 0) {
                            // let listOfInvoiceID = []; // untuk digunakan nanti pada saat validasi submit
                            elementToAppend += `<tr id="tr-bayar-bahan-baku-${trId}"><td colspan="6"><input id="input-kategori-level-one-${trId}" type="hidden" name="kategori_level_one[]" value="${kategori_level_one}"><div class="flex justify-center my-1"><div><table class="table-penerimaan-piutang"><tr><th></th><th>Nota</th><th>Harga Total</th><th>Sisa Bayar</th><th>Potongan Harga</th><th>Status Bayar</th><th>Total Bayar</th></tr>`;
                            let indexNota = 0;
                            let htmlRemainingBalanceKeluar = "";
                            data.notas.forEach(relatedInvoice => {
                                if (indexNota === 0) {
                                    htmlRemainingBalanceKeluar = `<td rowspan="${data.notas.length}" style="vertical-align: top;">
                                        <div class="font-bold">Balance.K</div>
                                        <div id="remaining_balance_keluar-${trId}" class="text-xs p-1">0</div>
                                        <input type="hidden" id="remaining_balance_keluar-${trId}-real" name="remaining_balance_keluar[${trId}]" value="0">
                                        <div id="div-saldo-${trId}">
                                            <div class="font-bold">Saldo Awal</div>
                                            <div id="saldo-awal-${trId}" class="text-xs p-1">${data.supplierBalance ? formatHargaIndo(data.supplierBalance.amount) : 0}</div>
                                            <input type="hidden" id="saldo-awal-${trId}-real" name="saldo_awal[${trId}]" value="${data.supplierBalance ? data.supplierBalance.amount : 0}" readonly>
                                            <div class="font-bold">Sisa Saldo</div>
                                            <div id="sisa-saldo-${trId}" class="text-xs p-1 text-indigo-500">${data.supplierBalance ? `=> ${formatHargaIndo(data.supplierBalance.amount)}` : 0}</div>
                                            <input type="hidden" id="sisa-saldo-${trId}-real" name="sisa_saldo[${trId}]" value="${data.supplierBalance ? data.supplierBalance.amount : 0}">
                                        </div>
                                    </td>
                                    `;
                                } else {
                                    htmlRemainingBalanceKeluar = "";
                                }
                                let linkUrl = relatedInvoice.table === 'notas' ? `/notas/${relatedInvoice.id}/show` : `/pembelians/${relatedInvoice.id}/show`;
                                elementToAppend += `
                                <tr>${htmlRemainingBalanceKeluar}
                                    <td class="font-bold">
                                        <label for="related_not_yet_paid_off_invoices[nota_id]" class="ml-1 hover:cursor-pointer"><a href="${linkUrl}" target="_blank">${relatedInvoice.nomor_nota}</a></label>
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
                                        <div class="text-xs text-center">
                                            <span class="text-emerald-400">=><input type="text" id="related_not_yet_paid_off_invoices[payment_status]-${trId}-${relatedInvoice.invoice_id}" name="related_not_yet_paid_off_invoices[payment_status][${trId}][]" class="text-xs p-0 border-none" value="${relatedInvoice.status_bayar}" readonly></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-bold text-xs">Dari Balance.K</div>
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
                                applyEvent(`related_not_yet_paid_off_invoices[discount_percent]-${trId}-${relatedInvoice.invoice_id}`, trId, 'PENGELUARAN');
                                applyFormatNumberAndEvent(`related_not_yet_paid_off_invoices[other_discount]-${trId}-${relatedInvoice.invoice_id}`, trId, 'PENGELUARAN');
                                applyFormatNumberAndEvent(`related_not_yet_paid_off_invoices[amount_paid]-${trId}-${relatedInvoice.invoice_id}`, trId, 'PENGELUARAN');
                                applyFormatNumberAndEvent(`related_not_yet_paid_off_invoices[balance_used]-${trId}-${relatedInvoice.invoice_id}`, trId, 'PENGELUARAN');
                            });
    
                            applyEvent(`keluar-${trId}`, trId, 'PENGELUARAN');
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
                        let trPenerimaanPiutang = document.getElementById(`tr-bayar-bahan-baku-${trId}`);
                        if (trPenerimaanPiutang) {
                            trPenerimaanPiutang.remove();
                        }
                        elementToAppend += `<tr id="tr-bayar-bahan-baku-${trId}" class="hidden"><td colspan="6">
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
    </script>
</div>