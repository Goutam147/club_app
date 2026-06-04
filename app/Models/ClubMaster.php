<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubMaster extends Model
{
    protected $table = 'clubmasters';

    protected $fillable = [
        'name',
        'logo',
        'address',
        'estd',
    ];
}
