# Troubleshooting Guide - Student Dashboard Plugin

This guide helps resolve common issues when setting up and using the Student Dashboard plugin.

## Issue: Plugin Not Working on Login

### Problem: "Plugin not working on login"

This usually means one of several things:

#### 1. Plugin Not Properly Installed

**Check if the plugin is installed:**
1. Go to **Site Administration > Plugins > Plugins overview**
2. Look for "Student Dashboard" under "Local plugins"
3. If not found, the plugin wasn't installed correctly

**Solution:**
- Reinstall the plugin following the installation guide
- Ensure files are in `/path/to/moodle/local/studentdashboard/`
- Check file permissions

#### 2. Users Can't Access the Dashboard

**Symptoms:**
- Dashboard link doesn't appear in navigation
- Users get "Access denied" errors
- Page redirects to login or error page

**Solutions:**

**Step 1: Check User Roles and Capabilities**
```bash
# Go to Site Administration > Users > Permissions > Define roles
# Edit the Student role or Authenticated user role
# Ensure 'local/studentdashboard:view' is set to Allow
```

**Step 2: Manual Capability Assignment**
If capabilities aren't working, manually assign them:
1. Go to **Site Administration > Users > Permissions > Assign system roles**
2. Find users who should have access
3. Temporarily assign them to a role with dashboard access

**Step 3: Test with Direct URL**
Try accessing the dashboard directly:
```
https://yourmoodle.com/local/studentdashboard/index.php
```

#### 3. Database/Permission Errors

**Check Moodle logs:**
1. Go to **Site Administration > Reports > Logs**
2. Look for errors related to "studentdashboard"
3. Check for database permission issues

**Common database fixes:**
```sql
-- Check if capabilities were created
SELECT * FROM mdl_capabilities WHERE name LIKE 'local/studentdashboard%';

-- If missing, reinstall the plugin or manually add:
INSERT INTO mdl_capabilities (name, captype, contextlevel, component, riskbitmask) 
VALUES ('local/studentdashboard:view', 'read', 10, 'local_studentdashboard', 0);
```

#### 4. File Permission Issues

**Check file permissions:**
```bash
# All files should be readable by web server
ls -la /path/to/moodle/local/studentdashboard/

# Fix permissions if needed
chmod -R 644 /path/to/moodle/local/studentdashboard/*.php
chmod -R 644 /path/to/moodle/local/studentdashboard/*.css
chmod 755 /path/to/moodle/local/studentdashboard/
```

## Quick Fixes

### 1. Clear All Caches
```bash
# In Moodle admin interface:
# Site Administration > Development > Purge all caches
```

### 2. Temporary Access Mode
If you need immediate access, temporarily edit `index.php`:

```php
// Comment out access control temporarily (line 35-40)
/*
if (is_siteadmin() || has_capability('moodle/site:config', $context)) {
    redirect(new moodle_url('/my/'));
}
*/
```

**⚠️ Remember to uncomment this after testing!**

### 3. Debug Mode
Enable debug mode to see detailed errors:
1. Go to **Site Administration > Development > Debugging**
2. Set **Debug messages** to "DEVELOPER"
3. Set **Display debug messages** to "Yes"

## Testing Steps

### 1. Test as Different User Types

**Test as Student:**
1. Create/use a test student account
2. Log in and check if dashboard link appears
3. Try accessing dashboard directly

**Test as Teacher:**
- Teachers should still see the dashboard (unless you want to restrict it)

**Test as Admin:**
- Admins should be redirected to standard dashboard

### 2. Check Navigation

**Dashboard Link Should Appear:**
- In main navigation menu
- For non-admin users only
- After successful login

### 3. Verify Data Display

**Dashboard Should Show:**
- User profile information
- Course enrollment stats (even if 0/0/0)
- Message if no courses are available
- Basic page layout even without data

## Common Error Messages

### "Access denied"
- **Cause:** User doesn't have required capability
- **Fix:** Check role assignments and capabilities

### "Page not found" 
- **Cause:** Plugin files not in correct location
- **Fix:** Verify file installation path

### "Database error"
- **Cause:** Plugin not properly installed in database
- **Fix:** Reinstall plugin or run upgrade

### "Class not found"
- **Cause:** PHP class autoloading issues
- **Fix:** Clear caches, check file permissions

## Manual Installation Check

Verify these files exist:
```
/local/studentdashboard/
├── version.php                    ✓ Contains plugin version
├── index.php                      ✓ Main dashboard page  
├── lib.php                        ✓ Navigation hooks
├── db/access.php                  ✓ Capability definitions
├── db/install.php                 ✓ Installation hooks
├── lang/en/local_studentdashboard.php  ✓ Language strings
├── classes/dashboard.php          ✓ Main dashboard class
├── classes/output/dashboard_page.php   ✓ Template data class
├── classes/output/renderer.php    ✓ Template renderer
├── templates/dashboard.mustache   ✓ Main template
└── styles.css                     ✓ Dashboard styling
```

## Getting Help

### Enable Detailed Logging
Add this to your config.php temporarily:
```php
$CFG->debug = E_ALL;
$CFG->debugdisplay = 1;
```

### Check Server Logs
```bash
# Check Apache/Nginx error logs
tail -f /var/log/apache2/error.log
# or
tail -f /var/log/nginx/error.log

# Check Moodle logs
tail -f /path/to/moodledata/temp/logs/error.log
```

### Test Plugin Step by Step

1. **Test basic access:**
   ```
   https://yourmoodle.com/local/studentdashboard/index.php
   ```

2. **Test without classes (add to index.php temporarily):**
   ```php
   echo "Dashboard plugin is working!";
   exit;
   ```

3. **Test with minimal output:**
   ```php
   echo $OUTPUT->header();
   echo "<h1>Dashboard Test</h1>";
   echo $OUTPUT->footer();
   ```

### Report Issues

When reporting issues, include:
- Moodle version
- PHP version  
- User role/permissions
- Exact error messages
- Browser console errors
- Steps to reproduce

## Recovery

### Complete Plugin Reset
If nothing works, reset the plugin:

1. **Remove plugin files:**
   ```bash
   rm -rf /path/to/moodle/local/studentdashboard/
   ```

2. **Clean database:**
   ```sql
   DELETE FROM mdl_capabilities WHERE name LIKE 'local/studentdashboard:%';
   DELETE FROM mdl_role_capabilities WHERE capability LIKE 'local/studentdashboard:%';
   DELETE FROM mdl_config_plugins WHERE plugin = 'local_studentdashboard';
   ```

3. **Reinstall fresh copy**

### Backup Before Changes
Always backup before making changes:
```bash
# Backup plugin directory
cp -r /path/to/moodle/local/studentdashboard /path/to/backup/

# Backup database
mysqldump -u username -p moodle > moodle_backup.sql
```