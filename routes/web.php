<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ComplaintMessageController;
use App\Http\Controllers\ComplaintPhotoController;
use App\Http\Controllers\ComplaintTrackController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PriceBoardController;
use App\Http\Controllers\PricePostingController;
use App\Http\Controllers\PriceVerificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Shopper;
use App\Http\Controllers\Vendor;
use App\Http\Middleware\EnsureRole;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| TindaPresyo routes: the map from a web address to the Controller that
| answers it. Routes are grouped by who may open them.
|--------------------------------------------------------------------------
*/

// Short helper:  role('admin', 'inspector')  =>  "EnsureRole:admin,inspector"
$role = fn (string ...$roles) => EnsureRole::class . ':' . implode(',', $roles);

// ---- Public pages: anyone can open these ---------------------------------
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/prices', [PriceBoardController::class, 'index'])->name('prices.index');
Route::get('/stalls/{stall}', [PriceBoardController::class, 'stall'])->name('stalls.show');
Route::get('/track', [ComplaintTrackController::class, 'index'])->middleware('throttle:30,1')->name('track');

// ---- Visitors who are not logged in ---------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
});

// ---- Any logged-in user ---------------------------------------------------
Route::middleware($role())->group(function () use ($role) {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/evaluation', [EvaluationController::class, 'form'])->name('evaluation.form');
    Route::post('/evaluation', [EvaluationController::class, 'store'])->name('evaluation.store');

    // Photo evidence is private; the controller checks who may see it.
    Route::get('/complaints/{complaint}/photo', [ComplaintPhotoController::class, 'show'])->name('complaints.photo');

    // Administrator <-> vendor messages on a verified complaint.
    Route::post('/complaints/{complaint}/messages', [ComplaintMessageController::class, 'store'])
        ->middleware($role('admin', 'vendor'))->name('complaints.messages.store');
});

// ---- Daily price posting: vendors (own stalls), inspectors, administrator --
Route::middleware($role('vendor', 'inspector', 'admin'))->prefix('post-prices')->name('posting.')->group(function () {
    Route::get('/', [PricePostingController::class, 'index'])->name('index');
    Route::get('/{stall}', [PricePostingController::class, 'edit'])->name('edit');
    Route::put('/{stall}', [PricePostingController::class, 'update'])->name('update');
});

// ---- Price verification: inspectors and administrator ---------------------
Route::middleware($role('inspector', 'admin'))->group(function () {
    Route::get('/verification', [PriceVerificationController::class, 'index'])->name('verification.index');
    Route::post('/verification', [PriceVerificationController::class, 'store'])->name('verification.store');
});

// ---- Vendors ---------------------------------------------------------------
Route::middleware($role('vendor'))->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/stalls', [Vendor\StallController::class, 'index'])->name('stalls.index');
    Route::get('/stalls/create', [Vendor\StallController::class, 'create'])->name('stalls.create');
    Route::post('/stalls', [Vendor\StallController::class, 'store'])->name('stalls.store');

    Route::get('/complaints', [Vendor\ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/{complaint}', [Vendor\ComplaintController::class, 'show'])->name('complaints.show');
});

// ---- Shoppers --------------------------------------------------------------
Route::middleware($role('shopper'))->prefix('shopper')->name('shopper.')->group(function () {
    Route::get('/complaints', [Shopper\ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/create', [Shopper\ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [Shopper\ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/complaints/{complaint}', [Shopper\ComplaintController::class, 'show'])->name('complaints.show');

    // Used by the complaint form to show the posted prices of the chosen stall.
    Route::get('/stalls/{stall}/prices', [Shopper\ComplaintController::class, 'stallPrices'])->name('stalls.prices');
});

// ---- Market administrator --------------------------------------------------
Route::middleware($role('admin'))->prefix('admin')->name('admin.')->group(function () {
    Route::get('/complaints', [Admin\ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/{complaint}', [Admin\ComplaintController::class, 'show'])->name('complaints.show');
    Route::post('/complaints/{complaint}/status', [Admin\ComplaintController::class, 'updateStatus'])->name('complaints.status');

    Route::get('/stalls', [Admin\StallController::class, 'index'])->name('stalls.index');
    Route::get('/stalls/{stall}/edit', [Admin\StallController::class, 'edit'])->name('stalls.edit');
    Route::put('/stalls/{stall}', [Admin\StallController::class, 'update'])->name('stalls.update');
    Route::patch('/stalls/{stall}/status', [Admin\StallController::class, 'setStatus'])->name('stalls.status');

    Route::get('/flagged', [Admin\FlaggedStallController::class, 'index'])->name('flagged.index');
    Route::post('/flagged/{stall}/clear', [Admin\FlaggedStallController::class, 'clear'])->name('flagged.clear');

    Route::resource('commodities', Admin\CommodityController::class)->except(['show', 'destroy']);

    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [Admin\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [Admin\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [Admin\UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle', [Admin\UserController::class, 'toggle'])->name('users.toggle');

    Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/{type}', [Admin\ReportController::class, 'export'])->name('reports.export');

    Route::get('/evaluation', [Admin\EvaluationResultController::class, 'index'])->name('evaluation.index');
});
