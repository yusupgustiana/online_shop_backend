<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    private string $baseUrl = 'https://rajaongkir.komerce.id/api/v1';

    private function headers(): array
    {
        return [
            'key' => config('services.rajaongkir.key'),
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];
    }

    // ===== PROVINCE =====
    public function getProvinces()
    {
        return Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/destination/province")
            ->json();
    }

    // ===== CITY =====
    public function getCitiesByProvince($provinceId)
    {
        return Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/destination/city/{$provinceId}")
            ->json();
    }

    // ===== DISTRICT =====
    public function getDistrictsByCity($cityId)
    {
        return Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/destination/district/{$cityId}")
            ->json();
    }

    // ===== SUB DISTRICT =====
    public function getSubDistrictsByCity($cityId)
    {
        return Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/destination/sub-district/{$cityId}")
            ->json();
    }

    // ===== COST (ONGKIR) =====
    public function getCost($origin, $destination, $courier, $weight = 1000, $price = 'lowest')
    {
        return Http::asForm()
            ->withHeaders([
                'key' => config('services.rajaongkir.key'),
                'Accept' => 'application/json',
            ])
            ->post("{$this->baseUrl}/calculate/domestic-cost", [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
                'price' => $price,
            ])
            ->json();
    }

    // ===== TRACKING =====
    public function getWaybill($courier, $waybill)
    {
        return Http::asForm()
            ->withHeaders([
                'key' => config('services.rajaongkir.delivery_key'),
                'Accept' => 'application/json',
            ])
            ->post("{$this->baseUrl}/waybill", [
                'courier' => $courier,
                'waybill' => $waybill,
            ])
            ->json();
    }
}