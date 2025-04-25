<?php

namespace App\Models\Leadman;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'bu_injection_date',
        'bagging_report_date',
        'predicted_stem_cut_y', // Add the new column to the fillable array
    ];
}
