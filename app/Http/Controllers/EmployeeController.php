<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::orderBy('full_name', 'asc')->get();
        $employeeTypes = EmployeeType::all();
        $idTypes = ['KTP', 'SIM', 'Passport', 'Other'];
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'employees.index',
            'parent_route' => 'employees.index',
            'profile_menus' => Menu::get_profile_menus(),
            'employees' => $employees,
            'employeeTypes' => $employeeTypes,
            'idTypes' => $idTypes,
        ];
        // dd($data);
        return view('employees.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [];
        DB::beginTransaction();
        try {
            $employee = Employee::create($data);

            $employee->update([
                'employee_code' => 'EMP' . str_pad($employee->id, 6, '0', STR_PAD_LEFT),
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $post = $request->post();
        dd($post);
        // VALIDASI
        // VALIDASI DATA EMPLOYEE
        $request->validate([
            'nationality' => 'nullable|max:50',
            'employee_type' => 'required|in:FULLTIME,PARTTIME,DAILY,WEEKLY,CONTRACT,INTERN,FREELANCER',
            'full_name' => 'required|max:255',
            'given_name' => 'nullable|max:255',
            'family_name' => 'nullable|max:100',
            'preferred_name' => 'nullable|max:50',
            'birthday' => 'required|date',
            'start_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'origin' => 'nullable|max:50',
            'domicile' => 'nullable|max:50',
        ]);
        // VALIDASI KONTAK
        if ($post['tipe'] !== null) {
            $request->validate(['nomor'=>'required']);
        }elseif ($post['nomor'] !== null) {
            $request->validate(['tipe'=>'required']);
        }

        // VALIDASI ALAMAT
        if ($post['short'] !== null) {
            $request->validate(['long'=>'required']);
        } elseif ($post['long'] !== null) {
            $request->validate(['short'=>'required']);
        }
        // END - VALIDASI
        $success_ = '';
        // STORE DATA_EMPLOYEE
        $tanggal_lahir = null;
        if ($post['day'] !== null && $post['month'] !== null && $post['year'] !== null) {
            $tanggal_lahir = date('Y-m-d', strtotime("$post[year]-$post[month]-$post[day]"));
        }
        $employee = Employee::create([
            'bentuk' => $post['bentuk'],
            'nama' => $post['nama'],
            'gender' => $post['gender'],
            'nik' => $post['nik'],
            'sapaan' => $post['sapaan'],
            'gelar' => $post['gelar'],
            'initial' => $post['initial'],
            'tanggal_lahir' => $tanggal_lahir,
            'keterangan' => $post['keterangan'],
            'creator' => Auth::user()->username,
            'updater' => Auth::user()->username,
        ]);
        $success_ .= '-employee created-';
        // END - STORE DATA_EMPLOYEE
        // STORE KONTAK
        if ($post['tipe'] !== null && $post['nomor'] !== null) {
            EmployeeContact::create([
                'employee_id' => $employee->id,
                'tipe' => $post['tipe'],
                'kodearea' => $post['kodearea'],
                'nomor' => $post['nomor'],
                'is_aktual' => 'yes',
            ]);
        }
        // END - STORE KONTAK
        // STORE ALAMAT
        if ($post['short'] !== null && $post['long'] !== null) {
            $post['long'] = json_encode(preg_split("/\r\n|\n|\r/", $post['long']));
            $alamat = Alamat::create([
                'jalan' => $post['jalan'],
                'komplek' => $post['komplek'],
                'rt' => $post['rt'],
                'rw' => $post['rw'],
                'desa' => $post['desa'],
                'kelurahan' => $post['kelurahan'],
                'kecamatan' => $post['kecamatan'],
                'kota' => $post['kota'],
                'kodepos' => $post['kodepos'],
                'kabupaten' => $post['kabupaten'],
                'provinsi' => $post['provinsi'],
                'pulau' => $post['pulau'],
                'negara' => $post['negara'],
                'short' => $post['short'],
                'long' => $post['long'],
            ]);

            Alamat::create([
                'table_name' => 'employees',
                'employee_id' => $employee->id,
                'alamat_id' => $alamat->id,
                'tipe' => 'UTAMA',
            ]);
            $success_ .= '-alamat, employee_alamat created-';
        }
        // END - STORE ALAMAT
        $feedback = [
            'success_' => $success_
        ];
        return back()->with($feedback);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
