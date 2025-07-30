# Messaging and Notification Module

## Overview
This document describes the implementation of the comprehensive Messaging and Notification module for the LMS system. The module provides announcements, internal messaging, email notifications, and auto-reminders for various academic events.

## Features Implemented

### ✅ 1. Announcements System
- **Post Announcements**: Admins and teachers can create announcements
- **Target Audience**: Specify who should see the announcement (all, students, teachers, parents, admins)
- **Priority Levels**: Low, Normal, High, Urgent with color coding
- **Announcement Types**: General, Academic, Event, Reminder, Emergency
- **Pinning**: Pin important announcements to the top
- **Scheduling**: Schedule announcements for future publication
- **Expiration**: Set expiration dates for announcements
- **Dashboard Integration**: Display relevant announcements on user dashboards

### ✅ 2. Internal Messaging System
- **Teacher-Parent Communication**: Direct messaging between teachers and parents
- **Student Linking**: Messages can be linked to specific student records
- **Message Types**: General, Academic, Behavioral, Attendance, Grade
- **Priority System**: Low, Normal, High, Urgent priority levels
- **Read/Unread Status**: Track message read status with timestamps
- **Archiving**: Archive old messages
- **Conversation View**: View full conversation history between users
- **Role-based Access**: Different messaging permissions based on user roles

### ✅ 3. Email Notifications
- **Automatic Email Alerts**: Send emails for important events
- **Upcoming Deadlines**: Notify about assignments, exams, etc.
- **Student Concerns**: Flag behavioral or academic concerns
- **Contextual Information**: Include student info and relevant context
- **Professional Templates**: Well-formatted email templates

### ✅ 4. Auto-Reminders System
- **Low Grade Detection**: Automatically detect students with low grades
- **Attendance Tracking**: Monitor missed classes and attendance patterns
- **Multi-recipient Alerts**: Send reminders to students, parents, and teachers
- **Smart Notifications**: Context-aware notifications based on user role

## Database Structure

### Announcements Table
```sql
CREATE TABLE announcements (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('general', 'academic', 'event', 'reminder', 'emergency') DEFAULT 'general',
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    target_audience ENUM('all', 'students', 'teachers', 'parents', 'admins') DEFAULT 'all',
    target_roles JSON NULL,
    target_sections JSON NULL,
    is_pinned BOOLEAN DEFAULT FALSE,
    is_scheduled BOOLEAN DEFAULT FALSE,
    scheduled_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);
```

### Messages Table
```sql
CREATE TABLE messages (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    subject VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    sender_id BIGINT UNSIGNED NOT NULL,
    recipient_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NULL,
    type ENUM('general', 'academic', 'behavioral', 'attendance', 'grade') DEFAULT 'general',
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    is_archived BOOLEAN DEFAULT FALSE,
    archived_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);
```

