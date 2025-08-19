<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_account_id',
        'from_account_type',
        'to_account_id',
        'to_account_type',
        'amount',
        'date',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function fromAccount()
    {
        return $this->morphTo();
    }

    public function toAccount()
    {
        return $this->morphTo();
    }
}
