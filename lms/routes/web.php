<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Setting;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AccountsController;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\AttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/** for side bar menu active */
function set_active( $route ) {
    if( is_array( $route ) ){
        return in_array(Request::path(), $route) ? 'active' : '';
    }
    return Request::path() == $route ? 'active' : '';
}

Route::get('/', function () {
    return view('auth.login');
});

Route::group(['middleware'=>'auth'],function()
{
    Route::get('home',function()
    {
        return view('home');
    });
});

Auth::routes();
Route::group(['namespace' => 'App\Http\Controllers\Auth'],function()
{
    // ----------------------------login ------------------------------//
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::post('/login', 'authenticate');
        Route::get('/logout', 'logout')->name('logout');
        Route::post('change/password', 'changePassword')->name('change/password');
    });

    // ----------------------------- register -------------------------//
    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'register')->name('register');
        Route::post('/register','storeUser')->name('register');    
    });
});

Route::group(['namespace' => 'App\Http\Controllers'],function()
{
    // -------------------------- main dashboard ----------------------//
    Route::controller(HomeController::class)->group(function () {
        Route::get('/home', 'index')->middleware('auth')->name('home');
        Route::get('/dashboard', 'dashboard')->middleware('auth')->name('dashboard');
        Route::get('user/profile/page', 'userProfile')->middleware('auth')->name('user/profile/page');
        Route::get('admin/dashboard', 'adminDashboardIndex')->middleware(['auth', 'role:Admin'])->name('admin/dashboard');
        Route::get('teacher/dashboard', 'teacherDashboardIndex')->middleware(['auth', 'role:Teacher'])->name('teacher/dashboard');
        Route::get('teacher/classes', 'teacherClasses')->middleware(['auth', 'role:Teacher'])->name('teacher.classes');
        Route::get('student/dashboard', 'studentDashboardIndex')->middleware(['auth', 'role:Student'])->name('student/dashboard');
        Route::get('parent/dashboard', 'parentDashboardIndex')->middleware(['auth', 'role:Parent'])->name('parent/dashboard');
    });

    // ----------------------------- user controller ---------------------//
    Route::controller(UserManagementController::class)->group(function () {
        Route::get('list/users', 'index')->middleware('auth')->name('list/users');
        Route::post('change/password', 'changePassword')->name('change/password');
        Route::get('view/user/edit/{id}', 'userView')->middleware('auth');
        Route::post('user/update', 'userUpdate')->name('user/update');
        Route::post('user/delete', 'userDelete')->name('user/delete');
        Route::get('get-users-data', 'getUsersData')->name('get-users-data'); /** get all data users */
        Route::get('user/profile/edit', 'editProfile')->middleware('auth')->name('user/profile/edit');
        Route::post('user/profile/update', 'updateProfile')->middleware('auth')->name('user/profile/update');
        Route::post('user/password/update', 'updatePassword')->middleware('auth')->name('user/password/update');
        Route::get('user/activity-log', 'activityLog')->middleware('auth')->name('user/activity-log');
    });

    // ------------------------ setting -------------------------------//
    Route::controller(Setting::class)->group(function () {
        Route::get('setting/page', 'index')->middleware('auth')->name('setting/page');
    });

    // ------------------------ student -------------------------------//
    Route::controller(StudentController::class)->group(function () {
        Route::get('student/list', 'student')->middleware('auth')->name('student/list'); // list student
        Route::get('student/grid', 'studentGrid')->middleware('auth')->name('student/grid'); // grid student
        Route::get('student/add/page', 'studentAdd')->middleware('auth')->name('student/add/page'); // page student
        Route::post('student/add/save', 'studentSave')->name('student/add/save'); // save record student
        Route::get('student/edit/{id}', 'studentEdit'); // view for edit
        Route::post('student/update', 'studentUpdate')->name('student/update'); // update record student
        Route::post('student/delete', 'studentDelete')->name('student/delete'); // delete record student
        Route::get('student/profile/{id}', 'studentProfile')->middleware('auth'); // profile student
    });

    // Restore archived student
    Route::post('student/restore/{id}', [App\Http\Controllers\StudentController::class, 'restore'])->name('student.restore');

    // ------------------------ teacher -------------------------------//
    Route::controller(TeacherController::class)->group(function () {
        Route::get('teacher/add/page', 'teacherAdd')->middleware('auth')->name('teacher/add/page'); // page teacher
        Route::get('teacher/list/page', 'teacherList')->middleware('auth')->name('teacher/list/page'); // page teacher
        Route::get('teacher/grid/page', 'teacherGrid')->middleware('auth')->name('teacher/grid/page'); // page grid teacher
        Route::post('teacher/save', 'saveRecord')->middleware('auth')->name('teacher/save'); // save record
        Route::get('teacher/edit/{user_id}', 'editRecord'); // view teacher record
        Route::post('teacher/update', 'updateRecordTeacher')->middleware('auth')->name('teacher/update'); // update record
        Route::post('teacher/delete', 'teacherDelete')->name('teacher/delete'); // delete record teacher
    });

    // ----------------------- department -----------------------------//
    Route::controller(DepartmentController::class)->group(function () {
        Route::get('department/list/page', 'departmentList')->middleware('auth')->name('department/list/page'); // department/list/page
        Route::get('department/add/page', 'indexDepartment')->middleware('auth')->name('department/add/page'); // page add department
        Route::get('department/edit/{department_id}', 'editDepartment'); // page add department
        Route::post('department/save', 'saveRecord')->middleware('auth')->name('department/save'); // department/save
        Route::post('department/update', 'updateRecord')->middleware('auth')->name('department/update'); // department/update
        Route::post('department/delete', 'deleteRecord')->middleware('auth')->name('department/delete'); // department/delete
        Route::get('get-data-list', 'getDataList')->name('get-data-list'); // get data list

    });

    // ----------------------- subject -----------------------------//
    Route::controller(SubjectController::class)->group(function () {
        Route::get('subject/list/page', 'subjectList')->middleware('auth')->name('subject/list/page'); // subject/list/page
        Route::get('subject/add/page', 'subjectAdd')->middleware('auth')->name('subject/add/page'); // subject/add/page
        Route::post('subject/save', 'saveRecord')->name('subject/save'); // subject/save
        Route::post('subject/update', 'updateRecord')->name('subject/update'); // subject/update
        Route::post('subject/delete', 'deleteRecord')->name('subject/delete'); // subject/delete
        Route::get('subject/edit/{subject_id}', 'subjectEdit'); // subject/edit/page
    });

    // ----------------------- invoice -----------------------------//
    Route::controller(InvoiceController::class)->group(function () {
        Route::get('invoice/list/page', 'invoiceList')->middleware('auth')->name('invoice/list/page'); // subjeinvoicect/list/page
        Route::get('invoice/paid/page', 'invoicePaid')->middleware('auth')->name('invoice/paid/page'); // invoice/paid/page
        Route::get('invoice/overdue/page', 'invoiceOverdue')->middleware('auth')->name('invoice/overdue/page'); // invoice/overdue/page
        Route::get('invoice/draft/page', 'invoiceDraft')->middleware('auth')->name('invoice/draft/page'); // invoice/draft/page
        Route::get('invoice/recurring/page', 'invoiceRecurring')->middleware('auth')->name('invoice/recurring/page'); // invoice/recurring/page
        Route::get('invoice/cancelled/page', 'invoiceCancelled')->middleware('auth')->name('invoice/cancelled/page'); // invoice/cancelled/page
        Route::get('invoice/grid/page', 'invoiceGrid')->middleware('auth')->name('invoice/grid/page'); // invoice/grid/page
        Route::get('invoice/add/page', 'invoiceAdd')->middleware('auth')->name('invoice/add/page'); // invoice/add/page
        Route::post('invoice/add/save', 'saveRecord')->name('invoice/add/save'); // invoice/add/save
        Route::post('invoice/update/save', 'updateRecord')->name('invoice/update/save'); // invoice/update/save
        Route::post('invoice/delete', 'deleteRecord')->name('invoice/delete'); // invoice/delete
        Route::get('invoice/edit/{invoice_id}', 'invoiceEdit')->middleware('auth')->name('invoice/edit/page'); // invoice/edit/page
        Route::get('invoice/view/{invoice_id}', 'invoiceView')->middleware('auth')->name('invoice/view/page'); // invoice/view/page
        Route::get('invoice/settings/page', 'invoiceSettings')->middleware('auth')->name('invoice/settings/page'); // invoice/settings/page
        Route::get('invoice/settings/tax/page', 'invoiceSettingsTax')->middleware('auth')->name('invoice/settings/tax/page'); // invoice/settings/tax/page
        Route::get('invoice/settings/bank/page', 'invoiceSettingsBank')->middleware('auth')->name('invoice/settings/bank/page'); // invoice/settings/bank/page
    });

    // ----------------------- accounts ----------------------------//
    Route::controller(AccountsController::class)->group(function () {
        Route::get('account/fees/collections/page', 'index')->middleware('auth')->name('account/fees/collections/page'); // account/fees/collections/page
        Route::get('add/fees/collection/page', 'addFeesCollection')->middleware('auth')->name('add/fees/collection/page'); // add/fees/collection
        Route::post('fees/collection/save', 'saveRecord')->middleware('auth')->name('fees/collection/save'); // fees/collection/save
    });
});

