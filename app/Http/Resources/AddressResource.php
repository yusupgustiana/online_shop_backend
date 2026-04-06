<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'full_address' => $this->full_address,
            'phone' => $this->phone,
            'postal_code' => $this->postal_code,

            // ID tetap ada (untuk update/edit)
            'prov_id' => $this->prov_id,
            'city_id' => $this->city_id,
            'district_id' => $this->district_id,

            // Nama wilayah (untuk display)
            'province' => $this->province->name ?? null,
            'city' => $this->city->name ?? null,
            'district' => $this->district->name ?? null,

            // Full address siap pakai 🔥
            'full_address_complete' =>
                $this->full_address . ', ' .
                ($this->district->name ?? '') . ', ' .
                ($this->city->name ?? '') . ', ' .
                ($this->province->name ?? ''),
        ];
    }
}