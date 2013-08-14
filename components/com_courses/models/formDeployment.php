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

class PdfFormDeployment
{
	/**
	 * Length of crumb indentifier for a form
	 *
	 * @var constant
	 **/
	const CRUMB_LEN = 20;

	/**
	 * Array of result view types
	 *
	 * @var array
	 **/
	private static $resultViews = array('confirmation', 'score', 'details');

	/**
	 * Deployment ID
	 *
	 * @var int
	 **/
	private $id;

	/**
	 * Form ID
	 *
	 * @var int
	 **/
	private $formId;

	/**
	 * Form deployment start time
	 *
	 * @var date/time
	 **/
	private $startTime;

	/**
	 * Form deployment end time
	 *
	 * @var date/time
	 **/
	private $endTime;

	/**
	 * Results open
	 *
	 * @var string
	 **/
	private $resultsOpen;

	/**
	 * Result closed
	 *
	 * @var string
	 **/
	private $resultsClosed;

	/**
	 * Form time limit
	 *
	 * @var string
	 **/
	private $timeLimit;

	/**
	 * Form identifier
	 *
	 * @var string
	 **/
	private $crumb;

	/**
	 * User ID
	 *
	 * @var int
	 **/
	private $userId;

	/**
	 * Username
	 *
	 * @var string
	 **/
	private $userName;

	/**
	 * Array of errors
	 *
	 * @var array
	 **/
	private $errors = array();

	/**
	 * Process form
	 *
	 * @return array
	 **/
	public static function forForm($formId)
	{
		$rv = array();
		
		$dbh = self::getDbh();
		$dbh->setQuery('SELECT id, form_id AS formId, start_time AS startTime, end_time AS endTime, results_open AS resultsOpen, time_limit AS timeLimit, crumb, results_closed AS resultsClosed, (SELECT name FROM #__users WHERE id = user_id) AS userName FROM #__courses_form_deployments WHERE form_id = '.(int)$formId);

		foreach ($dbh->loadAssocList() as $depData)
		{
			$dep = new PdfFormDeployment;
			foreach ($depData as $k=>$v)
			{
				$dep->$k = $v;
			}
			$rv[] = $dep;
		}

		$uid = JFactory::getUser()->id;
		usort($rv, function($a, $b) use($uid)
		{
			$au = $a->getUserId();
			$bu = $b->getUserId();
			if ($au == $uid && $bu != $uid)
			{
				return -1;
			}
			if ($bu == $uid && $au != $uid)
			{
				return 1;
			}
			$stateA = $a->getState();
			$stateB = $b->getState();
			if ($stateA == 'active' && $stateB != 'active')
			{
				return -1;
			}
			if ($stateB == 'active' && $stateA != 'active')
			{
				return 1;
			}
			if ($stateA == 'pending' && $stateB == 'expired')
			{
				return -1;
			}
			if ($stateB == 'pending' && $stateA == 'expired')
			{
				return 1;
			}
			$timeA = $a->getStartTime();
			$timeB = $b->getStartTime();
			if ($timeA == NULL || $timeB == NULL)
			{
				return 0;
			}
			$timeA = strtotime($timeA);
			$timeB = strtotime($timeB);
			return $timeA > $timeB ? -1 : 1;
		});

		return $rv;
	}

	/**
	 * Get user ID
	 *
	 * @return int
	 **/
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * Get deployment results
	 *
	 * @return array
	 **/
	public function getResults($include_incompletes=false, $key=null)
	{
		$dbh = self::getDBH();

		$join_type = ($include_incompletes) ? "LEFT" : "INNER";

		$dbh->setQuery(
			"SELECT name, email, started, finished, version, u.id as user_id, count(pfa.id)*100/count(pfr2.id) AS score
			FROM #__courses_form_respondents pfr 
			INNER JOIN #__users u ON u.id = pfr.user_id 
			LEFT JOIN #__courses_form_latest_responses_view pfr2 ON pfr2.respondent_id = pfr.id
			{$join_type} JOIN #__courses_form_questions pfq ON pfq.id = pfr2.question_id
			LEFT JOIN #__courses_form_answers pfa ON pfa.id = pfr2.answer_id AND pfa.correct
			WHERE deployment_id = {$this->id}
			GROUP BY name, email, started, finished, version"
		);

		return $dbh->loadAssocList($key);
	}

