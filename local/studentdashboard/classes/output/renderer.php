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
 * Renderer for Student Dashboard local plugin
 *
 * @package    local_studentdashboard
 * @copyright  2024 Learning Dashboard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_studentdashboard\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;

class renderer extends plugin_renderer_base {

    /**
     * Render the main dashboard
     * @param dashboard_page $page Dashboard page data
     * @return string HTML output
     */
    public function render_dashboard_page(dashboard_page $page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_studentdashboard/dashboard', $data);
    }

    /**
     * Render user profile section
     * @param user_profile $profile User profile data
     * @return string HTML output
     */
    public function render_user_profile(user_profile $profile) {
        $data = $profile->export_for_template($this);
        return $this->render_from_template('local_studentdashboard/user_profile', $data);
    }

    /**
     * Render course progress circle
     * @param course_progress $progress Course progress data
     * @return string HTML output
     */
    public function render_course_progress(course_progress $progress) {
        $data = $progress->export_for_template($this);
        return $this->render_from_template('local_studentdashboard/course_progress', $data);
    }

    /**
     * Render recent items section
     * @param recent_items $items Recent items data
     * @return string HTML output
     */
    public function render_recent_items(recent_items $items) {
        $data = $items->export_for_template($this);
        return $this->render_from_template('local_studentdashboard/recent_items', $data);
    }
}