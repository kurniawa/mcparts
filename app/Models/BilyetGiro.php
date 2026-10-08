<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BilyetGiro extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'accounting_id',
        'bilyet_number',
        'issuer_name',
        'issuer_bank',
        'issuer_account_number',
        'beneficiary_name',
        'beneficiary_bank',
        'beneficiary_account_number',
        'amount',
        'received_date',
        'due_date',
        'clearing_date',
        'status',
        'notes',
        'cleared_by',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'received_date' => 'date',
        'due_date' => 'date',
        'clearing_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($bilyetGiro) {
            $bilyetGiro->uuid ??= Str::uuid();
        });
    }

    public function clearedBy()
    {
        return $this->belongsTo(User::class, 'cleared_by');
    }

    public function accounting()
    {
        return $this->belongsTo(Accounting::class, 'accounting_id');
    }
}
