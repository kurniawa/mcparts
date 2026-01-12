@extends('layouts.main')
@section('content')
  <main>
        <div class="mx-1 py-1 sm:px-6 lg:px-8 text-xs">
            <x-errors-any></x-errors-any>
            <x-validation-feedback></x-validation-feedback>
            <div>
                <h1 class="font-bold text-md">Search Related Accounting</h1>
            </div>
            <div class="border-t-4 pt-2"></div>
            <div class="flex gap-1">
                <div class="border rounded p-1">
                    <div class="flex">
                        <table class="">
                            <tr>
                                <td>No.</td><td>:</td><td><div class="font-bold text-sm text-slate-500">{{ $nota['no_nota'] }}</div></td>
                                <td class="align-top">Alamat</td><td class="align-top">:</td>
                                <td class="align-top">
                                    @if ($nota['cust_long']!==null)
                                    @foreach (json_decode($nota['cust_long'],true) as $long)
                                    <div>{{ $long }}</div>
                                    @endforeach
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Tgl.</td><td>:</td>
                                <td>
                                    <div class="flex">
                                        <div class="flex">
                                            @if ($nota['finished_at'] === null)
                                            <div>
                                                <div class="rounded p-1 bg-red-500 text-white font-bold text-center">
                                                    <div>{{ date('d',strtotime($nota['created_at'])) }}</div>
                                                    <div>{{ date('m-y',strtotime($nota['created_at'])) }}</div>
                                                </div>
                                            </div>
                                            @else
                                            <div>
                                                <div class="rounded p-1 bg-yellow-500 text-white font-bold text-center">
                                                    <div>{{ date('d',strtotime($nota['created_at'])) }}</div>
                                                    <div>{{ date('m-y',strtotime($nota['created_at'])) }}</div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="flex ml-1 items-center">
                                            @if ($nota['finished_at'] !== null)
                                            <div>
                                                <div class="rounded p-1 bg-emerald-500 text-white font-bold text-center">
                                                    <div>{{ date('d',strtotime($nota['finished_at'])) }}</div>
                                                    <div>{{ date('m-y',strtotime($nota['finished_at'])) }}</div>
                                                </div>
                                            </div>
                                            @else
                                            <span class="font-bold">---</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>Kontak</td><td>:</td>
                                <td>
                                    @if (isset($cust_kontaks) && $cust_kontaks[$key_nota]!==null)
                                    {{ $cust_kontaks[$key_nota] }}
                                    @else-@endif
                                </td>
                            </tr>
                            <tr><td>Pelanggan</td><td>:</td><td>{{ $nota->pelanggan_nama }}</td></tr>
                            {{-- Nota Items --}}
                            <tr>
                                <td colspan="6">
                                    <div class="border rounded px-1 py-2" id="nota-items">
                                        <table class="text-xs small-padding-td">
                                            <tr>
                                                <th>Jml.</th>
                                                <th>Nama Barang</th>
                                                <th>Hrg.</th>
                                                <th>Hrg. t</th>
                                            </tr>
                                            <tr><td><div class="text-center">---</div></td><td><div class="text-center">-----</div></td><td><div class="text-center">---</div></td><td><div class="text-center">---</div></td></tr>
                                            @foreach ($nota->spk_produk_notas as $spk_produk_nota)
                                            <tr>
                                                <td><div class="text-center">{{ $spk_produk_nota->jumlah }}</div></td>
                                                <td>
                                                    <div>
                                                        {{ $spk_produk_nota->nama_nota }}
                                                    </div>
                                                    @if ($spk_produk_nota->keterangan)
                                                    <div class="border text-slate-400 italic rounded">
                                                        {{ $spk_produk_nota->keterangan }}
                                                    </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="text-center">{{ number_format($spk_produk_nota->harga,0,',','.') }}</div>
                                                </td>
                                                <td><div class="text-center">{{ number_format($spk_produk_nota->harga_t,0,',','.') }}</div></td>
                                            </tr>
                                            @endforeach
                                            <tr><td>---</td><td>-----</td><td>---</td><td>---</td></tr>
                                            <tr><td></td><td class="font-bold">Harga Total</td><td></td><td>{{ number_format($nota->harga_total,0,',','.') }}</td></tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            {{-- END - Nota Items --}}
                        </table>
                    </div>
                </div>
                {{-- Possible related Accounting --}}
                <div class="border rounded p-1 flex-1">
                    <div class="font-bold text-sm mb-1">Possible Related Accounting</div>
                    <div>
                        @if (count($nota->possible_related_accountings())===0)
                        <div class="italic text-slate-400">No related accounting found.</div>
                        @else
                        <table class="text-xs small-padding-td">
                            <tr>
                                <th>Tgl.</th>
                                <th>Kode</th>
                                <th>Keterangan</th>
                                <th>Jumlah</th>
                                <th></th>
                            </tr>
                            @foreach ($possible_related_accountings as $key_accounting => $accounting)
                            <tr>
                                <td>{{ date('d-m-Y',strtotime($accounting->created_at)) }}</td>
                                <td>{{ $accounting->kode }}</td>
                                <td>{{ $accounting->transaction_desc }}</td>
                                <td>
                                    <div class="text-right">
                                        {{ number_format($accounting->jumlah,0,',','.') }}
                                    </div>
                                </td>
                                
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <form action="{{ route('accounting.link_nota_accounting', ['nota'=>$nota->id,'accounting'=>$accounting->id]) }}" method="POST">
                                        @csrf
                                        <table>
                                            <tr><th>Balance</th><th>Amount Due</th><th>Discount</th><th>Balance Used</th></tr>
                                            <tr>
                                                <td>
                                                    <div id="remaining_balance-{{ $key_accounting }}">{{ number_format($accounting->jumlah, 0, ',', '.') }}</div>
                                                    <input type="hidden" id="remaining_balance-{{ $key_accounting }}-real" name="remaining_balance" value="{{ $accounting->jumlah }}">
                                                    <input type="hidden" name="balance_start" id="balance_start-{{ $key_accounting }}" value="{{ $accounting->jumlah }}">
                                                    <input type="hidden" name="total_price" id="total_price-{{ $key_accounting }}" value="{{ $nota->harga_total }}">
                                                </td>
                                                <td>
                                                    <div>
                                                        <span class="text-orange-400">=><input type="text" id="amount_due-{{ $key_accounting }}" value="{{ number_format($nota->amount_due, 0, ',', '.') }}" class="text-xs p-0 border-none" readonly></span>
                                                        <input type="hidden" name="amount_due_new" id="amount_due-{{ $key_accounting }}-real" value="{{ $nota->amount_due }}">
                                                        <input type="hidden" name="amount_paid" id="amount_paid-{{ $key_accounting }}-real" value="{{ $nota->amount_paid }}">
                                                        <input type="hidden" id="amount_due-{{ $key_accounting }}-start" value="{{ $nota->amount_due }}">
                                                    </div>
                                                    <div class="text-xs text-center">
                                                        <span class="text-emerald-400">=><input type="text" id="payment_status-{{ $key_accounting }}" name="payment_status" class="text-xs p-0 border-none" value="{{ $nota->status_bayar }}" readonly></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="flex w-full">
                                                        <input type="number" id="discount_percentage-{{ $key_accounting }}" name="discount_percentage" value="0" max="100" class="text-xs p-0 pl-1 w-full">
                                                        <span>%</span>
                                                        <input type="text" id="percent_discount-{{ $key_accounting }}" value="0" class="text-xs p-0 pl-1 w-full bg-slate-200" readonly>
                                                    </div>
                                                    <div class="grid grid-cols-5">
                                                        <input type="text" id="other_discount-{{ $key_accounting }}" value="0" class="text-xs p-0 pl-1 col-span-2">
                                                        <input type="text" id="total_discount-{{ $key_accounting }}" value="0" class="text-xs p-0 pl-1 col-span-3 bg-slate-200" readonly>
                                                    </div>
                                                    <input type="text" name="discount_description" placeholder="keterangan diskon" class="text-xs p-0 pl-1 w-full">
                                                    <input type="hidden" name="percent_discount" id="percent_discount-{{ $key_accounting }}-real" value="0">
                                                    <input type="hidden" name="other_discount" id="other_discount-{{ $key_accounting }}-real" value="0">
                                                    <input type="hidden" name="total_discount" id="total_discount-{{ $key_accounting }}-real" value="0">
                                                </td>
                                                <td>
                                                    <input type="text" id="balance_used-{{ $key_accounting }}" value="0" class="text-xs p-1">
                                                    <input type="hidden" id="balance_used-{{ $key_accounting }}-real" name="balance_used" value="0">
                                                </td>
                                            </tr>
                                        </table>
                                        <div>
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs">Link</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                        @endif
                    </div>
                </div>
                {{-- END - Possible related Accounting --}}
            </div>
        </div>
        <style>
            .small-padding-td td {
                padding-left: 0.25rem;
                padding-right: 0.25rem;
            }
        </style>
  </main>

