<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\DataVisualizationController;
use App\Http\Controllers\AdminController;

Route::get('/', fn() => redirect()->route('login'));

// Auth
Route::get('/login',     [AuthController::class,'showLogin'])->name('login');
Route::post('/login',    [AuthController::class,'login'])->name('login.post');
Route::get('/register',  [AuthController::class,'showRegister'])->name('register');
Route::post('/register', [AuthController::class,'register'])->name('register.post');
Route::post('/logout',   [AuthController::class,'logout'])->name('logout');

// Authenticated
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    Route::get('/countries',         [CountryController::class,'index'])->name('countries.index');
    Route::post('/countries/search', [CountryController::class,'search'])->name('countries.search');
    Route::get('/countries/{code}',  [CountryController::class,'show'])->name('countries.show');

    Route::get('/weather',        [WeatherController::class,'index'])->name('weather.index');
    Route::get('/weather/{code}', [WeatherController::class,'show'])->name('weather.show');

    Route::get('/currency',        [CurrencyController::class,'index'])->name('currency.index');
    Route::get('/currency/{code}', [CurrencyController::class,'show'])->name('currency.show');

    Route::get('/news',               [NewsController::class,'index'])->name('news.index');
    Route::get('/news/topic/{topic}', [NewsController::class,'byTopic'])->name('news.topic');

    Route::get('/ports',        [PortController::class,'index'])->name('ports.index');
    Route::get('/ports/search', [PortController::class,'search'])->name('ports.search');

    Route::get('/comparison',          [ComparisonController::class,'index'])->name('comparison.index');
    Route::post('/comparison/compare', [ComparisonController::class,'compare'])->name('comparison.compare');

    Route::get('/visualization',        [DataVisualizationController::class,'index'])->name('visualization.index');
    Route::get('/visualization/{code}', [DataVisualizationController::class,'show'])->name('visualization.show');

    Route::get('/watchlist',         [WatchlistController::class,'index'])->name('watchlist.index');
    Route::post('/watchlist/add',    [WatchlistController::class,'add'])->name('watchlist.add');
    Route::delete('/watchlist/{id}', [WatchlistController::class,'remove'])->name('watchlist.remove');
});

// Admin
Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                    [AdminController::class,'index'])->name('dashboard');
    Route::get('/users',               [AdminController::class,'users'])->name('users');
    Route::delete('/users/{id}',       [AdminController::class,'deleteUser'])->name('users.delete');
    Route::patch('/users/{id}/role',   [AdminController::class,'updateRole'])->name('users.role');
    Route::get('/ports',               [AdminController::class,'ports'])->name('ports');
    Route::delete('/ports/{id}',       [AdminController::class,'deletePort'])->name('ports.delete');
    Route::get('/articles',            [AdminController::class,'articles'])->name('articles');
    Route::get('/articles/create',     [AdminController::class,'createArticle'])->name('articles.create');
    Route::post('/articles',           [AdminController::class,'storeArticle'])->name('articles.store');
    Route::get('/articles/{id}/edit',  [AdminController::class,'editArticle'])->name('articles.edit');
    Route::put('/articles/{id}',       [AdminController::class,'updateArticle'])->name('articles.update');
    Route::delete('/articles/{id}',    [AdminController::class,'deleteArticle'])->name('articles.delete');
    Route::get('/settings',            [AdminController::class,'settings'])->name('settings');
    Route::post('/settings',           [AdminController::class,'saveSettings'])->name('settings.save');
});
