<?php

use Illuminate\Support\Facades\Route;
use App\Models\Section;

// Authentication
use App\Http\Controllers\Auth\LoginController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SchoolYearController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\StrandController;
use App\Http\Controllers\Admin\SectionAdviserController;

// Teacher Controllers
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\SettingsController as TeacherSettingsController;
use App\Http\Controllers\Teacher\ClassroomController;
use App\Http\Controllers\Teacher\StudentController;
use App\Http\Controllers\Teacher\SubmissionController;
use App\Http\Controllers\Teacher\ClassRecordTestController;
use App\Http\Controllers\Teacher\NotificationController as TeacherNotificationController;
use App\Http\Controllers\Teacher\StudentCardController;

// Principal Controllers
use App\Http\Controllers\Principal\DashboardController as PrincipalDashboardController;
use App\Http\Controllers\Principal\SettingsController as PrincipalSettingsController;
use App\Http\Controllers\Principal\SubmissionSettingController;
use App\Http\Controllers\Principal\SubmissionReviewController;
use App\Http\Controllers\Principal\ReturnSubmissionController;
use App\Http\Controllers\Principal\TeacherProgressController;

// ========================================================
// PUBLIC ROUTES
// =======================================================

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'index'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.submit');


// ========================================================
// AUTHENTICATED ROUTES
// ========================================================

