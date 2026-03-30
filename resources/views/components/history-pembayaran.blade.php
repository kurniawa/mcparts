{{-- HISTORI PEMBAYARAN --}}
<div class="rounded shadow drop-shadow p-2 bg-white text-xs">
    <div class="font-bold">
        <p>status: {{ $nota['status_bayar'] }}</p>
        <p class="text-emerald-500">pembayaran: {{ number_format($nota['amount_paid'] + $nota['balance_used'],0,',','.') }}</p>
        <p class="text-red-500">sisa bayar: {{ number_format($nota['amount_due'],0,',','.') }}</p>
    </div>
    <h5 class="font-bold text-lg">Histori Pembayaran:</h5>
    @if (count($nota->accountingInvoices) === 0)
    <div class="text-center italic text-slate-500">kosong</div>
    @else
    <table id="histori-pembayaran-{{ $key_nota }}" class="w-full text-xs border border-collapse">
        <tr>
            <th>Tgl.</th><th>Sisa Bayar</th><th>Jumlah Bayar</th><th>Metode</th>
        </tr>
        @foreach ($nota->accountingInvoices->sortByDesc('created_at') as $key_acc_inv => $accountingInvoice)
        <tr>
            {{-- {{ dump($key_acc_inv) }} --}}
            <td class="text-center">{{ date('d-m-Y', strtotime($accountingInvoice->created_at)) }}</td>
            <td class="text-center">{{ number_format($accountingInvoice->amount_due,0,',','.') }}</td>
            <td class="text-center">{{ number_format(($accountingInvoice->amount_paid + $accountingInvoice->balance_used),0,',','.') }}</td>
            <td class="text-center">
                {{ $accountingInvoice->user_instance_id ? 
                ($accountingInvoice->userInstance->username . '-' . $accountingInvoice->userInstance->instance_name . '-' . $accountingInvoice->userInstance->branch)
                : "-" }}
            </td>
            @if ($key_acc_inv == count($nota->accountingInvoices) - 1)
            <td class="text-center">
                <form action="{{ route('accounting_invoices.delete_last_payment_customer', [$nota->id, $accountingInvoice->id]) }}" method="POST" onsubmit="return confirm('Yakin menghapus histori pembayaran terakhir?')">
                    @csrf
                    <button type="submit" class="text-red-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </form>
            @endif
        </tr>
        @endforeach
    </table>
    @endif
    @if (Auth::user()->username === 'kuruniawa')
    <div class="flex justify-end mt-1">
        <a href="{{ route('accounting.search_related_accounting', $nota->id) }}" target="_blank" rel="noopener noreferrer" class="bg-sky-400 text-white font-bold rounded-xl px-1 text-xs">rel.accounting</a>
    </div>
    @endif
</div>
{{-- END - HISTORI PEMBAYARAN --}}