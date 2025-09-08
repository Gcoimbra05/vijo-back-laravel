<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Catalog extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'catalogs';

    protected $fillable = [
        'title', 
        'description', 
        'tags',
        'min_record_time', 
        'max_record_time',
        'emoji',
        'is_deleted',
        'status',
        'parent_catalog_id',
        'category_id',
        'is_promotional',
        'is_premium',
        'video_type_id', 
        'is_multipart',
        'admin_order'
    ];

    public function videoRequests()
    {
        return $this->hasMany(VideoRequest::class);
    }


    public function parentCatalog()
    {
        return $this->belongsTo(Catalog::class, 'parent_catalog_id');
    }

    public function childCatalogs()
    {
        return $this->hasMany(Catalog::class, 'parent_catalog_id');
    }

     public function videoType()
    {
        return $this->belongsTo(VideoType::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}