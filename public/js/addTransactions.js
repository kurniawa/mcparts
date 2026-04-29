function getHtmlRemainingBalance(kategori_level_one, data, index_nota, trId) {
    let htmlRemainingBalanceMasuk = `<td rowspan="${data.notas.length}" style="vertical-align: top;">
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
    let labelBalanceMasuk = 'Dari Balance.M';

    let htmlRemainingBalanceKeluar = `<td rowspan="${data.notas.length}" style="vertical-align: top;">
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
    let labelBalanceKeluar = 'Dari Balance.K';

    // let htmlRemainingBalance = '';
    // if (kategori_level_one === 'PENERIMAAN PIUTANG') {
    //     htmlRemainingBalance = htmlRemainingBalanceMasuk;
    // } else if (kategori_level_one === 'BAYAR HUTANG BAHAN BAKU') {
    //     htmlRemainingBalance = htmlRemainingBalanceKeluar;
    // }

    let htmlRemainingBalance = kategori_level_one === 'PENERIMAAN PIUTANG' ? htmlRemainingBalanceMasuk : kategori_level_one === 'BAYAR HUTANG BAHAN BAKU' ? htmlRemainingBalanceKeluar : '';
    let labelBalance = kategori_level_one === 'PENERIMAAN PIUTANG' ? labelBalanceMasuk : kategori_level_one === 'BAYAR HUTANG BAHAN BAKU' ? labelBalanceKeluar : '';

    if (index_nota !== 0) {
        htmlRemainingBalance = '';
    }

    return [htmlRemainingBalance, labelBalance];
}

function recalculateBalanceKeluar_TotalDue_TotalPaid_ForBayarBahanBaku(trId) {
    // Set the initial value: remainingBalance
    let kategoriLevelOne = document.getElementById(`input-kategori-level-one-${trId}`).value;
    // JUMLAH BAYAR
    let remainingBalanceKeluar = document.getElementById(`remaining_balance_keluar-${trId}`);
    let remainingBalanceKeluarReal = document.getElementById(`remaining_balance_keluar-${trId}-real`);

    let keluarReal = document.getElementById(`keluar-${trId}-real`);
    let keluarRealValue = 0;
    if (keluarReal) {
        keluarRealValue = keluarReal.value;
    }

    remainingBalanceKeluar.innerHTML = formatHargaIndo(keluarRealValue);
    remainingBalanceKeluarReal.value = keluarRealValue;
    let remainingBalanceKeluarRealValue = parseFloat(remainingBalanceKeluarReal.value);

    let sisaSaldo = document.getElementById(`sisa-saldo-${trId}`);
    let sisaSaldoReal = document.getElementById(`sisa-saldo-${trId}-real`);
    let saldoAwal = document.getElementById(`saldo-awal-${trId}-real`);
    let sisaSaldoRealValue = parseFloat(saldoAwal.value);

    // console.log('keluarReal.value:', keluarReal.value);
    // console.log('keluarReal', keluarReal);
    // console.log('remainingBalanceKeluar', remainingBalanceKeluar);
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
            remainingBalanceKeluarRealValue = remainingBalanceKeluarRealValue - amountPaidRealValue;
            remainingBalanceKeluarReal.value = remainingBalanceKeluarRealValue;
            remainingBalanceKeluar.innerHTML = formatHargaIndo(remainingBalanceKeluarRealValue);

            // Menentukan status_bayar
            setTimeout(() => {
                // console.log(amountPaidRealValue, amountDueRealValue);
                // console.log(amountDueRealValue);
                if (amountDueRealValue <= 0) {
                    paymentStatus.value = 'LUNAS';
                } else if (amountDueRealValue == (amountDueRealUnchangedValue-totalDiscountRealValue) || amountDueRealValue == totalPriceValue) {
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