<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'acquired_date',
        'unit_cost',
        'qty',
        'condition',
        'location',
        'note',
        'unit_usaha_id',
        'created_by'
    ];

    protected $casts = [
        'acquired_date' => 'date',
    ];

    public function unitUsaha()
    {
        return $this->belongsTo(UnitUsaha::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTotalValueAttribute(): int
    {
        return (int)$this->unit_cost * (int)$this->qty;
    }

    public function village()
    {
        return $this->belongsTo(\App\Models\Village::class);
    }
}
