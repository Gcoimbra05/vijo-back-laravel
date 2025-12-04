<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class VijoPlan extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'vijo_plans';
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'length_in_weeks',
        'ai_json',
    ];

    public function catalogs() {
        return $this->hasMany(Catalog::class);
    }

    public static function getByUser($userId)
    {
        return self::where('user_id', $userId)->get();
    }
}