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
 *
 * @package   hubzero-cms
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class PdfFormRespondent
{
	/**
	 * Form respondent id
	 *
	 * @var int
	 **/
	private $id;

	/**
	 * Deployment ID
	 *
	 * @var int
	 **/
	private $depId;

	/**
	 * Bool indicating whether the form has been started
	 *
	 * @var date/time
	 **/
	private $started;

	/**
	 * Bool indicating whether the form has been finished
	 *
	 * @var date/time
	 **/
	private $finished;

	/**
	 * Construtor
	 *
	 * @return void
	 **/
	public function __construct($depId, $uid=null)
	{
		if (!$uid && !($uid = JFactory::getUser()->id))
		{
			JError::raiseError(403, 'This area requires authentication');
		}

		$dbh = JFactory::getDBO();
		$dbh->setQuery('SELECT id, started, finished FROM #__courses_form_respondents WHERE deployment_id = '.(int)$depId.' AND user_id = '.(int)$uid);

		// Set deployment id
		$this->depId = (int)$depId;

		if (($res = $dbh->loadAssoc()))
		{
			$this->id = $res['id'];
			$this->started = $res['started'];
			$this->finished = $res['finished'];
		}
		else
		{
			$dbh->execute('INSERT INTO #__courses_form_respondents(deployment_id, user_id) VALUES ('.(int)$depId.', '.(int)$uid.')');
			$this->id = $dbh->insertid();
		}
	}

	/**
	 * Get confirmation code
	 *
	 * @return MD5 hash of confirmation code
	 **/
	public function getConfirmationCode($crumb)
	{
		return md5($this->id.$crumb);
	}

	/**
	 * Get respondent score
	 *
	 * @return float
	 **/
	public function getScore()
	{
		$dbh = JFactory::getDBO();
		$dbh->setQuery(
			'SELECT count(pfa.id)*100/count(pfr2.id) AS score
			FROM #__courses_form_respondents pfr 
			LEFT JOIN #__courses_form_latest_responses_view pfr2 ON pfr2.respondent_id = pfr.id
			INNER JOIN #__courses_form_questions pfq ON pfq.id = pfr2.question_id
			LEFT JOIN #__courses_form_answers pfa ON pfa.id = pfr2.answer_id AND pfa.correct
			WHERE deployment_id = ' . $this->depId . ' AND user_id = ' . JFactory::getUser()->get('id')
		);

		return $dbh->loadResult();
	}

	/**
	 * Save answers
	 *
	 * @return object
	 **/
	public function saveAnswers($answers)
	{
		$dbh = JFactory::getDBO();

		foreach ($answers as $key=>$val)
		{
			if (!preg_match('/^question-(\d+)$/', $key, $qid))
			{
				continue;
			}

			$dbh->execute('INSERT INTO #__courses_form_responses(respondent_id, question_id, answer_id) VALUES ('.$this->id.', '.$qid[1].', '.(int)$val.')');
		}

		// Sync grade to course grade book
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'gradebook.php');
		$gradebook = new CoursesModelGradeBook(null);
		$gradebook->saveScore($this->getScore(), $this->getAssetId());

		return $this;
	}

	/**
	 * Get answers
	 *
	 * @return array
	 **/
	public function getAnswers()
	{
		$dbh = JFactory::getDBO();
		$dbh->setQuery('SELECT pfr.question_id, answer_id, pfa.id AS correct_answer_id, version FROM #__courses_form_latest_responses_view pfr INNER JOIN #__courses_form_questions pfq ON pfq.id = pfr.question_id INNER JOIN #__courses_form_answers pfa ON pfr.question_id = pfa.question_id AND pfa.correct WHERE pfr.respondent_id = '.$this->id);

		$rv = array(
			'summary' => array('correct' => 0, 'total' => 0, 'score' => NULL, 'version' => NULL),
			'detail' => array()
		);

		foreach ($dbh->loadAssocList() as $answer)
		{
			if ($answer['answer_id'] == $answer['correct_answer_id'])
			{
				++$rv['summary']['correct'];
			}

			$rv['summary']['version'] = $answer['version'];
			++$rv['summary']['total'];
			$rv['detail'][$answer['question_id']] = $answer;
		}

		$rv['summary']['score'] = ($rv['summary']['total']>0) ? number_format($rv['summary']['correct']*100/$rv['summary']['total'], 1) : 0;

		return $rv;
	}

	/**
	 * Save current progress in form
	 *
	 * @return object
	 **/
	public function saveProgress($qid, $aid)
	{
		$dbh = JFactory::getDBO();
		$dbh->execute('DELETE FROM #__courses_form_respondent_progress WHERE respondent_id = '.(int)$this->id.' AND question_id = '.(int)$qid);
		$dbh->execute('INSERT INTO #__courses_form_respondent_progress(respondent_id, question_id, answer_id, submitted) VALUES ('.(int)$this->id.', '.(int)$qid.', '.(int)$aid.', '.$dbh->Quote(date("Y-m-d H:i:s")).')');

		return $this;
	}

	/**
	 * Get current progress in form
	 *
	 * @return array
	 **/
	public function getProgress()
	{
		$dbh = JFactory::getDBO();
		$dbh->setQuery('SELECT question_id, answer_id FROM #__courses_form_respondent_progress WHERE respondent_id = '.(int)$this->id);
		return $dbh->loadAssocList('question_id');
	}

	/**
	 * Get date/time form was started
	 *
	 * @return date/time
	 **/
	public function getStartTime()
	{
		return $this->started;
	}

	/**
	 * Get date/time form was ended
	 *
	 * @return date/time
	 **/
	public function getEndTime()
	{
		return $this->finished;
	}

	/**
	 * Mark time someone starts form
	 *
	 * @return object
	 **/
	public function markStart()
	{
		$this->started = date('Y-m-d H:i:s');
		JFactory::getDBO()->execute('UPDATE #__courses_form_respondents SET started = \''.$this->started.'\' WHERE started IS NULL AND id = '.(int)$this->id);

		return $this;
	}

	/**
	 * Mark time someone finishes form
	 *
	 * @return object
	 **/
	public function markEnd()
	{
		$this->started = date('Y-m-d H:i:s');
		JFactory::getDBO()->execute('UPDATE #__courses_form_respondents SET finished = \''.$this->started.'\' WHERE id = '.(int)$this->id);

		return $this;
	}

	/**
	 * Get asset id
	 *
	 * @return int
	 **/
	public function getAssetId()
	{
		$dbh = JFactory::getDBO();

		$dbh->setQuery(
			'SELECT `id`
				FROM #__courses_assets
				WHERE `content` 
				LIKE CONCAT(\'%{"form_id":"\', (SELECT form_id FROM #__courses_form_deployments WHERE id = ' . $this->depId . '), \'"}%\')'
		);

		return $dbh->loadResult();
	}
}