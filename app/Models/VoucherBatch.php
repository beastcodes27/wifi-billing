<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherBatch extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'batch_reference',
        'package_id',
        'created_by',
        'quantity',
        'notes',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'batch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
