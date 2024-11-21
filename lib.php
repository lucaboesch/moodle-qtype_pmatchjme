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
 * Serve question type files
 *
 * @since      2.0
 * @package    qtype_pmatchjme
 * @copyright  2012 The Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Checks file access for pmatchjme questions.
 *
 * @param stdClass $course The course object.
 * @param cm_info $cm The course module info.
 * @param context $context The context object.
 * @param string $filearea The file area.
 * @param array $args Additional arguments.
 * @param bool $forcedownload Whether or not force download.
 * @param array $options Additional options affecting the file serving.
 * @return void
 */
function qtype_pmatchjme_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=[]) {
    global $DB, $CFG;
    require_once($CFG->libdir . '/questionlib.php');
    question_pluginfile($course, $context, 'qtype_pmatchjme', $filearea, $args, $forcedownload, $options);
}
