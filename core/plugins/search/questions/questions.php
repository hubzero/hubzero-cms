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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Search questions
 */
class plgSearchQuestions extends \Hubzero\Plugin\Plugin
{
	/**
	 * Short description for 'sort_by_date'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $a Parameter description (if any) ...
	 * @param      object $b Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public static function sort_by_date($a, $b)
	{
		$aw = $a->get_date();
		$bw = $b->get_date();
		if ($aw == $bw)
		{
			return 0;
		}
		return $aw < $bw ? -1 : 1;
	}

	/**
	 * Short description for 'sort_by_weight'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $a Parameter description (if any) ...
	 * @param      mixed $b Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function sort_by_weight($a, $b)
	{
		return ($res = $a->get_weight() - $b->get_weight()) == 0 ? 0 : ($res > 0 ? -1 : 1);
	}

	/**
	 * Build search query and add it to the $results
	 *
	 * @param      object $request  \Components\Search\Models\Basic\Request
	 * @param      object &$results \Components\Search\Models\Basic\Result\Set
	 * @param      object $authz    \Components\Search\Models\Basic\Authorization
	 * @return     void
	 */
	public static function onSearch($request, &$results, $authz)
	{
		$terms = $request->get_term_ar();
		$qweight  = 'match(q.question, q.subject) against(\'' . join(' ', $terms['stemmed']) . '\')';
		$rweight  = 'match(r.answer) against(\'' . join(' ', $terms['stemmed']) . '\')';
		$r2weight = 'match(r2.answer) against(\'' . join(' ', $terms['stemmed']) . '\')';

		/*$qaddtl_where = array();
		$raddtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$qaddtl_where[] = "(q.subject LIKE '%$mand%' OR q.question LIKE '%$mand%')";
			$raddtl_where[] = "(r.answer LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$qaddtl_where[] = "(q.subject NOT LIKE '%$forb%' AND q.question NOT LIKE '%$forb%')";
			$raddtl_where[] = "(r.answer NOT LIKE '%$forb%')";
		}*/

		$dbh = App::get('db');
		$dbh->setQuery(
			"SELECT
				q.id AS qid, q.created AS q_created, q.subject, q.question, r.id AS rid, r.answer, r.created AS r_created, $qweight AS qweight, null AS rweight,
			CASE
				when q.anonymous > 0 then NULL
				else (SELECT name FROM `#__users` WHERE id = q.created_by)
			END AS qcontributors,
			CASE
				when q.anonymous > 0 then null
				else (SELECT id FROM `#__users` WHERE id = q.created_by)
			END AS qcontributor_ids,
			CASE
				when r.anonymous > 0 then NULL
				else (SELECT name FROM `#__users` WHERE id = r.created_by)
			END AS rcontributors,
			CASE
				when r.anonymous > 0 then null
				else (SELECT id FROM `#__users` WHERE id = r.created_by)
			END AS rcontributor_ids
			FROM `#__answers_questions` q
			LEFT JOIN `#__answers_responses` r ON r.question_id = q.id AND r.state != 2
			WHERE $qweight > 0 AND q.state != 2
			UNION
			SELECT
				q.id AS qid, q.created AS q_created, q.subject, q.question, r.id AS rid, r.answer, r.created AS r_created, null AS qweight, $rweight AS rweight,
			CASE
				when q.anonymous > 0 then NULL
				else (SELECT name FROM `#__users` WHERE id = q.created_by)
			END AS qcontributors,
			CASE
				when q.anonymous > 0 then null
				else (SELECT id FROM `#__users` WHERE id = q.created_by)
			END AS qcontributor_ids,
			CASE
				when r.anonymous > 0 then NULL
				else (SELECT name FROM `#__users` WHERE id = r.created_by)
			END AS rcontributors,
			CASE
				when r.anonymous > 0 then null
				else (SELECT id FROM `#__users` WHERE id = r.created_by)
			END AS rcontributor_ids
			FROM `#__answers_responses` r2
			INNER JOIN `#__answers_questions` q ON q.id = r2.question_id AND q.state != 2
			LEFT JOIN `#__answers_responses` r ON r.question_id = q.id AND r.state != 2
			WHERE $r2weight > 0 AND r2.state != 2
			ORDER BY q_created, r_created"
		);

		$questions = array();
		$seen_answers = array();
		foreach ($dbh->loadAssocList() as $row)
		{
			if (!array_key_exists($row['qid'], $questions))
			{
				$questions[$row['qid']] = new \Components\Search\Models\Basic\Result\AssocScalar(array(
					'title'           => $row['subject'],
					'description'     => $row['question'],
					'section'         => 'Questions',
					'date'            => $row['q_created'],
					'link'            => 'index.php?option=com_answers&task=question&id=' . $row['qid'],
					'weight'          => $row['qweight'],
					'contributors'    => $row['qcontributors'],
					'contributor_ids' => $row['qcontributor_ids']
				));
			}
			if (!array_key_exists($row['qid'] . '-' . $row['rid'], $seen_answers) && $row['answer'])
			{
				$questions[$row['qid']]->add_child(new \Components\Search\Models\Basic\Result\AssocScalar(array(
					'title'           => ($row['rcontributors'] ? $row['rcontributors'] : 'Anonymous') . (', ' . $row['r_created']),
					'description'     => $row['answer'],
					'section'         => 'Questions',
					'date'            => $row['r_created'],
					'link'            => 'index.php?option=com_answers&task=question&id=' . $row['qid'],
					'weight'          => $row['rweight'],
					'contributors'    => $row['rcontributors'],
					'contributor_ids' => $row['rcontributor_ids']
				)));
				//$questions[$row['qid']]->add_weight($row['rweight']/5);
				$seen_answers[$row['qid'] . '-' . $row['rid']] = 1;
			}
		}
		usort($questions, array('plgSearchQuestions', 'sort_by_weight'));
		$maxWeight = 0;
		foreach ($questions as $question)
		{
			$maxWeight = max($maxWeight, $question->get_weight());
		}
		foreach ($questions as $question)
		{
			$question->sort_children(array('plgSearchQuestions', 'sort_by_date'));
			$question->scale_weight($maxWeight, 'normalizing within plugin');
			$results->add($question);
		}
	}

