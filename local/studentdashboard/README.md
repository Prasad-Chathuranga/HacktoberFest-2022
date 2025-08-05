# Student Dashboard - Moodle Local Plugin

A modern, responsive student dashboard plugin for Moodle that provides an enhanced learning experience with course progress tracking, badges display, and recent activity overview.

![Student Dashboard](./screenshots/dashboard.png)

## Features

- **Student-Only Access**: Automatically filters access to students only, redirecting administrators to the standard dashboard
- **Course Progress Tracking**: Visual progress circles showing completion percentage for each course
- **Enrollment Statistics**: Overview of enrolled, completed, and incomplete courses
- **Badge Display**: Showcase of earned badges with visual indicators
- **Recent Activity**: Quick access to recently accessed courses and forum activities
- **Responsive Design**: Mobile-friendly interface that works on all devices
- **Modern UI**: Clean, professional design based on modern UX principles

## Requirements

- Moodle 4.0 or higher
- PHP 7.4 or higher
- Modern web browser with CSS3 and ES6 support

## Installation

### Method 1: Manual Installation

1. Download the plugin files or clone this repository
2. Extract/copy the files to your Moodle installation directory:
   ```
   /path/to/moodle/local/studentdashboard/
   ```

3. Log in to your Moodle site as an administrator
4. Navigate to **Site Administration > Notifications**
5. Follow the installation prompts to complete the installation

### Method 2: Using Moodle Plugin Installer

1. Zip the `studentdashboard` folder
2. Log in as administrator
3. Go to **Site Administration > Plugins > Install plugins**
4. Upload the zip file and follow the installation wizard

## Configuration

### Capabilities

The plugin automatically creates the following capability:

- `local/studentdashboard:view` - Allows users to view the student dashboard

### User Access

By default, the plugin grants access to users with the `student` archetype. Administrators and site managers are automatically redirected to the standard Moodle dashboard.

### Navigation

Once installed, students will see a "Learning Dashboard" link in their navigation menu.

## Usage

### For Students

1. Log in to Moodle
2. Navigate to the "Learning Dashboard" from the main navigation
3. View your course progress, badges, and recent activity
4. Click on course cards to resume learning
5. Access recently viewed content quickly from the recent items section

### For Administrators

- Students will be automatically redirected to the custom dashboard
- Administrators maintain access to the standard Moodle dashboard
- Monitor usage through standard Moodle logs

## Customization

### Styling

The plugin includes comprehensive CSS styling in `styles.css`. Key customizable elements:

- Color schemes (modify CSS variables)
- Card layouts and spacing
- Animation timing and effects
- Responsive breakpoints

### Language Strings

All text is internationalized. Modify language strings in:
```
/local/studentdashboard/lang/en/local_studentdashboard.php
```

Add new language packs by creating corresponding language directories.

### Templates

Dashboard layout can be modified by editing the Mustache templates in:
```
/local/studentdashboard/templates/
```

Main template: `dashboard.mustache`

## File Structure

```
local/studentdashboard/
├── amd/
│   └── src/
│       └── dashboard.js          # JavaScript functionality
├── classes/
│   ├── dashboard.php             # Main dashboard class
│   └── output/
│       ├── dashboard_page.php    # Dashboard page output class
│       └── renderer.php          # Template renderer
├── db/
│   └── access.php                # Capability definitions
├── lang/
│   └── en/
│       └── local_studentdashboard.php  # English language strings
├── pix/                          # Plugin images and icons
├── templates/
│   └── dashboard.mustache        # Main dashboard template
├── index.php                     # Main entry point
├── lib.php                       # Library functions and hooks
├── styles.css                    # Plugin styling
├── version.php                   # Plugin version information
└── README.md                     # This file
```

## API Reference

### Main Classes

#### `local_studentdashboard\dashboard`

Main class for retrieving dashboard data:

- `get_enrollment_stats()` - Returns enrollment statistics
- `get_user_courses()` - Gets user's enrolled courses with progress
- `get_user_badges()` - Retrieves user's earned badges
- `get_recent_items()` - Gets recently accessed items
- `get_last_accessed_course()` - Returns the most recently accessed course

#### `local_studentdashboard\output\dashboard_page`

Handles template data preparation:

- `export_for_template()` - Exports data for Mustache templates

### Functions

#### Library Functions (`lib.php`)

- `local_studentdashboard_extend_navigation()` - Adds navigation menu items
- `local_studentdashboard_is_student()` - Checks if user is a student
- `local_studentdashboard_pluginfile()` - Serves plugin files

## Browser Support

- Chrome 70+
- Firefox 65+
- Safari 12+
- Edge 79+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Troubleshooting

### Common Issues

1. **Dashboard not visible for students**
   - Check that the `local/studentdashboard:view` capability is assigned to student role
   - Verify the plugin is properly installed and enabled

2. **Styling issues**
   - Clear browser cache
   - Check that `styles.css` is being loaded
   - Verify Moodle theme compatibility

3. **JavaScript not working**
   - Check browser console for errors
   - Ensure AMD modules are properly built
   - Verify JavaScript is enabled in browser

### Debug Mode

Enable Moodle debugging to see detailed error messages:
1. Go to **Site Administration > Development > Debugging**
2. Set **Debug messages** to "DEVELOPER"
3. Check debug output for specific error information

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes following Moodle coding standards
4. Test thoroughly across different browsers and devices
5. Submit a pull request with a clear description

### Coding Standards

Follow Moodle coding guidelines:
- PHP: [Moodle PHP Guidelines](https://docs.moodle.org/dev/Coding_style)
- JavaScript: [Moodle JavaScript Guidelines](https://docs.moodle.org/dev/JavaScript_guidelines)
- CSS: [Moodle CSS Guidelines](https://docs.moodle.org/dev/CSS_guidelines)

## License

This plugin is licensed under the GNU GPL v3 or later.

Copyright (C) 2024 Learning Dashboard Contributors

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

## Support

For support, bug reports, or feature requests:

1. Check the documentation above
2. Search existing issues
3. Create a new issue with detailed information including:
   - Moodle version
   - Plugin version
   - Browser and version
   - Steps to reproduce the issue
   - Expected vs actual behavior

## Changelog

### Version 1.0.0 (2024-01-16)
- Initial release
- Student-only dashboard with course progress tracking
- Badge display and recent activity overview
- Responsive design with modern UI
- Full internationalization support