	/**
	 * Get form object
	 *
	 * @return object
	 **/
	public function getForm()
	{
		static $form;
		if (!$form && $this->formId)
		{
			$form = new PdfForm($this->formId);
		}

		return $form;
	}

	/**
	 * Get respondent object
	 *
	 * @return object
	 **/
	public function getRespondent($uid=NULL)
	{
		// @FIXME: should this have a static instance?  this causes a problem when loading a grade book type scenario
		static $resp;

		if (!$resp && $this->id)
		{
			$resp = new PdfFormRespondent($this->id, $uid);
		}

		return new PdfFormRespondent($this->id, $uid);
	}

	/**
	 * Load by ID
	 *
	 * @return object
	 **/
	public static function load($id, $section_id=null)
	{
		return self::find('id = '.((int)$id), $section_id);
	}

	/**
	 * Load by Crumb
	 *
	 * @return object
	 **/
	public static function fromCrumb($crumb, $section_id=null)
	{
		return self::find('crumb = '.self::getDbh()->quote($crumb), $section_id);
	}

	/**
	 * Load deployment
	 *
	 * @return object
	 **/
	private static function find($where, $section_id=null)
	{
		$dep = new PdfFormDeployment;
		$dbh = self::getDbh();
		$dbh->setQuery('SELECT id, form_id AS formId, start_time AS startTime, end_time AS endTime, results_open AS resultsOpen, time_limit AS timeLimit, crumb, results_closed AS resultsClosed FROM #__courses_form_deployments WHERE '.$where);

		if (!($res = $dbh->loadAssoc()))
		{
			JError::raiseError(404, 'no such deployment');
			return;
		}

		foreach ($res as $k=>$v)
		{
			$dep->$k = $v;
		}

		if (isset($section_id) && is_numeric($section_id))
		{
			// Now overload start and end times with section asset times if applicable
			// @FIXME: assuming student is only in one section, and that asset is only part of one offering
			$query = "SELECT cfd.id, cosd.publish_up, cosd.publish_down FROM `#__courses_form_deployments` cfd
						JOIN `#__courses_assets` ca ON cfd.crumb = substring(ca.url, 30)
						JOIN `#__courses_offering_section_dates` cosd ON ca.id = cosd.scope_id
						WHERE cosd.scope = 'asset'
						AND cosd.section_id = " . $dbh->quote($section_id) . "
						AND cfd." . $where;

			$dbh->setQuery($query);
			if ($result = $dbh->loadObject())
			{
				if (isset($result->publish_up) && $result->publish_up != '0000-00-00 00:00:00')
				{
					$dep->startTime = $result->publish_up;
				}
				if (isset($result->publish_down) && $result->publish_down != '0000-00-00 00:00:00')
				{
					$dep->endTime = $result->publish_down;
				}
			}
		}

		return $dep;
	}

	/**
	 * Get username
	 *
	 * @return string
	 **/
	public function getUserName()
	{
		return $this->userName;
	}

	/**
	 * Get ID
	 *
	 * @return int
	 **/
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set ID
	 *
	 * @return int
	 **/
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * Get link to form
	 *
	 * @return string
	 **/
	public function getLink($internal=false)
	{
		return ($internal) ? '/courses/form/complete?crumb='.$this->crumb : 'https://'.$_SERVER['HTTP_HOST'].'/courses/form/complete?crumb='.$this->crumb;
	}

	/**
	 * Get start time
	 *
	 * @return string
	 **/
	public function getStartTime()
	{
		return $this->startTime;
	}

