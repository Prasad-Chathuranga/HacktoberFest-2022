# Installation Guide - Student Dashboard Plugin

This guide provides step-by-step instructions for installing the Student Dashboard plugin for Moodle.

## Prerequisites

Before installing the plugin, ensure your system meets the following requirements:

### System Requirements
- **Moodle Version**: 4.0 or higher
- **PHP Version**: 7.4 or higher
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Database**: MySQL 5.7+ or PostgreSQL 10+
- **Browser Support**: Modern browsers (Chrome 70+, Firefox 65+, Safari 12+, Edge 79+)

### Permissions
- Administrator access to the Moodle installation
- File system write permissions to the Moodle directory
- Database access for creating new tables/capabilities

## Installation Methods

### Method 1: Manual Installation (Recommended)

#### Step 1: Download the Plugin
1. Download the plugin files or clone the repository
2. Extract the files if downloaded as a zip

#### Step 2: Copy Files
Copy the entire `studentdashboard` folder to your Moodle installation:

```bash
# Navigate to your Moodle root directory
cd /path/to/your/moodle/

# Copy the plugin to the local plugins directory
cp -r /path/to/studentdashboard ./local/
```

#### Step 3: Set Permissions
Ensure proper file permissions:

```bash
# Set ownership (replace www-data with your web server user)
chown -R www-data:www-data ./local/studentdashboard/

# Set permissions
find ./local/studentdashboard/ -type f -exec chmod 644 {} \;
find ./local/studentdashboard/ -type d -exec chmod 755 {} \;
```

#### Step 4: Complete Installation
1. Log in to your Moodle site as an administrator
2. Navigate to **Site Administration > Notifications**
3. You should see a notification about the new plugin
4. Click **Upgrade Moodle database now**
5. Follow the prompts to complete the installation

### Method 2: Using Moodle Plugin Installer

#### Step 1: Prepare Plugin Package
1. Create a zip file containing the `studentdashboard` folder
2. Ensure the folder structure is: `studentdashboard/version.php` (at the root of the zip)

#### Step 2: Upload via Web Interface
1. Log in to Moodle as an administrator
2. Navigate to **Site Administration > Plugins > Install plugins**
3. Click **Choose a file** and select your zip file
4. Click **Install plugin from the ZIP file**
5. Follow the installation wizard

#### Step 3: Verify Installation
1. Check that the plugin appears in **Site Administration > Plugins > Plugins overview**
2. Look for "Student Dashboard" under "Local plugins"

## Post-Installation Configuration

### Step 1: Verify Capabilities
1. Go to **Site Administration > Users > Permissions > Define roles**
2. Edit the **Student** role
3. Ensure `local/studentdashboard:view` is set to **Allow**

### Step 2: Test Student Access
1. Log in as a student user (or create a test student account)
2. Look for "Learning Dashboard" in the navigation menu
3. Access the dashboard to verify it loads correctly

### Step 3: Configure Navigation (Optional)
If you want to customize where the dashboard link appears:

1. Go to **Site Administration > Appearance > Navigation**
2. Modify navigation settings as needed
3. The plugin adds the dashboard link automatically for students

## Verification Steps

### Check File Structure
Verify the following files exist in your installation:

```
moodle/local/studentdashboard/
├── version.php
├── index.php
├── lib.php
├── styles.css
├── db/access.php
├── lang/en/local_studentdashboard.php
├── classes/dashboard.php
├── classes/output/dashboard_page.php
├── classes/output/renderer.php
├── templates/dashboard.mustache
└── amd/src/dashboard.js
```

### Test Functionality
1. **Student Dashboard Access**: Students should see the dashboard link
2. **Admin Redirection**: Administrators should not see the student dashboard
3. **Course Progress**: Progress circles should display correctly
4. **Recent Items**: Recently accessed items should appear
5. **Responsive Design**: Test on mobile devices

### Check Logs
Monitor your Moodle logs for any errors:
1. Go to **Site Administration > Reports > Logs**
2. Filter by the studentdashboard component
3. Look for any error entries

## Troubleshooting Installation Issues

### Common Problems

#### 1. Plugin Not Detected
**Symptoms**: Plugin doesn't appear in notifications or plugin list

**Solutions**:
- Verify file permissions (web server must be able to read files)
- Check that `version.php` exists and is properly formatted
- Ensure the plugin is in the correct directory: `moodle/local/studentdashboard/`

#### 2. Database Errors During Installation
**Symptoms**: SQL errors during the upgrade process

**Solutions**:
- Check database user permissions
- Verify database connection settings
- Review the installation logs in `moodledata/`

#### 3. Permission Denied Errors
**Symptoms**: Cannot access plugin files or features

**Solutions**:
```bash
# Fix file permissions
chmod -R 755 /path/to/moodle/local/studentdashboard/
chown -R www-data:www-data /path/to/moodle/local/studentdashboard/
```

#### 4. JavaScript/CSS Not Loading
**Symptoms**: Dashboard appears broken or lacks styling

**Solutions**:
- Clear Moodle cache: **Site Administration > Development > Purge all caches**
- Check web server configuration for serving static files
- Verify browser developer tools for 404 errors

### Getting Help

If you encounter issues not covered here:

1. **Check the README.md** for additional troubleshooting information
2. **Enable debugging**: Site Administration > Development > Debugging
3. **Review server logs**: Check Apache/Nginx error logs
4. **Moodle logs**: Monitor Site Administration > Reports > Logs

### Manual Cleanup (If Needed)

If you need to remove the plugin manually:

```bash
# Remove plugin files
rm -rf /path/to/moodle/local/studentdashboard/

# Clean database (run these SQL commands in your database)
DELETE FROM mdl_capabilities WHERE name LIKE 'local/studentdashboard:%';
DELETE FROM mdl_role_capabilities WHERE capability LIKE 'local/studentdashboard:%';
DELETE FROM mdl_config_plugins WHERE plugin = 'local_studentdashboard';
```

## Security Considerations

### File Permissions
- Plugin files should not be writable by the web server unless necessary
- Uploaded files should be stored outside the web root
- Regular security updates should be applied

### User Access
- The plugin automatically restricts access to students only
- Administrators are redirected to maintain security separation
- Monitor user activity through Moodle's standard logging

## Performance Optimization

### Caching
- The plugin respects Moodle's caching mechanisms
- Consider enabling application caching for better performance
- Monitor database queries and optimize if necessary

### Database Indexing
The plugin uses standard Moodle database functions, but consider adding indexes if you have a large number of users:

```sql
-- Optional: Add index for faster course access queries
ALTER TABLE mdl_user_lastaccess ADD INDEX idx_userid_courseid (userid, courseid);
```

## Backup and Recovery

### Before Installation
1. **Backup your Moodle database**:
```bash
mysqldump -u username -p moodle > moodle_backup_$(date +%Y%m%d).sql
```

2. **Backup your Moodle files**:
```bash
tar -czf moodle_files_backup_$(date +%Y%m%d).tar.gz /path/to/moodle/
```

### After Installation
- Test the backup and restore process with the plugin installed
- Verify that plugin data is included in backups
- Document any custom configurations for disaster recovery

## Next Steps

After successful installation:

1. **User Training**: Provide students with information about the new dashboard
2. **Monitoring**: Monitor usage and performance metrics
3. **Feedback**: Collect user feedback for future improvements
4. **Updates**: Stay informed about plugin updates and security patches

For ongoing support and updates, refer to the plugin documentation and community resources.