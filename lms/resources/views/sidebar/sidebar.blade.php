<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Main Menu</span>
                </li>

                {{-- ADMIN SIDEBAR --}}
                @if (Session::get('role_name') === 'Admin')
                    <li class="submenu">
                        <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                    </li>
                    
                    <li class="submenu">
                        <a href="#"><i class="fas fa-users-cog"></i> <span>User Management</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('list/users') }}"><i class="fas fa-list"></i> <span>All Users</span></a></li>
                            <li><a href="{{ route('enrollments.create') }}"><i class="fas fa-user-plus"></i> <span>Create User</span></a></li>
                            <li><a href="{{ route('student/list') }}"><i class="fas fa-user-graduate"></i> <span>Students</span></a></li>
                            <li><a href="{{ route('teacher/list/page') }}"><i class="fas fa-chalkboard-teacher"></i> <span>Teachers</span></a></li>
                            <li><a href="{{ route('enrollments.index') }}"><i class="fas fa-list"></i> <span>Enrollments</span></a></li>
                        </ul>   
                    </li>
                    
                    <li class="submenu">
                        <a href="#"><i class="fas fa-graduation-cap"></i> <span>Academic Management</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('class-subject.unified-management') }}"><i class="fas fa-cogs"></i> <span>Classes & Subjects</span></a></li>
                            <li><a href="{{ route('academic_years.index') }}"><i class="fas fa-calendar-alt"></i> <span>Academic Years</span></a></li>
                            <li><a href="{{ route('semesters.index') }}"><i class="fas fa-calendar-week"></i> <span>Semesters</span></a></li>
                            <li><a href="{{ route('curriculum.index') }}"><i class="fas fa-book"></i> <span>Curriculum</span></a></li>
                        </ul>   
                    </li>
                    
                    <li class="submenu">
                        <a href="#"><i class="fas fa-calendar-alt"></i> <span>Calendar & Events</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('calendar.events.list') }}"><i class="fas fa-list"></i> <span>All Events</span></a></li>
                            <li><a href="{{ route('calendar.create') }}"><i class="fas fa-plus"></i> <span>Create Event</span></a></li>
                        </ul>
                    </li>
                    
                    <li class="submenu">
                        <a href="#"><i class="fas fa-bullhorn"></i> <span>Communication</span> <span class="menu-arrow"></span></a>
                        <ul>    
                            <li><a href="{{ route('announcements.index') }}"><i class="fas fa-bullhorn"></i> <span>Announcements</span></a></li>
                            <li><a href="{{ route('messages.index') }}"><i class="fas fa-envelope"></i> <span>Messages</span></a></li>
                        </ul>
                    </li>
                    
                    <li class="submenu">
                        <a href="#"><i class="fas fa-chart-line"></i> <span>Analytics & Settings</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('analytics.admin-dashboard') }}"><i class="fas fa-chart-bar"></i> <span>School Analytics</span></a></li>
                            <li><a href="{{ route('setting/page') }}"><i class="fas fa-cog"></i> <span>System Settings</span></a></li>
                        </ul>
                    </li>
                @endif

                {{-- TEACHER SIDEBAR --}}
                @if (Session::get('role_name') === 'Teacher')
                    <li class="submenu">
                        <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                    </li>
                    
                    <li class="submenu">
                        <a href="#"><i class="fas fa-graduation-cap"></i> <span>Teaching & Learning</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('lessons.index') }}"><i class="fas fa-list"></i> <span>My Lessons</span></a></li>
                            <li><a href="{{ route('lessons.create') }}"><i class="fas fa-plus"></i> <span>Create Lesson</span></a></li>
                            <li><a href="{{ route('teacher.classes') }}"><i class="fas fa-users"></i> <span>My Classes</span></a></li>
                            <li><a href="{{ route('teacher.subjects') }}"><i class="fas fa-book"></i> <span>My Subjects</span></a></li>
                        </ul>
                    </li>
                    
                    <li class="submenu">
                        <a href="#"><i class="fas fa-tasks"></i> <span>Assignments & Grading</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('assignments.index') }}"><i class="fas fa-list"></i> <span>All Assignments</span></a></li>
                            <li><a href="{{ route('assignments.create') }}"><i class="fas fa-plus"></i> <span>Create Assignment</span></a></li>
                            <li><a href="{{ route('teacher.grading.grade-entry') }}"><i class="fas fa-edit"></i> <span>Grade Entry</span></a></li>
                            <li><a href="{{ route('teacher.grading.gpa-ranking') }}"><i class="fas fa-chart-bar"></i> <span>GPA Ranking</span></a></li>
                            <li><a href="{{ route('teacher.grading.performance-analytics') }}"><i class="fas fa-chart-line"></i> <span>Performance Analytics</span></a></li>
                            <li><a href="{{ route('teacher.grading.grade-alerts') }}"><i class="fas fa-exclamation-triangle"></i> <span>Grade Alerts</span></a></li>
                        </ul>
                    </li>
                    
                    <li class="submenu">
                        <a href="#"><i class="fas fa-bullhorn"></i> <span>Class Management</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('class-posts.index') }}"><i class="fas fa-list"></i> <span>All Posts</span></a></li>
                            <li><a href="{{ route('class-posts.create') }}"><i class="fas fa-plus"></i> <span>Create Post</span></a></li>
                            <li><a href="{{ route('attendance.index') }}"><i class="fas fa-calendar-check"></i> <span>Attendance</span></a></li>
                        </ul>
                    </li>
                    
                    <li class="submenu">
                        <a href="#"><i class="fas fa-calendar"></i> <span>Calendar & Communication</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('calendar.events.list') }}"><i class="fas fa-list"></i> <span>All Events</span></a></li>
                            <li><a href="{{ route('calendar.create') }}"><i class="fas fa-plus"></i> <span>Create Event</span></a></li>
                            <li><a href="{{ route('announcements.index') }}"><i class="fas fa-bullhorn"></i> <span>Announcements</span></a></li>
                            <li><a href="{{ route('messages.index') }}"><i class="fas fa-envelope"></i> <span>Messages</span></a></li>
                            <li><a href="{{ route('notifications.index') }}"><i class="fas fa-bell"></i> <span>Notifications</span></a></li>
                        </ul>
                    </li>
                @endif

                {{-- STUDENT SIDEBAR --}}
                @if (Session::get('role_name') === 'Student')
                    <li class="submenu">
                        <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                    </li>
                    
                    <li class="submenu">
                        <a href="#"><i class="fas fa-graduation-cap"></i> <span>My Classes</span> <span class="menu-arrow"></span></a>
                        <ul>
                            @php
                                $user = auth()->user();
                                $student = $user->student;
                                $enrollments = $student ? $student->enrollments()->with(['subject'])->where('status', 'active')->get() : collect();
                            @endphp
                            @foreach($enrollments as $enrollment)
                                <li>
                                    <a href="{{ route('student.class.detail', $enrollment->id) }}">
                                        <i class="fas fa-book"></i> 
                                        <span>#{{ $enrollment->subject->id }}: {{ $enrollment->subject->subject_code ?? 'SUB' . $enrollment->subject->id }}</span>
                                    </a>
                                </li>
                            @endforeach
                            @if($enrollments->count() == 0)
                                <li><a href="#"><i class="fas fa-info-circle"></i> <span>No classes enrolled</span></a></li>
                            @endif
                        </ul>
                    </li>
                    
                    <li class="submenu">
                        <a href="#"><i class="fas fa-chart-line"></i> <span>Academic Records</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('student.my-schedule') }}"><i class="fas fa-calendar-alt"></i> <span>My Schedule</span></a></li>
                            <li><a href="{{ route('student.grades') }}"><i class="fas fa-clipboard-list"></i> <span>Grades</span></a></li>
                            <li><a href="{{ route('student.attendance') }}"><i class="fas fa-user-check"></i> <span>Attendance Records</span></a></li>
                            <li><a href="{{ route('analytics.student-dashboard') }}"><i class="fas fa-chart-line"></i> <span>My Analytics</span></a></li>
                        </ul>
                    </li>
                    
                    <li class="submenu">
                        <a href="#"><i class="fas fa-bullhorn"></i> <span>Communication</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('announcements.index') }}"><i class="fas fa-bullhorn"></i> <span>Announcements</span></a></li>
                            <li><a href="{{ route('messages.index') }}"><i class="fas fa-envelope"></i> <span>Messages</span></a></li>
                            <li><a href="{{ route('notifications.index') }}"><i class="fas fa-bell"></i> <span>Notifications</span></a></li>
                        </ul>
                    </li>
                @endif

                {{-- PARENT SIDEBAR --}}
                @if (Session::get('role_name') === 'Parent')
                    @php
                        $parent = auth()->user();
                        $children = \App\Models\Student::where('parent_email', $parent->email)->get();
                        $selectedChild = $children->first();
                    @endphp
                    
                    <li class="submenu">
                        <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                    </li>
                    
                    <li class="submenu">
                        <a href="#"><i class="fas fa-user"></i> <span>Child Information</span> <span class="menu-arrow"></span></a>
                        <ul>
                            @if($selectedChild)
                                <li><a href="{{ route('parent.child.profile', $selectedChild->id) }}"><i class="fas fa-user"></i> <span>Child Profile</span></a></li>
                                <li><a href="{{ route('parent.child.attendance', $selectedChild->id) }}"><i class="fas fa-user-check"></i> <span>Attendance</span></a></li>
                                <li><a href="{{ route('parent.child.grades', $selectedChild->id) }}"><i class="fas fa-clipboard-list"></i> <span>Grades</span></a></li>
                            @else
                                <li><a href="#"><i class="fas fa-user"></i> <span>Child Profile</span></a></li>
                                <li><a href="#"><i class="fas fa-user-check"></i> <span>Attendance</span></a></li>
                                <li><a href="#"><i class="fas fa-clipboard-list"></i> <span>Grades</span></a></li>
                            @endif
                        </ul>
                    </li>
                    
                    <li class="submenu">
                        <a href="#"><i class="fas fa-calendar-alt"></i> <span>Schedule & Communication</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('parent.schedule') }}"><i class="fas fa-calendar-alt"></i> <span>Class Schedule</span></a></li>
                            <li><a href="{{ route('announcements.index') }}"><i class="fas fa-bullhorn"></i> <span>Announcements</span></a></li>
                            <li><a href="{{ route('messages.index') }}"><i class="fas fa-envelope"></i> <span>Messages</span></a></li>
                            <li><a href="{{ route('notifications.index') }}"><i class="fas fa-bell"></i> <span>Notifications</span></a></li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle submenu toggle
    $('.submenu > a').click(function(e) {
        e.preventDefault();
        var submenu = $(this).next('ul');
        var isVisible = submenu.is(':visible');
        
        // Hide all other submenus
        $('.submenu ul').slideUp();
        
        // Toggle current submenu
        if (!isVisible) {
            submenu.slideDown();
        }
    });
});
</script>

<style>
.sidebar-menu .divider {
    height: 1px;
    background-color: rgba(255, 255, 255, 0.1);
    margin: 8px 15px;
    border: none;
}

/* Enhanced submenu styling */
.submenu ul {
    background: rgba(0, 0, 0, 0.1);
    border-left: 3px solid rgba(255, 255, 255, 0.2);
}

.submenu ul li a {
    padding: 10px 20px 10px 50px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.submenu ul li a:hover {
    background: rgba(255, 255, 255, 0.1);
    padding-left: 55px;
}

.submenu ul li a i {
    margin-right: 8px;
    width: 16px;
    text-align: center;
}
</style>