<?php
/**
 * @package     hubzero-cms
 * @author      Steve Snyder <snyder13@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class plgYSearchQuestions extends YSearchPlugin
{
	public static function sort_by_date($a, $b)
	{
		$aw = $a->get_date();
		$bw = $b->get_date();
		if ($aw == $bw)
			return 0;
		return $aw < $bw ? -1 : 1;
	}

	public static function sort_by_weight($a, $b)
	{
		return ($res = $a->get_weight() - $b->get_weight()) == 0 ? 0 : ($res > 0 ? -1 : 1);
	}

	public static function onYSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$qweight = 'match(q.question, q.subject) against(\''.join(' ', $terms['stemmed']).'\')';
		$rweight = 'match(r.answer) against(\''.join(' ', $terms['stemmed']).'\')';
		$r2weight = 'match(r2.answer) against(\''.join(' ', $terms['stemmed']).'\')';

		$qaddtl_where = array();
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
		}

		$dbh =& JFactory::getDBO();
		$dbh->setQuery(
			"select 
				q.id as qid, q.created as q_created, q.subject, q.question, r.id as rid, r.answer, r.created as r_created, $qweight as qweight, null as rweight,
			case 
				when q.anonymous > 0 then NULL
				else (select name from jos_users where username = q.created_by)
			end as qcontributors,
			case
				when q.anonymous > 0 then null
				else (select id from jos_users where username = q.created_by)
			end as qcontributor_ids,
			case 
				when r.anonymous > 0 then NULL
				else (select name from jos_users where username = r.created_by)
			end as rcontributors,
			case
				when r.anonymous > 0 then null
				else (select id from jos_users where username = r.created_by)
			end as rcontributor_ids
			from jos_answers_questions q 
			left join jos_answers_responses r on r.qid = q.id and r.state != 2
			where $qweight > 0
			union
			select 
				q.id as qid, q.created as q_created, q.subject, q.question, r.id as rid, r.answer, r.created as r_created, null as qweight, $rweight as rweight,
			case
				when q.anonymous > 0 then NULL
				else (select name from jos_users where username = q.created_by)
			end as qcontributors,
			case
				when q.anonymous > 0 then null
				else (select id from jos_users where username = q.created_by)
			end as qcontributor_ids,
			case 
				when r.anonymous > 0 then NULL
				else (select name from jos_users where username = r.created_by)
			end as rcontributors,
			case
				when r.anonymous > 0 then null
				else (select id from jos_users where username = r.created_by)
			end as rcontributor_ids
			from jos_answers_responses r2 
			inner join jos_answers_questions q on q.id = r2.qid
			left join jos_answers_responses r on r.qid = q.id and r.state != 2
			where $r2weight > 0 and r2.state != 2
			order by q_created, r_created"
		);

		$questions = array();
		$seen_answers = array();
		foreach ($dbh->loadAssocList() as $row)
		{
			if (!array_key_exists($row['qid'], $questions))
			{
				$questions[$row['qid']] = new YSearchResultAssocScalar(array(
					'title' => $row['subject'],
					'description' => $row['question'],
					'section' => 'Questions',
					'date' => $row['q_created'],
					'link' => '/answers/question/'.$row['qid'],
					'weight' => $row['qweight'],
					'contributors' => $row['qcontributors'],
					'contributor_ids' => $row['qcontributor_ids']
				));
			}
			if (!array_key_exists($row['qid'].'-'.$row['rid'], $seen_answers) && $row['answer'])
			{
				$questions[$row['qid']]->add_child(new YSearchResultAssocScalar(array(
					'title' => ($row['rcontributors'] ? $row['rcontributors'] : 'Anonymous').
						(', '.$row['r_created']),
					'description' => $row['answer'],
					'section' => 'Questions',
					'date' => $row['r_created'],
					'link' => '/answers/question/'.$row['qid'],
					'weight' => $row['rweight'],
					'contributors' => $row['rcontributors'],
					'contributor_ids' => $row['rcontributor_ids']
				)));
#				$questions[$row['qid']]->add_weight($row['rweight']/5);
				$seen_answers[$row['qid'].'-'.$row['rid']] = 1;
			}
		}
		usort($questions, array('plgYSearchQuestions', 'sort_by_weight'));
		foreach ($questions as $question)
		{
			$question->sort_children(array('plgYSearchQuestions', 'sort_by_date'));
			$results->add($question);
		}
	}
}

