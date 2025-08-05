# Installation Guide - Enhanced Student Dashboard Plugin

This comprehensive guide provides detailed instructions for installing, configuring, and maintaining the enhanced Student Dashboard plugin for Moodle.

## Prerequisites

Before installing the Student Dashboard plugin, ensure your system meets the following requirements:

### System Requirements
- **Moodle Version**: 4.0 or higher
- **PHP Version**: 7.4 or higher (8.0+ recommended)
- **Database**: MySQL 5.7+ or PostgreSQL 10+
- **Web Server**: Apache 2.4+ or Nginx 1.14+
- **Browser Support**: Modern browsers with CSS3 and ES6 support

### User Requirements
- Administrator access to Moodle site
- Access to server file system (for manual installation)
- OR Plugin installation privileges (for web-based installation)

## New Features in Version 1.1.0

The enhanced version includes:
- **Upcoming Assignments**: Track assignments and quizzes due in the next 2 weeks
- **Recent Grades**: Display recent grades with performance indicators
- **Quick Navigation**: Fast access shortcuts to common Moodle features
- **Enhanced UI**: Improved responsive design and animations
- **Performance Optimizations**: Better database queries and caching

## Installation Methods

### Method 1: Manual Installation (Recommended)

1. **Download the Plugin**
   ```bash
   # Navigate to your Moodle local plugins directory
   cd /path/to/moodle/local/
   
   # Clone the repository (if using Git)
   git clone [repository-url] studentdashboard
   
   # OR extract downloaded ZIP file
   unzip studentdashboard.zip
   ```

2. **Set File Permissions**
   ```bash
   # Set appropriate permissions
   chmod -R 755 studentdashboard/
   chown -R www-data:www-data studentdashboard/
   ```

3. **Complete Installation via Moodle Interface**
   - Log in to Moodle as an administrator
   - Navigate to **Site Administration → Notifications**
   - You should see a notification about the new plugin
   - Click **Upgrade Moodle database now**
   - Follow the installation prompts

### Method 2: Web-based Installation

1. **Prepare Plugin Package**
   - Download the plugin as a ZIP file
   - Ensure the ZIP contains the `studentdashboard` folder at the root level

2. **Install via Moodle Interface**
   - Log in as administrator
   - Go to **Site Administration → Plugins → Install plugins**
   - Upload the ZIP file
   - Select **Plugin type**: Local plugin
   - Click **Install plugin from the ZIP file**
   - Follow the installation wizard

### Method 3: Upgrade from Version 1.0.0

If upgrading from the previous version:

1. **Backup Current Installation**
   ```bash
   # Backup existing plugin directory
   cp -r /path/to/moodle/local/studentdashboard /path/to/backup/studentdashboard_backup
   ```

2. **Replace Files**
   ```bash
   # Remove old files and install new version
   rm -rf /path/to/moodle/local/studentdashboard/*
   # Extract new version files
   ```

3. **Run Upgrade**
   - Visit **Site Administration → Notifications**
   - Complete the database upgrade process

## Post-Installation Configuration

### 1. Verify Installation

After installation, verify the plugin is working correctly:

1. **Check Plugin Status**
   - Go to **Site Administration → Plugins → Plugins overview**
   - Locate "local_studentdashboard" in the list
   - Status should show as "Enabled"
   - Version should display "1.1.0"

2. **Test Enhanced Features**
   - Log in as a test student account
   - Look for "Learning Dashboard" in the navigation menu
   - Verify new sections appear:
     - Quick Navigation shortcuts
     - Upcoming Assignments (if any exist)
     - Recent Grades (if any exist)

### 2. Configure Capabilities

The plugin automatically creates the `local/studentdashboard:view` capability. Verify and adjust as needed:

1. **Check Default Permissions**
   - Go to **Site Administration → Users → Permissions → Define roles**
   - Edit the "Student" role
   - Ensure `local/studentdashboard:view` is set to "Allow"

2. **Restrict Admin Access (Automatic)**
   - The plugin automatically prevents admin users from accessing the student dashboard
   - Admins are redirected to the standard Moodle dashboard

### 3. Enhanced Navigation Setup

The plugin now provides multiple navigation integration points:

- **Main Navigation**: "Learning Dashboard" appears for students
- **Quick Navigation Widget**: Six shortcut buttons for common actions
- **User Menu**: Dashboard link in user profile area
- **Responsive Mobile Menu**: Optimized mobile navigation

