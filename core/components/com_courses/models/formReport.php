<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Hubzero\Base\Model;
use User;

require_once dirname(__DIR__) . DS . 'tables' . DS . 'asset.php';

/**
 * Courses model class for form reporting/data
 */
class FormReport extends Model
{
	/**
	 * Generate letter responses array for a given asset id
	 *
	 * A lot of the complexity of this method stems from the fact that the form data is
	 * not actually labeled, but rather merely coordinates on a page. We must therefore
	 * make assumptions on question numbers and response labels based on location within
	 * the page. This is a significant shortfall, especially given that questions
	 * or responses may at some point read horizontally, rather than vertically.
	 *
	 * @param  object - database object
	 * @param  int    - asset id
	 * @param  bool   - whether or not to include header values
	 * @param  int    - limit to only a certain section
	 * @return array
	 **/
	public static function getLetterResponsesForAssetId($db, $asset_id, $include_headers=true, $section_id=NULL)
	{
		// Is it a number?
		if (!is_numeric($asset_id))
		{
			return false;
		}

		// Get the form id
		$query = "SELECT `id` FROM `#__courses_forms` WHERE `asset_id` = '{$asset_id}'";
		$db->setQuery($query);
		$form_id = $db->loadResult();

		if (!$form_id)
		{
			return false;
		}

		// Get all the questions for this form, properly ordered
		$query = "SELECT `id`, `version` FROM `#__courses_form_questions` WHERE form_id = '{$form_id}' ORDER BY version ASC, page ASC, top_dist ASC";
		$db->setQuery($query);
		$results = $db->loadObjectList();

		// Build array of questions/versions map
		$question_ids = array();
		$question_vs  = array();
		$questions    = array();
		$answers      = array();
		if ($results && count($results) > 0)
		{
			foreach ($results as $r)
			{
				$question_ids[] = $r->id;

				// Compute question label (i.e. 1, 2, 3, etc...) based on idx within a given form/questions version
				if (isset($question_vs[$r->version]))
				{
					++$question_vs[$r->version];
				}
				else
				{
					$question_vs[$r->version] = 1;
				}

				// Store a mapping of question id to question label computed above
				$questions[$r->id] = array('qidx' => $question_vs[$r->version], 'version' => $r->version);

				// Now grab all of the answers for each question
				$query = "SELECT `id` FROM `#__courses_form_answers` WHERE `question_id` = '{$r->id}' ORDER BY top_dist ASC;";
				$db->setQuery($query);
				$ans = $db->loadObjectList();

				if ($ans && count($ans) > 0)
				{
					$letter = NULL;

					foreach ($ans as $a)
					{
						$answers[$a->id] = self::getNextLetter($letter);
						$letter          = $answers[$a->id];
					}
				}
			}
		}
		else
		{
			return false;
		}

		// Now, select responses and start to build the csv...
		$query = "SELECT * FROM `#__courses_form_responses` WHERE `question_id` IN (".implode(',', $question_ids).") ORDER BY `respondent_id` ASC";
		$db->setQuery($query);
		$results = $db->loadObjectList();
		$output  = array();

		if ($results && count($results) > 0)
		{
			if ($include_headers)
			{
				$fields = array();

				for ($i=1; $i <= max($question_vs); $i++)
				{
					$fields[] = $i;
				}

				sort($fields);
				array_unshift($fields, 'Version');
				array_unshift($fields, 'Attempt');
				array_unshift($fields, 'Name');

				$output['headers'] = $fields;
			}

			$respondents       = array();

			foreach ($results as $response)
			{
				$respondents[$response->respondent_id][$questions[$response->question_id]['qidx']] = array(
					'question_id' => $response->question_id,
					'answer'      => (isset($answers[$response->answer_id])) ? $answers[$response->answer_id] : '--'
				);
			}

			foreach ($respondents as $respondent_id => $response)
			{
				// Get name and attempt number for this row
				$query = "SELECT `user_id`, `attempt`, `student`, `section_id` FROM `#__courses_members` cm JOIN `#__courses_form_respondents` cr ON cr.member_id = cm.id WHERE cr.`id` = '{$respondent_id}'";
				$db->setQuery($query);
				$aux   = $db->loadObject();

				if (!$aux->student)
				{
					continue;
				}

				if (isset($section_id) && $aux->section_id != $section_id)
				{
					continue;
				}

				$name    = User::getInstance($aux->user_id)->get('name');
				$attempt = $aux->attempt;
				$fields  = array();

				foreach ($response as $k => $v)
				{
					$version    = $questions[$v['question_id']]['version'];
					$fields[$k] = $v['answer'];
				}

				ksort($fields);
				array_unshift($fields, $version);
				array_unshift($fields, $attempt);
				array_unshift($fields, $name);
				$output['responses'][] = $fields;
			}
		}
		else
		{
			return false;
		}

		return $output;
	}

