<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Expense extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'title',
        'amount',
        'category',
        'expense_date',
        'payment_method',
        'reference_no',
        'notes',
        'site_id',
        'recorded_by',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function categories(): array
    {
        return [
            'Umeme & LUKU'     => 'Umeme & LUKU',
            'Bandwidth / ISP'  => 'Bandwidth (ISP)',
            'Vifaa & Cables'   => 'Vifaa & Waya',
            'Matengenezo'      => 'Matengenezo',
            'Kodi ya Mnara'    => 'Kodi ya Site/Mnara',
            'Mishahara'        => 'Mishahara & Posho',
            'Ulinzi'           => 'Ulinzi & Usalama',
            'Leseni & Ushuru'  => 'Leseni & Ushuru',
            'Mengineyo'        => 'Mengineyo',
        ];
    }

    public static function paymentMethods(): array
    {
        return [
            'Cash'         => 'Taslimu (Cash)',
            'M-Pesa'       => 'M-Pesa',
            'Tigo Pesa'    => 'Tigo Pesa',
            'Airtel Money' => 'Airtel Money',
            'Benki'        => 'Benki',
            'Nyingine'     => 'Nyingine',
        ];
    }
}
