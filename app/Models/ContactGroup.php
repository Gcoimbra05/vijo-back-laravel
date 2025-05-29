<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class ContactGroup extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'contact_groups';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'name',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    // Relationships
    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_n2n_group', 'group_id', 'contact_id')
            ->orderBy('contacts.first_name', 'ASC')
            ->orderBy('contacts.last_name', 'ASC');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Métodos utilitários similares ao CodeIgniter

    /**
     * Get all contacts associated with this group.
     *
     * @param int $groupId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getContacts(int $groupId)
    {
        return self::findOrFail($groupId)
            ->contacts()
            ->get();
    }

    /**
     * Get all groups for a specific user.
     *
     * @param int $userId
     * @param int $startAt
     * @param int $perPage
     * @param string $orderBy
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getGroupsByUser(int $userId, int $startAt = 0, int $perPage = 10, string $orderBy = 'name ASC')
    {
        [$column, $direction] = explode(' ', $orderBy . ' ');
        return self::where('user_id', $userId)
            ->orderBy($column, $direction ?: 'ASC')
            ->skip($startAt)
            ->take($perPage)
            ->paginate($perPage, ['*'], 'page', floor($startAt / $perPage) + 1);
    }
}