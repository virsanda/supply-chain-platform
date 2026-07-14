<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CountryApiController;
use App\Http\Controllers\Api\RiskApiController;
use App\Http\Controllers\Api\PortApiController;
use App\Http\Controllers\Api\NewsApiController;
use App\Http\Controllers\Api\CurrencyApiController;
use App\Http\Controllers\Api\WeatherApiController;

Route::prefix('v1')->group(function () {
    Route::get('/countries',             [CountryApiController::class,'index']);
    Route::get('/countries/{code}',      [CountryApiController::class,'show']);
    Route::get('/countries/{code}/gdp',  [CountryApiController::class,'gdp']);

    Route::get('/risk',                  [RiskApiController::class,'index']);
    Route::get('/risk/{code}',           [RiskApiController::class,'show']);
    Route::post('/risk/calculate',       [RiskApiController::class,'calculate']);

    Route::get('/ports',                 [PortApiController::class,'index']);
    Route::get('/ports/{id}',            [PortApiController::class,'show']);
    Route::get('/ports/country/{code}',  [PortApiController::class,'byCountry']);

    Route::get('/news',                  [NewsApiController::class,'index']);
    Route::get('/news/sentiment',        [NewsApiController::class,'sentiment']);
    Route::get('/news/topic/{topic}',    [NewsApiController::class,'byTopic']);

    Route::get('/currency',              [CurrencyApiController::class,'index']);
    Route::get('/currency/{code}',       [CurrencyApiController::class,'show']);

    Route::get('/weather/{code}',        [WeatherApiController::class,'show']);
    Route::get('/weather/risk/{code}',   [WeatherApiController::class,'riskScore']);
});
