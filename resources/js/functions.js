$('#amount').on('input', function(event) {
    handleFormattedInput(event, 2);
});
// =========================
// FORMATTER
// =========================

function formatIndonesia(value, decimals = null) {
    if (value === '' || value === null || value === undefined) {
        return '';
    }

    const number =
        typeof value === 'number'
            ? value
            : parseIndonesiaNumber(value);

    if (isNaN(number)) return '';

    const options = {};

    if (decimals !== null) {
        options.minimumFractionDigits = decimals;
        options.maximumFractionDigits = decimals;
    }

    return new Intl.NumberFormat('id-ID', options).format(number);
}

function parseIndonesiaNumber(value) {
    if (!value) return 0;

    // hapus semua selain angka , .
    value = value.replace(/[^\d.,]/g, '');

    // hapus titik ribuan
    value = value.replace(/\./g, '');

    // ubah koma menjadi titik desimal
    value = value.replace(',', '.');

    return parseFloat(value) || 0;
}

// =========================
// CURSOR SAFE FORMATTER
// =========================

export function handleFormattedInput(event, decimals, callback) {
    const input = event.target;

    const selectionStart = input.selectionStart ?? 0;

    // simpan jumlah digit sebelum cursor
    // const rawBeforeCursor = input.value
    //     .slice(0, selectionStart)
    //     .replace(/[^\d]/g, '').length;

    // hanya angka koma titik
    let clean = input.value.replace(/[^\d.,]/g, '');

    // hanya 1 koma
    const commaParts = clean.split(',');

    if (commaParts.length > 2) {
        clean =
            commaParts[0] +
            ',' +
            commaParts.slice(1).join('');
    }

    // limit desimal
    if (clean.includes(',')) {
        const [intPart, decPart] = clean.split(',');

        clean =
            intPart +
            ',' +
            (decPart ?? '').slice(0, decimals);
    }

    // const numericValue = parseIndonesiaNumber(clean);

    let formatted = clean;

    if (clean !== '' && clean !== ',') {
        const hasComma = clean.includes(',');
        const [intPart, decPart = ''] = clean.split(',');

        formatted = formatIndonesia(
            parseIndonesiaNumber(intPart),
            null
        );

        if (hasComma) {
            formatted += ',' + decPart;
        }
    }

    // restore cursor
    const originalValue = input.value;

    input.value = formatted;

    requestAnimationFrame(() => {
        const beforeCursor = originalValue.slice(0, selectionStart);

        // hitung digit + koma sebelum cursor
        const normalizedBeforeCursor = beforeCursor.replace(/[^\d,]/g, '');

        let normalizedFormatted = '';
        let cursorPos = formatted.length;

        for (let i = 0; i < formatted.length; i++) {
            const char = formatted[i];

            if (/[\d,]/.test(char)) {
                normalizedFormatted += char;
            }

            if (normalizedFormatted === normalizedBeforeCursor) {
                cursorPos = i + 1;
                break;
            }
        }

        input.setSelectionRange(cursorPos, cursorPos);
    });

    callback?.();
}