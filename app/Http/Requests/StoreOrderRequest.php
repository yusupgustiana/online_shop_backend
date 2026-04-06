<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'address_id' => 'required|exists:addresses,id',
            'shipping_cost' => 'required|numeric',
            'payment_method' => 'required',
            'bank' => 'required|in:bca,bni,bri,permata,cimb',
            'shipping_service' => 'required',
            'order_items' => 'required|array',
            'order_items.*.product_id' => 'required|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
        ];
    }
}