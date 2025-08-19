<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'exchange_rate',
        'is_default',
    ];

    public function banks()
    {
        return $this->belongsToMany(Bank::class)->withPivot('balance')->withTimestamps();
    }

    public function cashBoxes()
    {
        return $this->belongsToMany(CashBox::class)->withPivot('balance')->withTimestamps();
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class)
            ->withPivot('balance', 'total_donations', 'total_payments', 'total_expenses')
            ->withTimestamps();
    }
}
