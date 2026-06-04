<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'note',
        'start_at',
        'expiry_at',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'expiry_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
