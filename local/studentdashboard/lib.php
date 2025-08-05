<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library functions for Student Dashboard local plugin
 *
 * @package    local_studentdashboard
 * @copyright  2024 Learning Dashboard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Hook to insert link to student dashboard in navigation
 */
function local_studentdashboard_extend_navigation(global_navigation $navigation) {
    global $USER;
    
    // Only show for logged in non-admin users
    if (!isloggedin() || isguestuser() || is_siteadmin()) {
        return;
    }
    
    // Simple check - don't show for site admins
    $context = context_system::instance();
    if (has_capability('moodle/site:config', $context)) {
        return;
    }
    
    // Add the dashboard link to main navigation
    $url = new moodle_url('/local/studentdashboard/index.php');
    $dashboardnode = navigation_node::create(
        get_string('studentdashboard', 'local_studentdashboard'),
        $url,
        global_navigation::TYPE_CUSTOM,
        null,
        'studentdashboard',
        new pix_icon('i/dashboard', '')
    );
    
    // Try to add it to the main navigation
    $navigation->add_node($dashboardnode);
}

/**
 * Serve plugin files
 */
function local_studentdashboard_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $CFG;
    
    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }
    
    require_login();
    
    if ($filearea !== 'images') {
        return false;
    }
    
    $relativepath = implode('/', $args);
    $fullpath = "/{$context->id}/local_studentdashboard/$filearea/$relativepath";
    
    $fs = get_file_storage();
    $file = $fs->get_file_by_hash(sha1($fullpath));
    
    if (!$file || $file->is_directory()) {
        return false;
    }
    
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}

/**
 * Check if user is a student (has student capabilities but not admin)
 * @param object $user User object (optional, defaults to current user)
 * @return bool True if user is a student
 */
function local_studentdashboard_is_student($user = null) {
    global $USER;
    
    if (!$user) {
        $user = $USER;
    }
    
    if (is_siteadmin($user)) {
        return false;
    }
    
    $context = context_system::instance();
    return !has_capability('moodle/site:config', $context, $user);
}