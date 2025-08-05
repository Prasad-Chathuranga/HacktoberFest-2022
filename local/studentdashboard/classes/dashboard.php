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
 * Dashboard class for Student Dashboard local plugin
 *
 * @package    local_studentdashboard
 * @copyright  2024 Learning Dashboard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_studentdashboard;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/badges/lib.php');

class dashboard {

    /** @var object $user The user object */
    private $user;

    /**
     * Constructor
     * @param object $user User object
     */
    public function __construct($user = null) {
        global $USER;
        $this->user = $user ?: $USER;
    }

    /**
     * Get user enrollment statistics
     * @return array Array of enrollment stats
     */
    public function get_enrollment_stats() {
        global $DB;

        $sql = "SELECT 
                    COUNT(DISTINCT ue.courseid) as enrolled,
                    COUNT(DISTINCT CASE WHEN cc.timecompleted IS NOT NULL THEN ue.courseid END) as completed
                FROM {user_enrolments} ue 
                JOIN {enrol} e ON ue.enrolid = e.id 
                JOIN {course} c ON e.courseid = c.id
                LEFT JOIN {course_completions} cc ON cc.userid = ue.userid AND cc.course = e.courseid
                WHERE ue.userid = ? AND c.visible = 1 AND c.id != 1";

        $stats = $DB->get_record_sql($sql, [$this->user->id]);
        
        return [
            'enrolled' => (int)$stats->enrolled,
            'completed' => (int)$stats->completed,
            'incomplete' => (int)$stats->enrolled - (int)$stats->completed
        ];
    }

    /**
     * Get user's courses
     * @return array Array of course objects
     */
    public function get_user_courses() {
        global $DB;

        $sql = "SELECT c.*, 
                       ue.timemodified as enroldate,
                       cc.timecompleted,
                       (SELECT MAX(ul.timeaccess) 
                        FROM {user_lastaccess} ul 
                        WHERE ul.userid = ? AND ul.courseid = c.id) as lastaccess
                FROM {course} c
                JOIN {enrol} e ON e.courseid = c.id
                JOIN {user_enrolments} ue ON ue.enrolid = e.id
                LEFT JOIN {course_completions} cc ON cc.userid = ue.userid AND cc.course = c.id
                WHERE ue.userid = ? AND c.visible = 1 AND c.id != 1
                ORDER BY lastaccess DESC, ue.timemodified DESC";

        $courses = $DB->get_records_sql($sql, [$this->user->id, $this->user->id]);
        
        foreach ($courses as $course) {
            $course->progress = $this->get_course_progress($course->id);
            $course->url = new \moodle_url('/course/view.php', ['id' => $course->id]);
        }

        return array_values($courses);
    }

    /**
     * Get course progress percentage
     * @param int $courseid Course ID
     * @return int Progress percentage
     */
    public function get_course_progress($courseid) {
        global $DB;

        // Get course completion
        $completion = $DB->get_record('course_completions', [
            'userid' => $this->user->id,
            'course' => $courseid
        ]);

        if ($completion && $completion->timecompleted) {
            return 100;
        }

        // Calculate based on completed activities
        $sql = "SELECT 
                    COUNT(*) as total_activities,
                    COUNT(cmc.id) as completed_activities
                FROM {course_modules} cm
                LEFT JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id AND cmc.userid = ? AND cmc.completionstate > 0
                WHERE cm.course = ? AND cm.visible = 1 AND cm.completion > 0";

        $progress = $DB->get_record_sql($sql, [$this->user->id, $courseid]);
        
        if ($progress->total_activities > 0) {
            return round(($progress->completed_activities / $progress->total_activities) * 100);
        }

        return 0;
    }

    /**
     * Get user badges
     * @return array Array of badge objects
     */
    public function get_user_badges() {
        global $DB;

        $sql = "SELECT b.*, bi.dateissued
                FROM {badge} b
                JOIN {badge_issued} bi ON bi.badgeid = b.id
                WHERE bi.userid = ? AND b.status = 1
                ORDER BY bi.dateissued DESC
                LIMIT 10";

        return $DB->get_records_sql($sql, [$this->user->id]);
    }