### 4. Assignment and Grade Configuration

For optimal functionality of new features:

1. **Enable Course Completion**
   - Go to **Site Administration → Advanced features**
   - Enable "Enable completion tracking"
   - Configure completion criteria in individual courses

2. **Configure Assignment Settings**
   - Ensure assignments have due dates set
   - Enable assignment notifications if desired

3. **Set Up Gradebook**
   - Configure grade categories and items
   - Ensure proper grade calculation methods

## Advanced Configuration

### Database Optimization for Enhanced Features

The new features include additional database queries. Optimize performance:

1. **Create Performance Indexes**
   ```sql
   -- Indexes for assignment tracking
   CREATE INDEX idx_assign_duedate ON mdl_assign(duedate);
   CREATE INDEX idx_quiz_timeclose ON mdl_quiz(timeclose);
   
   -- Indexes for grade tracking
   CREATE INDEX idx_grade_grades_timemodified ON mdl_grade_grades(timemodified);
   CREATE INDEX idx_grade_grades_userid ON mdl_grade_grades(userid);
   
   -- Indexes for recent activity
   CREATE INDEX idx_user_lastaccess_timeaccess ON mdl_user_lastaccess(timeaccess);
   ```

2. **Enable Query Caching**
   - Configure MySQL query cache or PostgreSQL shared buffers
   - Consider implementing Redis or Memcached for application caching

### Customize Quick Navigation

Modify the quick navigation shortcuts in `classes/dashboard.php`:

```php
public function get_quick_navigation() {
    $navigation = [
        [
            'name' => 'Custom Feature',
            'url' => new \moodle_url('/custom/path'),
            'icon' => 'fa-custom-icon',
            'description' => 'Access custom feature'
        ],
        // ... existing navigation items
    ];
    return $navigation;
}
```

### Assignment Urgency Customization

Adjust urgency thresholds for assignments:

```php
// In get_upcoming_assignments() method
$assignment->urgency = $assignment->days_until <= 1 ? 'urgent' : 
                      ($assignment->days_until <= 3 ? 'soon' : 'normal');
```

### Grade Performance Indicators

Customize grade performance thresholds:

```php
// In get_recent_grades() method
$grade->grade_class = $grade->percentage >= 90 ? 'excellent' : 
                     ($grade->percentage >= 80 ? 'good' : 
                     ($grade->percentage >= 70 ? 'average' : 'needs_improvement'));
```

## Enhanced Language Customization

The new version includes additional language strings:

1. **Modify Enhanced Strings**
   - Edit `/local/studentdashboard/lang/en/local_studentdashboard.php`
   - Customize new strings like 'upcoming_assignments', 'recent_grades', etc.

2. **Add Institution-Specific Terms**
   ```php
   // Example customizations
   $string['upcoming_assignments'] = 'Tasks Due Soon';
   $string['recent_grades'] = 'Latest Results';
   $string['quick_navigation'] = 'Fast Access';
   ```

## Performance Optimization

### Caching Configuration

1. **Enable Application Caching**
   - **Site Administration → Plugins → Caching → Configuration**
   - Enable caching for dashboard data
   - Consider implementing custom cache definitions

2. **Database Query Optimization**
   ```php
   // Example of implementing query caching
   $cache = cache::make('local_studentdashboard', 'dashboard_data');
   $cachekey = 'user_' . $userid . '_assignments';
   
   if (!$assignments = $cache->get($cachekey)) {
       $assignments = $this->get_upcoming_assignments();
       $cache->set($cachekey, $assignments);
   }
   ```

### Image Optimization

1. **Optimize Course Images**
   - Compress course overview images
   - Use appropriate image formats (WebP when supported)
   - Implement lazy loading for better performance

2. **Configure Image Caching**
   ```apache
   # Apache .htaccess for image caching
   <IfModule mod_expires.c>
       ExpiresActive on
       ExpiresByType image/png "access plus 1 month"
       ExpiresByType image/jpg "access plus 1 month"
       ExpiresByType image/jpeg "access plus 1 month"
   </IfModule>
   ```

## Troubleshooting Enhanced Features

### Assignment Tracking Issues

1. **Assignments Not Appearing**
   - Verify assignments have due dates set
   - Check that courses are visible and user is enrolled
   - Ensure assignment module is enabled

2. **Incorrect Due Date Calculations**
   - Check server timezone settings
   - Verify user timezone preferences
   - Test with different date formats

