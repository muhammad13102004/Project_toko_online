<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaOngkirController extends Controller
{
    public function getProvinces()
    {
        $response = Http::withHeaders([
            'key' => config('services.rajaongkir.key')
        ])->get(
            config('services.rajaongkir.base_url') . '/destination/province'
        );

        return response()->json($response->json());
    }

    
public function getCities($provinceId)
{
    $response = Http::withHeaders([
        'key' => config('services.rajaongkir.key')
    ])->get(
        config('services.rajaongkir.base_url') . '/destination/city/' . $provinceId
    );

    return response()->json($response->json());
}

public function checkOngkir(Request $request)
{
    $response = Http::asForm()->withHeaders([
        'key' => config('services.rajaongkir.key')
    ])->post(
        config('services.rajaongkir.base_url') . '/calculate/domestic-cost',
        [
            'origin' => $request->origin,
            'destination' => $request->destination,
            'weight' => $request->weight,
            'courier' => $request->courier,
            'price' => 'lowest'
        ]
    );

    return response()->json($response->json());
}

}
