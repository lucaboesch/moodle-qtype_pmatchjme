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
 * Question type class for the pmatch with JME editor question type.
 *
 * @package    qtype_pmatchjme
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use qtype_pmatch\local\spell\qtype_pmatch_spell_checker;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/pmatch/questiontype.php');

/**
 * Class to represent a pmatchjme question answer, loaded from the question_answers table
 * in the database.
 */
class qtype_pmatchjme_answer extends question_answer {

    /**
     * @var int The number of atoms in the answer.
     */
    public $atomcount;

    /**
     * Constructor.
     * @param int $id the answer.
     * @param string $answer the answer.
     * @param number $fraction the fraction this answer is worth.
     * @param string $feedback the feedback for this answer.
     * @param int $feedbackformat the format of the feedback.
     * @param int $atomcount the number of atoms in the answer.
     */
    public function __construct($id, $answer, $fraction, $feedback, $feedbackformat, $atomcount) {
        parent::__construct($id, $answer, $fraction, $feedback, $feedbackformat);
        $this->atomcount = $atomcount;
    }
}


/**
 * The pmatchjme question type.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_pmatchjme extends qtype_pmatch {

    /**
     * {@inheritdoc}
     *
     * @param stdClass $fromform data from the form.
     */
    public function save_defaults_for_new_questions(stdClass $fromform): void {
        $grandparent = new question_type();
        $grandparent->save_defaults_for_new_questions($fromform);
        $this->set_default_value('allowsuperscript', $fromform->allowsuperscript);
        $this->set_default_value('allowsubscript', $fromform->allowsubscript);
    }

    /**
     * {@inheritdoc}
     *
     * @param object $question This holds the information from the editing form,
     *       it is not a standard question object.
     * @return bool|stdClass $result->error or $result->notice
     */
    public function save_question_options($question) {
        global $DB;
        $question->usecase = 1;
        $question->forcelength = 0;
        $question->applydictionarycheck = qtype_pmatch_spell_checker::DO_NOT_CHECK_OPTION;
        $question->extenddictionary = '';
        $question->converttospace = '';
        if (!empty($question->id)) {
            $this->delete_extra_answer_records($question->id);
        }
        return parent::save_question_options($question);
    }

    /**
     * {@inheritdoc}
     *
     * @param object $formdata The form data.
     * @param bool $withparts Whether to save the parts.
     */
    public function save_hints($formdata, $withparts = false) {
        parent::save_hints($formdata, true);
    }

    /**
     * {@inheritdoc}
     */
    public function extra_answer_fields() {
        return ['qtype_pmatchjme_answers', 'atomcount'];
    }

    /**
     * Save the extra answer data for a question.
     *
     * @param object $question the data being passed to the form.
     * @param string $key The key of the answer.
     * @param int $answerid The id of the answer.
     * @return void
     * @throws dml_exception
     */
    public function save_extra_answer_data($question, $key, $answerid) {
        global $DB;
        $extraanswerdata = new stdClass();
        $extraanswerdata->answerid = $answerid;
        if ($key === 'other') {
            $extraanswerdata->atomcount = $question->atomcount_other;
        } else {
            $extraanswerdata->atomcount = $question->atomcount[$key];
        }
        $DB->insert_record('qtype_pmatchjme_answers', $extraanswerdata);
    }
    /**
     * Initialise question_definition::answers field.
     *
     * @param question_definition $question the question_definition we are creating.
     * @param object $questiondata the question data loaded from the database.
     * @param bool $forceplaintextanswers most qtypes assume that answers are
     *      FORMAT_PLAIN, and dont use the answerformat DB column (it contains
     *      the default 0 = FORMAT_MOODLE). Therefore, by default this method
     *      ingores answerformat. Pass false here to use answerformat. For example
     *      multichoice does this.
     */
    protected function initialise_question_answers(question_definition $question,
            $questiondata, $forceplaintextanswers = true) {
        $question->answers = [];
        if (empty($questiondata->options->answers)) {
            return;
        }
        foreach ($questiondata->options->answers as $a) {
            $question->answers[$a->id] = new qtype_pmatchjme_answer($a->id, $a->answer,
                    $a->fraction, $a->feedback, $a->feedbackformat, $a->atomcount);
            if (!$forceplaintextanswers) {
                $question->answers[$a->id]->answerformat = $a->answerformat;
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param int $questionid the id of the question being deleted.
     * @param int $contextid the context this quesiotn belongs to.
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     */
    public function delete_question($questionid, $contextid): void {
        $this->delete_extra_answer_records($questionid);
        parent::delete_question($questionid, $contextid);
    }

    /**
     * Delete all the extra answer records for a question.
     *
     * @param int $questionid The id of the question.
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function delete_extra_answer_records($questionid) {
        global $DB;
        $answerids = $DB->get_records_menu('question_answers',
                                           ['question' => $questionid],
                                           '', 'id, 1');
        if (count($answerids) != 0) {
            list ($sql, $params) = $DB->get_in_or_equal(array_keys($answerids));
            $DB->delete_records_select('qtype_pmatchjme_answers', "answerid $sql", $params);
        }
    }
}
