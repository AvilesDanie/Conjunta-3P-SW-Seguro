<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'status',
        'payment_method',
        'transaction_number',
        'amount',
        'observations',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];


    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }


}
