<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EducationPostController as AdminEducationPostController;
use App\Http\Controllers\Admin\QuestionnaireVersionController as AdminQuestionnaireVersionController;
use App\Http\Controllers\Admin\RiskRuleVersionController as AdminRiskRuleVersionController;
use App\Http\Controllers\Admin\ScreeningController as AdminScreeningController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScreeningController;
use App\Http\Controllers\ScreeningHistoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');
Route::get('/offline.html', fn () => response()->file(public_path('offline.html'), [
    'Content-Type' => 'text/html; charset=UTF-8',
]))->name('pwa.offline');
Route::get('/manifest.webmanifest', fn () => response()->file(public_path('manifest.webmanifest'), [
    'Content-Type' => 'application/manifest+json',
]))->name('pwa.manifest');
Route::get('/sw.js', fn () => response()->file(public_path('sw.js'), [
    'Content-Type' => 'application/javascript',
]))->name('pwa.service-worker');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'no-store'])
    ->name('dashboard');

Route::middleware(['auth', 'no-store'])->group(function () {
    Route::post('/screenings/start', [ScreeningController::class, 'start'])
        ->middleware('throttle:screening-start')
        ->name('screenings.start');
    Route::get('/screenings/{screening}/questions/{step}', [ScreeningController::class, 'showQuestion'])
        ->whereNumber('step')
        ->name('screenings.questions.show');
    Route::put('/screenings/{screening}/questions/{step}', [ScreeningController::class, 'updateQuestion'])
        ->whereNumber('step')
        ->name('screenings.questions.update');
    Route::get('/screenings/{screening}/review', [ScreeningController::class, 'review'])->name('screenings.review');
    Route::post('/screenings/{screening}/submit', [ScreeningController::class, 'submit'])
        ->middleware('throttle:screening-submit')
        ->name('screenings.submit');
    Route::get('/screenings/{screening}/result', [ScreeningController::class, 'result'])->name('screenings.result');
    Route::get('/history', [ScreeningHistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{screening}', [ScreeningHistoryController::class, 'show'])->name('history.show');
    Route::get('/education', [EducationController::class, 'index'])->name('education.index');
    Route::get('/education/{slug}', [EducationController::class, 'show'])->name('education.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin', 'no-store'])
    ->group(function (): void {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');

        Route::get('/screenings', [AdminScreeningController::class, 'index'])->name('screenings.index');
        Route::get('/screenings/{screening}', [AdminScreeningController::class, 'show'])->name('screenings.show');

        Route::get('/questionnaires', [AdminQuestionnaireVersionController::class, 'index'])->name('questionnaires.index');
        Route::get('/questionnaires/create', [AdminQuestionnaireVersionController::class, 'create'])->name('questionnaires.create');
        Route::post('/questionnaires', [AdminQuestionnaireVersionController::class, 'store'])->name('questionnaires.store');
        Route::get('/questionnaires/{questionnaire}', [AdminQuestionnaireVersionController::class, 'show'])->name('questionnaires.show');
        Route::get('/questionnaires/{questionnaire}/edit', [AdminQuestionnaireVersionController::class, 'edit'])->name('questionnaires.edit');
        Route::put('/questionnaires/{questionnaire}', [AdminQuestionnaireVersionController::class, 'update'])->name('questionnaires.update');

        Route::get('/risk-rules', [AdminRiskRuleVersionController::class, 'index'])->name('risk-rules.index');
        Route::get('/risk-rules/create', [AdminRiskRuleVersionController::class, 'create'])->name('risk-rules.create');
        Route::post('/risk-rules', [AdminRiskRuleVersionController::class, 'store'])->name('risk-rules.store');
        Route::get('/risk-rules/{riskRule}', [AdminRiskRuleVersionController::class, 'show'])->name('risk-rules.show');
        Route::get('/risk-rules/{riskRule}/edit', [AdminRiskRuleVersionController::class, 'edit'])->name('risk-rules.edit');
        Route::put('/risk-rules/{riskRule}', [AdminRiskRuleVersionController::class, 'update'])->name('risk-rules.update');
        Route::post('/risk-rules/{riskRule}/publish', [AdminRiskRuleVersionController::class, 'publish'])->name('risk-rules.publish');

        Route::get('/education', [AdminEducationPostController::class, 'index'])->name('education.index');
        Route::get('/education/create', [AdminEducationPostController::class, 'create'])->name('education.create');
        Route::post('/education', [AdminEducationPostController::class, 'store'])->name('education.store');
        Route::get('/education/{education}/edit', [AdminEducationPostController::class, 'edit'])->name('education.edit');
        Route::put('/education/{education}', [AdminEducationPostController::class, 'update'])->name('education.update');
    });

require __DIR__.'/auth.php';
