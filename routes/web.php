<?php use Illuminate\Support\Facades\Route;
use App\Models\Section;

// ==========================================
// AUTHENTICATION
// ==========================================

use App\Http\Controllers\Auth\LoginController;


// ==========================================
// ADMIN CONTROLLERS
// ==========================================

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SchoolYearController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\StrandController;
use App\Http\Controllers\Admin\SectionAdviserController;


// ==========================================
// TEACHER CONTROLLERS
// ==========================================

use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\SettingsController as TeacherSettingsController;
use App\Http\Controllers\Teacher\ClassroomController;
use App\Http\Controllers\Teacher\StudentController;
use App\Http\Controllers\Teacher\SubmissionController;
use App\Http\Controllers\Teacher\ClassRecordTestController;
use App\Http\Controllers\Teacher\NotificationController as TeacherNotificationController;


// ==========================================
// PRINCIPAL CONTROLLERS
// ==========================================

use App\Http\Controllers\Principal\DashboardController as PrincipalDashboardController;
use App\Http\Controllers\Principal\SettingsController as PrincipalSettingsController;
use App\Http\Controllers\Principal\SubmissionSettingController;
use App\Http\Controllers\Principal\SubmissionReviewController;
use App\Http\Controllers\Principal\ReturnSubmissionController;
use App\Http\Controllers\Principal\TeacherProgressController;


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});


// ==========================================
// LOGIN
// ==========================================

Route::get(
    '/login',
    [LoginController::class, 'index']
)->name('login');

