<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'balanceable_id',
        'balanceable_type',
        'currency',
        'balance',
    ];

    public function balanceable()
    {
        return $this->morphTo();
    }
}
