@extends('layouts.main')
@section('content')
  
<main class="p-2">
    <x-validation-feedback></x-validation-feedback>

    <div class="grid gap-2 justify-center">
        <div class="border-2">
            <div class="flex justify-between p-2">
                <table class="">
                    <tr>
                        <td>No.</td><td>:</td><td><div class="font-bold text-sm text-slate-500">{{ $nota->nomor_nota }}</div></td>
                    </tr>
                    <tr>
                        <td>Tgl.</td><td>:</td>
                        <td>
                            <div class="w-fit">
                                <div class="flex">
                                    <div class="flex">
                                        @if ($nota->finished_at === null)
                                        <div>
                                            <div class="rounded p-1 bg-red-500 text-white font-bold text-center">
                                                <div>{{ date('d',strtotime($nota->created_at)) }}</div>
                                                <div>{{ date('m-y',strtotime($nota->created_at)) }}</div>
                                            </div>
                                        </div>
                                        @else
                                        <div>
                                            <div class="rounded p-1 bg-yellow-500 text-white font-bold text-center">
                                                <div>{{ date('d',strtotime($nota->created_at)) }}</div>
                                                <div>{{ date('m-y',strtotime($nota->created_at)) }}</div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="flex ml-1 items-center">
                                        @if ($nota->finished_at !== null)
                                        <div>
                                            <div class="rounded p-1 bg-emerald-500 text-white font-bold text-center">
                                                <div>{{ date('d',strtotime($nota->finished_at)) }}</div>
                                                <div>{{ date('m-y',strtotime($nota->finished_at)) }}</div>
                                            </div>
                                        </div>
                                        @else
                                        <span class="font-bold">---</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td class="align-top">Alamat</td><td class="align-top">:</td>
                        <td class="align-top">
                            @if ($nota->cust_long!==null)
                            @foreach (json_decode($nota->cust_long,true) as $long)
                            <div>{{ $long }}</div>
                            @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Kontak</td><td>:</td>
                        <td>
                            @php
                                $kontak = json_decode($nota->cust_kontak, true);
                            @endphp

                            @if (data_get($kontak, 'nomor'))
                                @if (data_get($kontak, 'kodearea'))
                                    <span>({{ $kontak['kodearea'] }})</span>
                                @endif
                                <span>{{ $kontak['nomor'] }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </div>


            {{-- Nota Items --}}
            <div class="flex justify-center mt-2" id="nota-items">
                <div class="border-b border-t px-1">
                    <table class="text-xs">
                        <tr>
                            <th>Jml.</th>
                            <th>
                                <div class="flex items-center justify-center">
                                    <span>Nama Barang</span>
                                    <button type="button" id="spk_produk_nota_detail_button" class="ml-1 border rounded border-yellow-500 text-yellow-500 p-1" onclick="toggle_detail_classes(this.id,'spk_produk_nota_detail')">D</button>
                                </div>
                            </th>
                            <th>Hrg.</th><th>Hrg. t</th>
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
                        <tr><td></td><td><div class="text-center">-----</div></td><td></td><td><div class="text-center">---</div></td></tr>
                        <tr><th></th><th>Total</th><th></th><th>{{ number_format($nota->harga_total,0,',','.') }}</th></tr>
                    </table>
                </div>
            </div>
            {{-- END - Nota Items --}}
            {{-- OPSI NOTA --}}
            <div class="flex justify-between p-2 border-b">
                <div>
                    <p class="font-bold">status: {{ $nota->status_bayar }}</p>
                    <p class="font-bold text-emerald-500">pembayaran: {{ number_format($nota->amount_paid + $nota->balance_used,0,',','.') }}</p>
                    <p class="font-bold text-red-500">sisa bayar: {{ number_format($nota->amount_due,0,',','.') }}</p>
                </div>
                <div class="flex justify-end mt-1 mb-2 items-center gap-1">
                    <a href="{{ route('notas.print_out', ['nota' => $nota->id]) }}" class="rounded text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                        </svg>
                    </a>
                </div>
            </div>
            {{-- END - OPSI NOTA --}}
            {{-- HISTORI PEMBAYARAN --}}
            <div class="p-2">
                <div>
                    <h5 class="font-bold">Histori Pembayaran:</h5>
                    @if (count($nota->accountingInvoices) === 0)
                    <div class="text-center italic text-slate-500">kosong</div>
                    @else
                    <table id="histori-pembayaran" class="text-xs border border-collapse w-full">
                        <tr>
                            <th>Tgl.</th><th>Sisa Bayar</th><th>Jumlah Bayar</th><th>Akun</th>
                        </tr>
                        @foreach ($nota->accountingInvoices as $key_acc_inv => $accountingInvoice)
                        <tr>
                            {{-- {{ dump($key_acc_inv) }} --}}
                            <td class="text-center">{{ date('d-m-Y', strtotime($accountingInvoice->created_at)) }}</td>
                            <td class="text-center">{{ number_format($accountingInvoice->amount_due,0,',','.') }}</td>
                            <td class="text-center">
                                {{-- <div class="text-slate-500">{{ number_format(($accountingInvoice->accounting->jumlah),0,',','.') }}</div> --}}
                                <div class="text-emerald-500 font-bold">{{ number_format(($accountingInvoice->amount_paid + $accountingInvoice->balance_used),0,',','.') }}</div>
                                <div class="text-sky-400 font-bold">{{ number_format(($accountingInvoice->amount_paid_total),0,',','.') }}</div>
                            </td>
                            <td class="text-center">
                                {{ $accountingInvoice->user_instance_id ? 
                                ($accountingInvoice->userInstance->username . '-' . $accountingInvoice->userInstance->instance_name . '-' . $accountingInvoice->userInstance->branch)
                                : "-" }}
                            </td>
                            {{-- @if ($key_acc_inv == count($nota->accountingInvoices) - 1)
                            <td class="text-center">
                                <form action="{{ route('accounting_invoices.delete_last_payment_customer', [$nota->id, $accountingInvoice->id]) }}" method="POST" onsubmit="return confirm('Yakin menghapus histori pembayaran terakhir?')">
                                    @csrf
                                    <button type="submit" class="text-red-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            @endif --}}
                        </tr>
                        @endforeach
                    </table>
                    @endif
                </div>
            </div>
            {{-- END - HISTORI PEMBAYARAN --}}
        </div>
    </div>
</main>      

<script>
</script>
@endsection
