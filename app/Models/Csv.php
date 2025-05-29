<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Csv extends Model
{
    use HasFactory;

    protected $table = 'csvs';

    protected $fillable = [
        'response_id',
        's3_url',
    ];

    // Relationships
    public function emloResponse()
    {
        return $this->belongsTo(EmloResponse::class, 'response_id');
    }

    // Reusable Business Logic Methods

    /**
     * Get all CSV records with pagination.
     *
     * @param int $startAt
     * @param int $perPage
     * @param string $orderBy
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllCsvs(int $startAt = 0, int $perPage = 15, string $orderBy = 'created_at DESC')
    {
        [$column, $direction] = explode(' ', $orderBy . ' ');
        return self::orderBy($column, $direction ?: 'DESC')
            ->skip($startAt)
            ->take($perPage)
            ->get();
    }

    /**
     * Find a CSV by ID.
     *
     * @param int $csvId
     * @return \App\Models\Csv|null
     */
    public static function findCsv(int $csvId)
    {
        return self::find($csvId);
    }

    /**
     * Create a new CSV record.
     *
     * @param string $responseId
     * @param string|null $s3Url
     * @return \App\Models\Csv
     */
    public static function createCsv(string $responseId, ?string $s3Url = null)
    {
        return self::create([
            'response_id' => $responseId,
            's3_url' => $s3Url,
        ]);
    }

    /**
     * Update a CSV record.
     *
     * @param int $csvId
     * @param array $data
     * @return bool
     */
    public static function updateCsv(int $csvId, array $data)
    {
        $csv = self::find($csvId);
        
        if (!$csv) {
            return false;
        }

        return $csv->update($data);
    }

    /**
     * Delete a CSV record.
     *
     * @param int $csvId
     * @return bool
     */
    public static function deleteCsv(int $csvId)
    {
        $csv = self::find($csvId);
        
        if (!$csv) {
            return false;
        }

        return $csv->delete();
    }

    /**
     * Get CSVs by response ID.
     *
     * @param string $responseId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCsvsByResponse(string $responseId)
    {
        return self::where('response_id', $responseId)
                   ->orderBy('created_at', 'DESC')
                   ->get();
    }

    /**
     * Get CSVs with S3 URLs only.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCsvsWithUrls()
    {
        return self::whereNotNull('s3_url')
                   ->where('s3_url', '!=', '')
                   ->orderBy('created_at', 'DESC')
                   ->get();
    }

    /**
     * Count total CSV records.
     *
     * @return int
     */
    public static function countCsvs()
    {
        return self::count();
    }

    /**
     * Search CSVs by response ID pattern.
     *
     * @param string $search
     * @param int $perPage
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function searchCsvs(string $search, int $perPage = 15)
    {
        return self::where('response_id', 'like', "%{$search}%")
                   ->orderBy('created_at', 'DESC')
                   ->take($perPage)
                   ->get();
    }

    /**
     * Create CSV with S3 upload (for uploadAndStore functionality).
     *
     * @param string $responseId
     * @param string $s3Url
     * @return \App\Models\Csv
     */
    public static function createWithS3Upload(string $responseId, string $s3Url)
    {
        return self::create([
            'response_id' => $responseId,
            's3_url' => $s3Url,
        ]);
    }

    /**
     * Get the latest CSV for a response.
     *
     * @param string $responseId
     * @return \App\Models\Csv|null
     */
    public static function getLatestForResponse(string $responseId)
    {
        return self::where('response_id', $responseId)
                   ->orderBy('created_at', 'DESC')
                   ->first();
    }
}