// Add resource routes for academic years and enrollments
Route::resource('academic_years', AcademicYearController::class)->middleware('auth');
Route::resource('enrollments', EnrollmentController::class)->middleware('auth');
Route::resource('sections', SectionController::class)->middleware('auth');
// Attendance routes (available to teachers and admins)
Route::get('teacher/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
Route::resource('teacher/attendance', AttendanceController::class)->except(['index']);
Route::get('attendance/export', [App\Http\Controllers\AttendanceController::class, 'export'])->name('attendance.export');
Route::get('attendance/student', [App\Http\Controllers\AttendanceController::class, 'studentView'])->name('attendance.student');
Route::get('attendance/parent', [App\Http\Controllers\AttendanceController::class, 'parentView'])->name('attendance.parent');

// Admin-only routes
Route::group(['middleware' => ['role:Admin']], function () {
    // Curriculum Management (Admin Only)
    Route::resource('curriculum', App\Http\Controllers\CurriculumController::class);
    Route::get('curriculum/{curriculum}/assign-subjects', [App\Http\Controllers\CurriculumController::class, 'assignSubjectsForm'])->name('curriculum.assignSubjectsForm');
    Route::post('curriculum/{curriculum}/assign-subjects', [App\Http\Controllers\CurriculumController::class, 'assignSubjects'])->name('curriculum.assignSubjects');
    
    // ----------------------- Grading Module Routes (Admin Access) -----------------------------//
    Route::group(['prefix' => 'admin/grading'], function () {
        // GPA and Ranking (Admin can view all)
        Route::get('gpa-ranking', [App\Http\Controllers\GradingController::class, 'gpaRanking'])->name('admin.grading.gpa-ranking');
        
        // Performance Analytics (Admin can view all)
        Route::get('performance-analytics', [App\Http\Controllers\GradingController::class, 'performanceAnalytics'])->name('admin.grading.performance-analytics');
        
        // Grade Alerts (Admin can manage all)
        Route::get('grade-alerts', [App\Http\Controllers\GradingController::class, 'gradeAlerts'])->name('admin.grading.grade-alerts');
        Route::post('resolve-alert/{alert}', [App\Http\Controllers\GradingController::class, 'resolveAlert'])->name('admin.grading.resolve-alert');
        
        // Export Routes (Admin can export all)
        Route::get('export-grades', [App\Http\Controllers\GradingController::class, 'exportGrades'])->name('admin.grading.export-grades');
        Route::get('export-gpa', [App\Http\Controllers\GradingController::class, 'exportGpa'])->name('admin.grading.export-gpa');
    });
});

// Teacher-only routes
Route::group(['middleware' => ['role:Teacher']], function () {
    // ----------------------- Grading Module Routes (Teacher Only) -----------------------------//
    Route::group(['prefix' => 'grading'], function () {
        // Grade Entry
        Route::get('grade-entry', [App\Http\Controllers\GradingController::class, 'gradeEntryForm'])->name('teacher.grading.grade-entry');
        Route::post('store-grades', [App\Http\Controllers\GradingController::class, 'storeGrades'])->name('teacher.grading.store-grades');
        
        // GPA and Ranking
        Route::get('gpa-ranking', [App\Http\Controllers\GradingController::class, 'gpaRanking'])->name('teacher.grading.gpa-ranking');
        
        // Performance Analytics
        Route::get('performance-analytics', [App\Http\Controllers\GradingController::class, 'performanceAnalytics'])->name('teacher.grading.performance-analytics');
        
        // Weight Settings
        Route::get('weight-settings', [App\Http\Controllers\GradingController::class, 'weightSettings'])->name('teacher.grading.weight-settings');
        Route::post('store-weight-settings', [App\Http\Controllers\GradingController::class, 'storeWeightSettings'])->name('teacher.grading.store-weight-settings');
        
        // Grade Alerts
        Route::get('grade-alerts', [App\Http\Controllers\GradingController::class, 'gradeAlerts'])->name('teacher.grading.grade-alerts');
        Route::post('resolve-alert/{alert}', [App\Http\Controllers\GradingController::class, 'resolveAlert'])->name('teacher.grading.resolve-alert');
        
        // Export Routes
        Route::get('export-grades', [App\Http\Controllers\GradingController::class, 'exportGrades'])->name('teacher.grading.export-grades');
        Route::get('export-gpa', [App\Http\Controllers\GradingController::class, 'exportGpa'])->name('teacher.grading.export-gpa');
    });

    // ----------------------- Lesson Planner Module Routes (Teacher Only) -----------------------------//
    Route::group(['prefix' => 'lessons'], function () {
        // Lesson Management
        Route::get('/', [App\Http\Controllers\LessonController::class, 'index'])->name('lessons.index');
        Route::get('/create', [App\Http\Controllers\LessonController::class, 'create'])->name('lessons.create');
        Route::post('/', [App\Http\Controllers\LessonController::class, 'store'])->name('lessons.store');
        Route::get('/{lesson}', [App\Http\Controllers\LessonController::class, 'show'])->name('lessons.show');
        Route::get('/{lesson}/edit', [App\Http\Controllers\LessonController::class, 'edit'])->name('lessons.edit');
        Route::put('/{lesson}', [App\Http\Controllers\LessonController::class, 'update'])->name('lessons.update');
        Route::delete('/{lesson}', [App\Http\Controllers\LessonController::class, 'destroy'])->name('lessons.destroy');
        
        // Lesson Status Management
        Route::post('/{lesson}/publish', [App\Http\Controllers\LessonController::class, 'publish'])->name('lessons.publish');
        Route::post('/{lesson}/complete', [App\Http\Controllers\LessonController::class, 'complete'])->name('lessons.complete');
        
        // Activity Management
        Route::get('/{lesson}/activities', [App\Http\Controllers\ActivityController::class, 'index'])->name('lessons.activities.index');
        Route::get('/{lesson}/activities/create', [App\Http\Controllers\ActivityController::class, 'create'])->name('lessons.activities.create');
        Route::post('/{lesson}/activities', [App\Http\Controllers\ActivityController::class, 'store'])->name('lessons.activities.store');
        Route::get('/{lesson}/activities/{activity}', [App\Http\Controllers\ActivityController::class, 'show'])->name('lessons.activities.show');
        Route::get('/{lesson}/activities/{activity}/edit', [App\Http\Controllers\ActivityController::class, 'edit'])->name('lessons.activities.edit');
        Route::put('/{lesson}/activities/{activity}', [App\Http\Controllers\ActivityController::class, 'update'])->name('lessons.activities.update');
        Route::delete('/{lesson}/activities/{activity}', [App\Http\Controllers\ActivityController::class, 'destroy'])->name('lessons.activities.destroy');
        
                        // Activity Rubric Management
                Route::get('/{lesson}/activities/{activity}/rubric', [App\Http\Controllers\ActivityController::class, 'rubric'])->name('lessons.activities.rubric');
                Route::post('/{lesson}/activities/{activity}/rubric', [App\Http\Controllers\ActivityController::class, 'storeRubric'])->name('lessons.activities.store-rubric');
                Route::get('/{lesson}/activities/{activity}/rubric/{rubric}/edit', [App\Http\Controllers\ActivityController::class, 'editRubric'])->name('lessons.activities.edit-rubric');
                Route::put('/{lesson}/activities/{activity}/rubric/{rubric}', [App\Http\Controllers\ActivityController::class, 'updateRubric'])->name('lessons.activities.update-rubric');
                Route::delete('/{lesson}/activities/{activity}/rubric/{rubric}', [App\Http\Controllers\ActivityController::class, 'destroyRubric'])->name('lessons.activities.destroy-rubric');
                
                // Activity Submissions Management
                Route::get('/{lesson}/activities/{activity}/submissions', [App\Http\Controllers\SubmissionController::class, 'index'])->name('lessons.activities.submissions');
                Route::post('/{lesson}/activities/{activity}/submissions', [App\Http\Controllers\SubmissionController::class, 'store'])->name('lessons.activities.store-submission');
                Route::get('/{lesson}/activities/{activity}/submissions/{submission}', [App\Http\Controllers\SubmissionController::class, 'show'])->name('lessons.activities.show-submission');
                Route::delete('/{lesson}/activities/{activity}/submissions/{submission}', [App\Http\Controllers\SubmissionController::class, 'destroy'])->name('lessons.activities.destroy-submission');
                
                // Grading Management
                Route::get('/{lesson}/activities/{activity}/submissions/{submission}/grade', [App\Http\Controllers\SubmissionController::class, 'gradeSubmission'])->name('lessons.activities.grade-submission');
                Route::post('/{lesson}/activities/{activity}/submissions/{submission}/grade', [App\Http\Controllers\SubmissionController::class, 'storeGrade'])->name('lessons.activities.store-grade');
                Route::get('/{lesson}/activities/{activity}/submissions/{submission}/grade/view', [App\Http\Controllers\SubmissionController::class, 'viewGrade'])->name('lessons.activities.view-grade');
                Route::get('/{lesson}/activities/{activity}/submissions/{submission}/grade/edit', [App\Http\Controllers\SubmissionController::class, 'editGrade'])->name('lessons.activities.edit-grade');
                Route::put('/{lesson}/activities/{activity}/submissions/{submission}/grade', [App\Http\Controllers\SubmissionController::class, 'updateGrade'])->name('lessons.activities.update-grade');
                
                // Export
                Route::get('/{lesson}/activities/{activity}/submissions/export', [App\Http\Controllers\SubmissionController::class, 'exportSubmissions'])->name('lessons.activities.export-submissions');
                
                // Lesson Recommendations
                Route::get('/recommendations/student-analysis', [App\Http\Controllers\LessonRecommendationController::class, 'studentAnalysis'])->name('lessons.recommendations.student-analysis');
                Route::get('/recommendations/class-analysis', [App\Http\Controllers\LessonRecommendationController::class, 'classAnalysis'])->name('lessons.recommendations.class-analysis');
                Route::get('/recommendations/export', [App\Http\Controllers\LessonRecommendationController::class, 'exportAnalysis'])->name('lessons.recommendations.export');
    });
});

// Student-only routes
Route::group(['middleware' => ['role:Student']], function () {
    // My Classes route
    Route::get('/my-classes', [App\Http\Controllers\StudentController::class, 'myClasses'])->name('student.my-classes');
    // Class detail route
    Route::get('/class/{enrollmentId}', [App\Http\Controllers\StudentController::class, 'classDetail'])->name('student.class.detail');
    // Student grades route
    Route::get('/grades', [App\Http\Controllers\StudentController::class, 'grades'])->name('student.grades');
    // Student attendance route
    Route::get('/attendance', [App\Http\Controllers\StudentController::class, 'attendance'])->name('student.attendance');
});

// Parent-only routes
Route::group(['middleware' => ['role:Parent']], function () {
    // Place parent-only routes here
});

Route::get('subjects/{id}/assign-teachers', [SubjectController::class, 'assignTeachersForm'])->name('subjects.assignTeachersForm');
Route::post('subjects/{id}/assign-teachers', [SubjectController::class, 'assignTeachers'])->name('subjects.assignTeachers');
Route::get('sections/{id}/assign-students', [SectionController::class, 'assignStudentsForm'])->name('sections.assignStudentsForm');
Route::post('sections/{id}/assign-students', [SectionController::class, 'assignStudents'])->name('sections.assignStudents');
Route::get('teacher/{id}/assign-grade-levels', [App\Http\Controllers\TeacherController::class, 'assignGradeLevelsForm'])->name('teacher.assignGradeLevelsForm');
Route::post('teacher/{id}/assign-grade-levels', [App\Http\Controllers\TeacherController::class, 'assignGradeLevels'])->name('teacher.assignGradeLevels');
Route::get('api/sections/{section}/subjects', function(App\Models\Section $section) {
    return $section->students()
        ->with('subjects')
        ->get()
        ->pluck('subjects')
        ->flatten()
        ->unique('id')
        ->values();
})->name('api.section.subjects');

// Analytics Routes
Route::group(['prefix' => 'analytics'], function () {
    // Student Analytics
    Route::get('/student-dashboard', [App\Http\Controllers\AnalyticsController::class, 'studentDashboard'])->name('analytics.student-dashboard');
    
    // Teacher Analytics
    Route::get('/teacher-dashboard', [App\Http\Controllers\AnalyticsController::class, 'teacherDashboard'])->name('analytics.teacher-dashboard');
    
    // Admin Analytics
    Route::get('/admin-dashboard', [App\Http\Controllers\AnalyticsController::class, 'adminDashboard'])->name('analytics.admin-dashboard');
    
    // API endpoints for chart data
    Route::get('/chart-data', [App\Http\Controllers\AnalyticsController::class, 'getChartData'])->name('analytics.chart-data');
    
    // Export reports
    Route::get('/export-report', [App\Http\Controllers\AnalyticsController::class, 'exportReport'])->name('analytics.export-report');
    
    // Detail views (for teachers/admins viewing specific students/teachers)
    Route::get('/student/{studentId}', [App\Http\Controllers\AnalyticsController::class, 'getStudentAnalytics'])->name('analytics.student-detail');
    Route::get('/teacher/{teacherId}', [App\Http\Controllers\AnalyticsController::class, 'getTeacherAnalytics'])->name('analytics.teacher-detail');
});

// Calendar Routes
Route::group(['prefix' => 'calendar', 'middleware' => ['role:Admin,Teacher']], function () {
    Route::get('/', [App\Http\Controllers\CalendarEventController::class, 'index'])->name('calendar.index');
    Route::get('/create', [App\Http\Controllers\CalendarEventController::class, 'create'])->name('calendar.create');
    Route::post('/', [App\Http\Controllers\CalendarEventController::class, 'store'])->name('calendar.store');
    Route::get('/{calendarEvent}', [App\Http\Controllers\CalendarEventController::class, 'show'])->name('calendar.show');
    Route::get('/{calendarEvent}/edit', [App\Http\Controllers\CalendarEventController::class, 'edit'])->name('calendar.edit');
    Route::put('/{calendarEvent}', [App\Http\Controllers\CalendarEventController::class, 'update'])->name('calendar.update');
    Route::delete('/{calendarEvent}', [App\Http\Controllers\CalendarEventController::class, 'destroy'])->name('calendar.destroy');
    
    // API endpoints for calendar functionality
    Route::get('/available-slots', [App\Http\Controllers\CalendarEventController::class, 'getAvailableSlots'])->name('calendar.available-slots');
    Route::get('/check-conflicts', [App\Http\Controllers\CalendarEventController::class, 'checkConflicts'])->name('calendar.check-conflicts');
    
    // Test route for debugging
    Route::get('/test/events', function() {
        $events = App\Models\CalendarEvent::with(['subject', 'teacher', 'room'])->take(5)->get();
        return response()->json($events);
    })->name('calendar.test-events');
});

// Test route for calendar events
Route::get('/calendar/test/events', function() {
    $events = App\Models\CalendarEvent::with(['subject', 'teacher', 'room'])->get();
    return response()->json([
        'total_events' => $events->count(),
        'events' => $events->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start_time' => $event->start_time->toISOString(),
                'end_time' => $event->end_time->toISOString(),
                'event_type' => $event->event_type,
                'subject' => $event->subject?->subject_name,
                'teacher' => $event->teacher?->full_name
            ];
        })
    ]);
});