    /**
     * Get recently accessed items
     * @return array Array of recent items
     */
    public function get_recent_items() {
        global $DB;

        $items = [];

        // Recent courses
        $sql = "SELECT c.id, c.fullname, c.shortname, ul.timeaccess, 'course' as itemtype
                FROM {course} c
                JOIN {user_lastaccess} ul ON ul.courseid = c.id
                JOIN {enrol} e ON e.courseid = c.id
                JOIN {user_enrolments} ue ON ue.enrolid = e.id
                WHERE ul.userid = ? AND ue.userid = ? AND c.visible = 1 AND c.id != 1
                ORDER BY ul.timeaccess DESC
                LIMIT 5";

        $recent_courses = $DB->get_records_sql($sql, [$this->user->id, $this->user->id]);

        foreach ($recent_courses as $course) {
            $items[] = [
                'id' => $course->id,
                'name' => $course->fullname,
                'type' => 'course',
                'url' => new \moodle_url('/course/view.php', ['id' => $course->id]),
                'timeaccess' => $course->timeaccess,
                'icon' => 'fa-book'
            ];
        }

        // Recent forum posts
        $sql = "SELECT f.id, f.name, fp.modified, c.fullname as coursename, 'forum' as itemtype
                FROM {forum_posts} fp
                JOIN {forum_discussions} fd ON fd.id = fp.discussion
                JOIN {forum} f ON f.id = fd.forum
                JOIN {course} c ON c.id = f.course
                JOIN {enrol} e ON e.courseid = c.id
                JOIN {user_enrolments} ue ON ue.enrolid = e.id
                WHERE fp.userid = ? AND ue.userid = ? AND c.visible = 1
                ORDER BY fp.modified DESC
                LIMIT 3";

        $recent_forums = $DB->get_records_sql($sql, [$this->user->id, $this->user->id]);

        foreach ($recent_forums as $forum) {
            $items[] = [
                'id' => $forum->id,
                'name' => $forum->name,
                'type' => 'forum',
                'coursename' => $forum->coursename,
                'url' => new \moodle_url('/mod/forum/view.php', ['id' => $forum->id]),
                'timeaccess' => $forum->modified,
                'icon' => 'fa-comments'
            ];
        }

        // Sort by time access
        usort($items, function($a, $b) {
            return $b['timeaccess'] - $a['timeaccess'];
        });

        return array_slice($items, 0, 6);
    }

    /**
     * Get last accessed course
     * @return object|null Last accessed course
     */
    public function get_last_accessed_course() {
        $courses = $this->get_user_courses();
        return !empty($courses) ? $courses[0] : null;
    }

