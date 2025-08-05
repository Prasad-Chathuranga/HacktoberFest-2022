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
    global $USER, $PAGE;
    
    // Only show for logged in users who are students
    if (!isloggedin() || isguestuser()) {
        return;
    }
    
    $context = context_system::instance();
    
    // Check if user has capability and is not an admin
    if (has_capability('local/studentdashboard:view', $context) && 
        !has_capability('moodle/site:config', $context) && 
        !is_siteadmin()) {
        
        // Find the home node
        $homenode = $navigation->find('home', global_navigation::TYPE_SETTING);
        if ($homenode) {
            $url = new moodle_url('/local/studentdashboard/index.php');
            $dashboardnode = navigation_node::create(
                get_string('studentdashboard', 'local_studentdashboard'),
                $url,
                global_navigation::TYPE_CUSTOM,
                null,
                'studentdashboard',
                new pix_icon('i/dashboard', '')
            );
            $homenode->add_node($dashboardnode);
        }
    }
}

/**
 * Hook to extend navigation settings
 */
function local_studentdashboard_extend_settings_navigation(settings_navigation $navigation, context $context) {
    global $USER;
    
    // Only for students
    if (!has_capability('local/studentdashboard:view', context_system::instance()) ||
        has_capability('moodle/site:config', context_system::instance()) ||
        is_siteadmin()) {
        return;
    }
    
    // Add to user preferences if on user profile page
    if ($context instanceof context_user && $context->instanceid == $USER->id) {
        $url = new moodle_url('/local/studentdashboard/index.php');
        $node = navigation_node::create(
            get_string('studentdashboard', 'local_studentdashboard'),
            $url,
            navigation_node::TYPE_SETTING,
            null,
            'studentdashboard'
        );
        
        $usernode = $navigation->find('userviewingsettings', null);
        if ($usernode) {
            $usernode->add_node($node);
        }
    }
}

/**
 * Get course overview files for a course
 * @param stdClass $course Course object
 * @return array Array of file objects
 */
function local_studentdashboard_get_course_overview_files($course) {
    global $CFG;
    require_once($CFG->libdir . '/filestorage/file_storage.php');
    
    $fs = get_file_storage();
    $context = context_course::instance($course->id);
    $files = $fs->get_area_files($context->id, 'course', 'overviewfiles', 0, 'filename', false);
    
    return $files;
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
    
    $context = context_system::instance();
    
    return has_capability('local/studentdashboard:view', $context, $user) &&
           !has_capability('moodle/site:config', $context, $user) &&
           !is_siteadmin($user);
}