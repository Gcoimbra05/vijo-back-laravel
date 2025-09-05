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
        'title', #text OK
        'description', #text OK
        'tags', #text OK
        'min_record_time', #numerico
        'max_record_time', #numerico
        'emoji', #text OK
        'is_deleted', #boleano (sim não) OK
        'status', #boleano (sim não) OK
        'parent_catalog_id', #numerico OK
        'category_id', #numerico ok
        'is_promotional', #boleano (sim não) OK
        'is_premium', #booleano (sim não) OK
        'video_type_id', #numerico OK
        'is_multipart', #booleano (sim não) OK
        'admin_order' #numerico OK
    ];

    public function videoRequests()
    {
        return $this->hasMany(VideoRequest::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
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
        return $this->belongsTo(VideoType::class, 'video_type_id');
    }
}