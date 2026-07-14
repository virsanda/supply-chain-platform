<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Watchlist;
use App\Services\RiskScoringEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    public function __construct(private RiskScoringEngine $riskEngine) {}

    public function index()
    {
        $watchlist = Watchlist::where('user_id',Auth::id())->with('country')->latest()->get();

        $riskScores = [];
        foreach ($watchlist as $item) {
            $riskScores[$item->country_code] = $this->riskEngine->calculate($item->country_code);
        }

        $countries = Country::active()->orderBy('name')->get(['code','name','flag_emoji','region']);
        return view('watchlist.index', compact('watchlist','riskScores','countries'));
    }

    public function add(Request $request)
    {
        $request->validate(['country_code'=>'required|string|size:2']);
        $code    = strtoupper($request->country_code);
        $country = Country::where('code',$code)->firstOrFail();

        $existing = Watchlist::where('user_id',Auth::id())->where('country_code',$code)->exists();
        if ($existing) {
            return response()->json(['success'=>false,'message'=>"{$country->name} sudah ada di watchlist Anda."]);
        }

        Watchlist::create(['user_id'=>Auth::id(),'country_code'=>$code,'country_name'=>$country->name,'notify_risk_change'=>true]);
        return response()->json(['success'=>true,'message'=>"{$country->name} ditambahkan ke watchlist.",'country_code'=>$code,'country_name'=>$country->name,'flag'=>$country->flag_emoji]);
    }

    public function remove(int $id)
    {
        $item = Watchlist::where('id',$id)->where('user_id',Auth::id())->firstOrFail();
        $name = $item->country_name;
        $item->delete();
        if (request()->ajax()) return response()->json(['success'=>true,'message'=>"{$name} dihapus dari watchlist."]);
        return back()->with('success', "{$name} dihapus dari watchlist.");
    }
}
