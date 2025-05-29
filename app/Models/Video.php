<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Video extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'videos';

    protected $fillable = [
        'request_id',
        'video_name',
        'video_url',
        'video_duration',
        'thumbnail_name',
        'thumbnail_url',
    ];

    public function videoRequest()
    {
        return $this->belongsTo(VideoRequest::class, 'request_id');
    }

    public function delete()
    {
        // Extract the relative path of the files from the URLs
        $videoPath = $this->getStoragePathFromUrl($this->video_url, 'videos');
        $thumbnailPath = $this->getStoragePathFromUrl($this->thumbnail_url, 'thumbnails');

        $disk = config('filesystems.default', 'local');

        if ($videoPath && Storage::disk($disk)->exists($videoPath)) {
            Storage::disk($disk)->delete($videoPath);
        }
        if ($thumbnailPath && Storage::disk($disk)->exists($thumbnailPath)) {
            Storage::disk($disk)->delete($thumbnailPath);
        }

        return parent::delete();
    }

    protected function getStoragePathFromUrl($url, $folder)
    {
        // Remove the base storage URL to get the relative path
        $parsed = parse_url($url, PHP_URL_PATH);
        $filename = basename($parsed);
        return $folder . '/' . $filename;
    }
}