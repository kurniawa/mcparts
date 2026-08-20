@extends('layouts.main')
@section('content')
{{-- <header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-xl font-bold tracking-tight text-gray-900">App</h1>
    </div>
  </header> --}}
<main class="text-xs">
    <x-errors-any></x-errors-any>
    <x-validation-feedback></x-validation-feedback>
    {{-- Form New Bilyet Giro --}}
    <div class="flex justify-center">
        <div class="bg-white p-2 rounded shadow drop-shadow">
            <h1 class="text-lg font-bold mb-2">Tambah Bilyet Giro</h1>
            <form action="{{ route('bilyet-giros.store') }}" method="POST" class="flex flex-col gap-2">
                @csrf
                <div class="grid grid-cols-2 md:flex gap-2">
                    <div class="flex flex-col gap-1">
                        <label for="receive_date">Tgl. Terima</label>
                        <input type="date" name="receive_date" id="receive_date" value="{{ old('receive_date') }}" class="text-xs border border-slate-300 rounded p-1">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="issuer_bank">Nama Bank</label>
                        <input type="text" name="issuer_bank" id="issuer_bank" value="{{ old('issuer_bank') }}" class="text-xs border border-slate-300 rounded p-1">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="bilyet_number">No. BG</label>
                        <input type="text" name="bilyet_number" id="bilyet_number" value="{{ old('bilyet_number') }}" class="text-xs border border-slate-300 rounded p-1">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="due_date">Tgl. Jatuh Tempo</label>
                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" class="text-xs border border-slate-300 rounded p-1">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="amount">Nominal</label>
                        <input type="number" name="amount" id="amount" value="{{ old('amount') }}" class="text-xs border border-slate-300 rounded p-1">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="issuer_name">Dari</label>
                        <input type="text" name="issuer_name" id="issuer_name" value="{{ old('issuer_name') }}" class="text-xs border border-slate-300 rounded p-1">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="issuer_account_number">No. Rek</label>
                        <input type="text" name="issuer_account_number" id="issuer_account_number" value="{{ old('issuer_account_number') }}" class="text-xs border border-slate-300 rounded p-1">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="customer_name">Connect to Customer</label>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" class="text-xs border border-slate-300 rounded p-1">
                        <input type="hidden" name="customer_id" id="customer_id">
                    </div>
                </div>
                <div class="flex justify-center md:justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Tambah</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Daftar Bilyet Giro dalam table --}}
    <div class="flex justify-center mt-4">
        <div class="bg-white p-2 rounded shadow drop-shadow overflow-x-auto">
            <table class="table-auto border-collapse border border-slate-400 min-w-max">
                <thead>
                    <tr>
                        <th class="border border-slate-300 px-4 py-2">No.</th>
                        <th class="border border-slate-300 px-4 py-2">Tgl. Terima</th>
                        <th class="border border-slate-300 px-4 py-2">Nama Bank</th>
                        <th class="border border-slate-300 px-4 py-2">No. BG</th>
                        <th class="border border-slate-300 px-4 py-2">Tgl. Jatuh Tempo</th>
                        <th class="border border-slate-300 px-4 py-2">Nominal</th>
                        <th class="border border-slate-300 px-4 py-2">Dari</th>
                        <th class="border border-slate-300 px-4 py-2">No. Rek</th>
                        <th class="border border-slate-300 px-4 py-2">Tgl. Kliring</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bilyetGiros as $key_bg => $bg)
                        <tr>
                            <td class="border border-slate-300 px-4 py-2">{{ $key_bg + 1 }}</td>
                            <td class="border border-slate-300 px-4 py-2">{{ \Carbon\Carbon::parse($bg->received_date)->format('d/m/Y') }}</td>
                            <td class="border border-slate-300 px-4 py-2">{{ $bg->issuer_bank }}</td>
                            <td class="border border-slate-300 px-4 py-2">{{ $bg->bilyet_number }}</td>
                            <td class="border border-slate-300 px-4 py-2">{{ \Carbon\Carbon::parse($bg->due_date)->format('d/m/Y') }}</td>
                            <td class="border border-slate-300 px-4 py-2">{{ number_format($bg->amount, 2, ',', '.') }}</td>
                            <td class="border border-slate-300 px-4 py-2">{{ $bg->issuer_name }}</td>
                            <td class="border border-slate-300 px-4 py-2">{{ $bg->issuer_account_number }}</td>
                            <td class="border border-slate-300 px-4 py-2">{{ $bg->clearing_date ? \Carbon\Carbon::parse($bg->clearing_date)->format('d/m/Y') : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    const label_issuer = @json($label_issuer);
    const label_customer = @json($label_customer);
    // console.log(label_issuer);
    // console.log(label_customer);

    $('#issuer_bank').autocomplete({
        source: function(request, response) {
            // Ambil nilai input lainnya
            const issuerName   = $('#issuer_name').val().toLowerCase().trim();
            const customerName = $('#customer_name').val().toLowerCase().trim();
            const issuerAccountNumber = $('#issuer_account_number').val().toLowerCase().trim();
            // const customerId   = $('#customer_id').val().trim();

            let bankOptions = [];
            const searchTerm = request.term.toLowerCase().trim();
            if (!issuerName && !customerName) {
                // bankOptions = label_issuer.map(function(item) {
                //     return {
                //         label: item.bank,
                //         value: item.bank
                //     };
                // });
                bankOptions = [...new Set(label_issuer.map(item => item.issuer_bank).filter(bank => bank.toLowerCase().includes(searchTerm))
                )].map(bank => ({
                    label: bank,
                    value: bank
                }));

                // console.log('searchTerm:', searchTerm);
                // console.log('bankOptions:', bankOptions);
            } else if (issuerName && !customerName) {
                bankOptions = label_issuer
                    .filter(item => item.issuer_name.toLowerCase().includes(issuerName))
                    .map(item => ({
                        label: item.issuer_bank,
                        value: item.issuer_bank
                    }));
            } else if (!issuerName && customerName) {
                bankOptions = label_issuer
                    .filter(item => item.customer_name && item.customer_name.toLowerCase().includes(customerName))
                    .map(item => ({
                        label: item.issuer_bank,
                        value: item.issuer_bank
                    }));
            } else if (issuerName && customerName) {
                bankOptions = label_issuer
                    .filter(item => item.issuer_name.toLowerCase().includes(issuerName))
                    .filter(item => item.customer_name && item.customer_name.toLowerCase().includes(customerName))
                    .map(item => ({
                        label: item.issuer_bank,
                        value: item.issuer_bank
                    }));
            }

            response(bankOptions);
        }
    });

    $('#issuer_name').autocomplete({
        source: function(request, response) {
            // Ambil nilai input lainnya
            const issuerBank   = $('#issuer_name').val().toLowerCase().trim();
            const customerName = $('#customer_name').val().toLowerCase().trim();

            let issuerOptions = [];
            const searchTerm = request.term.toLowerCase().trim();
            if (!issuerBank && !customerName) {
                issuerOptions = [...new Set(label_issuer.map(item => item.issuer_name).filter(issuerName => issuerName.toLowerCase().includes(searchTerm))
                )].map(issuerName => ({
                    label: issuerName,
                    value: issuerName
                }));
            } else if (issuerBank && !customerName) {
                issuerOptions = label_issuer
                    .filter(item => item.issuer_bank.toLowerCase().includes(issuerBank))
                    .map(item => ({
                        label: item.issuer_name,
                        value: item.issuer_name
                    }));
            } else if (!issuerBank && customerName) {
                issuerOptions = label_issuer
                    .filter(item => item.customer_name && item.customer_name.toLowerCase().includes(customerName))
                    .map(item => ({
                        label: item.issuer_name,
                        value: item.issuer_name
                    }));
            } else if (issuerBank && customerName) {
                issuerOptions = label_issuer
                    .filter(item => item.issuer_name.toLowerCase().includes(issuerBank))
                    .filter(item => item.customer_name && item.customer_name.toLowerCase().includes(customerName))
                    .map(item => ({
                        label: item.issuer_name,
                        value: item.issuer_name
                    }));
            }

            response(issuerOptions);
        }
    });

    $('#issuer_account_number').autocomplete({
        source: function(request, response) {
            // Ambil nilai input lainnya
            const issuerBank   = $('#issuer_bank').val().toLowerCase().trim();
            const customerName = $('#customer_name').val().toLowerCase().trim();

            let accountOptions = [];
            const searchTerm = request.term.toLowerCase().trim();
            if (!issuerBank && !customerName) {
                accountOptions = [...new Set(label_issuer.map(item => item.issuer_account_number).filter(accountNumber => accountNumber.toLowerCase().includes(searchTerm))
                )].map(accountNumber => ({
                    label: accountNumber,
                    value: accountNumber
                }));
            } else if (issuerBank && !customerName) {
                accountOptions = label_issuer
                    .filter(item => item.issuer_bank.toLowerCase().includes(issuerBank))
                    .map(item => ({
                        label: item.issuer_account_number,
                        value: item.issuer_account_number
                    }));
            } else if (!issuerBank && customerName) {
                accountOptions = label_issuer
                    .filter(item => item.customer_name && item.customer_name.toLowerCase().includes(customerName))
                    .map(item => ({
                        label: item.issuer_account_number,
                        value: item.issuer_account_number
                    }));
            } else if (issuerBank && customerName) {
                accountOptions = label_issuer
                    .filter(item => item.issuer_bank.toLowerCase().includes(issuerBank))
                    .filter(item => item.customer_name && item.customer_name.toLowerCase().includes(customerName))
                    .map(item => ({
                        label: item.issuer_account_number,
                        value: item.issuer_account_number
                    }));
            }

            response(accountOptions);
        }
    });

    $('#customer_name').autocomplete({
        source: label_customer,
        select: function(event, ui) {
            $('#customer_id').val(ui.item.id);
        }
    });
</script>

@endsection