    /**
     * Get upcoming assignments and deadlines
     * @return array Array of upcoming assignments
     */
    public function get_upcoming_assignments() {
        global $DB;

        $sql = "SELECT a.id, a.name, a.duedate, c.fullname as coursename, c.id as courseid,
                       'assign' as modname
                FROM {assign} a
                JOIN {course} c ON c.id = a.course
                JOIN {enrol} e ON e.courseid = c.id
                JOIN {user_enrolments} ue ON ue.enrolid = e.id
                LEFT JOIN {assign_submission} asub ON asub.assignment = a.id AND asub.userid = ?
                WHERE ue.userid = ? 
                AND a.duedate > ? 
                AND a.duedate < ?
                AND c.visible = 1 
                AND c.id != 1
                AND (asub.id IS NULL OR asub.status = 'draft')
                
                UNION ALL
                
                SELECT q.id, q.name, q.timeclose as duedate, c.fullname as coursename, c.id as courseid,
                       'quiz' as modname
                FROM {quiz} q
                JOIN {course} c ON c.id = q.course
                JOIN {enrol} e ON e.courseid = c.id
                JOIN {user_enrolments} ue ON ue.enrolid = e.id
                LEFT JOIN {quiz_attempts} qa ON qa.quiz = q.id AND qa.userid = ?
                WHERE ue.userid = ? 
                AND q.timeclose > ? 
                AND q.timeclose < ?
                AND c.visible = 1 
                AND c.id != 1
                AND qa.id IS NULL
                
                ORDER BY duedate ASC
                LIMIT 5";

        $now = time();
        $twoweeks = $now + (14 * 24 * 60 * 60); // Next 2 weeks

        $assignments = $DB->get_records_sql($sql, [
            $this->user->id, $this->user->id, $now, $twoweeks,
            $this->user->id, $this->user->id, $now, $twoweeks
        ]);

        foreach ($assignments as $assignment) {
            $assignment->url = new \moodle_url('/mod/' . $assignment->modname . '/view.php', ['id' => $assignment->id]);
            $assignment->days_until = ceil(($assignment->duedate - $now) / (24 * 60 * 60));
            $assignment->urgency = $assignment->days_until <= 3 ? 'urgent' : ($assignment->days_until <= 7 ? 'soon' : 'normal');
        }

        return array_values($assignments);
    }

    /**
     * Get recent grades
     * @return array Array of recent grades
     */
    public function get_recent_grades() {
        global $DB;

        $sql = "SELECT gi.id, gi.itemname, gi.itemmodule, gg.finalgrade, gg.timemodified,
                       c.fullname as coursename, c.id as courseid, gi.grademax
                FROM {grade_grades} gg
                JOIN {grade_items} gi ON gi.id = gg.itemid
                JOIN {course} c ON c.id = gi.courseid
                JOIN {enrol} e ON e.courseid = c.id
                JOIN {user_enrolments} ue ON ue.enrolid = e.id
                WHERE gg.userid = ? 
                AND ue.userid = ?
                AND gg.finalgrade IS NOT NULL
                AND c.visible = 1 
                AND c.id != 1
                AND gi.itemtype = 'mod'
                ORDER BY gg.timemodified DESC
                LIMIT 5";

        $grades = $DB->get_records_sql($sql, [$this->user->id, $this->user->id]);

        foreach ($grades as $grade) {
            $grade->percentage = $grade->grademax > 0 ? round(($grade->finalgrade / $grade->grademax) * 100) : 0;
            $grade->grade_class = $grade->percentage >= 80 ? 'excellent' : 
                                 ($grade->percentage >= 70 ? 'good' : 
                                 ($grade->percentage >= 60 ? 'average' : 'needs_improvement'));
        }

        return array_values($grades);
    }

    /**
     * Get quick navigation items
     * @return array Array of navigation shortcuts
     */
    public function get_quick_navigation() {
        global $CFG;

        $navigation = [
            [
                'name' => 'My Courses',
                'url' => new \moodle_url('/my/courses.php'),
                'icon' => 'fa-book',
                'description' => 'View all enrolled courses'
            ],
            [
                'name' => 'Messages',
                'url' => new \moodle_url('/message/index.php'),
                'icon' => 'fa-envelope',
                'description' => 'Check your messages'
            ],
            [
                'name' => 'Calendar',
                'url' => new \moodle_url('/calendar/view.php'),
                'icon' => 'fa-calendar',
                'description' => 'View your calendar'
            ],
            [
                'name' => 'Grades',
                'url' => new \moodle_url('/grade/report/overview/index.php'),
                'icon' => 'fa-chart-line',
                'description' => 'View your grades'
            ],
            [
                'name' => 'Files',
                'url' => new \moodle_url('/user/files.php'),
                'icon' => 'fa-folder',
                'description' => 'Manage your files'
            ],
            [
                'name' => 'Profile',
                'url' => new \moodle_url('/user/profile.php'),
                'icon' => 'fa-user',
                'description' => 'Edit your profile'
            ]
        ];

        return $navigation;
    }
}