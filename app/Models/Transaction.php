<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_id',
        'user_id',
        'amount',
        'remark',
        'document_url',
        'method',
        'type',
        'status',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'created_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_id)) {
                $transaction->transaction_id = self::generateTransactionId();
            }
        });
    }

    public static function generateTransactionId(): string
    {
        $dateStr = now()->format('dmy'); // Format: DDMMYY
        
        // Count transactions starting with today's date prefix to find the next sequence number
        $count = self::where('transaction_id', 'like', $dateStr . '%')->count();
        
        do {
            $count++;
            $id = $dateStr . str_pad($count, 3, '0', STR_PAD_LEFT);
        } while (self::where('transaction_id', $id)->exists());

        return $id;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
