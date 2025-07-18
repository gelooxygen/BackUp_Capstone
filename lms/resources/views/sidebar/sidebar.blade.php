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
                        <a href="#"><i class="fas fa-calendar-alt"></i> <span>Academic Year & Semester</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('academic_years.index') }}">Academic Years</a></li>
                        </ul>
                    </li>
                    <li><a href="#"><i class="fas fa-user-check"></i> <span>Attendance Overview</span></a></li>
                    <li><a href="#"><i class="fas fa-file-alt"></i> <span>Reports & Logs</span></a></li>
                    <li><a href="{{ route('setting/page') }}"><i class="fas fa-cog"></i> <span>System Settings</span></a></li>
                    <li><a href="#"><i class="fas fa-bullhorn"></i> <span>Announcement Management</span></a></li>
                @endif

                {{-- TEACHER SIDEBAR --}}
                @if (Session::get('role_name') === 'Teacher')
                    <li class="submenu">
                        <a href="{{ route('teacher/dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                    </li>
                    <li><a href="#"><i class="fas fa-users"></i> <span>My Class List</span></a></li>
                    <li><a href="#"><i class="fas fa-user-check"></i> <span>Student Attendance</span></a></li>
                    <li><a href="#"><i class="fas fa-clipboard-list"></i> <span>Grade Entry</span></a></li>
                    <li><a href="#"><i class="fas fa-upload"></i> <span>Upload Lesson Materials</span></a></li>
                    <li><a href="#"><i class="fas fa-bullhorn"></i> <span>Class Announcements</span></a></li>
                    <li><a href="#"><i class="fas fa-user"></i> <span>Student Profiles</span></a></li>
                @endif

                {{-- STUDENT SIDEBAR --}}
                @if (Session::get('role_name') === 'Student')
                    <li class="submenu">
                        <a href="{{ route('student/dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                    </li>
                    <li><a href="#"><i class="fas fa-calendar-alt"></i> <span>Class Schedule</span></a></li>
                    <li><a href="#"><i class="fas fa-download"></i> <span>Learning Materials</span></a></li>
                    <li><a href="#"><i class="fas fa-clipboard-list"></i> <span>Grades</span></a></li>
                    <li><a href="#"><i class="fas fa-user-check"></i> <span>Attendance Records</span></a></li>
                    <li><a href="#"><i class="fas fa-bullhorn"></i> <span>Announcements</span></a></li>
                @endif

                {{-- PARENT SIDEBAR --}}
                @if (Session::get('role_name') === 'Parent')
                    <li class="submenu">
                        <a href="{{ route('parent/dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                    </li>
                    <li><a href="#"><i class="fas fa-user"></i> <span>Child Profile</span></a></li>
                    <li><a href="#"><i class="fas fa-user-check"></i> <span>Attendance</span></a></li>
                    <li><a href="#"><i class="fas fa-clipboard-list"></i> <span>Grades</span></a></li>
                    <li><a href="#"><i class="fas fa-calendar-alt"></i> <span>Class Schedule</span></a></li>
                    <li><a href="#"><i class="fas fa-envelope"></i> <span>Contact Teachers</span></a></li>
                @endif
            </ul>
        </div>
    </div>
</div>