<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Type\Decimal;

class LlmTemplate extends Model
{
    use HasFactory;

    protected $table = 'llm_templates';

    protected $fillable = [
        'name',
        'user_id',
        'llm',
        'system_prompt',
        'examples',
        'llm_temperature',
        'llm_response_max_length'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Reusable Business Logic Methods

    /**
     * Get all templates for a specific user.
     *
     * @param int $userId
     * @param int $startAt
     * @param int $perPage
     * @param string $orderBy
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTemplatesByUser(int $userId, int $startAt = 0, int $perPage = 15, string $orderBy = 'created_at DESC')
    {
        [$column, $direction] = explode(' ', $orderBy . ' ');
        return self::where('user_id', $userId)
            ->orderBy($column, $direction ?: 'DESC')
            ->skip($startAt)
            ->take($perPage)
            ->get();
    }

    /**
     * Find a template by ID that belongs to the specified user.
     *
     * @param int $templateId
     * @param int $userId
     * @return \App\Models\LlmTemplate|null
     */
    public static function findUserTemplate(int $templateId, int $userId)
    {
        return self::where('id', $templateId)
                   ->where('user_id', $userId)
                   ->first();
    }

    /**
     * Create a new template for a user.
     *
     * @param int $userId
     * @param string $systemPrompt
     * @param string $llm
     * @return \App\Models\LlmTemplate
     */
    public static function createForUser(int $userId, string $name, $llm, $systemPrompt, $examples, float $llm_temperature, int $llm_response_max_length)
    {
        return self::create([
            'user_id' => $userId,
            'name' => $name,
            'llm' => $llm,
            'system_prompt' => $systemPrompt,
            'examples' => $examples,

            'llm_temperature' => $llm_temperature,
            'llm_response_max_length' => $llm_response_max_length,

        ]);
    }

    /**
     * Update a template if it belongs to the user.
     *
     * @param int $templateId
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public static function updateUserTemplate(int $templateId, int $userId, array $data)
    {
        $template = self::findUserTemplate($templateId, $userId);
        
        if (!$template) {
            return false;
        }

        return $template->update($data);
    }

    /**
     * Delete a template if it belongs to the user.
     *
     * @param int $templateId
     * @param int $userId
     * @return bool
     */
    public static function deleteUserTemplate(int $templateId, int $userId)
    {
        $template = self::findUserTemplate($templateId, $userId);
        
        if (!$template) {
            return false;
        }

        return $template->delete();
    }

    /**
     * Search templates for a user by system prompt content.
     *
     * @param int $userId
     * @param string $search
     * @param int $perPage
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function searchUserTemplates(int $userId, string $search, int $perPage = 15)
    {
        return self::where('user_id', $userId)
                   ->where('system_prompt', 'like', "%{$search}%")
                   ->orderBy('created_at', 'DESC')
                   ->take($perPage)
                   ->get();
    }

    /**
     * Get templates by LLM type for a user.
     *
     * @param int $userId
     * @param string $llmType
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTemplatesByLlm(int $userId, string $llmType)
    {
        return self::where('user_id', $userId)
                   ->where('llm', $llmType)
                   ->orderBy('created_at', 'DESC')
                   ->get();
    }

    /**
     * Count total templates for a user.
     *
     * @param int $userId
     * @return int
     */
    public static function countUserTemplates(int $userId)
    {
        return self::where('user_id', $userId)->count();
    }
}