### Grade Display Problems

1. **Grades Not Showing**
   - Verify gradebook configuration
   - Check grade item visibility settings
   - Ensure proper grade calculation methods

2. **Incorrect Grade Percentages**
   - Check grade scale configurations
   - Verify maximum grade values
   - Test with different grade types

### Navigation Issues

1. **Quick Navigation Not Working**
   - Check user capabilities for target pages
   - Verify URL generation in navigation method
   - Test with different user roles

2. **Mobile Responsiveness Problems**
   - Test on various device sizes
   - Check CSS media queries
   - Verify touch-friendly interactions

## Security Considerations

### Enhanced Security Features

1. **Input Validation**
   - All user inputs are properly sanitized
   - Database queries use parameterized statements
   - XSS protection implemented throughout

2. **Access Control**
   - Capability checks on all new features
   - Proper context validation
   - Role-based feature visibility

3. **Data Privacy**
   - Student data access limited to own records
   - Proper anonymization in shared contexts
   - GDPR compliance considerations

## Monitoring and Analytics

### Performance Monitoring

1. **Dashboard Load Times**
   ```php
   // Add timing checks in dashboard methods
   $starttime = microtime(true);
   $assignments = $this->get_upcoming_assignments();
   $loadtime = microtime(true) - $starttime;
   // Log performance metrics
   ```

2. **Database Query Analysis**
   - Monitor slow query logs
   - Use EXPLAIN to analyze query performance
   - Implement query profiling for optimization

### Usage Analytics

1. **Track Feature Usage**
   - Monitor which features are most used
   - Analyze user engagement patterns
   - Collect feedback on new features

2. **Performance Metrics**
   - Dashboard page load times
   - Database query execution times
   - User session duration on dashboard

## Backup and Maintenance

### Enhanced Backup Procedures

1. **Complete System Backup**
   ```bash
   # Database backup with new tables
   mysqldump -u username -p --single-transaction database_name > moodle_enhanced_backup.sql
   
   # File system backup including new assets
   tar -czf moodle_enhanced_backup.tar.gz /path/to/moodle/ --exclude='*/cache/*'
   ```

2. **Configuration Backup**
   ```bash
   # Backup custom configurations
   cp /path/to/moodle/local/studentdashboard/classes/dashboard.php /backup/dashboard_config.php
   cp /path/to/moodle/local/studentdashboard/styles.css /backup/custom_styles.css
   ```

### Regular Maintenance Tasks

1. **Clean Up Old Data**
   - Remove expired assignment data
   - Archive old grade records
   - Clean up unused cache entries

2. **Update Dependencies**
   - Keep Font Awesome icons updated
   - Update JavaScript libraries
   - Monitor for security updates

## Migration and Scaling

### Multi-Site Deployment

For institutions with multiple Moodle instances:

1. **Standardize Configuration**
   - Create configuration templates
   - Use environment-specific settings
   - Implement centralized monitoring

2. **Performance Scaling**
   - Implement load balancing for database queries
   - Use CDN for static assets
   - Consider database clustering for large installations

## Support and Resources

### Enhanced Documentation

1. **Feature-Specific Guides**
   - Assignment tracking configuration
   - Grade display customization
   - Navigation widget setup

2. **API Documentation**
   - New method signatures
   - Enhanced template variables
   - Custom event triggers

### Community Resources

1. **Best Practices**
   - Optimization techniques
   - Customization examples
   - Integration patterns

2. **Troubleshooting Database**
   - Common configuration issues
   - Performance optimization tips
   - Error resolution guides

### Professional Support

1. **Development Services**
   - Custom feature development
   - Performance optimization
   - Integration consulting

2. **Training Resources**
   - Administrator training materials
   - User adoption guides
   - Best practice workshops

## CLI Tools and Automation

### Command Line Utilities

```bash
# Check dashboard configuration
php /path/to/moodle/local/studentdashboard/cli/check_config.php

# Rebuild dashboard caches
php /path/to/moodle/local/studentdashboard/cli/rebuild_cache.php

# Analyze dashboard performance
php /path/to/moodle/local/studentdashboard/cli/performance_report.php

# Clean up old dashboard data
php /path/to/moodle/local/studentdashboard/cli/cleanup_data.php --days=30
```

This enhanced installation guide provides comprehensive coverage of the new features and capabilities in version 1.1.0. For additional support or advanced customization needs, refer to the main README.md file or contact the development team.