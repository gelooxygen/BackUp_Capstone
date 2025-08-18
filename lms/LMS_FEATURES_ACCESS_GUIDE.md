# LMS Features Access Guide

## Overview
This guide shows where teachers, students, and administrators can access the newly implemented LMS features: **Assignments**, **Class Posts**, and **Student Assignment Management**.

## 🔐 Access Control
- **Teachers Only**: Can create and manage assignments and class posts
- **Students**: Can view assignments, submit work, and view grades
- **Admins**: Can view and monitor all content but cannot create

---

## 👨‍🏫 **TEACHER ACCESS**

### Dashboard Quick Actions
- **Location**: Teacher Dashboard (`/dashboard`)
- **Section**: "Quick Actions" cards at the top
- **Features**:
  - 🟦 **Create Assignment** → `/assignments/create`
  - 🟩 **Create Class Post** → `/class-posts/create`
  - 🔵 **View Assignments** → `/assignments`
  - 🟡 **View Class Posts** → `/class-posts`

### Sidebar Navigation
- **Location**: Left sidebar menu
- **Sections**:
  - **Assignment Management**
    - All Assignments → `/assignments`
    - Create Assignment → `/assignments/create`
    - Grade Submissions → (via assignment view)
  - **Class Posts**
    - All Posts → `/class-posts`
    - Create Post → `/class-posts/create`
    - Manage Comments → (via post view)

---

## 👨‍🎓 **STUDENT ACCESS**

### Dashboard Quick Actions
- **Location**: Student Dashboard (`/dashboard`)
- **Section**: "My Learning" cards
- **Features**:
  - 🟦 **My Assignments** → `/student/assignments`
  - 🟩 **My Lessons** → (access learning materials)
  - 🔵 **Class Posts** → (read announcements)
  - 🟡 **My Progress** → (track performance)

### Sidebar Navigation
- **Location**: Left sidebar menu
- **Section**: **Assignments**
  - My Assignments → `/student/assignments`
  - Submit Work → (via assignment view)
  - View Grades → (via assignment view)

---

## 👨‍💼 **ADMIN ACCESS**

### Dashboard Quick Actions
- **Location**: Admin Dashboard (`/dashboard`)
- **Section**: "LMS Management" cards
- **Features**:
  - 🔵 **View Assignments** → `/assignments` (monitor all)
  - 🟩 **View Class Posts** → `/class-posts` (monitor all)
  - 🔵 **Student Submissions** → `/student/assignments` (view all)
  - 🔒 **Create Content** → (disabled - teachers only)

### Sidebar Navigation
- **Location**: Left sidebar menu
- **Section**: **LMS Monitoring**
  - All Assignments → `/assignments`
  - All Class Posts → `/class-posts`
  - Student Submissions → `/student/assignments`

---

## 📍 **DIRECT URL ACCESS**

### Assignments
- **Create**: `/assignments/create` (Teachers only)
- **View All**: `/assignments`
- **View Specific**: `/assignments/{id}`
- **Edit**: `/assignments/{id}/edit`
- **Delete**: `/assignments/{id}` (DELETE method)

### Class Posts
- **Create**: `/class-posts/create` (Teachers only)
- **View All**: `/class-posts`
- **View Specific**: `/class-posts/{id}`
- **Edit**: `/class-posts/{id}/edit`
- **Delete**: `/class-posts/{id}` (DELETE method)

### Student Assignments
- **View All**: `/student/assignments`
- **View Specific**: `/student/assignments/{id}`
- **Submit Work**: `/student/assignments/{id}/submit`
- **View Submission**: `/student/assignments/{id}/submission`

---

## 🚫 **RESTRICTED ACCESS**

### Creation Restrictions
- **Admins**: Cannot create assignments or class posts
- **Students**: Cannot create assignments or class posts
- **Teachers**: Can create assignments and class posts for their subjects

### Error Messages
- **403 Forbidden**: "Only teachers can create assignments/class posts"
- **Unauthorized**: "Unauthorized access" for non-teacher/admin users

---

## 🔄 **WORKFLOW**

### Teacher Workflow
1. **Create** → Dashboard Quick Actions or Sidebar
2. **Manage** → View existing items, edit, delete
3. **Grade** → View student submissions and assign grades
4. **Monitor** → Track student progress and engagement

### Student Workflow
1. **View** → See assigned work and due dates
2. **Download** → Access assignment materials
3. **Submit** → Upload completed work
4. **Track** → Monitor grades and feedback

### Admin Workflow
1. **Monitor** → View all content across the system
2. **Oversee** → Track teacher and student activity
3. **Report** → Generate analytics and reports
4. **Support** → Assist with technical issues

---

## 💡 **TIPS**

1. **Teachers**: Use Quick Actions for fast access to creation tools
2. **Students**: Check dashboard regularly for new assignments
3. **Admins**: Use LMS Monitoring to track system usage
4. **Navigation**: Sidebar provides organized access to all features
5. **Quick Access**: Dashboard cards offer one-click navigation

---

## 🆘 **TROUBLESHOOTING**

### Common Issues
- **403 Error**: Check if you're logged in as a teacher
- **Route Not Found**: Ensure you're using the correct URL
- **Access Denied**: Verify your user role and permissions

### Support
- Contact system administrator for access issues
- Check user role in profile settings
- Verify subject assignments for teachers
