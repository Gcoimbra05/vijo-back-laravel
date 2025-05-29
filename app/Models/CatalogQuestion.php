<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class CatalogQuestion extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'catalog_questions';

    protected $fillable = [
        'catalog_id',
        'reference_type',
        'metric1_title',
        'metric1_question',
        'metric1_question_option1',
        'metric1_question_option2',
        'metric1_question_option1val',
        'metric1_question_option2val',
        'metric1_question_label',
        'metric1_significance',
        'metric2_title',
        'metric2_question',
        'metric2_question_option1',
        'metric2_question_option2',
        'metric2_question_option1val',
        'metric2_question_option2val',
        'metric2_question_label',
        'metric2_significance',
        'metric3_title',
        'metric3_question',
        'metric3_question_option1',
        'metric3_question_option2',
        'metric3_question_option1val',
        'metric3_question_option2val',
        'metric3_question_label',
        'metric3_significance',
        'video_question',
        'metric4_significance',
        'metric5_significance',
        'status',
    ];

    public function catalog()
    {
        return $this->belongsTo(Catalog::class, 'catalog_id');
    }
}