<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBox extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'manager',
    ];

    public function currencies()
    {
        return $this->belongsToMany(Currency::class)->withPivot('balance')->withTimestamps();
    }

    public function donations()
    {
        return $this->morphMany(Donation::class, 'accountable');
    }

    public function paymentVouchers()
    {
        return $this->morphMany(PaymentVoucher::class, 'accountable');
    }

    public function expenseVouchers()
    {
        return $this->morphMany(ExpenseVoucher::class, 'accountable');
    }

    public function getViewLink()
    {
        return route('cash-boxes.show', $this->id);
    }
}
