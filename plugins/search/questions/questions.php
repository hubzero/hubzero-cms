<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License AS published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Search questions
 */
class plgSearchQuestions extends SearchPlugin
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
	 * Build search query AND add it to the $results
	 *
	 * @param      object $request  YSearchModelRequest
	 * @param      object &$results YSearchModelResultSet
	 * @return     void
	 */
	public static function onSearch($request, &$results)
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

		$dbh = JFactory::getDBO();
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
				$questions[$row['qid']] = new SearchResultAssocScalar(array(
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
				$questions[$row['qid']]->add_child(new SearchResultAssocScalar(array(
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
}

