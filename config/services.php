<?php
return [
    'openmeteo'    => ['base_url' => env('OPENMETEO_BASE_URL','https://api.open-meteo.com/v1')],
    'worldbank'    => ['base_url' => env('WORLDBANK_BASE_URL','https://api.worldbank.org/v2')],
    'restcountries'=> ['base_url' => env('RESTCOUNTRIES_BASE_URL','https://restcountries.com/v3.1')],
    'exchangerate' => ['base_url' => env('EXCHANGERATE_BASE_URL','https://v6.exchangerate-api.com/v6'),'key'=>env('EXCHANGERATE_API_KEY','')],
    'gnews'        => ['base_url' => env('GNEWS_BASE_URL','https://gnews.io/api/v4'),'key'=>env('GNEWS_API_KEY','')],
    'mailgun'      => ['domain'=>env('MAILGUN_DOMAIN'),'secret'=>env('MAILGUN_SECRET'),'endpoint'=>env('MAILGUN_ENDPOINT','api.mailgun.net'),'scheme'=>'https'],
];
