<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'request_id' => 'required|exists:video_requests,id',
            'video_name' => 'required|string|max:255',
            'video_url' => 'required|string',
            'video_duration' => 'required|integer',
            'thumbnail_name' => 'required|string|max:255',
            'thumbnail_url' => 'required|string',
        ];
    }
}