### Notifications Table (Laravel Default)
```sql
CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT UNSIGNED NOT NULL,
    data TEXT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## Models

### Announcement Model
- **Fillable Fields**: All announcement attributes
- **Casts**: JSON fields, boolean fields, datetime fields
- **Relationships**: `creator()` - belongs to User
- **Scopes**: `active()`, `forRole()`, `pinned()`
- **Methods**: `isVisibleTo()`, `getPriorityColorAttribute()`, `getTypeIconAttribute()`

### Message Model
- **Fillable Fields**: All message attributes
- **Casts**: Boolean fields, datetime fields
- **Relationships**: `sender()`, `recipient()`, `student()`
- **Scopes**: `unread()`, `read()`, `archived()`, `notArchived()`, `forUser()`, `fromUser()`
- **Methods**: `markAsRead()`, `markAsUnread()`, `archive()`, `unarchive()`, `getConversation()`

## Controllers

### AnnouncementController
- **Index**: Display announcements for user's role
- **Create/Store**: Create new announcements (Admin/Teacher only)
- **Show**: View announcement details
- **Edit/Update**: Edit announcements (Admin/Teacher only)
- **Destroy**: Delete announcements (Admin/Teacher only)
- **TogglePin**: Pin/unpin announcements (Admin only)
- **getDashboardAnnouncements**: API for dashboard widget

### MessageController
- **Index**: Display inbox messages
- **Sent**: Display sent messages
- **Archived**: Display archived messages
- **Create/Store**: Send new messages
- **Show**: View message details
- **Conversation**: View conversation between users
- **Mark as Read/Unread**: Update read status
- **Archive/Unarchive**: Manage message archiving
- **Destroy**: Delete messages
- **getUnreadCount**: API for unread count

## Notifications

### AnnouncementNotification
- **Channels**: Database, Mail
- **Content**: Announcement details with priority and type
- **Email Template**: Professional announcement email

### NewMessageNotification
- **Channels**: Database, Mail
- **Content**: Message details with sender and student info
- **Email Template**: New message notification email

### LowGradeAlertNotification
- **Channels**: Database, Mail
- **Content**: Grade details with subject and component info
- **Role-based Content**: Different content for students, parents, teachers

### AttendanceAlertNotification
- **Channels**: Database, Mail
- **Content**: Attendance details with missed days count
- **Role-based Content**: Different content for students, parents, teachers

## Routes

### Announcement Routes
```
GET    /announcements                    - List announcements
GET    /announcements/create            - Create form
POST   /announcements                   - Store announcement
GET    /announcements/{id}              - Show announcement
GET    /announcements/{id}/edit         - Edit form
PUT    /announcements/{id}              - Update announcement
DELETE /announcements/{id}              - Delete announcement
PATCH  /announcements/{id}/toggle-pin   - Toggle pin status
GET    /announcements/dashboard/data    - Dashboard API
```

### Message Routes
```
GET    /messages                        - Inbox
GET    /messages/sent                   - Sent messages
GET    /messages/archived               - Archived messages
GET    /messages/create                 - Compose message
POST   /messages                        - Send message
GET    /messages/{id}                   - View message
DELETE /messages/{id}                   - Delete message
PATCH  /messages/{id}/read              - Mark as read
PATCH  /messages/{id}/unread            - Mark as unread
PATCH  /messages/{id}/archive           - Archive message
PATCH  /messages/{id}/unarchive         - Unarchive message
GET    /messages/conversation/{userId}  - View conversation
GET    /messages/unread-count           - Unread count API
```

### Notification Routes
```
GET    /notifications                   - List notifications
PATCH  /notifications/{id}/mark-read    - Mark as read
PATCH  /notifications/{id}/mark-unread  - Mark as unread
DELETE /notifications/{id}              - Delete notification
PATCH  /notifications/mark-all-read     - Mark all as read
```

## Role-based Access Control

### Announcements
- **Admin**: Full access (create, edit, delete, pin)
- **Teacher**: Create, edit own announcements
- **Student/Parent**: View relevant announcements only

### Messages
- **Admin**: Message anyone
- **Teacher**: Message parents and students
- **Parent**: Message teachers only
- **Student**: Message teachers only

## Auto-Reminder System

### Low Grade Detection
- **Trigger**: When grade is below threshold (configurable)
- **Recipients**: Student, Parent, Assigned Teacher
- **Content**: Grade details, subject, component, suggestions

### Attendance Monitoring
- **Trigger**: When student is marked absent
- **Recipients**: Student, Parent, Assigned Teacher
- **Content**: Attendance details, missed days count, recommendations

## Integration Points

### Dashboard Integration
- **Announcement Widget**: Display relevant announcements
- **Message Count**: Show unread message count
- **Notification Bell**: Display recent notifications

### Sidebar Navigation
- **Announcements**: Link to announcements list
- **Messages**: Link to inbox
- **Notifications**: Link to notifications center

## Email Configuration

### Required Environment Variables
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="LMS System"
```

## Usage Examples

