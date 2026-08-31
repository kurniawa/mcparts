@extends('layouts.main')
@section('content')
<main class="text-xs">
    <x-errors-any></x-errors-any>
    <x-validation-feedback></x-validation-feedback>
    <form action="{{ route('accounting.store_kliring_bg', $user_instance->id) }}" class="w-full" method="POST">
        @csrf
        <div class="w-full md:flex md:justify-center">
            <div class="border rounded bg-white shadow drop-shadow p-2 mt-5 w-full md:w-fit">
                <div>
                    <label for="clearing_date" class="font-bold">Tgl. Kliring:</label>
                    <div class="mt-1">
                        <input type="date" name="clearing_date" id="clearing_date" value="{{ old('clearing_date') ? old('clearing_date') : date('Y-m-d') }}" class="text-xs border border-slate-300 rounded p-1">
                    </div>
                </div>
                <div class="mt-5 font-bold">Pilihan Bilyet Giro:</div>
                <div class="mt-2 w-full overflow-x-auto">
                    <table class="table-auto border-collapse border border-slate-400 min-w-max">
                        <thead>
                            <tr>
                                <th class="border border-slate-300 px-4 py-2">Pilih</th>
                                <th class="border border-slate-300 px-4 py-2">Tgl. Terima</th>
                                <th class="border border-slate-300 px-4 py-2">Nama Bank</th>
                                <th class="border border-slate-300 px-4 py-2">No. BG</th>
                                <th class="border border-slate-300 px-4 py-2">Tgl. Jatuh Tempo</th>
                                <th class="border border-slate-300 px-4 py-2">Nominal</th>
                                <th class="border border-slate-300 px-4 py-2">Dari</th>
                                <th class="border border-slate-300 px-4 py-2">No. Rek</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bilyetGiros as $key_bg => $bg)
                                <tr>
                                    <td class="border border-slate-300 px-4 py-2"><input type="radio" name="bilyet_giro_id" id="" value="{{ $bg->id }}"></td>
                                    <td class="border border-slate-300 px-4 py-2">{{ \Carbon\Carbon::parse($bg->received_date)->format('d/m/Y') }}</td>
                                    <td class="border border-slate-300 px-4 py-2">{{ $bg->issuer_bank }}</td>
                                    <td class="border border-slate-300 px-4 py-2">{{ $bg->bilyet_number }}</td>
                                    <td class="border border-slate-300 px-4 py-2">{{ \Carbon\Carbon::parse($bg->due_date)->format('d/m/Y') }}</td>
                                    <td class="border border-slate-300 px-4 py-2">{{ number_format($bg->amount, 2, ',', '.') }}</td>
                                    <td class="border border-slate-300 px-4 py-2">{{ $bg->issuer_name }}</td>
                                    <td class="border border-slate-300 px-4 py-2">{{ $bg->issuer_account_number }}</td>
                                    <td>
                                        <button id="btn_detail_bg-{{ $key_bg }}" type="button" class="border rounded border-orange-400 text-orange-400 px-1" onclick="toggleForm(this,'detail_bg-{{ $key_bg }}', 'bg-orange-200')">D</button>
                                    </td>
                                </tr>
                                <tr id="detail_bg-{{ $key_bg }}" class="hidden">
                                    <td colspan="10">
                                        <div class="flex justify-center m-2">
                                            <div class="flex max-w-fit rounded border border-sky-300 p-2">
                                                <div class="flex flex-col gap-1">
                                                    <label for="beneficiary_name">Kepada</label>
                                                    <input type="text" name="beneficiary_name[{{ $key_bg }}]" id="beneficiary_name-{{ $key_bg }}" value="{{ old('beneficiary_name', $bg->beneficiary_name) }}" class="text-xs border border-slate-300 rounded p-1">
                                                </div>
                                                <div class="flex flex-col gap-1">
                                                    <label for="beneficiary_bank">Bank Tujuan</label>
                                                    <input type="text" name="beneficiary_bank[{{ $key_bg }}]" id="beneficiary_bank-{{ $key_bg }}" value="{{ old('beneficiary_bank', $bg->beneficiary_bank) }}" class="text-xs border border-slate-300 rounded p-1">
                                                </div>
                                                <div class="flex flex-col gap-1">
                                                    <label for="beneficiary_account_number">No. Rek Tujuan</label>
                                                    <input type="text" name="beneficiary_account_number[{{ $key_bg }}]" id="beneficiary_account_number-{{ $key_bg }}" value="{{ old('beneficiary_account_number', $bg->beneficiary_account_number) }}" class="text-xs border border-slate-300 rounded p-1">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="bg-emerald-300 p-2 rounded font-bold text-white">Submit</button>
                </div>
            </div>
        </div>
    </form>
</main>

<script>
    function toggleForm(button, elementID, className) {
        // add some classes to button, if the classes not exist, remove the classes if the classes exist, with delay 300ms
        setTimeout(function() {
            $(button).toggleClass(className);
        }, 300);
        $('#' + elementID).toggle(300);
    }
</script>

@endsection
