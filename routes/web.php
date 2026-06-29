<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\ProdiController;
use App\Http\Controllers\Master\SemesterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\UnitActivityController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/rekap', [DashboardController::class, 'rekap'])->name('dashboard.rekap');
    Route::get('/dashboard/charts/prestasi-by-semester', [DashboardController::class, 'prestasiBySemester'])->name('charts.prestasi.semester');
    Route::get('/dashboard/charts/prestasi-by-prodi', [DashboardController::class, 'prestasiByProdi'])->name('charts.prestasi.prodi');
    Route::get('/dashboard/charts/claims', [DashboardController::class, 'claims'])->name('charts.claims');
    Route::get('/dashboard/charts/beasiswa', [DashboardController::class, 'beasiswa'])->name('charts.beasiswa');
    Route::get('/dashboard/charts/tracer-study', [DashboardController::class, 'tracerStudy'])->name('charts.tracer');
    Route::get('/dashboard/charts/summary-cards', [DashboardController::class, 'summaryCardCharts'])->name('charts.summary.cards');
    Route::get('/dashboard/charts/unit/{unit}', [DashboardController::class, 'unitActivityChart'])->name('charts.unit-activities');

    foreach (['prestasi', 'event', 'tracer-study', 'beasiswa'] as $module) {
        Route::get("/{$module}", [RecordController::class, 'index'])->defaults('module', $module)->name($module.'.index');
        Route::get("/{$module}/create", fn () => redirect()->route('records.index', $module))->name($module.'.create');
        Route::post("/{$module}", [RecordController::class, 'store'])->defaults('module', $module)->name($module.'.store');
        Route::get("/{$module}/{id}/edit", fn () => redirect()->route('records.index', $module))->name($module.'.edit');
        Route::put("/{$module}/{id}", [RecordController::class, 'update'])->defaults('module', $module)->name($module.'.update');
        Route::delete("/{$module}/{id}", [RecordController::class, 'destroy'])->defaults('module', $module)->name($module.'.destroy');
    }

    Route::redirect('/claim-transport', '/event')->name('claim-transport.index');
    Route::redirect('/claim-fasilitas', '/event')->name('claim-fasilitas.index');

    Route::prefix('records/{module}')->name('records.')->group(function () {
        Route::get('/', [RecordController::class, 'index'])->name('index');
        Route::get('/create', fn (string $module) => redirect()->route('records.index', $module))->name('create');
        Route::post('/', [RecordController::class, 'store'])->name('store');
        Route::get('/{id}/edit', fn (string $module) => redirect()->route('records.index', $module))->name('edit');
        Route::put('/{id}', [RecordController::class, 'update'])->name('update');
        Route::delete('/{id}', [RecordController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('unit/{unit}')->name('unit-activities.')->group(function () {
        Route::get('/', [UnitActivityController::class, 'index'])->name('index');
        Route::post('/', [UnitActivityController::class, 'store'])->name('store');
        Route::put('/{activity}', [UnitActivityController::class, 'update'])->name('update');
        Route::delete('/{activity}', [UnitActivityController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('role:super user|admin')->group(function () {
        Route::get('/master/prodi', [ProdiController::class, 'index'])->name('master.prodi.index');
        Route::post('/master/prodi', [ProdiController::class, 'store'])->name('master.prodi.store');
        Route::put('/master/prodi/{prodi}', [ProdiController::class, 'update'])->name('master.prodi.update');
        Route::delete('/master/prodi/{prodi}', [ProdiController::class, 'destroy'])->name('master.prodi.destroy');

        Route::get('/master/semester', [SemesterController::class, 'index'])->name('master.semester.index');
        Route::post('/master/semester', [SemesterController::class, 'store'])->name('master.semester.store');
        Route::put('/master/semester/{semester}', [SemesterController::class, 'update'])->name('master.semester.update');
        Route::delete('/master/semester/{semester}', [SemesterController::class, 'destroy'])->name('master.semester.destroy');
    });

    Route::middleware('role:super user')->group(function () {
        Route::get('/management-user', [UserController::class, 'index'])->name('users.index');
        Route::redirect('/users', '/management-user');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
