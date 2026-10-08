// console.log(supplierID);
$("#pembelian-new-supplier-nama").autocomplete({
    source: labelSupplier,
    select: function(event, ui) {
        $("#pembelian-new-supplier-id").val(ui.item.id);
        $("#pembelian-new-supplier-nama").val(ui.item.value);
        supplierID = ui.item.id;
    }
});

function addItem(trID, parentID) {
    document.getElementById(trID).remove();
    let parent = document.getElementById(parentID);
    let html_pembelian_barang_id = "";
    if (mode === 'edit') {
        html_pembelian_barang_id = `<input type="hidden" name="pembelian_barang_id[]" value="new" id="pembelian-barang-id-${indexItem}">`;
    }
    parent.insertAdjacentHTML('beforeend',
    `<tr id="tr-barang-${indexItem}">
        <td>
            <div class="flex items-center mt-1">
                <button id="toggle-barang-keterangan-${indexItem}" type="button" class="border border-yellow-500 rounded text-yellow-500" onclick="toggle_light(this.id,'barang-keterangan-${indexItem}', [], ['bg-yellow-300'], 'block')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>
                <input type="text" name="barang_nama[]" id="barang-nama-${indexItem}" class="border-slate-300 rounded-lg text-xs p-1 ml-1 placeholder:text-slate-400 w-56" placeholder="nama item...">
                <input type="hidden" name="barang_id[]" id="barang-id-${indexItem}">
                ${html_pembelian_barang_id}
            </div>
            <div class="mt-1 hidden" id="barang-keterangan-${indexItem}">
                <textarea name="barang_keterangan[]" cols="30" rows="3" class="border-slate-300 rounded-lg text-xs p-0 placeholder:text-slate-400" placeholder="keterangan item..."></textarea>
            </div>
        </td>
        <td>
            <div class="text-center">
                <div class="flex items-center">
                    <input type="text" name="jumlah_sub[]" id="jumlah-sub-${indexItem}" min="1" step="1" class="border-slate-300 rounded-lg text-xs p-1 w-1/2" oninput="countHargaTotal(${indexItem})">
                    <span id="satuan-sub-${indexItem}" class="ml-1"></span>
                </div>
            </div>
        </td>
        <td>
            <div class="text-center">
                <div class="flex items-center">
                    <input type="text" name="jumlah_main[]" id="jumlah-main-${indexItem}" min="1" step="1" class="border-slate-300 rounded-lg text-xs p-1 w-1/2" oninput="countHargaTotal(${indexItem})">
                    <span class="satuan-main-${indexItem} ml-1"></span>
                </div>
            </div>
        </td>
        <td>
            <div class="text-center">
                <div class="flex items-center">
                    <input type="text" id="harga-main-${indexItem}" min="1" step="1" class="border-slate-300 rounded-lg text-xs p-1 w-3/4" onchange="formatNumber(this, 'harga-main-real-${indexItem}'); countHargaTotal(${indexItem})">/<span class="satuan-main-${indexItem} ml-1"></span>
                    <input type="hidden" name="harga_main[]" id="harga-main-real-${indexItem}">
                </div>
            </div>
        </td>
        <td>
            <div class="text-center">
                <div class="flex">
                    <input type="text" id="harga-t-${indexItem}" min="1" step="1" class="border-slate-300 rounded-lg text-xs p-1 w-full" onchange="formatNumber(this, 'harga-t-real-${indexItem}');">
                    <input type="hidden" name="harga_t[]" id="harga-t-real-${indexItem}" class="harga-t-real">
                </div>
            </div>
        </td>
        <td>
            <button type="button" class="text-red-500" onclick="remove_item(${indexItem})">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
        </td>
    </tr>
    <tr id="tr-add-item">
        <td>
            <button type="button" class="rounded bg-emerald-200 text-emerald-600" onclick="addItem('tr-add-item', 'table-pembelian-items')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </button>
        </td>
    </tr>
    `);
    setTimeout(() => {
        setAutocompleteItem(indexItem);
        indexItem++;
    }, 100);
}

