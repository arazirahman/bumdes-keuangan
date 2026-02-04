<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'trx_date',
        'type',
        'unit_usaha_id',
        'category_id',
        'description',
        'amount',
        'proof_path',
        'created_by'
    ];

    protected $casts = [
        'trx_date' => 'date',
    ];

    public function unitUsaha()
    {
        return $this->belongsTo(UnitUsaha::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function village()
    {
        return $this->belongsTo(\App\Models\Village::class);
    }
}