// Test calendar page
Route::get('/calendar/test', function() {
    return view('calendar.test');
});

// Calendar events list route
Route::get('/calendar/events/list', function(\Illuminate\Http\Request $request) {
    $query = App\Models\CalendarEvent::with(['subject', 'teacher', 'room']);
    
    // Filter by event type
    if ($request->filled('event_type')) {
        $query->where('event_type', $request->event_type);
    }
    
    // Filter by date range
    if ($request->filled('date_range')) {
        switch ($request->date_range) {
            case 'today':
                $query->whereDate('start_time', today());
                break;
            case 'week':
                $query->whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('start_time', now()->month);
                break;
            case 'future':
                $query->where('start_time', '>', now());
                break;
            case 'past':
                $query->where('start_time', '<', now());
                break;
        }
    }
    
    // Search functionality
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhereHas('subject', function($sq) use ($search) {
                  $sq->where('subject_name', 'like', "%{$search}%");
              })
              ->orWhereHas('teacher', function($sq) use ($search) {
                  $sq->where('full_name', 'like', "%{$search}%");
              });
        });
    }
    
    $events = $query->orderBy('start_time', 'desc')->paginate(15);
    return view('calendar.events-list', compact('events'));
})->name('calendar.events.list');

// Schedule Routes
Route::group(['prefix' => 'schedule', 'middleware' => ['role:Student,Admin,Teacher,Parent']], function () {
    Route::get('/', [App\Http\Controllers\ClassScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/dashboard-data', [App\Http\Controllers\ClassScheduleController::class, 'getDashboardSchedule'])->name('schedule.dashboard-data');
    Route::get('/test-my-schedule', function() {
        return view('test.my-schedule-test');
    })->name('schedule.test');
});

// Student-specific routes
Route::group(['middleware' => ['role:Student']], function () {
    Route::get('/my-schedule', [App\Http\Controllers\ClassScheduleController::class, 'mySchedule'])->name('student.my-schedule');
});

// Messaging and Notification Routes
Route::group(['prefix' => 'announcements', 'middleware' => ['auth']], function () {
    Route::get('/', [App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/create', [App\Http\Controllers\AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/', [App\Http\Controllers\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/{announcement}', [App\Http\Controllers\AnnouncementController::class, 'show'])->name('announcements.show');
    Route::get('/{announcement}/edit', [App\Http\Controllers\AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('/{announcement}', [App\Http\Controllers\AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/{announcement}', [App\Http\Controllers\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::patch('/{announcement}/toggle-pin', [App\Http\Controllers\AnnouncementController::class, 'togglePin'])->name('announcements.toggle-pin');
    Route::get('/dashboard/data', [App\Http\Controllers\AnnouncementController::class, 'getDashboardAnnouncements'])->name('announcements.dashboard-data');
});

Route::group(['prefix' => 'messages', 'middleware' => ['auth']], function () {
    Route::get('/', [App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::get('/sent', [App\Http\Controllers\MessageController::class, 'sent'])->name('messages.sent');
    Route::get('/archived', [App\Http\Controllers\MessageController::class, 'archived'])->name('messages.archived');
    Route::get('/create', [App\Http\Controllers\MessageController::class, 'create'])->name('messages.create');
    Route::post('/', [App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
    Route::get('/{message}', [App\Http\Controllers\MessageController::class, 'show'])->name('messages.show');
    Route::delete('/{message}', [App\Http\Controllers\MessageController::class, 'destroy'])->name('messages.destroy');
    Route::patch('/{message}/read', [App\Http\Controllers\MessageController::class, 'markAsRead'])->name('messages.mark-read');
    Route::patch('/{message}/unread', [App\Http\Controllers\MessageController::class, 'markAsUnread'])->name('messages.mark-unread');
    Route::patch('/{message}/archive', [App\Http\Controllers\MessageController::class, 'archive'])->name('messages.archive');
    Route::patch('/{message}/unarchive', [App\Http\Controllers\MessageController::class, 'unarchive'])->name('messages.unarchive');
    Route::get('/conversation/{userId}', [App\Http\Controllers\MessageController::class, 'conversation'])->name('messages.conversation');
    Route::get('/unread-count', [App\Http\Controllers\MessageController::class, 'getUnreadCount'])->name('messages.unread-count');
});

// Notification Routes
Route::group(['prefix' => 'notifications', 'middleware' => ['auth']], function () {
    Route::get('/', function() {
        return view('notifications.index');
    })->name('notifications.index');
    
    Route::patch('/{id}/mark-as-read', function($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.mark-read');
    
    Route::patch('/{id}/mark-as-unread', function($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsUnread();
        return response()->json(['success' => true]);
    })->name('notifications.mark-unread');
    
    Route::delete('/{id}', function($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();
        return response()->json(['success' => true]);
    })->name('notifications.delete');
    
    Route::patch('/mark-all-read', function() {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.mark-all-read');
});

// Parent Routes
Route::group(['prefix' => 'parent', 'middleware' => ['auth', 'role:Parent']], function () {
    Route::get('/child/{childId}/profile', [App\Http\Controllers\ParentController::class, 'childProfile'])->name('parent.child.profile');
    Route::get('/child/{childId}/grades', [App\Http\Controllers\ParentController::class, 'childGrades'])->name('parent.child.grades');
    Route::get('/child/{childId}/attendance', [App\Http\Controllers\ParentController::class, 'childAttendance'])->name('parent.child.attendance');
    Route::get('/child/{childId}/activities', [App\Http\Controllers\ParentController::class, 'childActivities'])->name('parent.child.activities');
    Route::get('/schedule', [App\Http\Controllers\ClassScheduleController::class, 'index'])->name('parent.schedule');
    Route::get('/test', function() { return 'Parent route working!'; })->name('parent.test');
});

