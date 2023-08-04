function toggleMenu(menu_id, close_layer_id) {
    $(`#${menu_id}`).toggle(350);
    $(`#${close_layer_id}`).show();
}

function hideMenuCloseLayer(menu_id, close_layer_id) {
    console.log("hideMenuCloseLayer");
    $(`#${menu_id}`).hide(350);
    $(`#${close_layer_id}`).hide();
}

function toggle_element(element_id) {
    $(`#${element_id}`).toggle(300);
}

function toggle_light(btn_id, id, classes_to_remove, classes_to_add, display_ref) {
    $(`#${id}`).toggle(300);
    setTimeout(() => {
        let display = $(`#${id}`).css('display');
        // console.log(display);
        let detail_button = document.getElementById(btn_id)
        if (display === display_ref) {
            classes_to_remove.forEach(element => {
                detail_button.classList.remove(element);
            });
            classes_to_add.forEach(element => {
                detail_button.classList.add(element);
            });
        } else {
            classes_to_remove.forEach(element => {
                detail_button.classList.add(element);
            });
            classes_to_add.forEach(element => {
                detail_button.classList.remove(element);
            });
        }
    }, 500);
}

function formatHarga(harga) {
    // console.log(harga);
    let harga_ohne_titik = harga.replace(".", "");
    if (harga_ohne_titik.length < 4) {
        return harga;
    }
    let hargaRP = "";
    let akhir = harga_ohne_titik.length;
    let posisi = akhir - 3;
    let jmlTitik = Math.ceil(harga_ohne_titik.length / 3 - 1);
    // console.log(jmlTitik);
    for (let i = 0; i < jmlTitik; i++) {
        hargaRP = "." + harga_ohne_titik.slice(posisi, akhir) + hargaRP;
        // console.log(hargaRP);
        akhir = posisi;
        posisi = akhir - 3;
    }
    hargaRP = harga_ohne_titik.slice(0, akhir) + hargaRP;
    return hargaRP;
}

// function formatNumber(number, element) {
//     // console.log('formatNumber');
//     // console.log(element);
//     if (isNaN(number)) {
//         console.log("NAN");
//         number = 0;
//     }
//     var formatted_number = formatHarga(number.toString());
//     if (element == null) {
//         return formatted_number;
//     } else {
//         element.textContent = formatted_number;
//         return true;
//     }
// }

function formatCurrencyRp(number, element) {
    // console.log(element);
    var formatted_number = formatHarga(number.toString());
    if (element == null) {
        return formatted_number;
    } else {
        element.innerHTML = `<div><div class="d-flex justify-content-between"><span>Rp</span><span>${formatted_number},-</span></div></div>`;
        // console.log(element);
        return true;
    }
}

function formatNumberK(number, element) {
    // console.log(element);

    number = Math.ceil(number / 1000);
    // console.log(number);
    var formatted_number = formatHarga(number.toString());
    if (element == null) {
        return formatted_number;
    } else {
        element.textContent = formatted_number + "k";
        return true;
    }
}

function formatNumberHargaRemoveDecimal(harga) {
    // console.log(harga);
    let harga_2 = "";
    if (harga.includes(".")) {
        let harga_1 = harga.slice(0, harga.indexOf("."));
        harga_2 = harga.slice(harga.indexOf("."), harga.length);
        // console.log(harga_1); console.log(harga_2);
        harga = harga_1;
        if (parseInt(harga_2[1]) >= 5) {
            harga = (parseInt(harga) + 1).toString();
        }
    }
    let harga_ohne_titik = harga.replace(".", "");
    if (harga_ohne_titik.length < 4) {
        return harga;
    }
    let hargaRP = "";
    let akhir = harga_ohne_titik.length;
    let posisi = akhir - 3;
    let jmlTitik = Math.ceil(harga_ohne_titik.length / 3 - 1);
    // console.log(jmlTitik);
    for (let i = 0; i < jmlTitik; i++) {
        hargaRP = "." + harga_ohne_titik.slice(posisi, akhir) + hargaRP;
        // console.log(hargaRP);
        akhir = posisi;
        posisi = akhir - 3;
    }
    hargaRP = harga_ohne_titik.slice(0, akhir) + hargaRP;
    // console.log(hargaRP);
    return hargaRP;
    // console.log(harga_2);
    // return (parseFloat(hargaRP) + harga_2).toString();
}

function pin_formatted_number_on_certain_element(value, element_id) {
    document.getElementById(element_id).textContent =
        formatNumberHargaRemoveDecimal(value.toString());
}

function formatNumber(ipt, hidden_id) {
    // console.log(ipt);
    // console.log(isNaN(ipt.value));
    var num = parseFloat(ipt.value.split(".").join(""));
    document.getElementById(hidden_id).value = num;
    // console.log(ipt.value, num);
    if (!isNaN(num)) {
        ipt.value = num.toLocaleString("id-ID", {
            style: "decimal",
        });
    }
}

function remove_element_confirm(id, confirm_message) {
    if (confirm(confirm_message)) {
        document.getElementById(id).remove();
    }
}

// FUNGSI - PHOTO
function preview_photo(
    input_id,
    container_preview_photo_id,
    preview_photo_id,
    label_choose_photo_id
) {
    const el_input = document.getElementById(input_id);
    const el_container_preview_photo = document.getElementById(
        container_preview_photo_id
    );
    const el_preview_photo = document.getElementById(preview_photo_id);
    const el_label_choose_photo = document.getElementById(
        label_choose_photo_id
    );
    // console.log(el_input.files[0]);
    const blob = URL.createObjectURL(el_input.files[0]);
    el_preview_photo.src = blob;
    el_container_preview_photo.classList.remove("hidden");
    el_label_choose_photo.classList.add("hidden");
}

function remove_photo(
    input_id,
    container_preview_photo_id,
    preview_photo_id,
    label_choose_photo_id
) {
    const el_input = document.getElementById(input_id);
    const el_container_preview_photo = document.getElementById(
        container_preview_photo_id
    );
    const el_preview_photo = document.getElementById(preview_photo_id);
    const el_label_choose_photo = document.getElementById(
        label_choose_photo_id
    );
    el_input.value = null;
    el_preview_photo.src = null;
    console.log(el_container_preview_photo);
    el_container_preview_photo.classList.add("hidden");
    el_label_choose_photo.classList.remove("hidden");
}
