@extends('layouts.main')
@section('content')
{{-- <header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-xl font-bold tracking-tight text-gray-900">App</h1>
    </div>
  </header> --}}
<main>
    <x-errors-any></x-errors-any>
    <x-validation-feedback></x-validation-feedback>
    <div class="mx-1 py-1 sm:px-6 lg:px-8 text-xs">
        <div class="flex">
            <button id="filter" class="hidden border rounded border-yellow-500 text-yellow-500 px-3 py-1" onclick="toggle_light(this.id,'filter-content',[],['bg-yellow-200'], 'block')">Filter</button>
            {{-- <form action="{{ route('employees.create') }}" method="GET" class="flex ml-2">
                <button type="submit" class="rounded bg-emerald-500 text-white font-semibold px-3 py-1">+ Pelanggan</button>
            </form> --}}
            <button type="submit" class="border rounded border-emerald-300 text-emerald-500 font-semibold px-3 py-1 ml-1" id="btn_new_employee" onclick="toggle_light(this.id, 'form_new_employee', [], ['bg-emerald-200'], 'block')">+ Employee</button>
        </div>
        {{-- SEARCH / FILTER --}}
        <div class="hidden" id="filter-content">
            <div class="rounded p-2 bg-white shadow drop-shadow inline-block mt-1">
                <form action="" method="GET">
                    <div class="ml-1 mt-2 flex items-center">
                        <div class="flex mt-1">
                            <input type="text" class="input" name="employee_name" placeholder="Nama Employee..." id="employee_name">
                        </div>
                        <div>
                            <button type="submit" class="ml-2 flex items-center bg-yellow-500 text-white py-1 px-1 rounded hover:bg-yellow-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                                <span class="ml-1">Search</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        {{-- END - SEARCH / FILTER --}}
        
    </div>
    <div class="flex justify-center gap-2">
        <div class='pb-1 text-xs lg:w-1/2 md:w-3/4'>
            <table class="table-nice w-full">
                <tr>
                    <th class="border-b">No.</th>
                    <th class="border-b">Full Name</th>
                    <th class="border-b">Origin</th>
                    <th class="border-b">Domicile</th>
                    <th class="border-b">Phone</th>
                </tr>
                @for ($i = 0; $i < count($employees); $i++)
                <tr class="border-b">
                    <td class="text-center">{{ $i + 1 }}.</td>
                    <td>
                        <a href="{{ route('employees.show', $employees[$i]->id) }}" class="text-sky-500">
                            {{ $employees[$i]['full_name'] }}
                        </a>
                    </td>
                    <td class="text-center">{{ $employees[$i]['origin'] }}</td>
                    <td class="text-center">{{ $employees[$i]['domicile'] }}</td>
                    <td class="text-center">
                        {{-- <button id="btn_detail_employee-{{ $i }}" class="border rounded" onclick="showDropdown(this.id, 'detail_employee-{{ $i }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button> --}}
                        {{ $employees[$i]['phone'] }}
                    </td>
                </tr>
                <tr class="hidden" id="detail_employee-{{ $i }}">
                    <td colspan="3">
                        <table>
                            <tr>
                                {{-- <td>
                                    @if ($alamats[$i]!==null)
                                    @if ($alamats[$i]['long']!==null)
                                    @foreach (json_decode($alamats[$i]['long'],true) as $alamat)
                                    {{ $alamat }}<br>
                                    @endforeach
                                    @endif
                                    @else
                                    -
                                    @endif
                                </td> --}}
                                <td>
                                    @if ($employees[$i]['phone']!==null)
                                    {{ $employees[$i]['phone'] }}
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endfor
            </table>
        </div>
        {{-- FORM_NEW_EMPLOYEE --}}
        <x-create-employee :employeeTypes="$employeeTypes" :idTypes="$idTypes"></x-create-employee>
        {{-- END - FORM_NEW_EMPLOYEE --}}
    </div>
</main>


@endsection
