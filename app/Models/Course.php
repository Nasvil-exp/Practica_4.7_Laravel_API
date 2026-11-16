<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    protected $fillable = [
        'academy_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'capacity',
    ];

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academy::class);
    }
}
