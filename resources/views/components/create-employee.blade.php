<div class="text-xs mt-1 hidden border rounded p-2 bg-white shadow drop-shadow-sm" id="form_new_employee">
    <form class="rounded" action="{{ route('employees.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-2 gap-1">
            <table>
                <tr>
                    <td>Type</td><td>:</td>
                    <td>
                        <select name="bentuk" id="bentuk" class="rounded py-0">
                            @foreach ($employeeTypes as $employeeType)
                            <option value="{{ $employeeType->code }}">{{ $employeeType->code }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr><td>Full Name</td><td>:</td><td><input type="text" class="rounded p-1 text-xs" name="full_name" value="{{ old('full_name') ? old('full_name') : '' }}"></td></tr>
                <tr><td>Given Name</td><td>:</td><td><input type="text" class="rounded p-1 text-xs" name="given_name" value="{{ old('given_name') ? old('given_name') : '' }}"></td></tr>
                <tr><td>Family Name</td><td>:</td><td><input type="text" class="rounded p-1 text-xs" name="family_name" value="{{ old('family_name') ? old('family_name') : '' }}"></td></tr>
                <tr><td>Preferred Name</td><td>:</td><td><input type="text" class="rounded p-1 text-xs" name="preferred_name" value="{{ old('preferred_name') ? old('preferred_name') : '' }}"></td></tr>
                <tr>
                    <td>Birthday</td><td>:</td>
                    <td>
                        <input type="date" name="birthday" id="birthday" class="rounded p-1 text-xs">
                    </td>
                </tr>
            </table>
            <div>
                <div>
                    <label for="gender"> Gender:</label>
                    <div>
                        <input type="radio" name="gender" id="male" value="male" class="ml-2">
                        <label for="male" class="ml-1">Male</label>
                        <input type="radio" name="gender" id="female" value="female" class="ml-5">
                        <label for="female" class="ml-1">Female</label>
                    </div>
                </div>
                <div class="mt-2">
                    <label for="description">Description (opt.):</label>
                    <div class="mt-1">
                        <textarea name="description" id="description" cols="30" rows="5" class="text-xs rounded"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- KONTAK --}}
        <div>
            <div class="flex justify-center mt-5">
                <div class="flex items-center bg-white rounded p-1 shadow drop-shadow">
                    <h5 class="font-semibold ml-2">Phone:</h5>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-center"><label for="phone">Nomor (format +62812XXX):</label></div>
                <div class="text-center">
                    <input type="text" name="phone" class="p-1 text-xs rounded" placeholder="format +62812XXX" id="phone">
                </div>
            </div>
        </div>
        {{-- END - KONTAK --}}
        <div class="flex justify-center mt-5">
            <div class="flex items-center bg-white rounded p-1 shadow drop-shadow">
                <h5 class="font-semibold ml-2">Alamat:</h5>
            </div>
        </div>
        <table class="mt-1 w-full">
            <tr><td>jalan</td><td>:</td><td><input type="text" name="jalan" class="text-xs p-1 rounded"></td></tr>
            <tr><td>komplek</td><td>:</td><td><input type="text" name="komplek" class="text-xs p-1 rounded"></td></tr>
            <tr>
                <td>rt</td><td>:</td><td><input type="text" name="rt" class="text-xs p-1 rounded"></td>
                <td>rw</td><td>:</td><td><input type="text" name="rw" class="text-xs p-1 rounded"></td>
            </tr>
            <tr>
                <td>desa</td><td>:</td><td><input type="text" name="desa" class="text-xs p-1 rounded"></td>
                <td>kelurahan</td><td>:</td><td><input type="text" name="kelurahan" class="text-xs p-1 rounded">
            </tr>
            <tr>
                <td>kecamatan</td><td>:</td><td><input type="text" name="kecamatan" class="text-xs p-1 rounded"></td>
                <td>kota</td><td>:</td><td><input type="text" name="kota" class="text-xs p-1 rounded">
            </tr>
            <tr><td>kodepos</td><td>:</td><td><input type="text" name="kodepos" class="text-xs p-1 rounded"></td></tr>
            <tr>
                <td>kabupaten</td><td>:</td><td><input type="text" name="kabupaten" class="text-xs p-1 rounded"></td>
                <td>provinsi</td><td>:</td><td><input type="text" name="provinsi" class="text-xs p-1 rounded"></td>
            </tr>
            <tr>
                <td>pulau</td><td>:</td><td><input type="text" name="pulau" class="text-xs p-1 rounded"></td>
                <td>negara</td><td>:</td><td><input type="text" name="negara" class="text-xs p-1 rounded"></td>
            </tr>
            <tr>
                <td>(*)short(daerah)</td><td>:</td><td><input type="text" name="short" class="text-xs p-1 rounded"></td>
                <td>(*)long</td><td>:</td><td><textarea name="long" id="" cols="30" rows="4" class="border border-slate-400 rounded p-1 text-xs"></textarea></td>
            </tr>
        </table>

        <div class="text-center mt-2">
            <button type="submit" class="bg-emerald-500 rounded text-white py-2 px-5 font-bold">+ CREATE NEW EMPLOYEE</button>
        </div>
    </form>
</div>