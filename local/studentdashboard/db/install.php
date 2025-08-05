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
 * Install script for Student Dashboard local plugin
 *
 * @package    local_studentdashboard
 * @copyright  2024 Learning Dashboard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Custom post installation hook
 */
function xmldb_local_studentdashboard_install() {
    global $DB;
    
    // Ensure the capability is assigned to authenticated users by default
    $context = context_system::instance();
    
    // Get the authenticated user archetype role
    $roles = get_archetype_roles('user');
    
    foreach ($roles as $role) {
        // Assign the view capability to user roles
        role_change_permission($role->id, $context, 'local/studentdashboard:view', CAP_ALLOW);
    }
    
    // Also assign to student roles specifically
    $studentroles = get_archetype_roles('student');
    
    foreach ($studentroles as $role) {
        role_change_permission($role->id, $context, 'local/studentdashboard:view', CAP_ALLOW);
    }
    
    return true;
}