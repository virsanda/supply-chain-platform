<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RiskScore;
use App\Services\RiskScoringEngine;
use Illuminate\Http\Request;

class RiskApiController extends Controller
{
    public function __construct(private RiskScoringEngine $engine) {}

    public function index()
    {
        return response()->json(RiskScore::whereDate('score_date',today())->with('country')->get());
    }

    public function show(string $code)
    {
        return response()->json($this->engine->calculate(strtoupper($code)));
    }

    public function calculate(Request $request)
    {
        $request->validate(['country_code'=>'required|string|size:2']);
        return response()->json($this->engine->calculate(strtoupper($request->country_code)));
    }
}