Route::post(
    '/login',
    [LoginController::class, 'login']
)
->middleware('throttle:5,1')
->name('login.submit');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {


            // ==================================
            // ADMIN DASHBOARD
            // ==================================

            Route::get(
                '/dashboard',
                [DashboardController::class, 'index']
            )->name('dashboard');


            // ==================================
            // ADMIN SETTINGS
            // ==================================

            Route::get(
                '/settings',
                [SettingsController::class, 'index']
            )->name('settings');

            Route::post(
                '/settings',
                [SettingsController::class, 'update']
            )->name('settings.update');

            Route::post(
                '/settings/password',
                [SettingsController::class, 'updatePassword']
            )->name('settings.password.update');


            // ==================================
            // USER MANAGEMENT
            // ==================================

            Route::get(
                '/manage-users',
                [UserController::class, 'index']
            )->name('users.index');

            Route::post(
                '/manage-users',
                [UserController::class, 'store']
            )->name('users.store');

            Route::put(
                '/manage-users/{user}',
                [UserController::class, 'update']
            )->name('users.update');

            Route::patch(
                '/manage-users/{user}/status',
                [UserController::class, 'toggleStatus']
            )->name('users.status');


            // ==================================
            // SCHOOL YEAR MANAGEMENT
            // ==================================

            Route::get(
                '/school-years',
                [SchoolYearController::class, 'index']
            )->name('school-years.index');

            Route::post(
                '/school-years',
                [SchoolYearController::class, 'store']
            )->name('school-years.store');

            Route::put(
                '/school-years/{schoolYear}',
                [SchoolYearController::class, 'update']
            )->name('school-years.update');

            Route::patch(
                '/school-years/{schoolYear}/activate',
                [SchoolYearController::class, 'activate']
            )->name('school-years.activate');


            // ==================================
            // ACADEMIC SEMESTERS
            // ==================================

            Route::post(
                '/school-years/{schoolYear}/periods',
                [SchoolYearController::class, 'savePeriod']
            )->name('school-years.periods.save');


            // ==================================
            // SECTION MANAGEMENT
            // ==================================

            Route::get(
                '/sections',
                [SectionController::class, 'index']
            )->name('sections.index');

            Route::post(
                '/sections',
                [SectionController::class, 'store']
            )->name('sections.store');

            Route::put(
                '/sections/{section}',
                [SectionController::class, 'update']
            )->name('sections.update');

            Route::patch(
                '/sections/{section}/status',
                [SectionController::class, 'toggleStatus']
            )->name('sections.status');


            // ==================================
            // STRAND MANAGEMENT
            // ==================================

            Route::post(
                '/strands',
                [StrandController::class, 'store']
            )->name('strands.store');

            Route::put(
                '/strands/{strand}',
                [StrandController::class, 'update']
            )->name('strands.update');

            Route::patch(
                '/strands/{strand}/status',
                [StrandController::class, 'toggleStatus']
            )->name('strands.status');


            // ==================================
            // SECTION ADVISERS
            // ==================================

            Route::get(
                '/section-advisers',
                [SectionAdviserController::class, 'index']
            )->name('advisers.index');

            Route::post(
                '/section-advisers',
                [SectionAdviserController::class, 'store']
            )->name('advisers.store');

            Route::put(
                '/section-advisers/{sectionAdviser}',
                [SectionAdviserController::class, 'update']
            )->name('advisers.update');

            Route::delete(
                '/section-advisers/{sectionAdviser}',
                [SectionAdviserController::class, 'destroy']
            )->name('advisers.destroy');

        });



    /*
    |--------------------------------------------------------------------------
    | TEACHER ROUTES
    |--------------------------------------------------------------------------
    */

    Route::prefix('teacher')
        ->name('teacher.')
        ->middleware('role:teacher')
        ->group(function () {


            // ==================================
            // TEACHER DASHBOARD
            // ==================================

            Route::get(
                '/dashboard',
                [TeacherDashboardController::class, 'index']
            )->name('dashboard');


            // ==================================
            // TEACHER SETTINGS
            // ==================================

            Route::get(
                '/settings',
                [TeacherSettingsController::class, 'index']
            )->name('settings');

            Route::post(
                '/settings',
                [TeacherSettingsController::class, 'update']
            )->name('settings.update');

            Route::post(
                '/settings/password',
                [TeacherSettingsController::class, 'updatePassword']
            )
            ->middleware('throttle:5,1')
            ->name('settings.password.update');


            // ==================================
            // MY CLASSROOMS
            // ==================================

            Route::get(
                '/classrooms',
                [ClassroomController::class, 'index']
            )->name('classrooms.index');


            // ==================================
            // CLASSROOM OVERVIEW
            // ==================================

            Route::get(
                '/classrooms/{section}',
                [ClassroomController::class, 'show']
            )->name('classrooms.show');


            // ==================================
            // STUDENT INFORMATION
            // ==================================

            Route::get(
                '/classrooms/{section}/students',
                [StudentController::class, 'index']
            )->name('students.index');

            Route::get(
                '/classrooms/{section}/students/create',
                [StudentController::class, 'create']
            )->name('students.create');

            Route::post(
                '/classrooms/{section}/students',
                [StudentController::class, 'store']
            )->name('students.store');


            // ==================================
            // STUDENT LRN LOOKUP
            // ==================================

            Route::post(
                '/classrooms/{section}/students/lookup',
                [StudentController::class, 'lookup']
            )
            ->middleware('throttle:10,1')
            ->name('students.lookup');


            // ==================================
            // ATTACH EXISTING STUDENT
            // ==================================

            Route::post(
                '/classrooms/{section}/students/attach-existing',
                [StudentController::class, 'attachExisting']
            )->name('students.attach-existing');


            // ==================================
            // STUDENT PROFILE CRUD
            // ==================================

            Route::get(
                '/classrooms/{section}/students/{student}',
                [StudentController::class, 'show']
            )->name('students.show');

            Route::get(
                '/classrooms/{section}/students/{student}/edit',
                [StudentController::class, 'edit']
            )->name('students.edit');

            Route::put(
                '/classrooms/{section}/students/{student}',
                [StudentController::class, 'update']
            )->name('students.update');

            Route::delete(
                '/classrooms/{section}/students/{student}',
                [StudentController::class, 'destroy']
            )->name('students.destroy');


            // ==================================
            // CLASS RECORDS
            // ==================================

            Route::get(
                '/classrooms/{section}/class-records',
                function (Section $section) {

                    $teacher = request()->user();

                    $gradeAccess = $teacher->grade_access;

                    $allowedGrades = is_array($gradeAccess)
                        ? array_map('strval', $gradeAccess)
                        : match ((string) $gradeAccess) {
                            '11' => ['11'],
                            '12' => ['12'],
                            'both' => ['11', '12'],
                            default => [],
                        };

                    abort_unless(
                        in_array(
                            (string) $section->grade_level,
                            $allowedGrades,
                            true
                        )
                        &&
                        $section->adviser()
                            ->where('teacher_id', $teacher->id)
                            ->exists(),

                        403,

                        'You are not assigned to this classroom.'
                    );

                    $section->load([
                        'schoolYear',
                        'strand',
                        'adviser.teacher',
                    ]);

                    $isCurrent =
                        (bool) $section->is_active
                        &&
                        (bool) $section->schoolYear?->is_active
                        &&
                        (bool) $section->strand?->is_active;

                    return view(
                        'teacher.classrooms.class-records',
                        compact(
                            'section',
                            'isCurrent'
                        )
                    );
                }
            )->name('classrooms.class-records');


            /*
            |--------------------------------------------------------------------------
            | TEACHER SUBMISSION MANAGEMENT
            |--------------------------------------------------------------------------
            */


            // ==================================
            // VIEW SUBMISSION REQUIREMENTS
            // ==================================

            Route::get(
                '/classrooms/{section}/submissions',
                [SubmissionController::class, 'index']
            )->name('classrooms.submissions');


            // ==================================
            // UPLOAD REQUIREMENT FILES
            // ==================================

            Route::post(
                '/classrooms/{section}/submissions/{semester}/requirements/{requirement}/files',
                [SubmissionController::class, 'upload']
            )
            ->middleware('throttle:20,1')
            ->name('classrooms.submissions.files.store');


            // ==================================
            // DOWNLOAD SUBMISSION FILE
            // ==================================

            Route::get(
                '/classrooms/{section}/submissions/files/{submissionFile}/download',
                [SubmissionController::class, 'download']
            )->name('classrooms.submissions.files.download');


            // ==================================
            // DELETE / REPLACE RETURNED FILE
            // ==================================

            Route::delete(
                '/classrooms/{section}/submissions/files/{submissionFile}',
                [SubmissionController::class, 'destroyFile']
            )->name('classrooms.submissions.files.destroy');


            // ==================================
            // FINAL SUBMISSION / RESUBMISSION
            // ==================================

            Route::post(
                '/classrooms/{section}/submissions/{semester}/submit',
                [SubmissionController::class, 'submit']
            )
            ->middleware('throttle:10,1')
            ->name('classrooms.submissions.submit');


            /*
            |--------------------------------------------------------------------------
            | CLASS RECORDS & MASTER SHEETS
            |--------------------------------------------------------------------------
            */


            // ==================================
            // LEGACY WORKSPACE LINK
            // ==================================
            // Select a classroom before opening
            // the workspace.

            Route::get(
                '/class-records/workspace',
                function () {
                    return redirect()->route(
                        'teacher.classrooms.index'
                    );
                }
            )->name('class-records.workspace');


            // ==================================
            // MASTER SHEETS WORKSPACE
            // ==================================

            Route::get(
                '/classrooms/{section}/master-sheets',
                function (Section $section) {

                    $teacher = request()->user();

                    $gradeAccess = $teacher->grade_access;

                    $allowedGrades = is_array($gradeAccess)
                        ? array_map('strval', $gradeAccess)
                        : match ((string) $gradeAccess) {
                            '11' => ['11'],
                            '12' => ['12'],
                            'both' => ['11', '12'],
                            default => [],
                        };

                    abort_unless(
                        in_array(
                            (string) $section->grade_level,
                            $allowedGrades,
                            true
                        )
                        &&
                        $section->adviser()
                            ->where('teacher_id', $teacher->id)
                            ->exists(),

                        403,

                        'You are not assigned to this classroom.'
                    );

                    $section->load([
                        'schoolYear',
                        'strand',
                        'adviser.teacher'
                    ]);

                    $isCurrent =
                        (bool) $section->is_active
                        &&
                        (bool) $section->schoolYear?->is_active
                        &&
                        (bool) $section->strand?->is_active;

                    return view(
                        'teacher.class-records.workspace',
                        compact(
                            'section',
                            'isCurrent'
                        )
                    );
                }
            )->name('classrooms.master-sheets');


            // ==================================
            // EXTRACT CLASS RECORD GRADES
            // ==================================

            Route::post(
                '/classrooms/{section}/master-sheets/test-extract',
                [ClassRecordTestController::class, 'extract']
            )->name('classrooms.master-sheets.test-extract');


            /*
            |--------------------------------------------------------------------------
            | TEACHER HEADER NOTIFICATIONS
            |--------------------------------------------------------------------------
            */


            // ==================================
            // GET NOTIFICATIONS
            // ==================================

            Route::get(
                '/notifications',
                [TeacherNotificationController::class, 'index']
            )->name('notifications.index');


            // ==================================
            // MARK ALL AS READ
            // ==================================

            Route::post(
                '/notifications/read-all',
                [TeacherNotificationController::class, 'readAll']
            )->name('notifications.read-all');


            // ==================================
            // MARK ONE AS READ
            // ==================================

            Route::post(
                '/notifications/{notification}/read',
                [TeacherNotificationController::class, 'read']
            )->name('notifications.read');

        });



    /*
    |--------------------------------------------------------------------------
    | PRINCIPAL ROUTES
    |--------------------------------------------------------------------------
    */

    Route::prefix('principal')
        ->name('principal.')
        ->middleware('role:principal')
        ->group(function () {

        // ==========================================
        // TEACHER PROGRESS MONITORING
        // ==========================================

        Route::get(
            '/teacher-progress',
            [TeacherProgressController::class, 'index']
        )->name('teacher-progress.index');


            // ==================================
            // PRINCIPAL DASHBOARD
            // ==================================

                    
            // ======================================
            // PRINCIPAL DASHBOARD
            // ======================================

            // Display dashboard
            Route::get(
                '/dashboard',
                [PrincipalDashboardController::class, 'index']
            )->name('dashboard');


            // ======================================
            // EXPORT DASHBOARD REPORT
            // ======================================

            // Export actual database records as CSV
            Route::get(
                '/dashboard/export',
                [PrincipalDashboardController::class, 'export']
            )->name('dashboard.export');


            // ==================================
            // PRINCIPAL SETTINGS
            // ==================================

            Route::get(
                '/settings',
                [PrincipalSettingsController::class, 'index']
            )->name('settings');

            Route::post(
                '/settings',
                [PrincipalSettingsController::class, 'update']
            )->name('settings.update');

            Route::post(
                '/settings/password',
                [PrincipalSettingsController::class, 'updatePassword']
            )->name('settings.password.update');


            /*
            |--------------------------------------------------------------------------
            | PRINCIPAL SUBMISSION SETTINGS
            |--------------------------------------------------------------------------
            */


            // ==================================
            // VIEW SUBMISSION SETTINGS
            // ==================================

            Route::get(
                '/submissions',
                [SubmissionSettingController::class, 'index']
            )->name('submissions.index');


            // ==================================
            // UPDATE REQUIREMENTS AND DEADLINES
            // ==================================

            Route::put(
                '/submissions/{semester}',
                [SubmissionSettingController::class, 'update']
            )->name('submissions.update');


            /*
            |--------------------------------------------------------------------------
            | PRINCIPAL SUBMISSION REVIEW
            |--------------------------------------------------------------------------
            |
            | Review, approval, return, classroom selection,
            | and file preview are all in this route group.
            |
            */

            Route::prefix('submissions/review')
                ->name('submissions.review.')
                ->group(function () {


                    // ==================================
                    // LIST ALL TEACHER SUBMISSIONS
                    // ==================================

                    Route::get(
                        '/',
                        [SubmissionReviewController::class, 'index']
                    )->name('index');


                    // ==================================
                    // SELECT CLASSROOM TO RETURN
                    // ==================================
                    // IMPORTANT:
                    // Static route before /{submission}.

                    Route::get(
                        '/return-queue',
                        [ReturnSubmissionController::class, 'queue']
                    )->name('return-queue');


                    // ==================================
                    // SHOW RETURN FOR CORRECTION PAGE
                    // ==================================

                    Route::get(
                        '/{submission}/return',
                        [ReturnSubmissionController::class, 'show']
                    )
                    ->whereNumber('submission')
                    ->name('return-page');


                    // ==================================
                    // PROCESS RETURNED DOCUMENTS
                    // ==================================
                    // Only one POST route for returns.

                    Route::post(
                        '/{submission}/return',
                        [ReturnSubmissionController::class, 'store']
                    )
                    ->whereNumber('submission')
                    ->middleware('throttle:10,1')
                    ->name('return');


                    // ==================================
                    // VIEW SUBMISSION DETAILS
                    // ==================================

                    Route::get(
                        '/{submission}',
                        [SubmissionReviewController::class, 'show']
                    )
                    ->whereNumber('submission')
                    ->name('show');


                    // ==================================
                    // DOWNLOAD PRIVATE DOCUMENT
                    // ==================================

                    Route::get(
                        '/{submission}/files/{file}/download',
                        [SubmissionReviewController::class, 'download']
                    )
                    ->whereNumber('submission')
                    ->name('files.download');


                    // ==================================
                    // PREVIEW EXCEL IN BROWSER
                    // ==================================

                    Route::get(
                        '/{submission}/files/{file}/preview',
                        [SubmissionReviewController::class, 'preview']
                    )
                    ->whereNumber('submission')
                    ->name('files.preview');


                    // ==================================
                    // APPROVE COMPLETE SUBMISSION
                    // ==================================

                    Route::post(
                        '/{submission}/approve',
                        [SubmissionReviewController::class, 'approve']
                    )
                    ->whereNumber('submission')
                    ->middleware('throttle:10,1')
                    ->name('approve');

                });

        });



    /*
    |--------------------------------------------------------------------------
    | LOGOUT ROUTE
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/logout',
        [LoginController::class, 'logout']
    )->name('logout');

});
