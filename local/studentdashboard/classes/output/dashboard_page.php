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
 * Dashboard page output class
 *
 * @package    local_studentdashboard
 * @copyright  2024 Learning Dashboard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_studentdashboard\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use local_studentdashboard\dashboard;

class dashboard_page implements renderable, templatable {

    /** @var dashboard $dashboard */
    private $dashboard;

    /** @var object $user */
    private $user;

    /**
     * Constructor
     * @param object $user User object
     */
    public function __construct($user) {
        $this->user = $user;
        $this->dashboard = new dashboard($user);
    }

    /**
     * Export data for template
     * @param renderer_base $output
     * @return array Template data
     */
    public function export_for_template(renderer_base $output) {
        global $OUTPUT;
        
        $stats = $this->dashboard->get_enrollment_stats();
        $courses = $this->dashboard->get_user_courses();
        $badges = $this->dashboard->get_user_badges();
        $recent_items = $this->dashboard->get_recent_items();
        $last_course = $this->dashboard->get_last_accessed_course();
        $upcoming_assignments = $this->dashboard->get_upcoming_assignments();
        $recent_grades = $this->dashboard->get_recent_grades();
        $quick_navigation = $this->dashboard->get_quick_navigation();

        // Prepare courses data
        $coursesdata = [];
        foreach (array_slice($courses, 0, 3) as $course) {
            $coursesdata[] = [
                'id' => $course->id,
                'fullname' => format_string($course->fullname),
                'shortname' => format_string($course->shortname),
                'summary' => format_text($course->summary, FORMAT_HTML),
                'url' => $course->url->out(),
                'progress' => $course->progress,
                'completed' => $course->timecompleted ? true : false,
                'image' => $this->get_course_image($course->id)
            ];
        }

        // Prepare badges data
        $badgesdata = [];
        foreach (array_slice($badges, 0, 4) as $badge) {
            $badgesdata[] = [
                'id' => $badge->id,
                'name' => format_string($badge->name),
                'description' => format_text($badge->description, FORMAT_HTML),
                'image' => $this->get_badge_image($badge->id)
            ];
        }

        // Prepare recent items data
        $recentdata = [];
        foreach (array_slice($recent_items, 0, 3) as $item) {
            $recentdata[] = [
                'id' => $item['id'],
                'name' => format_string($item['name']),
                'type' => $item['type'],
                'url' => $item['url']->out(),
                'icon' => $item['icon'],
                'timeaccess' => $item['timeaccess'] ? userdate($item['timeaccess'], get_string('strftimerecent')) : '',
                'coursename' => isset($item['coursename']) ? format_string($item['coursename']) : ''
            ];
        }

        // Prepare assignments data
        $assignmentsdata = [];
        foreach ($upcoming_assignments as $assignment) {
            $assignmentsdata[] = [
                'id' => $assignment->id,
                'name' => format_string($assignment->name),
                'coursename' => format_string($assignment->coursename),
                'duedate' => userdate($assignment->duedate, get_string('strftimedaydatetime')),
                'days_until' => $assignment->days_until,
                'urgency' => $assignment->urgency,
                'url' => $assignment->url->out(),
                'modname' => $assignment->modname
            ];
        }

        // Prepare grades data
        $gradesdata = [];
        foreach ($recent_grades as $grade) {
            $gradesdata[] = [
                'id' => $grade->id,
                'itemname' => format_string($grade->itemname),
                'coursename' => format_string($grade->coursename),
                'finalgrade' => number_format($grade->finalgrade, 1),
                'grademax' => number_format($grade->grademax, 1),
                'percentage' => $grade->percentage,
                'grade_class' => $grade->grade_class,
                'timemodified' => userdate($grade->timemodified, get_string('strftimerecent'))
            ];
        }

        // Prepare navigation data
        $navigationdata = [];
        foreach ($quick_navigation as $nav) {
            $navigationdata[] = [
                'name' => $nav['name'],
                'url' => $nav['url']->out(),
                'icon' => $nav['icon'],
                'description' => $nav['description']
            ];
        }

        return [
            'user' => [
                'id' => $this->user->id,
                'fullname' => fullname($this->user),
                'email' => $this->user->email,
                'profileurl' => (new \moodle_url('/user/profile.php', ['id' => $this->user->id]))->out(),
                'avatar' => $OUTPUT->user_picture($this->user, ['size' => 80, 'class' => 'rounded'])
            ],
            'stats' => $stats,
            'courses' => $coursesdata,
            'badges' => $badgesdata,
            'recent_items' => $recentdata,
            'last_course' => $last_course ? [
                'id' => $last_course->id,
                'fullname' => format_string($last_course->fullname),
                'progress' => $last_course->progress,
                'url' => $last_course->url->out()
            ] : null,
            'dashboard_url' => (new \moodle_url('/local/studentdashboard/index.php'))->out(),
            'has_courses' => !empty($courses),
            'has_badges' => !empty($badges),
            'has_recent_items' => !empty($recent_items),
            'upcoming_assignments' => $assignmentsdata,
            'has_assignments' => !empty($upcoming_assignments),
            'recent_grades' => $gradesdata,
            'has_grades' => !empty($recent_grades),
            'quick_navigation' => $navigationdata
        ];
    }

    /**
     * Get course image URL
     * @param int $courseid Course ID
     * @return string Image URL
     */
    private function get_course_image($courseid) {
        global $CFG;
        
        $course = get_course($courseid);
        $coursefiles = get_course_overviewfiles($course);
        
        if (!empty($coursefiles)) {
            $file = reset($coursefiles);
            $url = \moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                null,
                $file->get_filepath(),
                $file->get_filename()
            );
            return $url->out();
        }
        
        return $CFG->wwwroot . '/local/studentdashboard/pix/course_default.png';
    }

    /**
     * Get badge image URL
     * @param int $badgeid Badge ID
     * @return string Image URL
     */
    private function get_badge_image($badgeid) {
        global $CFG;
        
        $context = \context_system::instance();
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'badges', 'badgeimage', $badgeid, 'filename', false);
        
        if (!empty($files)) {
            $file = reset($files);
            $url = \moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename()
            );
            return $url->out();
        }
        
        return $CFG->wwwroot . '/local/studentdashboard/pix/badge_default.png';
    }
}