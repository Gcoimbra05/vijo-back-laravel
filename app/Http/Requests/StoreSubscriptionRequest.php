<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'plan_id' => 'required|exists:membership_plans,id',
            'stripe_customer_id' => 'nullable|string|max:100',
            'stripe_subscription_id' => 'nullable|string|max:255',
            'status' => 'required|integer|in:1,2,3,4,5',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'cancel_at' => 'nullable|date',
            'cancelled_at' => 'nullable|date',
            'reason' => 'nullable|string|max:255',
        ];
    }
}