	/**
	 * onGetTypes - Announces the available hubtype
	 * 
	 * @param mixed $type 
	 * @access public
	 * @return void
	 */
	public function onGetTypes($type = null)
	{
		// The name of the hubtype
		$hubtype = 'question';

		if (isset($type) && $type == $hubtype)
		{
			return $hubtype;
		}
		elseif (!isset($type))
		{
			return $hubtype;
		}
	}

	/**
	 * onIndex 
	 * 
	 * @param string $type
	 * @param integer $id 
	 * @param boolean $run 
	 * @access public
	 * @return void
	 */
	public function onIndex($type, $id, $run = false)
	{
		if ($type == 'question')
		{
			if ($run === true)
			{
				// Establish a db connection
				$db = App::get('db');

				// Sanitize the string
				$id = \Hubzero\Utility\Sanitize::paranoid($id);

				// Get the record
				$sql = "SELECT * FROM #__answers_questions WHERE id={$id};";
				$row = $db->setQuery($sql)->query()->loadObject();

				// Get the name of the author
				if ($row->anonymous == 0)
				{
					$sql1 = "SELECT name FROM #__users WHERE id={$row->created_by};";
					$author = $db->setQuery($sql1)->query()->loadResult();
				}
				else
				{
					$author = 'anonymous';
				}

				// Get any tags
				$sql2 = "SELECT tag 
					FROM #__tags
					LEFT JOIN #__tags_object
					ON #__tags.id=#__tags_object.tagid
					WHERE #__tags_object.objectid = {$id} AND #__tags_object.tbl = 'answers';";
				$tags = $db->setQuery($sql2)->query()->loadColumn();

				// Get the associated responses
				$sql3 = "SELECT * FROM #__answers_responses WHERE question_id={$id};";
				$responses = $db->setQuery($sql3)->query()->loadObjectList();

				// Concatenate responses
				$responseString = '';
				foreach ($responses as  $response)
				{
					if ($response->state == 0)
					{
						$responseString .= $response->answer . ' ';
					}
				}
				
				// Determine the path
				$path = '/answers/qustion/' . $id;

				// Always public condition
				$access_level = 'public';
				$owner_type = 'user';
				$owner = $row->created_by;

				// Get the title
				$title = $row->subject;

				// Build the description, clean up text
				$content = $row->question . ' ' . $responseString;
				$content = preg_replace('/<[^>]*>/', ' ', $content);
				$content = preg_replace('/ {2,}/', ' ', $content);
				$description = \Hubzero\Utility\Sanitize::stripAll($content);

				// Create a record object
				$record = new \stdClass;
				$record->id = $type . '-' . $id;
				$record->title = $title;
				$record->description = $description;
				$record->author = array($author);
				$record->tags = $tags;
				$record->path = $path;
				$record->access_level = $access_level;
				$record->owner = $owner;
				$record->owner_type = $owner_type;

				// Return the formatted record
				return $record;
			}
			else
			{
				$db = App::get('db');
				$sql = "SELECT id FROM #__answers_questions;";
				$ids = $db->setQuery($sql)->query()->loadColumn();
				return array($type => $ids);
			}
		}
	}
}

