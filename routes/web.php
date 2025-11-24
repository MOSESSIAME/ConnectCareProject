<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Admin area controllers
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;

// App controllers
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\ChurchController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\DynamicDropdownController;
use App\Http\Controllers\FollowUpHistoryController;   // (kept if you still use it elsewhere)
use App\Http\Controllers\FollowUpReportController;
use App\Http\Controllers\HomecellController;
use App\Http\Controllers\HomecellLeaderDashboardController;
use App\Http\Controllers\HomecellReportController;
use App\Http\Controllers\HomecellReportDashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PastorDashboardController;
use App\Http\Controllers\ServiceAttendanceController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceUnitController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamDashboardController;
use App\Http\Controllers\TeamMemberDashboardController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\ZonalDashboardController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\FollowUpController;

// communications + templates
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\TemplateController;

// keep service import at the top (prevents “unexpected token use”)
use App\Services\SmsClient;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'))
    ->middleware('guest')
    ->name('home');

/*
|--------------------------------------------------------------------------
| Role-based dashboard redirect
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = Auth::user();
    if (!$user) {
        return redirect()->route('login');
    }

    $role = $user->role->name ?? '';

    return match ($role) {
        'Admin'                 => redirect()->route('admin.dashboard'),
        'Pastor'                => redirect()->route('pastor.dashboard'),
        'Zonal Leader'          => redirect()->route('zone.dashboard'),
        'Homecell Leader'       => redirect()->route('homecell.dashboard'),
        'Team Leader'           => redirect()->route('team.dashboard'),
        'Team Member', 'Staff'  => redirect()->route('team-member.dashboard'),
        default => redirect()->route('login')->withErrors([
            'role' => 'Your account does not have a valid role assigned. Please contact the administrator.',
        ]),
    };
})->middleware('auth')->name('dashboard');

/*
|--------------------------------------------------------------------------
| ADMIN (prefix /admin, names admin.*)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Users + service units
        Route::resource('users', UserController::class);
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('users.reset-password');

        Route::resource('service-units', ServiceUnitController::class);

        // Structure resources inside admin
        Route::resources([
            'churches'  => ChurchController::class,
            'districts' => DistrictController::class,
            'zones'     => ZoneController::class,
            'homecells' => HomecellController::class,
            'teams'     => TeamController::class,
        ]);
    });

/*
|--------------------------------------------------------------------------
| PASTOR
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Pastor'])->group(function () {
    Route::get('/pastor/dashboard', [PastorDashboardController::class, 'index'])
        ->name('pastor.dashboard');
});

/*
|--------------------------------------------------------------------------
| ZONAL LEADER
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Zonal Leader'])->group(function () {
    Route::get('/zone/dashboard', [ZonalDashboardController::class, 'index'])
        ->name('zone.dashboard');
});

/*
|--------------------------------------------------------------------------
| HOMECELL LEADER
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Homecell Leader'])->group(function () {
    Route::get('/homecell/dashboard', [HomecellLeaderDashboardController::class, 'index'])
        ->name('homecell.dashboard');
});

/*
|--------------------------------------------------------------------------
| TEAM LEADER
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Team Leader'])->group(function () {
    Route::get('/team/dashboard', [TeamDashboardController::class, 'index'])
        ->name('team.dashboard');
});

/*
|--------------------------------------------------------------------------
| TEAM MEMBER / STAFF
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Team Member,Staff'])->group(function () {
    Route::get('/team-member/dashboard', [TeamMemberDashboardController::class, 'index'])
        ->name('team-member.dashboard');
});

/*
|--------------------------------------------------------------------------
| MEMBERS (Admin + Staff)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin,Staff'])->group(function () {
    Route::resource('members', MemberController::class);

    // ⬇️ Export routes (respect current filters via query string)
    Route::get('members-export/excel', [MemberController::class, 'exportExcel'])
        ->name('members.export.excel');
    Route::get('members-export/pdf', [MemberController::class, 'exportPdf'])
        ->name('members.export.pdf');
});

/*
|--------------------------------------------------------------------------
| HOMECELL REPORTS (Admin + Zonal + Staff)
| (Add 'Pastor' here too if pastors must access these)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin,Zonal Leader,Staff'])->group(function () {
    Route::get('/reports/homecells', [HomecellReportController::class, 'index'])
        ->name('reports.homecells.index');

    Route::get('/reports/homecells/create', [HomecellReportController::class, 'create'])
        ->name('reports.homecells.create');

    Route::post('/reports/homecells', [HomecellReportController::class, 'store'])
        ->name('reports.homecells.store');

    // ✅ NEW: export PDF for filtered Homecell Reports
    Route::get('/reports/homecells/export/pdf', [HomecellReportController::class, 'exportPdf'])
        ->name('reports.homecells.export.pdf');
});

/*
|--------------------------------------------------------------------------
| DYNAMIC DROPDOWNS (AJAX)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/get-districts/{church_id}', [DynamicDropdownController::class, 'getDistricts'])
        ->whereNumber('church_id')->name('getDistricts');

    Route::get('/get-zones/{district_id}', [DynamicDropdownController::class, 'getZones'])
        ->whereNumber('district_id')->name('getZones');

    Route::get('/get-homecells/{zone_id}', [DynamicDropdownController::class, 'getHomecells'])
        ->whereNumber('zone_id')->name('getHomecells');
});

/*
|--------------------------------------------------------------------------
| ANALYTICS DASHBOARDS (Homecell)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin,Pastor,Zonal Leader'])->group(function () {
    Route::get('/reports/dashboard', [HomecellReportDashboardController::class, 'index'])
        ->name('reports.dashboard');
});

/*
|--------------------------------------------------------------------------
| ASSIGNMENTS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin,Team Leader,Team Member'])->group(function () {
    Route::get('/assignments', [AssignmentController::class, 'index'])
        ->name('assignments.index');

    Route::post('/assignments/{id}/status', [AssignmentController::class, 'updateStatus'])
        ->whereNumber('id')->name('assignments.updateStatus');

    Route::delete('/assignments/{id}', [AssignmentController::class, 'destroy'])
        ->whereNumber('id')->name('assignments.destroy');
});

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/assignments/create', [AssignmentController::class, 'create'])
        ->name('assignments.create');

    Route::post('/assignments', [AssignmentController::class, 'store'])
        ->name('assignments.store');

    Route::get('/assignments/bulk-assign', [AssignmentController::class, 'bulkForm'])
        ->name('assignments.bulkAssign');

    Route::post('/assignments/bulk-assign', [AssignmentController::class, 'bulkStore'])
        ->name('assignments.bulkAssign.post');
});

Route::middleware(['auth', 'role:Admin,Team Leader'])->group(function () {
    Route::get('/assignments/standby', [AssignmentController::class, 'standby'])
        ->name('assignments.standby');

    Route::post('/assignments/{assignment}/assign-to-member', [AssignmentController::class, 'assignToMember'])
        ->whereNumber('assignment')->name('assignments.assignToMember');

    Route::get('/assignments/reassign', [AssignmentController::class, 'reassignForm'])
        ->name('assignments.reassign');

    Route::post('/assignments/reassign', [AssignmentController::class, 'reassign'])
        ->name('assignments.reassign.post');

    // NEW: edit / update routes (Admin + Team Leader)
    Route::get('/assignments/{assignment}/edit', [AssignmentController::class, 'edit'])
        ->whereNumber('assignment')->name('assignments.edit');

    Route::put('/assignments/{assignment}', [AssignmentController::class, 'update'])
        ->whereNumber('assignment')->name('assignments.update');
});

/*
|--------------------------------------------------------------------------
| FOLLOW-UP TRACKING (Admin, Team Leader, Team Member, Staff)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin,Team Leader,Team Member,Staff'])->group(function () {
    // Make /followups work for old links expecting name 'followups.my'
    Route::get('/followups', [FollowUpController::class, 'index'])
        ->name('followups.my');

    // alias with name 'followups.index'
    Route::get('/followups/all', [FollowUpController::class, 'index'])
        ->name('followups.index');

    // Create/store follow-up for a specific assignment
    Route::get('/followups/{assignment}/create', [FollowUpController::class, 'create'])
        ->whereNumber('assignment')->name('followups.create');

    Route::post('/followups/{assignment}', [FollowUpController::class, 'store'])
        ->whereNumber('assignment')->name('followups.store');

    // List follow-ups for a specific assignment
    Route::get('/followups/{assignment}', [FollowUpController::class, 'assignmentIndex'])
        ->whereNumber('assignment')->name('followups.assignment');

    // Mark a follow-up as completed
    Route::post('/followups/complete/{id}', [FollowUpController::class, 'complete'])
        ->whereNumber('id')->name('followups.complete');

    // NEW: Edit / Update / Destroy followups (Admin / Team Leader / assignee)
    Route::get('/followups/{followup}/edit', [FollowUpController::class, 'edit'])
        ->whereNumber('followup')->name('followups.edit');

    Route::put('/followups/{followup}', [FollowUpController::class, 'update'])
        ->whereNumber('followup')->name('followups.update');

    Route::delete('/followups/{followup}', [FollowUpController::class, 'destroy'])
        ->whereNumber('followup')->name('followups.destroy');
});

/*
|--------------------------------------------------------------------------
| FOLLOW-UP REPORTS (Admin + Pastor + Team Leader)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin,Pastor,Team Leader'])->group(function () {
    Route::get('/reports/followups', [FollowUpReportController::class, 'index'])
        ->name('reports.followups');
});

/*
|--------------------------------------------------------------------------
| SERVICES & ATTENDANCE (Admin + Pastor + Staff)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin,Pastor,Staff'])->group(function () {
    Route::resource('services', ServiceController::class);

    Route::get('/attendance/export/pdf', [ServiceAttendanceController::class, 'exportPdf'])
    ->name('attendance.export.pdf');

    // includes edit/update/destroy so the edit page works
    Route::resource('attendance', ServiceAttendanceController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});

/*
|--------------------------------------------------------------------------
| COMMUNICATIONS & TEMPLATES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','role:Admin,Pastor,Staff'])->group(function () {
    Route::resource('communications', CommunicationController::class)
        ->only(['index','create','store','show']);

    Route::resource('templates', TemplateController::class);
});

/*
|--------------------------------------------------------------------------
| Twilio testing
|--------------------------------------------------------------------------
*/
Route::get('/test-sms', function () {
    $sms = new SmsClient();

    try {
        $to = '+260979964985'; // replaced with my verified number
        $sid = $sms->send($to, 'Hello from ConnectCare via Twilio 🎉');
        return "✅ SMS sent successfully! Message SID: {$sid}";
    } catch (Exception $e) {
        return "❌ SMS failed: " . $e->getMessage();
    }
});

/*
|--------------------------------------------------------------------------
| AUTH (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Team member management (Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('teams/{team}/members', [TeamMemberController::class, 'index'])->name('teams.members');
    Route::post('teams/{team}/members', [TeamMemberController::class, 'store'])->name('teams.members.store');
    Route::delete('teams/{team}/members/{user}', [TeamMemberController::class, 'destroy'])->name('teams.members.destroy');
});
