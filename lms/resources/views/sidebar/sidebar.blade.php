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
                        <a href="{{ route('admin/dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                    </li>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-shield-alt"></i> <span>User Management</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('list/users') }}">List Users</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-chalkboard-teacher"></i> <span>Class & Subject </span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('sections.index') }}">Sections</a></li>
                            <li><a href="{{ route('subject/list/page') }}">Subjects</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-calendar-alt"></i> <span>Academic Module</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('academic_years.index') }}">Academic Years</a></li>
                        </ul>
                    </li>
                    <li><a href="{{ route('calendar.index') }}"><i class="fas fa-calendar"></i> <span>Calendar Management</span></a></li>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-bullhorn"></i> <span>Communication</span> <span class="menu-arrow"></span></a>
                        <ul>    
                            <li><a href="{{ route('announcements.index') }}"><i class="fas fa-bullhorn"></i> <span>Announcements</span></a></li>
                            <li><a href="{{ route('messages.index') }}"><i class="fas fa-envelope"></i> <span>Messages</span></a></li>
                            <li><a href="{{ route('notifications.index') }}"><i class="fas fa-bell"></i> <span>Notifications</span></a></li>
                        </ul>
                    </li>
                    {{-- Curriculum Management only for Admins --}}
                    <li>
                        <a href="{{ route('curriculum.index') }}"><i class="fas fa-book"></i> <span>Curriculum Management</span></a>
                    </li>
                    <li><a href="{{ route('analytics.admin-dashboard') }}"><i class="fas fa-chart-bar"></i> <span>School Analytics</span></a></li>
                    <li><a href="{{ route('setting/page') }}"><i class="fas fa-cog"></i> <span>System Settings</span></a></li>
                @endif

                {{-- TEACHER SIDEBAR --}}
                @if (Session::get('role_name') === 'Teacher')
                    <li class="submenu">
                        <a href="{{ route('teacher/dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                    </li>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-graduation-cap"></i> <span>Grading Management</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('teacher.grading.grade-entry') }}"><i class="fas fa-edit"></i> <span>Grade Entry</span></a></li>
                            <li><a href="{{ route('teacher.grading.gpa-ranking') }}"><i class="fas fa-chart-bar"></i> <span>GPA Ranking</span></a></li>
                            <li><a href="{{ route('teacher.grading.performance-analytics') }}"><i class="fas fa-chart-line"></i> <span>Performance Analytics</span></a></li>
                            <li><a href="{{ route('teacher.grading.weight-settings') }}"><i class="fas fa-cog"></i> <span>Weight Settings</span></a></li>
                            <li><a href="{{ route('teacher.grading.grade-alerts') }}"><i class="fas fa-exclamation-triangle"></i> <span>Grade Alerts</span></a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-book-open"></i> <span>Lesson Planner</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('lessons.index') }}"><i class="fas fa-list"></i> <span>My Lessons</span></a></li>
                            <li><a href="{{ route('lessons.create') }}"><i class="fas fa-plus"></i> <span>Create Lesson</span></a></li>
                            <li><a href="{{ route('lessons.recommendations.student-analysis') }}"><i class="fas fa-chart-line"></i> <span>Performance Analysis</span></a></li>
                            <li><a href="{{ route('lessons.recommendations.class-analysis') }}"><i class="fas fa-users"></i> <span>Class Analysis</span></a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('attendance.index') }}"><i class="fas fa-calendar-check"></i> <span>Attendance</span></a>
                    </li> 
                    <li><a href="{{ route('analytics.teacher-dashboard') }}"><i class="fas fa-chart-bar"></i> <span>Performance Analytics</span></a></li>
                    <li><a href="{{ route('calendar.index') }}"><i class="fas fa-calendar"></i> <span>Calendar Management</span></a></li>
                    <li><a href="#"><i class="fas fa-users"></i> <span>My Class List</span></a></li>
                    <li><a href="#"><i class="fas fa-upload"></i> <span>Upload Lesson Materials</span></a></li>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-bullhorn"></i> <span>Communication</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('announcements.index') }}"><i class="fas fa-bullhorn"></i> <span>Announcements</span></a></li>
                            <li><a href="{{ route('messages.index') }}"><i class="fas fa-envelope"></i> <span>Messages</span></a></li>
                            <li><a href="{{ route('notifications.index') }}"><i class="fas fa-bell"></i> <span>Notifications</span></a></li>
                        </ul>
                    </li>
                    <li><a href="#"><i class="fas fa-user"></i> <span>Student Profiles</span></a></li>
                   
                @endif

                {{-- STUDENT SIDEBAR --}}
                @if (Session::get('role_name') === 'Student')
                    <li class="submenu">
                        <a href="{{ route('student/dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                    </li>
                    <li><a href="{{ route('student.my-schedule') }}"><i class="fas fa-calendar-alt"></i> <span>My Schedule</span></a></li>
                    <li><a href="#"><i class="fas fa-download"></i> <span>Learning Materials</span></a></li>
                    <li><a href="#"><i class="fas fa-clipboard-list"></i> <span>Grades</span></a></li>
                    <li><a href="#"><i class="fas fa-user-check"></i> <span>Attendance Records</span></a></li>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-bullhorn"></i> <span>Communication</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('announcements.index') }}"><i class="fas fa-bullhorn"></i> <span>Announcements</span></a></li>
                            <li><a href="{{ route('messages.index') }}"><i class="fas fa-envelope"></i> <span>Messages</span></a></li>
                            <li><a href="{{ route('notifications.index') }}"><i class="fas fa-bell"></i> <span>Notifications</span></a></li>
                        </ul>
                    </li>
                    <li><a href="{{ route('analytics.student-dashboard') }}"><i class="fas fa-chart-line"></i> <span>My Analytics</span></a></li>
                @endif

                {{-- PARENT SIDEBAR --}}
                @if (Session::get('role_name') === 'Parent')
                    <li class="submenu">
                        <a href="{{ route('parent/dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                    </li>
                    <li><a href="#"><i class="fas fa-user"></i> <span>Child Profile</span></a></li>
                    <li><a href="#"><i class="fas fa-user-check"></i> <span>Attendance</span></a></li>
                    <li><a href="#"><i class="fas fa-clipboard-list"></i> <span>Grades</span></a></li>
                    <li><a href="{{ route('parent.schedule') }}"><i class="fas fa-calendar-alt"></i> <span>Class Schedule</span></a></li>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-bullhorn"></i> <span>Communication</span> <span class="menu-arrow"></span></a>
                        <ul>
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