function setAutocompleteItem(index) {
    let hargaMain = document.getElementById(`harga-main-${index}`);
    let hargaT = document.getElementById(`harga-t-${index}`);
    // console.log(labelBarang);
    let labelBarangFiltered = labelBarang.filter(item => item.supplier_id == supplierID);
    // console.log(labelBarangFiltered);
    // return;
    $(`#barang-nama-${index}`).autocomplete({
        source: labelBarangFiltered,
        select: function (event, ui) {
            // console.log(ui.item);
            document.getElementById(`barang-nama-${index}`).value = ui.item.value;
            document.getElementById(`barang-id-${index}`).value = ui.item.id;
            document.getElementById(`satuan-sub-${index}`).textContent = ui.item.satuan_sub;
            if (ui.item.satuan_sub !== null) {
                document.getElementById(`jumlah-sub-${index}`).value = 1;
            }
            let satuan_mains = document.querySelectorAll(`.satuan-main-${index}`);
            for (let index = 0; index < satuan_mains.length; index++) {
                satuan_mains[index].textContent = ui.item.satuan_main;
            }
            document.getElementById(`jumlah-main-${index}`).value = ui.item.jumlah_main;
            hargaMain.value = ui.item.harga_main;
            document.getElementById(`harga-main-real-${index}`).value = ui.item.harga_main;
            hargaT.value = ui.item.harga_total_main;
            document.getElementById(`harga-t-real-${index}`).value = ui.item.harga_total_main;
            formatNumber(hargaMain, `harga-main-real-${index}`);
            formatNumber(hargaT, `harga-t-real-${index}`);
            countHargaTotal(index);
        }
    });
}

function countHargaTotal(index) {
    let jumlahSub = document.getElementById(`jumlah-sub-${index}`).value;
    if (jumlahSub === '') {
        jumlahSub = 1;
    }
    let jumlahMain = document.getElementById(`jumlah-main-${index}`).value;
    let hargaMain = document.getElementById(`harga-main-real-${index}`).value;
    let hargaTotalEl = document.getElementById(`harga-t-${index}`);
    let hargaTotal = parseInt(jumlahSub) * parseInt(jumlahMain) * parseFloat(hargaMain);
    
    hargaTotalEl.value = hargaTotal;
    formatNumber(hargaTotalEl, `harga-t-real-${index}`);
    let hargaTRealAll = document.querySelectorAll('.harga-t-real');

    let hargaTotalPembelian = 0;
    hargaTRealAll.forEach(harga_t => {
        hargaTotalPembelian += parseFloat(harga_t.value);
    });
    let hargaTotalPembelianEl = document.getElementById('harga-total-pembelian');
    hargaTotalPembelianEl.value = hargaTotalPembelian;
    formatNumber(hargaTotalPembelianEl, 'harga-total-pembelian-real');
}

// FUNGSI BARANG
function countHargaTotalMain() {
    let hargaMain = document.getElementById('barang-new-harga-main-real').value;
    let jumlahMain = document.getElementById('barang-new-jumlah-main').value;
    let hargaTotalMainEl = document.getElementById('barang-new-harga_total_main');

    let hargaTotalMain = 0;
    if (jumlahMain !== '' && hargaMain !== '') {
        hargaTotalMain = jumlahMain * hargaMain;
        hargaTotalMainEl.value = hargaTotalMain;
        formatNumber(hargaTotalMainEl, 'barang-new-harga-total-main-real');
        let harga_sub = document.getElementById('barang-new-harga-sub');
        harga_sub.value = hargaTotalMain;
        formatNumber(harga_sub, 'barang-new-harga-sub-real');
    }
    // console.log(hargaMain);
    console.log(jumlahMain);
    // console.log(hargaTotalMain);
}

function copyToHargaSub() {
    let hargaTotalMainReal = document.getElementById('barang-new-harga-total-main-real');
    // console.log(hargaTotalMainReal.value);
    let hargaSub = document.getElementById('barang-new-harga-sub');

    hargaSub.value = hargaTotalMainReal.value;
    formatNumber(hargaSub, 'barang-new-harga-sub-real')
}

function countHargaTotalSub() {
    let hargaSub = document.getElementById('barang-new-harga-sub-real').value;
    let jumlahSub = document.getElementById('barang-new-jumlah-sub').value;
    let hargaTotalSubEl = document.getElementById('barang-new-harga-total-sub');

    let hargaTotalSub = 0;
    if (jumlahSub !== '' && hargaSub !== '') {
        hargaTotalSub = jumlahSub * hargaSub;
        hargaTotalSubEl.value = hargaTotalSub;
        formatNumber(hargaTotalSubEl, 'barang-new-harga-total-sub-real');
    }
}
// END - FUNGSI BARANG

function remove_item(index) {
    // console.log(index);
    document.getElementById(`tr-barang-${index}`).remove();
}