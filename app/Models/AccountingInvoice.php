<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingInvoice extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function accounting()
    {
        return $this->belongsTo(Accounting::class);
    }

    public function userInstance()
    {
        return $this->belongsTo(UserInstance::class);
    }
}
