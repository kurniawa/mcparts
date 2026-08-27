<?php

namespace App\Http\Controllers;

use App\Http\Requests\BilyetGiroRequest;
use App\Models\BilyetGiro;
use App\Models\Menu;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BilyetGiroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bilyetGiros = BilyetGiro::orderBy('created_at', 'desc')->get();
        // $label_issuer = BilyetGiro::select('issuer_name', 'issuer_account_number', 'issuer_bank', 'beneficiary_name', 'beneficiary_account_number', 'beneficiary_bank')->groupBy('issuer_account_number')->get();
        // dd($label_issuer);
        $label_customer = Pelanggan::select('id', 'nama as value', 'nama as label')->get();
        $label_issuer = DB::table('bilyet_giros')
            ->select(
                DB::raw('MAX(customer_name) as customer_name'),
                DB::raw('MAX(issuer_name) as issuer_name'),
                'issuer_account_number',
                DB::raw('MAX(issuer_bank) as issuer_bank'),
                DB::raw('MAX(beneficiary_name) as beneficiary_name'),
                DB::raw('MAX(beneficiary_account_number) as beneficiary_account_number'),
                DB::raw('MAX(beneficiary_bank) as beneficiary_bank')
            )
            ->whereNull('deleted_at')
            ->groupBy('issuer_account_number')
            ->get();
        // dd($label_issuer);
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'bilyet-giros.index',
            'parent_route' => 'bilyet-giros.index',
            'profile_menus' => Menu::get_profile_menus(),
            'spk_menus' => Menu::get_spk_menus(),
            'bilyetGiros' => $bilyetGiros,
            'label_issuer' => $label_issuer,
            'label_customer' => $label_customer,
        ];
        // dd($data);
        return view('bilyet-giros.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [];
        DB::beginTransaction();
        try {
            $bilyetGiro = BilyetGiro::create($data);

            $bilyetGiro->update([
                'bilyet_giro_code' => 'BG' . str_pad($bilyetGiro->id, 6, '0', STR_PAD_LEFT),
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
    public function store(BilyetGiroRequest $request)
    {
        $post = $request->post();
        // dd($post);
        // VALIDASI
        $validated = $request->validated();
        
        DB::beginTransaction();
        $success_ = '';
        try {
            $bilyetGiro = BilyetGiro::create($validated);

            $success_ .= '-bilyet giro created-';

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dump($post);
            dd($th);
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
    public function edit(BilyetGiro $bilyetGiro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BilyetGiroRequest $request, BilyetGiro $bilyetGiro)
    {
        // dump($bilyetGiro);
        // dd($request->post());

        $validated = $request->validated();
        // dd($validated);

        DB::beginTransaction();
        $success_ = '';
        try {
            $bilyetGiro->update($validated);
            $success_ .= '-bilyet giro updated-';
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
        }
        
        $feedback = [
            'success_' => $success_
        ];

        return back()->with($feedback);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BilyetGiro $bilyetGiro)
    {
        $bilyetGiro->delete();
        return redirect()
            ->route('bilyet-giros.index')
            ->with('success', 'Bilyet giro berhasil dihapus.');
    }
}
