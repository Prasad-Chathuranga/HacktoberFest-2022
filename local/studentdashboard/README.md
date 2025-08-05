# Student Dashboard Plugin for Moodle

A comprehensive dashboard plugin designed specifically for students to enhance their learning experience in Moodle. This plugin provides a centralized view of course progress, upcoming assignments, recent grades, and quick navigation shortcuts.

## Features

### 🎯 Core Dashboard Features
- **User Profile Overview**: Display student information with avatar and quick profile access
- **Enrollment Statistics**: Show enrolled, completed, and incomplete course counts
- **Course Progress Tracking**: Visual progress indicators with animated circles
- **Badge Integration**: Display earned badges and achievements

### 📚 Enhanced Learning Tools
- **Upcoming Assignments**: Shows assignments and quizzes due in the next 2 weeks with urgency indicators
- **Recent Grades**: Display recent grades with color-coded performance indicators
- **Quick Navigation**: Fast access to common Moodle features (courses, messages, calendar, etc.)
- **Recently Accessed Items**: Quick links to recently viewed courses and activities

### 🎨 Modern UI/UX
- **Responsive Design**: Works seamlessly on desktop, tablet, and mobile devices
- **Modern Animations**: Smooth transitions and hover effects
- **Color-Coded Elements**: Visual indicators for urgency, performance, and status
- **Intuitive Layout**: Clean, organized interface designed for student workflows

## Installation

### Requirements
- Moodle 4.0 or later
- PHP 7.4 or later
- Student role capabilities properly configured

### Installation Steps

1. **Download or Clone**
   ```bash
   cd /path/to/moodle/local/
   git clone [repository-url] studentdashboard
   ```

2. **Set Permissions**
   ```bash
   chmod -R 755 studentdashboard
   chown -R www-data:www-data studentdashboard
   ```

3. **Install Plugin**
   - Go to Site Administration → Notifications
   - Follow the installation prompts
   - The plugin will create necessary capabilities and settings

4. **Configure Permissions**
   - Go to Site Administration → Users → Permissions → Define roles
   - Ensure students have the `local/studentdashboard:view` capability

## Configuration

### Access Control
The plugin automatically restricts access to students only. Users with administrative capabilities are redirected to the standard Moodle dashboard.

### Navigation Integration
The plugin adds navigation links in:
- Main navigation menu (for students)
- User profile settings
- Quick access shortcuts

### Customization Options
- **Course Image Fallbacks**: Default images for courses without overview files
- **Badge Display**: Configure badge display preferences
- **Navigation Shortcuts**: Customize quick navigation links

## Plugin Structure

```
local/studentdashboard/
├── classes/
│   ├── dashboard.php              # Core dashboard functionality
│   └── output/
│       ├── dashboard_page.php     # Template data preparation
│       └── renderer.php           # Output rendering
├── db/
│   └── access.php                 # Capability definitions
├── lang/
│   └── en/
│       └── local_studentdashboard.php  # Language strings
├── templates/
│   └── dashboard.mustache         # Main dashboard template
├── amd/
│   └── src/
│       └── dashboard.js           # JavaScript functionality
├── pix/                          # Plugin icons and images
├── styles.css                    # Plugin styling
├── index.php                     # Main dashboard page
├── lib.php                       # Library functions
└── version.php                   # Plugin version information
```

## Features in Detail

### Upcoming Assignments
- Displays assignments and quizzes due within 2 weeks
- Color-coded urgency levels:
  - **Red**: Due within 3 days (urgent)
  - **Yellow**: Due within 7 days (soon)
  - **Green**: Due later (normal)
- Direct links to assignment/quiz pages
- Filters out already submitted work

### Recent Grades
- Shows the 5 most recent grades
- Performance indicators:
  - **Green**: Excellent (80%+)
  - **Blue**: Good (70-79%)
  - **Yellow**: Average (60-69%)
  - **Red**: Needs Improvement (<60%)
- Displays both percentage and point scores

### Quick Navigation
- **My Courses**: Direct access to course list
- **Messages**: Moodle messaging system
- **Calendar**: Personal calendar view
- **Grades**: Complete grade overview
- **Files**: Personal file management
- **Profile**: User profile editing

### Progress Tracking
- Animated progress circles for course completion
- Based on activity completion and course completion criteria
- Visual feedback for student motivation

## Technical Details

### Database Queries
The plugin uses optimized SQL queries to:
- Retrieve enrollment statistics
- Get course progress data
- Fetch upcoming assignments and deadlines
- Load recent grades and activities

### Performance Considerations
- Efficient database queries with proper indexes
- Lazy loading of course images
- Caching of frequently accessed data
- Responsive design for fast mobile loading

### Security Features
- Proper capability checks for student access
- SQL injection prevention with parameterized queries
- Cross-site scripting (XSS) protection
- Role-based access control

## Customization

### Adding Custom Navigation Items
Edit the `get_quick_navigation()` method in `classes/dashboard.php`:

```php
$navigation[] = [
    'name' => 'Custom Link',
    'url' => new \moodle_url('/custom/path'),
    'icon' => 'fa-custom-icon',
    'description' => 'Custom description'
];
```

### Modifying Urgency Thresholds
Adjust assignment urgency levels in `get_upcoming_assignments()`:

```php
$assignment->urgency = $assignment->days_until <= 1 ? 'urgent' : 
                      ($assignment->days_until <= 5 ? 'soon' : 'normal');
```

### Styling Customization
Override styles in your theme or modify `styles.css`:

```css
.student-dashboard {
    /* Custom dashboard styles */
}
```

## Troubleshooting

### Common Issues

1. **Dashboard not showing for students**
   - Check capability permissions
   - Verify role assignments
   - Ensure plugin is properly installed

2. **Missing course images**
   - Check course overview files
   - Verify file permissions
   - Add default images to pix/ directory

3. **JavaScript not working**
   - Clear theme caches
   - Check for JavaScript errors in browser console
   - Verify AMD module loading

### Debug Mode
Enable Moodle debugging to see detailed error messages:
- Site Administration → Development → Debugging
- Set Debug messages to "DEVELOPER"

## Support and Contributing

### Bug Reports
Please report bugs with:
- Moodle version
- PHP version
- Browser information
- Error messages or screenshots

### Feature Requests
We welcome suggestions for new features that would benefit student learning.

### Development
The plugin follows Moodle coding standards and best practices:
- PSR-4 autoloading
- Proper documentation
- Unit testing (recommended)
- Accessibility compliance

## License

This plugin is licensed under the GNU General Public License v3.0. See the LICENSE file for details.

## Changelog

### Version 1.1.0 (2024-12-16)
- ✨ Added upcoming assignments and deadlines tracking
- ✨ Integrated recent grades overview with performance indicators
- ✨ Implemented quick navigation shortcuts
- 🎨 Enhanced UI with modern design elements
- 🐛 Improved mobile responsiveness
- 📚 Updated documentation and installation guide

### Version 1.0.0 (2024-01-16)
- 🎉 Initial release
- 📊 Basic dashboard with course progress
- 👤 User profile integration
- 🎖️ Badge system support
- 📱 Responsive design

## Credits

Developed by the Learning Dashboard Team
Copyright 2024 - Licensed under GPL v3.0