	/**
	 * Get end time
	 *
	 * @return string
	 **/
	public function getEndTime()
	{
		return $this->endTime;
	}

	/**
	 * Get results
	 *
	 * @return string
	 **/
	public function getActiveResults()
	{
		return $this->getState() == 'active' ? $this->resultsOpen : $this->resultsClosed;
	}

	/**
	 * Get results for open form
	 *
	 * @return string
	 **/
	public function getResultsOpen()
	{
		return $this->resultsOpen;
	}

	/**
	 * Get results for a closed form
	 *
	 * @return string
	 **/
	public function getResultsClosed()
	{
		return $this->resultsClosed;
	}

	/**
	 * Get form time limit
	 *
	 * @return int
	 **/
	public function getTimeLimit()
	{
		return $this->timeLimit;
	}

	/**
	 * Get real time limit
	 *
	 * @return string
	 **/
	public function getRealTimeLimit()
	{
		if (!$this->endTime)
		{
			return $this->timeLimit;
		}

		return min($this->timeLimit, (strtotime($this->endTime) - time())/60);
	}

	/**
	 * Get crumb
	 *
	 * @return string
	 **/
	public function getCrumb()
	{
		return $this->crumb;
	}

	/**
	 * Get state
	 *
	 * @return string
	 **/
	public function getState()
	{
		if ($this->endTime && strtotime($this->endTime) <= time())
		{
			return 'expired';
		}

		return (!$this->startTime || strtotime($this->startTime) <= time()) ? 'active' : 'pending';
	}

	/**
	 * Check if there are errors
	 *
	 * @return bool
	 **/
	public function hasErrors($col = NULL, $update = FALSE)
	{
		static $checked;

		if (!$checked)
		{
			$checked = true;
			if ($this->endTime && !strtotime($this->endTime))
			{
				$this->errors['endTime'] = array('Invalid end time');
			}
			if ($this->startTime && !strtotime($this->startTime))
			{
				$this->errors['startTime'] = array('Invalid start time');
			}
			if ($this->timeLimit != '' && (($this->timeLimit != (int)$this->timeLimit) || $this->timeLimit <= 0))
			{
				$this->errors['timeLimit'] = array('Expected a positive, nonzero, integer number of minutes');
			}
			if (!$update && $this->endTime && strtotime($this->endTime) <= time())
			{
				if (!isset($this->errors['endTime']))
				{
					$this->errors['endTime'] = array();
				}
				$this->errors['endTime'][] = 'The deployment would already be expired with this end time';
			}
			if ($this->endTime && $this->startTime && strtotime($this->endTime) <= strtotime($this->startTime))
			{
				if (!isset($this->errors['endTime']))
				{
					$this->errors['endTime'] = array();
				}
				$this->errors['endTime'][] = 'The end time must be after the start time';
			}
			if ($this->resultsOpen && !in_array($this->resultsOpen, self::$resultViews))
			{
				$this->errors['resultsOpen'] = array('Invalid selection');
			}
			if ($this->resultsClosed && !in_array($this->resultsClosed, self::$resultViews))
			{
				$this->errors['resultsClosed'] = array('Invalid selection');
			}
		}

		return is_null($col) ? (bool)$this->errors : isset($this->errors[$col]) && (bool)$this->errors[$col];
	}

	/**
	 * Get errors
	 *
	 * @return array
	 **/
	public function getErrors($field)
	{
		return isset($this->errors[$field]) ? $this->errors[$field] : array();
	}

	/**
	 * Create a deployment object from incomming form data
	 *
	 * @return object
	 **/
	public static function fromFormData($fid, $data)
	{
		$dep = new PdfFormDeployment;
		$dep->formId = $fid;
		foreach (array('startTime', 'endTime', 'resultsOpen', 'resultsClosed', 'timeLimit') as $key)
		{
			if (!isset($data[$key]))
			{
				JError::raiseError(422, 'expected a value to be supplied for '.$key);
				return;
			}

			$dep->$key = $data[$key];
		}

		$dep->crumb = str_replace(array('/', '+'), array('-', '-'), substr(base64_encode(openssl_random_pseudo_bytes(self::CRUMB_LEN + 1)), 0, self::CRUMB_LEN));

		return $dep;
	}

