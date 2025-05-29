<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'group_id' => 'nullable|exists:contact_groups,id',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'country_code' => 'nullable|integer',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|max:200',
            'status' => 'required|integer|in:0,1,2',
        ];
    }
}