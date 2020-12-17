<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->state || !$request->dateStart || !$request->dateEnd) {
            $result = [
                'error' => true,
                'msg' => "Para consultar os dados informe a UF e as data de início e fim da consulta no formato '?state=PR&dateStart=2020-05-10&dateEnd=2020-05-18'"
            ];

            return json_encode($result, JSON_UNESCAPED_UNICODE);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Token cd06accc7cba9e0b48b4d3106f3ea4359f593725'
        ])->get("https://api.brasil.io/dataset/covid19/caso/data/?state=$request->state&date=$request->dateEnd");

        $response = $response->json();
        $collection = collect($response['results']);
        $sorted = $collection->sortByDesc('confirmed_per_100k_inhabitants')->values();
        $arr_result = [];
        for ($count = 0; $count < 10; $count++) {
            array_push($arr_result, $sorted[$count]);
        };

        $newCount = 0;
        while ($newCount < count($arr_result)) {

            $response = Http::withHeaders([
                'MeuNome' => 'Rogerio'
            ])->post('https://us-central1-lms-nuvem-mestra.cloudfunctions.net/testApi', [
                'id' => $newCount,
                'nomeCidade' => $arr_result[0]['city'],
                'percentualDeCasos' => $arr_result[0]['confirmed_per_100k_inhabitants']
            ]);
            $newCount++;
        }
    }
}
