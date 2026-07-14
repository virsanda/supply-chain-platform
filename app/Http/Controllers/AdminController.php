<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Port;
use App\Models\Article;
use App\Models\ApiLog;
use App\Models\UserActivityLog;
use App\Models\RiskScore;
use App\Models\NewsCache;
use App\Models\SystemSetting;
use App\Services\SentimentAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'users'          => User::count(),
            'active_users'   => User::where('is_active',true)->count(),
            'ports'          => Port::count(),
            'articles'       => Article::count(),
            'api_calls_today'=> ApiLog::whereDate('called_at',today())->count(),
            'api_errors'     => ApiLog::whereDate('called_at',today())->where('success',false)->count(),
            'news_today'     => NewsCache::whereDate('fetched_at',today())->count(),
            'high_risk'      => RiskScore::whereDate('score_date',today())->whereIn('risk_level',['high','critical'])->count(),
        ];
        $recentLogs    = UserActivityLog::with('user')->latest()->take(10)->get();
        $recentApiLogs = ApiLog::latest()->take(10)->get();
        return view('admin.dashboard', compact('stats','recentLogs','recentApiLogs'));
    }

    // ── Users ─────────────────────────────────────────────────

    public function users()
    {
        $users = User::orderBy('role')->orderBy('name')->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function deleteUser(int $id)
    {
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) return back()->with('error','Tidak bisa menghapus akun sendiri.');
        $user->delete();
        return back()->with('success',"User {$user->name} dihapus.");
    }

    public function updateRole(Request $request, int $id)
    {
        $request->validate(['role'=>'required|in:admin,user']);
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) return response()->json(['success'=>false,'message'=>'Tidak bisa mengubah role sendiri.']);
        $user->update(['role'=>$request->role]);
        return response()->json(['success'=>true,'message'=>"Role {$user->name} diubah ke {$request->role}."]);
    }

    // ── Ports ─────────────────────────────────────────────────

    public function ports()
    {
        $ports = Port::orderBy('country_name')->orderBy('port_name')->paginate(30);
        return view('admin.ports', compact('ports'));
    }

    public function deletePort(int $id)
    {
        Port::findOrFail($id)->delete();
        return back()->with('success','Pelabuhan dihapus.');
    }

    // ── Articles ──────────────────────────────────────────────

    public function articles()
    {
        $articles = Article::with('author')->orderByDesc('created_at')->paginate(15);
        return view('admin.articles', compact('articles'));
    }

    public function createArticle()
    {
        return view('admin.articles_form', ['article'=>null,'action'=>route('admin.articles.store'),'method'=>'POST']);
    }

    public function storeArticle(Request $request)
    {
        $request->validate(['title'=>'required|string|max:255','body'=>'required|string','category'=>'required','status'=>'required']);
        $data             = $request->only('title','body','excerpt','category','status','country_code','cover_image');
        $data['author_id']= Auth::id();
        $data['slug']     = Str::slug($request->title).'-'.Str::random(5);
        if ($request->status==='published') $data['published_at'] = now();
        Article::create($data);
        return redirect()->route('admin.articles')->with('success','Artikel berhasil dibuat.');
    }

    public function editArticle(int $id)
    {
        $article = Article::findOrFail($id);
        return view('admin.articles_form', ['article'=>$article,'action'=>route('admin.articles.update',$id),'method'=>'PUT']);
    }

    public function updateArticle(Request $request, int $id)
    {
        $request->validate(['title'=>'required|string|max:255','body'=>'required|string','category'=>'required','status'=>'required']);
        $article = Article::findOrFail($id);
        $data    = $request->only('title','body','excerpt','category','status','country_code','cover_image');
        if ($request->status==='published' && !$article->published_at) $data['published_at'] = now();
        $article->update($data);
        return redirect()->route('admin.articles')->with('success','Artikel diperbarui.');
    }

    public function deleteArticle(int $id)
    {
        Article::findOrFail($id)->delete();
        return back()->with('success','Artikel dihapus.');
    }

    // ── Settings ──────────────────────────────────────────────

    public function settings()
    {
        $settings = SystemSetting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('admin.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            SystemSetting::set($key, $value);
        }
        SentimentAnalysisService::clearCache();
        return back()->with('success','Pengaturan disimpan.');
    }
}