	/**
	 * Get assessment details data
	 *
	 * @param  obj   - database connection object
	 * @param  int   - asset id for which to retrieve letter counts
	 * @param  int   - section id
	 * @return array - counts of letter responses
	 **/
	public static function getLetterResponseCountsForAssetId($db, $asset_id, $section_id=NULL)
	{
		if (!is_numeric($asset_id))
		{
			return false;
		}

		// Get the form id
		$query = "SELECT `id` FROM `#__courses_forms` WHERE `asset_id` = '{$asset_id}'";
		$db->setQuery($query);
		$form_id = $db->loadResult();

		if (!$form_id)
		{
			return false;
		}

		// Get all the questions for this form, properly ordered
		$query = "SELECT `id`, `version` FROM `#__courses_form_questions` WHERE form_id = '{$form_id}' ORDER BY version ASC, page ASC, top_dist ASC";
		$db->setQuery($query);
		$results = $db->loadObjectList();

		// Build array of questions/versions map
		$question_id  = 1;
		$version_id   = 1;
		$questions    = array();
		$question_ids = array();
		$high_letter  = 97;
		if ($results && count($results) > 0)
		{
			foreach ($results as $r)
			{
				if ($version_id == $r->version)
				{
					$questions[$r->id] = $question_id;
					$question_id++;
				}
				else
				{
					$version_id = $r->version;
					$question_id = 1;
					$questions[$r->id] = $question_id;
					$question_id++;
				}

				$question_ids[] = $r->id;

				// Now grab all of the answers for each question
				$query = "SELECT `id`, `correct` FROM `#__courses_form_answers` WHERE `question_id` = '{$r->id}' ORDER BY top_dist ASC;";
				$db->setQuery($query);
				$ans = $db->loadObjectList();

				if ($ans && count($ans) > 0)
				{
					$letter = NULL;

					foreach ($ans as $a)
					{
						$answers[$a->id] = array('label' => self::getNextLetter($letter), 'correct' => $a->correct);
						$letter          = $answers[$a->id]['label'];

						if (ord($letter) > $high_letter)
						{
							$high_letter = ord($letter);
						}
					}
				}
			}
		}
		else
		{
			return false;
		}

		// Now, select responses and start to build the csv...
		$query = "SELECT * FROM `#__courses_form_responses` WHERE `question_id` IN (".implode(',', $question_ids).") ORDER BY `respondent_id` ASC";
		$db->setQuery($query);
		$results = $db->loadObjectList();

		if ($results && count($results) > 0)
		{
			$counts = array();

			foreach ($results as $response)
			{
				// We only want data for students, so check that first
				$query  = "SELECT `student`, `section_id` FROM `#__courses_members` AS cm, ";
				$query .= "`#__courses_form_respondents` AS cfr WHERE cm.id = cfr.member_id ";
				$query .= "AND cfr.id = " . $db->quote($response->respondent_id);;
				$db->setQuery($query);
				if (!$student = $db->loadObject())
				{
					continue;
				}
				if (!$student->student)
				{
					continue;
				}
				if (isset($section_id) && $student->section_id != $section_id)
				{
					continue;
				}

				$question_id = $questions[$response->question_id];

				$letter = (isset($answers[$response->answer_id])) ? $answers[$response->answer_id]['label'] : 'z';

				if (isset($counts[$question_id][$letter]))
				{
					$counts[$question_id][$letter]['count']++;
				}
				else
				{
					$counts[$question_id][$letter]['count'] = 1;
				}

				if (isset($answers[$response->answer_id]) && $answers[$response->answer_id]['correct'])
				{
					$counts[$question_id][$letter]['correct'] = true;
				}
				else
				{
					$counts[$question_id][$letter]['correct'] = false;
				}
			}
		}
		else
		{
			return false;
		}

		return $counts;
	}

	/**
	 * Little helper function to get the next letter in the alphabet
	 *
	 * @param  $letter - letter after which the next letter should be returned
	 * @return $chr - string: 1 character
	 **/
	private static function getNextLetter($letter)
	{
		if (is_null($letter))
		{
			return 'a';
		}

		$ord = ord($letter);
		$chr = chr($ord + 1);

		return $chr;
	}
}