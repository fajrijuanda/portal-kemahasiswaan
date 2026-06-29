<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CareerPostController;
use App\Http\Controllers\Master\AchievementQuotaController;
use App\Http\Controllers\Master\OrmawaController;
use App\Http\Controllers\Master\ProdiController;
use App\Http\Controllers\Master\SemesterController;
use App\Http\Controllers\Master\SimpleMasterController;
use App\Http\Controllers\OrmawaPanelController;
use App\Http\Controllers\PressReleaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPortalController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\StudentSubmissionController;
use App\Http\Controllers\UnitActivityController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/publik', [PublicPortalController::class, 'index'])->name('public.index');
Route::get('/publik/press-release/{pressRelease}', [PublicPortalController::class, 'pressRelease'])->name('public.press.show');

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

    Route::middleware('role:super user|admin|kaprodi|kabag|warek')->group(function () {
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
    });

    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('student.')->group(function () {
        Route::get('/pengajuan', [StudentSubmissionController::class, 'index'])->name('submissions');
        Route::post('/beasiswa', [StudentSubmissionController::class, 'storeBeasiswa'])->name('beasiswa.store');
        Route::post('/prestasi', [StudentSubmissionController::class, 'storePrestasi'])->name('prestasi.store');
    });

    Route::middleware('role:ormawa')->prefix('ormawa-panel')->name('ormawa.')->group(function () {
        Route::get('/', [OrmawaPanelController::class, 'index'])->name('panel');
        Route::post('/proposal', [OrmawaPanelController::class, 'storeProposal'])->name('proposals.store');
        Route::post('/reimbursement', [OrmawaPanelController::class, 'storeReimbursement'])->name('reimbursements.store');
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

        Route::get('/master/{master}', [SimpleMasterController::class, 'index'])->name('master.simple.index');
        Route::post('/master/{master}', [SimpleMasterController::class, 'store'])->name('master.simple.store');
        Route::put('/master/{master}/{id}', [SimpleMasterController::class, 'update'])->name('master.simple.update');
        Route::delete('/master/{master}/{id}', [SimpleMasterController::class, 'destroy'])->name('master.simple.destroy');

        Route::get('/master-ormawa', [OrmawaController::class, 'index'])->name('master.ormawa.index');
        Route::post('/master-ormawa', [OrmawaController::class, 'store'])->name('master.ormawa.store');
        Route::put('/master-ormawa/{ormawa}', [OrmawaController::class, 'update'])->name('master.ormawa.update');
        Route::delete('/master-ormawa/{ormawa}', [OrmawaController::class, 'destroy'])->name('master.ormawa.destroy');

        Route::get('/master-kuota-prestasi', [AchievementQuotaController::class, 'index'])->name('master.quotas.index');
        Route::post('/master-kuota-prestasi', [AchievementQuotaController::class, 'store'])->name('master.quotas.store');
        Route::put('/master-kuota-prestasi/{quota}', [AchievementQuotaController::class, 'update'])->name('master.quotas.update');
        Route::delete('/master-kuota-prestasi/{quota}', [AchievementQuotaController::class, 'destroy'])->name('master.quotas.destroy');
    });

    Route::middleware('role:super user|admin|kabag')->group(function () {
        Route::get('/press-releases', [PressReleaseController::class, 'index'])->name('press-releases.index');
        Route::post('/press-releases', [PressReleaseController::class, 'store'])->name('press-releases.store');
        Route::put('/press-releases/{pressRelease}', [PressReleaseController::class, 'update'])->name('press-releases.update');
        Route::delete('/press-releases/{pressRelease}', [PressReleaseController::class, 'destroy'])->name('press-releases.destroy');
    });

    Route::middleware('role:super user|admin')->group(function () {
        Route::get('/karir', [CareerPostController::class, 'index'])->name('careers.index');
        Route::post('/karir', [CareerPostController::class, 'store'])->name('careers.store');
        Route::put('/karir/{careerPost}', [CareerPostController::class, 'update'])->name('careers.update');
        Route::delete('/karir/{careerPost}', [CareerPostController::class, 'destroy'])->name('careers.destroy');
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
