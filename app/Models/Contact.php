<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Contact extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'contacts';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'parent_contact_id',
        'user_id',
        'business_id',
        'first_name',
        'last_name',
        'country_code',
        'mobile',
        'email',
        'is_advisor',
        'is_administrator',
        'member_type',
        'status',
    ];

    // Timestamps
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function groups()
    {
        return $this->belongsToMany(ContactGroup::class, 'contact_n2n_group', 'contact_id', 'group_id');
    }

    /**
     * Get all groups associated with this contact.
     *
     * @param int $contactId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getGroups(int $contactId)
    {
        return self::findOrFail($contactId)
            ->groups()
            ->get();
    }
}