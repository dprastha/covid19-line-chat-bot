<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DataCovidService
{
    public function index()
    {
        $results = Http::get('https://api.kawalcorona.com/indonesia')->json();

        $message = $results[0]["name"] . "\n" . "Positif : " . $results[0]["positif"] . "\n" . "Sembuh : " . $results[0]["sembuh"] . "\n" . "Meninggal : " . $results[0]["meninggal"] . "\n" . "Dirawat : " . $results[0]["dirawat"];

        return $message;
    }
}
