<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::all();
        $data = [
            'menus' => Menu::get(),
            'profile_menus' => Menu::get_profile_menus(),
            'employees' => $employees,
        ];
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
        //
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