	/**
	 * Get a database handle
	 *
	 * @return object
	 **/
	private static function getDbh()
	{
		static $dbh;

		if (!$dbh)
		{
			$dbh = JFactory::getDBO();
		}

		return $dbh;
	}

	/**
	 * Save a deployment
	 *
	 * @return int
	 **/
	public function save($id = NULL)
	{
		$dbh = self::getDbh();

		if (is_null($id))
		{
			$dbh->execute(
				'INSERT INTO #__courses_form_deployments(form_id, start_time, end_time, results_open, results_closed, time_limit, crumb, user_id) VALUES ('.
					(int)$this->formId.', '.
					($this->startTime ? date('\'Y-m-d H:i:s\'', strtotime($this->startTime)) : 'NULL').', '.
					($this->endTime   ? date('\'Y-m-d H:i:s\'', strtotime($this->endTime)) : 'NULL').', '.
					$dbh->quote($this->resultsOpen).', '.
					$dbh->quote($this->resultsClosed).', '.
					(int)$this->timeLimit.', '.
					$dbh->quote($this->crumb).', '.
					(int)JFactory::getUser()->id.
				')'
			);

			$this->id = $dbh->insertid();

			// Update related asset, if applicable
			$this->updateAsset();

			return $this->id;
		}

		$dbh->execute(
			'UPDATE #__courses_form_deployments SET '.
				'start_time = '.($this->startTime ? date('\'Y-m-d H:i:s\'', strtotime($this->startTime)) : 'NULL').', '.
				'end_time = '.($this->endTime   ? date('\'Y-m-d H:i:s\'', strtotime($this->endTime)) : 'NULL').', '.
				'results_open = '.$dbh->quote($this->resultsOpen).', '.
				'results_closed = '.$dbh->quote($this->resultsClosed).', '.
				'time_limit = '.(int)$this->timeLimit.
			' WHERE id = '.(int)$id
		);

		// Update related asset, if applicable
		$this->updateAsset();

		return ($this->id = $id);
	}

	/**
	 * Update associated asset, if applicable
	 *
	 * @return void
	 **/
	public function updateAsset()
	{
		$dbh = self::getDBH();
		$fid = $this->formId;

		$dbh->setQuery(
			'SELECT `asset_id` FROM `#__courses_forms` WHERE `id` = ' . $dbh->Quote($fid)
		);

		if($result = $dbh->loadResult())
		{
			// Get our asset object
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'asset.php');
			$asset = new CoursesModelAsset($result);
			$asset->set('url', $this->getLink(true));
			$asset->store();
		}
	}

	/**
	 * Get course info for form
	 *
	 * @return course object
	 **/
	public function getCourseInfo()
	{
		$dbh = self::getDBH();
		$fid = $this->formId;

		$dbh->setQuery(
			'SELECT ca.course_id, o.id
			FROM #__courses_assets as ca
			INNER JOIN #__courses_asset_associations as caa ON caa.asset_id = ca.id
			INNER JOIN #__courses_asset_groups as cag ON cag.id = caa.scope_id
			INNER JOIN #__courses_units as u ON u.id = cag.unit_id
			INNER JOIN #__courses_offerings as o ON o.id = u.offering_id
			INNER JOIN #__courses_forms as cf ON ca.id = cf.asset_id
			WHERE cf.id = ' . $dbh->Quote($fid)
		);

		if($result = $dbh->loadAssoc())
		{
			// Get course model
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');
			$course = CoursesModelCourse::getInstance($result['course_id']);
			$course->offering($result['id']);

			return $course;
		}
		else
		{
			return false;
		}
	}
}