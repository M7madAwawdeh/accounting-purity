<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'amount',
        'donor_name',
        'description',
        'payment_method',
        'project_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function accountable()
    {
        return $this->morphTo();
    }

    public function cheque()
    {
        return $this->morphOne(Cheque::class, 'chequeable');
    }
}
