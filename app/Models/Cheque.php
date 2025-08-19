<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    use HasFactory;

    const STATUSES = ['pending', 'cleared', 'bounced'];

    const STATUS_COLOR = [
        'pending' => 'warning',
        'cleared' => 'success',
        'bounced' => 'danger',
    ];

    protected $fillable = [
        'bank_id',
        'number',
        'account_number',
        'amount',
        'due_date',
        'recipient',
        'payer',
        'type',
        'status',
        'description',
        'chequeable_id',
        'chequeable_type',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Get the parent chequeable model (donation, expense voucher, etc.).
     */
    public function chequeable()
    {
        return $this->morphTo();
    }

    public function donation()
    {
        return $this->hasOne(Donation::class);
    }

    public function expenseVoucher()
    {
        return $this->hasOne(ExpenseVoucher::class);
    }

    public function paymentVoucher()
    {
        return $this->hasOne(PaymentVoucher::class);
    }
}
