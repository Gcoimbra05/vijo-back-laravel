<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkipVijo extends Model
{
    protected $table = 'skip_vijos';

    protected $fillable = [
        'user_id',
        'catalog_id',
        'skipped_at',
    ];

    protected $dates = [
        'skipped_at',
        'created_at',
        'updated_at',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function catalog()
    {
        return $this->belongsTo(Catalog::class);
    }
}
