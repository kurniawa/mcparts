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
        $validated = $request->validate([
            'nationality' => 'nullable|max:50',
            'employee_type' => 'required|in:FULLTIME,PARTTIME,DAILY,WEEKLY,CONTRACT,INTERN,FREELANCER',
            'id_type' => 'required|in:KTP,SIM,Passport,Other',
            'id_number' => 'required|unique:employees,id_number',
            'full_name' => 'required|max:255',
            'given_name' => 'nullable|max:255',
            'family_name' => 'nullable|max:100',
            'preferred_name' => 'nullable|max:50',
            'birthday' => 'required|date',
            'start_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'origin' => 'nullable|max:50',
            'domicile' => 'nullable|max:50',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable',
        ]);
        
        DB::beginTransaction();
        $success_ = '';
        try {
            $employee_type = EmployeeType::where('code', $validated['employee_type'])->first();
            $validated['employee_type_id'] = $employee_type->id;
            $validated['created_by'] = Auth::user()->id;
            Employee::create($validated);

            $success_ .= '-employee created-';
            
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }
        
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
