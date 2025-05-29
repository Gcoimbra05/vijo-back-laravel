<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipPlanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:250',
            'payment_mode' => 'required|integer|in:1,2',
            'monthly_cost' => 'required|numeric',
            'annual_cost' => 'required|numeric',
            'payment_link' => 'nullable|string|max:255',
            'status' => 'required|integer|in:0,1,2',
        ];
    }
}