### Creating an Announcement
```php
$announcement = Announcement::create([
    'title' => 'Exam Schedule Update',
    'content' => 'The final exam schedule has been updated...',
    'type' => 'academic',
    'priority' => 'high',
    'target_audience' => 'students',
    'is_pinned' => true,
    'created_by' => auth()->id()
]);
```

### Sending a Message
```php
$message = Message::create([
    'subject' => 'Student Performance Discussion',
    'content' => 'I would like to discuss your child\'s recent performance...',
    'sender_id' => auth()->id(),
    'recipient_id' => $parent->id,
    'student_id' => $student->id,
    'type' => 'academic',
    'priority' => 'normal'
]);
```

### Sending Notifications
```php
// Announcement notification
$user->notify(new AnnouncementNotification($announcement));

// Message notification
$recipient->notify(new NewMessageNotification($message));

// Low grade alert
$student->user->notify(new LowGradeAlertNotification($grade, $student, $subject));
$parent->notify(new LowGradeAlertNotification($grade, $student, $subject));
$teacher->notify(new LowGradeAlertNotification($grade, $student, $subject));
```

## Future Enhancements

### Planned Features
1. **Real-time Notifications**: WebSocket integration for instant notifications
2. **Message Templates**: Pre-defined message templates for common scenarios
3. **Bulk Messaging**: Send messages to multiple recipients
4. **File Attachments**: Support for file uploads in messages
5. **Message Search**: Advanced search functionality
6. **Notification Preferences**: User-configurable notification settings
7. **SMS Integration**: Send SMS notifications for urgent alerts
8. **Mobile App Support**: Push notifications for mobile apps

### Technical Improvements
1. **Queue System**: Implement job queues for better performance
2. **Caching**: Cache frequently accessed data
3. **API Endpoints**: RESTful API for mobile app integration
4. **Webhooks**: External system integration
5. **Analytics**: Message and notification analytics

## Testing

### Unit Tests
- Model relationships and scopes
- Controller methods
- Notification classes
- Validation rules

### Feature Tests
- Announcement CRUD operations
- Message sending and receiving
- Notification delivery
- Role-based access control

### Integration Tests
- Email delivery
- Database operations
- Route accessibility
- Middleware functionality

## Security Considerations

### Data Protection
- **Encryption**: Sensitive data encryption
- **Access Control**: Role-based permissions
- **Audit Trail**: Activity logging
- **Data Retention**: Configurable retention policies

### Privacy Compliance
- **GDPR Compliance**: Data protection regulations
- **Consent Management**: User consent for notifications
- **Data Portability**: Export user data
- **Right to Deletion**: Remove user data on request

## Performance Optimization

### Database Optimization
- **Indexing**: Proper database indexes
- **Query Optimization**: Efficient queries
- **Pagination**: Large dataset handling
- **Caching**: Redis/Memcached integration

### Application Optimization
- **Lazy Loading**: Efficient relationship loading
- **Eager Loading**: Reduce N+1 queries
- **Queue Processing**: Background job processing
- **CDN Integration**: Static asset delivery

## Maintenance

### Regular Tasks
- **Cleanup**: Remove expired announcements
- **Archive**: Archive old messages
- **Backup**: Regular database backups
- **Monitoring**: System health monitoring

### Updates
- **Security Patches**: Regular security updates
- **Feature Updates**: New functionality
- **Bug Fixes**: Issue resolution
- **Performance Tuning**: Optimization improvements

## Support

### Documentation
- **API Documentation**: Comprehensive API docs
- **User Guides**: End-user documentation
- **Developer Guides**: Technical documentation
- **Troubleshooting**: Common issues and solutions

### Contact
- **Technical Support**: Developer assistance
- **User Support**: End-user help
- **Feature Requests**: Enhancement suggestions
- **Bug Reports**: Issue reporting

---

This module provides a comprehensive communication and notification system that enhances the overall user experience and facilitates effective communication between all stakeholders in the educational process. 