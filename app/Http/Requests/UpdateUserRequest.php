<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $this->user->id,
            'password' => 'sometimes|required|string|min:8',
            'country_code' => 'sometimes|nullable|string|max:10',
            'mobile' => 'sometimes|nullable|string|max:20',
            'guided_tours' => 'sometimes|nullable|boolean',
            'status' => 'sometimes|required|integer|in:0,1,2,3',
            'is_verified' => 'sometimes|nullable|boolean',
            'plan_id' => 'sometimes|nullable|exists:membership_plans,id',
        ];
    }
}