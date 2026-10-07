<?php

use App\Http\Controllers\Admin\AttendanceReportController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Staff\AttendanceController;
use App\Http\Controllers\Staff\LeaveRequestController;
use App\Http\Controllers\Staff\MeetingController;
use App\Http\Controllers\Staff\ProfileController;
use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Phase 03 - authentication and system roles.
|
| Three groups:
|
|   guest      only for signed-out visitors (login)
|   auth       any signed-in account (dashboard, logout, self-service)
|   auth+admin administrator accounts only (every management module)
|
| There is NO online leave approval workflow. Leave requests are printed
| and signed by a supervisor, so no approve/reject route exists.
|
*/

/*
 * NOTE: routes/web.php is already wrapped in the "web" middleware group by
 * bootstrap/app.php (withRouting), so sessions, CSRF and cookie encryption
 * are active. Do not add another ->middleware('web') wrapper here.
 */

/* ---------- Health check (no Tailwind, plain JSON) ---------- */
Route::get('/up', function () {
    event(new DiagnosingHealth);

    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'laravel' => app()->version(),
        'php' => PHP_VERSION,
        'database' => config('database.default'),
        'time' => now()->toDateTimeString(),
        'timezone' => config('app.timezone'),
    ]);
})->name('health');

/* ======================================================================
 | GUEST - signed-out visitors only
 |===================================================================== */

Route::middleware('guest')->group(function (): void {

    Route::get('/', function () {
        return redirect()->route('login');
    })->name('home');

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('login.store');
});

/* ======================================================================
 | AUTHENTICATED - any signed-in account
 |===================================================================== */

Route::middleware('auth')->group(function (): void {

    /*
     | Logout is POST only. A GET route would let a link or an <img>
     | tag end somebody's session.
     */
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    