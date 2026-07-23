<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    protected $fillable = [
        'title',
        'type',
        'default_amount',
        'due_date',
        'event_id',
        'status',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'default_amount' => 'decimal:2',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class, 'fee_type_id');
    }
}
