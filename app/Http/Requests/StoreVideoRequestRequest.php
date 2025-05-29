<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequestRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'catalog_id' => 'required|exists:catalogs,id',
            'ref_user_id' => 'nullable|exists:users,id',
            'ref_first_name' => 'nullable|string|max:100',
            'ref_last_name' => 'nullable|string|max:100',
            'ref_country_code' => 'nullable|integer',
            'ref_mobile' => 'nullable|string|max:20',
            'ref_email' => 'nullable|string|email|max:200',
            'status' => 'required|integer|in:0,1,2,3',
        ];
    }
}