Route::middleware('auth')->group(function () {

    // ====================================================
    // ADMIN ROUTES
    // ====================================================

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [
                DashboardController::class,
                'index'
            ])->name('dashboard');

            // Settings
            Route::get('/settings', [
                SettingsController::class,
                'index'
            ])->name('settings');

            Route::post('/settings', [
                SettingsController::class,
                'update'
            ])->name('settings.update');

            Route::post('/settings/password', [
                SettingsController::class,
                'updatePassword'
            ])->name('settings.password.update');

            // User Management
            Route::get('/manage-users', [
                UserController::class,
                'index'
            ])->name('users.index');

            Route::post('/manage-users', [
                UserController::class,
                'store'
            ])->name('users.store');

            Route::put('/manage-users/{user}', [
                UserController::class,
                'update'
            ])->name('users.update');

            Route::patch('/manage-users/{user}/status', [
                UserController::class,
                'toggleStatus'
            ])->name('users.status');

            // School Years
            Route::get('/school-years', [
                SchoolYearController::class,
                'index'
            ])->name('school-years.index');

            Route::post('/school-years', [
                SchoolYearController::class,
                'store'
            ])->name('school-years.store');

            Route::put('/school-years/{schoolYear}', [
                SchoolYearController::class,
                'update'
            ])->name('school-years.update');

            Route::patch('/school-years/{schoolYear}/activate', [
                SchoolYearController::class,
                'activate'
            ])->name('school-years.activate');

            // Academic Periods
            Route::post('/school-years/{schoolYear}/periods', [
                SchoolYearController::class,
                'savePeriod'
            ])->name('school-years.periods.save');

            // Section Management
            Route::get('/sections', [
                SectionController::class,
                'index'
            ])->name('sections.index');

            Route::post('/sections', [
                SectionController::class,
                'store'
            ])->name('sections.store');

            Route::put('/sections/{section}', [
                SectionController::class,
                'update'
            ])->name('sections.update');

            Route::patch('/sections/{section}/status', [
                SectionController::class,
                'toggleStatus'
            ])->name('sections.status');

            // Strand Management
            Route::post('/strands', [
                StrandController::class,
                'store'
            ])->name('strands.store');

            Route::put('/strands/{strand}', [
                StrandController::class,
                'update'
            ])->name('strands.update');

            Route::patch('/strands/{strand}/status', [
                StrandController::class,
                'toggleStatus'
            ])->name('strands.status');

            // Section Advisers
            Route::get('/section-advisers', [
                SectionAdviserController::class,
                'index'
            ])->name('advisers.index');

            Route::post('/section-advisers', [
                SectionAdviserController::class,
                'store'
            ])->name('advisers.store');

            Route::put('/section-advisers/{sectionAdviser}', [
                SectionAdviserController::class,
                'update'
            ])->name('advisers.update');

            Route::delete('/section-advisers/{sectionAdviser}', [
                SectionAdviserController::class,
                'destroy'
            ])->name('advisers.destroy');

        });


    // ====================================================
    // TEACHER ROUTES
    // ====================================================

    Route::prefix('teacher')
        ->name('teacher.')
        ->middleware('role:teacher')
        ->group(function () {

            // ============================================
            // TEACHER DASHBOARD
            // ============================================

            Route::get('/dashboard', [
                TeacherDashboardController::class,
                'index'
            ])->name('dashboard');


            // ============================================
            // TEACHER SETTINGS
            // ============================================

            Route::get('/settings', [
                TeacherSettingsController::class,
                'index'
            ])->name('settings');

            Route::post('/settings', [
                TeacherSettingsController::class,
                'update'
            ])->name('settings.update');

            Route::post('/settings/password', [
                TeacherSettingsController::class,
                'updatePassword'
            ])
            ->middleware('throttle:5,1')
            ->name('settings.password.update');


            // ============================================
            // MY CLASSROOMS
            // ============================================

            Route::get('/classrooms', [
                ClassroomController::class,
                'index'
            ])->name('classrooms.index');

            Route::get('/classrooms/{section}', [
                ClassroomController::class,
                'show'
            ])->name('classrooms.show');


            // ============================================
            // STUDENT INFORMATION
            // ============================================

            Route::get('/classrooms/{section}/students', [
                StudentController::class,
                'index'
            ])->name('students.index');

            Route::get('/classrooms/{section}/students/create', [
                StudentController::class,
                'create'
            ])->name('students.create');

            Route::post('/classrooms/{section}/students', [
                StudentController::class,
                'store'
            ])->name('students.store');

            Route::post('/classrooms/{section}/students/lookup', [
                StudentController::class,
                'lookup'
            ])
            ->middleware('throttle:10,1')
            ->name('students.lookup');

            Route::post(
                '/classrooms/{section}/students/attach-existing',
                [StudentController::class, 'attachExisting']
            )->name('students.attach-existing');

            // Student Profile CRUD
            Route::get('/classrooms/{section}/students/{student}', [
                StudentController::class,
                'show'
            ])->name('students.show');

            Route::get('/classrooms/{section}/students/{student}/edit', [
                StudentController::class,
                'edit'
            ])->name('students.edit');

            Route::put('/classrooms/{section}/students/{student}', [
                StudentController::class,
                'update'
            ])->name('students.update');

            Route::delete('/classrooms/{section}/students/{student}', [
                StudentController::class,
                'destroy'
            ])->name('students.destroy');


            // ============================================
            // CLASS RECORDS
            // ============================================

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
                        compact('section', 'isCurrent')
                    );
                }
            )->name('classrooms.class-records');


            // ============================================
            // SUBMISSION MANAGEMENT
            // ============================================

            Route::get('/classrooms/{section}/submissions', [
                SubmissionController::class,
                'index'
            ])->name('classrooms.submissions');

            Route::post(
                '/classrooms/{section}/submissions/{semester}/requirements/{requirement}/files',
                [SubmissionController::class, 'upload']
            )
            ->middleware('throttle:20,1')
            ->name('classrooms.submissions.files.store');

            Route::get(
                '/classrooms/{section}/submissions/files/{submissionFile}/download',
                [SubmissionController::class, 'download']
            )->name('classrooms.submissions.files.download');

            Route::delete(
                '/classrooms/{section}/submissions/files/{submissionFile}',
                [SubmissionController::class, 'destroyFile']
            )->name('classrooms.submissions.files.destroy');

            Route::post(
                '/classrooms/{section}/submissions/{semester}/submit',
                [SubmissionController::class, 'submit']
            )
            ->middleware('throttle:10,1')
            ->name('classrooms.submissions.submit');


            // ============================================
            // CLASS RECORD WORKSPACE
            // ============================================

            Route::get(
                '/class-records/workspace',
                function () {
                    return redirect()->route(
                        'teacher.classrooms.index'
                    );
                }
            )->name('class-records.workspace');


            // ============================================
            // MASTER SHEETS WORKSPACE
            // ============================================

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
                        compact('section', 'isCurrent')
                    );
                }
            )->name('classrooms.master-sheets');


            // ============================================
            // EXTRACT CLASS RECORD GRADES
            // ============================================

            Route::post(
                '/classrooms/{section}/master-sheets/test-extract',
                [ClassRecordTestController::class, 'extract']
            )->name('classrooms.master-sheets.test-extract');


            // ============================================
            // STUDENT CARD GENERATOR - FIXED
            // ============================================

            // Open Student Card Generator page
            Route::get(
                '/classrooms/{section}/student-cards',
                [StudentCardController::class, 'index']
            )->name('classrooms.student-cards');

            // Upload Excel Master Sheet
            Route::post(
                '/classrooms/{section}/student-cards/upload',
                [StudentCardController::class, 'upload']
            )->name('classrooms.student-cards.upload');

            // Generate Word Student Cards
            Route::post(
                '/classrooms/{section}/student-cards/generate',
                [StudentCardController::class, 'generate']
            )->name('classrooms.student-cards.generate');

            // Old Student Cards link redirects to classrooms
            Route::get('/student-cards', function () {
                return redirect()->route('teacher.classrooms.index');
            })->name('student-cards.index');


            // ============================================
            // TEACHER NOTIFICATIONS
            // ============================================

            Route::get('/notifications', [
                TeacherNotificationController::class,
                'index'
            ])->name('notifications.index');

            Route::post('/notifications/read-all', [
                TeacherNotificationController::class,
                'readAll'
            ])->name('notifications.read-all');

            Route::post('/notifications/{notification}/read', [
                TeacherNotificationController::class,
                'read'
            ])->name('notifications.read');

        });


    // ====================================================
    // PRINCIPAL ROUTES
    // ====================================================

    Route::prefix('principal')
        ->name('principal.')
        ->middleware('role:principal')
        ->group(function () {

            // Teacher Progress Monitoring
            Route::get('/teacher-progress', [
                TeacherProgressController::class,
                'index'
            ])->name('teacher-progress.index');


            // Principal Dashboard
            Route::get('/dashboard', [
                PrincipalDashboardController::class,
                'index'
            ])->name('dashboard');

            Route::get('/dashboard/export', [
                PrincipalDashboardController::class,
                'export'
            ])->name('dashboard.export');


            // Principal Settings
            Route::get('/settings', [
                PrincipalSettingsController::class,
                'index'
            ])->name('settings');

            Route::post('/settings', [
                PrincipalSettingsController::class,
                'update'
            ])->name('settings.update');

            Route::post('/settings/password', [
                PrincipalSettingsController::class,
                'updatePassword'
            ])->name('settings.password.update');


            // Submission Settings
            Route::get('/submissions', [
                SubmissionSettingController::class,
                'index'
            ])->name('submissions.index');

            Route::put('/submissions/{semester}', [
                SubmissionSettingController::class,
                'update'
            ])->name('submissions.update');


            // ============================================
            // PRINCIPAL SUBMISSION REVIEW
            // ============================================

            Route::prefix('submissions/review')
                ->name('submissions.review.')
                ->group(function () {

                    Route::get('/', [
                        SubmissionReviewController::class,
                        'index'
                    ])->name('index');

                    Route::get('/return-queue', [
                        ReturnSubmissionController::class,
                        'queue'
                    ])->name('return-queue');

                    Route::get('/{submission}/return', [
                        ReturnSubmissionController::class,
                        'show'
                    ])
                    ->whereNumber('submission')
                    ->name('return-page');

                    Route::post('/{submission}/return', [
                        ReturnSubmissionController::class,
                        'store'
                    ])
                    ->whereNumber('submission')
                    ->middleware('throttle:10,1')
                    ->name('return');

                    Route::get('/{submission}', [
                        SubmissionReviewController::class,
                        'show'
                    ])
                    ->whereNumber('submission')
                    ->name('show');

                    Route::get(
                        '/{submission}/files/{file}/download',
                        [SubmissionReviewController::class, 'download']
                    )
                    ->whereNumber('submission')
                    ->name('files.download');

                    Route::get(
                        '/{submission}/files/{file}/preview',
                        [SubmissionReviewController::class, 'preview']
                    )
                    ->whereNumber('submission')
                    ->name('files.preview');

                    Route::post('/{submission}/approve', [
                        SubmissionReviewController::class,
                        'approve'
                    ])
                    ->whereNumber('submission')
                    ->middleware('throttle:10,1')
                    ->name('approve');

                });

        });


    // ====================================================
    // LOGOUT
    // ====================================================

    Route::post('/logout', [
        LoginController::class,
        'logout'
    ])->name('logout');

});
