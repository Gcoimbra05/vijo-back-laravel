<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'country_code' => 'nullable|string|max:10',
            'mobile' => 'nullable|string|max:20',
            'guided_tours' => 'nullable|boolean',
            'status' => 'required|integer|in:0,1,2,3',
            'is_verified' => 'nullable|boolean',
            'plan_id' => 'nullable|exists:membership_plans,id',
        ];
    }
}