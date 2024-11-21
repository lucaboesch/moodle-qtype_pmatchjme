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
 * Short answer question definition class.
 *
 * @package    qtype_pmatchjme
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/pmatch/question.php');

/**
 * Represents a pmatchjme question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_pmatchjme_question extends qtype_pmatch_question {

    /**
     * @var int The use case of the question.
     */
    public $usecase;

    /**
     * @var string The dictionary to extend.
     */
    public $extenddictionary;

    /**
     * @var string The sentence dividers.
     */
    public $sentencedividers;

    /**
     * @var string The characters that should be convert to space.
     */
    public $converttospace;

    /**
     * {@inheritdoc}
     */
    public function get_expected_data() {
        return ['answer' => PARAM_RAW, 'jme' => PARAM_RAW, 'mol' => PARAM_RAW];
    }

    /**
     * {@inheritdoc}
     */
    public function get_correct_response() {
        $response = parent::get_correct_response();
        if (!$response) {
            return $response;
        }

        $matches = [];
        $pmatchanswer = preg_replace('!\s!', '', $response['answer']);
        if (preg_match('!match\((.+)\)$!iA', $pmatchanswer, $matches)) {
            return ['answer' => $matches[1]];
        } else {
            return null;
        }
    }

    /**
     * Check the atom count of the student response.
     *
     * @param string $response The student response.
     * @return array The messages to display.
     * @throws coding_exception
     */
    public function check_atom_count($response) {
        $correctresponse = $this->get_correct_response();
        $pmatchanswer = $correctresponse['answer'];
        $answerparts = $this->count_compound_parts($pmatchanswer);

        $smilesresponse = preg_replace('!\s!', '', $response['answer']);
        $responseparts = $this->count_compound_parts($smilesresponse);

        $messages = [];
        $anyerror = (count($answerparts) != count($responseparts));
        $i = 0;
        foreach ($responseparts as $part => $responsecount) {
            $answercount = isset($answerparts[$part]) ? $answerparts[$part] : 0;
            list($messages[$i], $errorhere) = $this->part_comparison($answercount,
                                                                       $responsecount,
                                                                       $part);
            $i++;
            $anyerror = $anyerror || $errorhere;
        }

        if (!$anyerror) {
            // Count of smiles elements for student response and correct answer is the same
            // clear all other messages and replace it with this one.
            $messages = [get_string('smilescorrectcount', 'qtype_pmatchjme')];
        }
        return $messages;
    }

    /**
     * Compare the number of parts of a compound.
     *
     * @param int $answercount
     * @param int $responsecount
     * @param string $part symbol for part of a molecule.
     * @return array
     * @throws coding_exception
     */
    protected function part_comparison($answercount, $responsecount, $part) {
        $humanreadablepart = $this->get_part_name($part);
        if ($answercount < $responsecount) {
            $message = get_string('smilestoomany', 'qtype_pmatchjme', $humanreadablepart);
            $error = true;
        } else if ($answercount > $responsecount) {
            $message = get_string('smilestoofew', 'qtype_pmatchjme', $humanreadablepart);
            $error = true;
        } else {
            $message = get_string('smilesequal', 'qtype_pmatchjme', $humanreadablepart);
            $error = false;
        }
        return [$message, $error];
    }

    /**
     * Get the human-readable name for a part of a molecule.
     *
     * @param string $part symbol for part of a molecule.
     * @return string the human-readable name for that part.
     */
    protected function get_part_name($part) {
        switch ($part) {
            case '=':
                return get_string('smiles_doublebond', 'qtype_pmatchjme');
            case '#':
                return get_string('smiles_triplebond', 'qtype_pmatchjme');
            case 'c':
                return get_string('smiles_aromatic_c', 'qtype_pmatchjme');
            default:
                return get_string('smiles_' . strtolower($part), 'qtype_pmatchjme');
        }
    }

    /**
     * Count parts of compound.
     *
     * @param string $compound The compound to count.
     * @return array The count of each part.
     */
    protected function count_compound_parts($compound): array {
        $partstocount = ['Cl', 'Br', 'c', 'C', 'O', 'N', 'S', 'F', 'I', '=', '#'];
        $cursor = 0;
        $count = [];
        while ($cursor < strlen($compound)) {
            $counted = false;
            foreach ($partstocount as $parttocount) {
                if ($parttocount == substr($compound, $cursor, strlen($parttocount))) {
                    $cursor = $cursor + strlen($parttocount);
                    if (!isset($count[$parttocount])) {
                        $count[$parttocount] = 1;
                    } else {
                        $count[$parttocount]++;
                    }
                    $counted = true;
                    break;
                }
            }
            if (!$counted) {
                $cursor++;
            }
        }
        return $count;
    }

    /**
     * {@inheritdoc}
     *
     * @param question_attempt_step $step The step to start.
     * @param int $variant which variant of this question to start.
     * @return void
     */
    public function start_attempt(question_attempt_step $step, $variant) {
    }

    /**
     * {@inheritdoc}
     *
     * @param question_attempt_step $step The step to start.
     * @return void
     */
    public function apply_attempt_state(question_attempt_step $step) {
    }
}
