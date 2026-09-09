<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkspaceSetting extends Model
{
    protected $fillable = [
        'company_name',
        'timezone',
        'workweek_start',
        'standard_hours',
        'currency',
        'date_format',
        'require_time_notes',
        'weekly_summary',
        'leave_approval_role',
        'updated_by',
    ];

    protected $casts = [
        'standard_hours' => 'decimal:2',
        'require_time_notes' => 'boolean',
        'weekly_summary' => 'boolean',
    ];
}
