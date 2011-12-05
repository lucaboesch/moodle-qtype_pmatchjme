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
 * Defines the editing form for the pmatch question type.
 *
 * @package    qtype
 * @subpackage pmatchjme
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/pmatch/edit_pmatch_form.php');

/**
 * pmatchjme question editing form definition.
 *
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_pmatchjme_edit_form extends qtype_pmatch_edit_form {
    public function qtype() {
        return 'pmatchjme';
    }

    protected function general_answer_fields($mform) {
    }

    protected function data_preprocessing_hints($question, $withclearwrong = false,
                                                $withshownumpartscorrect = false) {
        $withclearwrong = true;
        return parent::data_preprocessing_hints($question, $withclearwrong, $withshownumpartscorrect);
    }

    protected function get_hint_fields($withclearwrong = false, $withshownumpartscorrect = false) {
        list($repeated, $repeatedoptions) = parent::get_hint_fields(false, false);

        $mform = $this->_form;
        $repeated[] = $mform->createElement('advcheckbox', 'hintclearwrong',
                                            get_string('options', 'question'),
                                            get_string('allowanothertry', 'qtype_pmatchjme'));
        return array($repeated, $repeatedoptions);
    }
}