<script>
    const possible_related_accountings = @json($possible_related_accountings);
    possible_related_accountings.forEach((element, index) => {
        applyFormatNumber(`percent_discount-${index}`);
        applyFormatNumberAndEvent(`other_discount-${index}`, index);
        applyFormatNumber(`total_discount-${index}`);
        applyFormatNumberAndEvent(`balance_used-${index}`, index);
        applyEvent(`discount_percentage-${index}`, index);
    });

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

    function recalculateBalanceMasuk_TotalDue_TotalPaid(trId) {
        // Set the initial value: remainingBalance
        let remainingBalance = document.getElementById(`remaining_balance-${trId}`);
        let remainingBalanceReal = document.getElementById(`remaining_balance-${trId}-real`);
        let balanceStart = document.getElementById(`balance_start-${trId}`);
        let amountPaidReal = document.getElementById(`amount_paid-${trId}-real`);
        let amountDue = document.getElementById(`amount_due-${trId}`);
        let amountDueReal = document.getElementById(`amount_due-${trId}-real`);
        let amountDueStart = document.getElementById(`amount_due-${trId}-start`);
        let paymentStatus = document.getElementById(`payment_status-${trId}`);
        let discountPercentage = document.getElementById(`discount_percentage-${trId}`);
        let percentDiscount = document.getElementById(`percent_discount-${trId}`);
        let percentDiscountReal = document.getElementById(`percent_discount-${trId}-real`);
        let otherDiscount = document.getElementById(`other_discount-${trId}`);
        let otherDiscountReal = document.getElementById(`other_discount-${trId}-real`);
        let totalDiscount = document.getElementById(`total_discount-${trId}`);
        let totalDiscountReal = document.getElementById(`total_discount-${trId}-real`);
        let balanceUsed = document.getElementById(`balance_used-${trId}`);
        let balanceUsedReal = document.getElementById(`balance_used-${trId}-real`);
        let totalPrice = document.getElementById(`total_price-${trId}`);
        
        // parseFloat beberapa Value
        let discountPercentageValue = parseFloat(discountPercentage.value);
        let otherDiscountRealValue = parseFloat(otherDiscountReal.value);
        let balanceUsedRealValue = parseFloat(balanceUsedReal.value);
        let remainingBalanceRealValue = parseFloat(balanceStart.value);
        let amountPaidRealValue = balanceUsedRealValue;
        let amountDueStartValue = parseFloat(amountDueStart.value);
        let amountDueRealValue = amountDueStartValue;
        let totalPriceValue = parseFloat(totalPrice.value);

        // Hitung Potongan Harga
        let percentDiscountRealValue = (discountPercentageValue) * amountDueRealValue;
        percentDiscountReal.value = percentDiscountRealValue;
        percentDiscount.value = formatHargaIndo(percentDiscountRealValue);
        let totalDiscountRealValue = percentDiscountRealValue + otherDiscountRealValue;
        otherDiscount.value = formatHargaIndo(otherDiscountRealValue);
        totalDiscountReal.value = totalDiscountRealValue;
        totalDiscount.value = formatHargaIndo(totalDiscountRealValue);
        // console.log("discountPercentageValue", discountPercentageValue);
        // console.log("totalDiscountRealValue", totalDiscountRealValue);

        // Hitung Sisa Bayar
        amountDueRealValue = amountDueRealValue - totalDiscountRealValue - balanceUsedRealValue;
        amountDueReal.value = amountDueRealValue;
        amountDue.value = formatHargaIndo(amountDueRealValue); // Format angka yang ditampilkan
        
        // Hitung Sisa Balance
        remainingBalanceRealValue = remainingBalanceRealValue - balanceUsedRealValue;
        remainingBalanceReal.value = remainingBalanceRealValue;
        remainingBalance.innerHTML = formatHargaIndo(remainingBalanceRealValue);

        // Hitung Jumlah Bayar
        amountPaidReal.value = amountPaidRealValue;

        // Menentukan status_bayar
        setTimeout(() => {
            // console.log(amountPaidRealValue, amountDueRealValue);
            // console.log(amountDueRealValue);
            if (amountDueRealValue <= 0) {
                paymentStatus.value = 'lunas';
            } else if (amountDueRealValue == (amountDueStartValue-totalDiscountRealValue) || amountDueRealValue == totalPriceValue) {
                paymentStatus.value = 'belum_lunas'; 
            } else if (amountDueRealValue > 0 && (amountDueRealValue < (amountDueStartValue-totalDiscountRealValue) || amountDueRealValue < totalPriceValue)) {
                paymentStatus.value = 'sebagian';
            }
            // console.log(paymentStatus.value);
        }, 1000);
    }
</script>
@endsection
