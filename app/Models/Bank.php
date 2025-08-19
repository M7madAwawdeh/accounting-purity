<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'account_number',
        'iban',
        'swift_code',
        'contact_person',
        'phone',
        'address',
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
        return route('banks.show', $this->id);
    }
}
