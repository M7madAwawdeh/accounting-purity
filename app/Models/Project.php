<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'funder_id',
        'value',
        'currency',
        'start_date',
        'end_date',
        'description',
        'status',
        'budget',
        'total_donations',
        'total_payments',
        'total_expenses',
        'balance',
    ];

    public function currencies()
    {
        return $this->belongsToMany(Currency::class)
            ->withPivot('balance', 'total_donations', 'total_payments', 'total_expenses')
            ->withTimestamps();
    }

    public function funder()
    {
        return $this->belongsTo(Funder::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function paymentVouchers()
    {
        return $this->hasMany(PaymentVoucher::class);
    }

    public function expenseVouchers()
    {
        return $this->hasMany(ExpenseVoucher::class);
    }
}
