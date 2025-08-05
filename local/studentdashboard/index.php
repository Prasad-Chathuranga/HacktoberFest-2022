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
 * Student Dashboard main page
 *
 * @package    local_studentdashboard
 * @copyright  2024 Learning Dashboard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/filelib.php');

// Require login first
require_login();

// Only proceed if user is logged in and not guest
if (!isloggedin() || isguestuser()) {
    redirect(new moodle_url('/login/index.php'));
}

$context = context_system::instance();

// Simple access check - redirect admins to standard dashboard
if (is_siteadmin() || has_capability('moodle/site:config', $context)) {
    redirect(new moodle_url('/my/'));
}

// For now, allow all logged-in non-admin users
// You can add more specific capability checks after the plugin is installed

// Set up page
$PAGE->set_url('/local/studentdashboard/index.php');
$PAGE->set_context($context);
$PAGE->set_title(get_string('dashboard_title', 'local_studentdashboard'));
$PAGE->set_heading(get_string('dashboard_title', 'local_studentdashboard'));
$PAGE->set_pagelayout('mydashboard');

// Add CSS with proper Moodle URL
$PAGE->requires->css(new moodle_url('/local/studentdashboard/styles.css'));

// Add JavaScript for progress animation
$PAGE->requires->js_call_amd('local_studentdashboard/dashboard', 'init');

// Use autoloading for classes
use local_studentdashboard\output\dashboard_page;

try {
    // Create renderer
    $output = $PAGE->get_renderer('local_studentdashboard');
    
    // Create dashboard page
    $dashboardpage = new dashboard_page($USER);
    
    // Output
    echo $OUTPUT->header();
    echo $output->render_dashboard_page($dashboardpage);
    echo $OUTPUT->footer();
    
} catch (Exception $e) {
    // Fallback if there are any errors
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('dashboard_title', 'local_studentdashboard'));
    echo html_writer::div('Dashboard is being set up. Please check back later.', 'alert alert-info');
    
    // For debugging, show error to admins
    if (is_siteadmin()) {
        echo html_writer::div('Debug info: ' . $e->getMessage(), 'alert alert-warning');
    }
    
    echo $OUTPUT->footer();
}