<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use App\Models\BilyetGiro;
use App\Models\UserInstance;
use Illuminate\Http\Request;

class AccountingController3 extends Controller
{
    public function destroy_accounting_bg(Request $request, Accounting $accounting, UserInstance $user_instance, BilyetGiro $bilyet_giro)
    {
        dd($request->all(), $accounting, $user_instance, $bilyet_giro);

        return response()->json(['message' => 'Accounting entry destroyed successfully.']);
    }
}
