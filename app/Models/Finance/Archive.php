<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;
    protected $table = 'archives';

    protected $fillable = [
        'from_date',
        'date',
    ];
}
