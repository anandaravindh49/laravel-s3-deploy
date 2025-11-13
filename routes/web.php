<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\S3TestController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $dashboardData = [
        'stats' => [
            ['label' => 'Total Players',  'value' => '1,250', 'icon' => '🎮', 'bg' => 'bg-gradient-to-br from-blue-500 to-blue-600'],
            ['label' => 'Active Players', 'value' => '940',   'icon' => '✅', 'bg' => 'bg-gradient-to-br from-green-500 to-green-600'],
            ['label' => 'Inactive',      'value' => '310',   'icon' => '⏸️', 'bg' => 'bg-gradient-to-br from-yellow-500 to-yellow-600'],
            ['label' => 'Revenue',       'value' => '$42.5K','icon' => '💰', 'bg' => 'bg-gradient-to-br from-purple-500 to-purple-600'],
        ],
        'players' => [
            ['id' => 1, 'name' => 'Alex Morgan',    'team' => 'Lions',   'status' => 'Active',   'joined' => '2022-01-08', 'score' => 4850],
            ['id' => 2, 'name' => 'Sam Carter',     'team' => 'Wolves',  'status' => 'Active',   'joined' => '2021-09-12', 'score' => 5230],
            ['id' => 3, 'name' => 'Jamie Lee',      'team' => 'Hawks',   'status' => 'Inactive', 'joined' => '2020-07-24', 'score' => 3100],
            ['id' => 4, 'name' => 'Casey Nguyen',   'team' => 'Tigers',  'status' => 'Active',   'joined' => '2023-03-02', 'score' => 4920],
            ['id' => 5, 'name' => 'Riley Parker',   'team' => 'Bulls',   'status' => 'Active',   'joined' => '2019-11-30', 'score' => 5810],
            ['id' => 6, 'name' => 'Jordan Smith',   'team' => 'Eagles',  'status' => 'Active',   'joined' => '2023-05-15', 'score' => 4100],
            ['id' => 7, 'name' => 'Taylor Johnson', 'team' => 'Lions',   'status' => 'Inactive', 'joined' => '2021-02-20', 'score' => 2950],
            ['id' => 8, 'name' => 'Morgan Davis',   'team' => 'Wolves',  'status' => 'Active',   'joined' => '2022-08-10', 'score' => 5050],
        ]
    ];
    return view('dashboard', $dashboardData);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
Route::get('/test-s3', [S3TestController::class, 'upload']);
