# Grading Module Documentation

## Overview

The Grading Module is a comprehensive solution for managing student grades, GPA calculations, and performance analytics in the Laravel School Management System. It provides both basic grading functionalities and intelligent features for monitoring student performance.

## Features

### 📌 Basic Grading Features

1. **Manual Grade Entry**
   - Teachers can input grades per student, per subject
   - Support for multiple grading components (Quiz, Assignment, Exam, etc.)
   - Real-time percentage and letter grade calculation
   - Bulk grade entry for entire classes

2. **Weighted Grading System**
   - Configurable weight settings per subject and component
   - Support for different weight configurations per academic period
   - Automatic weight validation (must equal 100%)

3. **GPA Calculation**
   - Automatic GPA computation based on subject grades
   - Support for different grading scales (4.0 scale)
   - GPA tracking per academic year and semester

4. **Class Ranking**
   - Automatic ranking calculation based on GPA
   - Support for section-based and school-wide rankings
   - Visual indicators for top performers

5. **Export Functionality**
   - Excel export for grade reports
   - PDF export for official transcripts
   - Filtered exports by subject, section, or academic period

### 💡 Intelligent Features

1. **Auto-Flag Low Grades**
   - Automatic highlighting of grades below 75% (configurable threshold)
   - Color-coded grade display (red for low, yellow for warning, green for good)

2. **Performance Trends**
   - Line charts showing student progress over time
   - Subject-wise performance analysis
   - Semester-to-semester comparison

3. **At-Risk Alerts**
   - Automatic detection of students with multiple low grades
   - Performance drop alerts
   - Alert management system with resolution tracking

## Database Structure

### New Tables Created

1. **weight_settings**
   - Stores configurable weights for grading components
   - Links to subjects, components, and academic periods

2. **student_gpa**
   - Stores calculated GPA values per student per period
   - Includes ranking and academic standing

3. **grade_alerts**
   - Stores performance alerts and warnings
   - Tracks alert resolution status

### Enhanced Existing Tables

1. **grades** (already existed)
   - Enhanced with percentage calculation
   - Added support for academic periods

2. **subject_components** (already existed)
   - Enhanced with weight management
   - Added active/inactive status

## Models

### New Models

1. **WeightSetting**
   - Manages grading weights per subject and component
   - Supports academic period-specific configurations

2. **StudentGpa**
   - Handles GPA calculations and storage
   - Provides letter grade and description attributes

3. **GradeAlert**
   - Manages performance alerts and warnings
   - Includes severity levels and resolution tracking

### Enhanced Models

1. **Student**
   - Added relationships to GPA records and alerts
   - Added methods for current GPA and active alerts

2. **Subject**
   - Added relationships to weight settings and alerts
   - Enhanced with grading component management

3. **Grade**
   - Enhanced with automatic percentage calculation
   - Added comprehensive relationships

## Controllers

### GradingController

Main controller handling all grading operations:

- `gradeEntryForm()` - Display grade entry interface
- `storeGrades()` - Save grades and trigger calculations
- `gpaRanking()` - Display GPA rankings and statistics
- `performanceAnalytics()` - Show detailed performance analysis
- `weightSettings()` - Manage grading weights
- `storeWeightSettings()` - Save weight configurations
- `gradeAlerts()` - Display and manage alerts
- `resolveAlert()` - Mark alerts as resolved
- `exportGrades()` - Export grade reports
- `exportGpa()` - Export GPA reports

## Views

### Blade Templates

1. **grade-entry.blade.php**
   - Comprehensive grade entry form
   - Dynamic student loading based on filters
   - Real-time grade calculation

2. **gpa-ranking.blade.php**
   - GPA ranking table with statistics
   - Interactive charts for grade distribution
   - Export functionality

3. **performance-analytics.blade.php**
   - Detailed student performance analysis
   - Performance trend charts
   - Alert display and management

4. **weight-settings.blade.php**
   - Weight configuration interface
   - Real-time weight validation
   - Visual weight distribution

5. **grade-alerts.blade.php**
   - Alert management dashboard
   - Alert statistics and filtering
   - Quick action buttons

## Routes

All grading routes are prefixed with `/grading` and protected by authentication:

