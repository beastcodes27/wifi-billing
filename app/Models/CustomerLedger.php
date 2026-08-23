<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerLedger extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'customer_id',
        'type', // 'debt' or 'payment'
        'amount',
        'description',
        'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
