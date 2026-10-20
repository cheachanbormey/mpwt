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
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\WorkingHoursChartController;

Route::middleware(['auth'])->get(
    '/admin/working-hours-chart',
    [WorkingHoursChartController::class, 'index']
)->name('admin.working-hours-chart');

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

    //     /* ---------- Staff self-service (both roles may use these) ---------- */

    Route::get('/profile', [ProfileController::class, 'show'])->name('staff.profile');
    Route::middleware(['auth'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('staff.attendance');

//     /*
//      | The clock takes its time from the server, so both actions are empty
//      | POSTs. There is no way to post a timestamp of your own.
//      */
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])
        ->name('staff.attendance.check-in');
    Route::get('/attendance/check-in/qr', [AttendanceController::class, 'checkInQr'])
        ->name('staff.attendance.check-in.qr');
    Route::get('/attendance/qr', [AttendanceController::class, 'showQr'])
        ->name('staff.attendance.qr');
    Route::get('/attendance/qr/image', [AttendanceController::class, 'qrCode'])
        ->name('staff.attendance.qr.image');

    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])
        ->name('staff.attendance.check-out');

    Route::get('/meetings', [MeetingController::class, 'index'])->name('staff.meetings.index');
    Route::get('/meetings/{meeting}', [MeetingController::class, 'show'])->name('staff.meetings.show');

    Route::get('/leave-requests', [LeaveRequestController::class, 'index'])
        ->name('staff.leave-requests.index');
    Route::get('/leave-requests/create', [LeaveRequestController::class, 'create'])
        ->name('staff.leave-requests.create');

    /*
     | Submitting a request always creates a "pending" row.
     | There is deliberately no approve, reject or cancel route.
     */
    Route::post('/leave-requests', [LeaveRequestController::class, 'store'])
        ->name('staff.leave-requests.store');
});

// /* ======================================================================
//  | ADMINISTRATOR ONLY
//  |
//  | Each entry of config/hrms.php becomes a full CRUD resource.
//  |
//  | The routes are written out instead of using Route::resource because the
//  | URL segment is a literal ("admin/departments"), not a {module}
//  | parameter. The module name is therefore attached as a route default,
//  | which ModuleController reads back. See ModuleController::module().
//  |
//  | Two extra actions are opt-in, so a module must ask for them in the
//  | registry rather than gaining them by accident:
//  |
//  |   show     registered when the entry sets `show` to true
//  |   toggle   registered when the entry names a `status_field`
//  |
//  | The {id} constraint keeps /create and /{id}/edit from colliding with
//  | the show route.
//  |===================================================================== */
Route::middleware(['auth'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile');
});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {

//         /*
//          | Attendance overviews. The row-by-row record list is generated from
//          | config/hrms.php below; these two screens are hand written because a
//          | roll-up per day or per person is not a flat list of rows.
//          */
        Route::get('/attendance/daily', [AttendanceReportController::class, 'day'])
            ->name('attendances.day');

        Route::get('/attendance/monthly', [AttendanceReportController::class, 'month'])
            ->name('attendances.month');

        $actions = [
            'index' => ['get', '', 'index'],
            'create' => ['get', '/create', 'create'],
            'store' => ['post', '', 'store'],
            'edit' => ['get', '/{id}/edit', 'edit'],
            'update' => ['put,patch', '/{id}', 'update'],
            'destroy' => ['delete', '/{id}', 'destroy'],
        ];

        $extras = [
            'show' => ['get', '/{id}', 'show'],
            'toggle' => ['patch', '/{id}/toggle', 'toggle'],
        ];

        foreach (ModuleController::registry() as $module => $definition) {
            $moduleActions = $actions;

            if ($definition['show'] ?? false) {
                $moduleActions['show'] = $extras['show'];
            }

            if (($definition['status_field'] ?? null) !== null) {
                $moduleActions['toggle'] = $extras['toggle'];
            }

            foreach ($moduleActions as $action => [$verbs, $suffix, $method]) {
                Route::match(explode(',', $verbs), "/{$module}{$suffix}", [
                    ModuleController::class, $method,
                ])
                    ->where('id', '[0-9]+')
                    ->defaults('module', $module)
                    ->name("{$module}.{$action}");
            }
        }
    });

    
    Route::get('/user/chart', [UserController::class, 'showChart']);        

});