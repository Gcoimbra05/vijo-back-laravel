<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReferralCodeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'affiliate_id' => 'required|exists:affiliates,id',
            'code' => 'required|string|max:100|unique:referral_codes,code',
            'commission' => 'nullable|numeric',
            'number_uses' => 'nullable|integer',
            'max_number_uses' => 'nullable|integer',
            'discount' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ];
    }
}