```php
Route::group(['middleware' => 'auth', 'prefix' => 'grading'], function () {
    // Grade Entry
    Route::get('grade-entry', 'gradeEntryForm')->name('grading.grade-entry');
    Route::post('store-grades', 'storeGrades')->name('grading.store-grades');
    
    // GPA and Ranking
    Route::get('gpa-ranking', 'gpaRanking')->name('grading.gpa-ranking');
    
    // Performance Analytics
    Route::get('performance-analytics', 'performanceAnalytics')->name('grading.performance-analytics');
    
    // Weight Settings
    Route::get('weight-settings', 'weightSettings')->name('grading.weight-settings');
    Route::post('store-weight-settings', 'storeWeightSettings')->name('grading.store-weight-settings');
    
    // Grade Alerts
    Route::get('grade-alerts', 'gradeAlerts')->name('grading.grade-alerts');
    Route::post('resolve-alert/{alert}', 'resolveAlert')->name('grading.resolve-alert');
    
    // Export Routes
    Route::get('export-grades', 'exportGrades')->name('grading.export-grades');
    Route::get('export-gpa', 'exportGpa')->name('grading.export-gpa');
});
```

## Installation & Setup

### 1. Run Migrations

```bash
php artisan migrate
```

### 2. Seed Sample Data (Optional)

```bash
php artisan db:seed --class=GradingSeeder
```

### 3. Add Menu Items

The menu items are automatically added via migration, but you can manually add them to your navigation if needed.

### 4. Configure Dependencies

Ensure the following packages are installed:

```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

## Usage Guide

### For Teachers

1. **Entering Grades**
   - Navigate to Grading Management → Grade Entry
   - Select subject, section, and academic period
   - Choose grading component
   - Enter scores for each student
   - Save grades

2. **Managing Weight Settings**
   - Navigate to Grading Management → Weight Settings
   - Select subject and academic period
   - Configure weights for each component
   - Ensure total equals 100%
   - Save settings

3. **Viewing Performance**
   - Navigate to Grading Management → Performance Analytics
   - Select student to analyze
   - View detailed performance charts and trends

### For Administrators

1. **Monitoring Alerts**
   - Navigate to Grading Management → Grade Alerts
   - Review active alerts
   - Resolve alerts when appropriate
   - Monitor alert statistics

2. **Generating Reports**
   - Use export functionality for grade reports
   - Generate GPA reports for academic periods
   - Export data in Excel or PDF format

3. **Viewing Rankings**
   - Navigate to Grading Management → GPA Ranking
   - View class and section rankings
   - Analyze grade distributions
   - Monitor academic performance trends

## Configuration

### Grading Thresholds

Default thresholds can be modified in the `GradingController`:

```php
private function checkGradeAlerts($subjectId, $academicYearId, $semesterId)
{
    $lowGradeThreshold = 75; // Modify this value
    // ... rest of the method
}
```

### GPA Scale

The GPA calculation uses a 4.0 scale. Modify the `percentageToGradePoints` method in `GradingController` to change the scale:

```php
private function percentageToGradePoints($percentage)
{
    if ($percentage >= 90) return 4.0;
    if ($percentage >= 85) return 3.7;
    // ... modify as needed
}
```

## API Endpoints

The module provides several API endpoints for integration:

- `GET /grading/gpa-ranking` - Get GPA rankings
- `GET /grading/performance-analytics` - Get performance data
- `GET /grading/grade-alerts` - Get active alerts
- `POST /grading/store-grades` - Store grades
- `POST /grading/store-weight-settings` - Store weight settings

## Security

- All routes are protected by authentication middleware
- Role-based access control can be implemented
- Input validation for all grade entries
- SQL injection protection through Eloquent ORM

## Performance Considerations

- GPA calculations are cached in the database
- Bulk operations use database transactions
- Charts use client-side rendering for better performance
- Pagination implemented for large datasets

## Troubleshooting

### Common Issues

1. **Grades not saving**
   - Check database permissions
   - Verify form validation rules
   - Check for JavaScript errors

2. **GPA not calculating**
   - Ensure grades have valid percentages
   - Check academic year and semester settings
   - Verify weight settings are configured

3. **Export not working**
   - Ensure Excel/PDF packages are installed
   - Check file permissions
   - Verify route configuration

### Debug Mode

Enable debug mode in `.env` to see detailed error messages:

```env
APP_DEBUG=true
```

## Future Enhancements

1. **Advanced Analytics**
   - Predictive performance modeling
   - Learning outcome tracking
   - Competency-based assessment

2. **Integration Features**
   - Parent portal integration
   - SMS/Email notifications
   - Third-party LMS integration

3. **Mobile Support**
   - Mobile-responsive design
   - Native mobile app
   - Offline grade entry

## Support

For technical support or feature requests, please refer to the main project documentation or contact the development team.

---

**Version:** 1.0.0  
**Last Updated:** January 2025  
**Compatibility:** Laravel 10.x, PHP 8.1+ 