<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Date;
use App;

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
	 * Number of attempts allowed
	 *
	 * @var int
	 **/
	private $allowedAttempts;

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
			$dep = new self;
			foreach ($depData as $k => $v)
			{
				$dep->$k = $v;
			}
			$rv[] = $dep;
		}

		$uid = \User::get('id');
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
			if ($timeA == null || $timeB == null)
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
	public function getResults($key=null, $members=null)
	{
		$dbh = self::getDBH();

		$where = '';

		if (isset($members))
		{
			if (!is_array($members))
			{
				$members = (array) $members;
			}

			if (!empty($members))
			{
				$where = ' AND member_id IN (' . implode(',', $members) . ')';
			}
		}

		$dbh->setQuery(
			"SELECT member_id, started, finished, count(pfa.id)*100/count(pfr2.id) AS score
			FROM `#__courses_form_respondents` pfr
			LEFT JOIN `#__courses_form_latest_responses_view` pfr2 ON pfr2.respondent_id = pfr.id
			LEFT JOIN `#__courses_form_questions` pfq ON pfq.id = pfr2.question_id
			LEFT JOIN `#__courses_form_answers` pfa ON pfa.id = pfr2.answer_id AND pfa.correct
			WHERE deployment_id = {$this->id} {$where}
			GROUP BY member_id, started, finished, version
			ORDER BY member_id ASC, score ASC"
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
	public function getRespondent($member_id, $attempt=1)
	{
		static $resp = array();

		if ($attempt > $this->getAllowedAttempts())
		{
			return false;
		}

		if ($this->id && !isset($resp[$this->id.'.'.$member_id.'.'.$attempt]))
		{
			$resp[$this->id.'.'.$member_id.'.'.$attempt] = new PdfFormRespondent($this->id, $member_id, $attempt);
		}

		return $resp[$this->id.'.'.$member_id.'.'.$attempt];
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
	 * Load by form id
	 *
	 * @return object
	 **/
	public static function latestFromFormId($formId, $section_id=null)
	{
		return self::find('form_id = ' . (int)$formId . ' ORDER BY `id` DESC');
	}

	/**
	 * Load deployment
	 *
	 * @return object
	 **/
	private static function find($where, $section_id=null)
	{
		$dep = new self;
		$dbh = self::getDbh();
		$dbh->setQuery('SELECT id, form_id AS formId, start_time AS startTime, end_time AS endTime, results_open AS resultsOpen, time_limit AS timeLimit, crumb, results_closed AS resultsClosed, allowed_attempts AS allowedAttempts FROM #__courses_form_deployments WHERE '.$where);

		if (!($res = $dbh->loadAssoc()))
		{
			throw new \Hubzero\Error\Exception\RuntimeException("no such deployment", 404);
		}

		foreach ($res as $k => $v)
		{
			$dep->$k = $v;
		}

		if (isset($section_id) && is_numeric($section_id))
		{
			// Now overload start and end times with section asset times if applicable
			$query = "SELECT cfd.id, cosd.publish_up, cosd.publish_down FROM `#__courses_form_deployments` cfd
						JOIN `#__courses_assets` ca ON cfd.crumb = ca.url
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
	 * Sets the form id
	 *
	 * @return void
	 **/
	public function setFormId($formId)
	{
		$this->formId = $formId;
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

		return min($this->timeLimit, (strtotime($this->endTime) - strtotime(Date::of('now')))/60);
	}

	/**
	 * Get number of allowed attempts
	 *
	 * @return string
	 **/
	public function getAllowedAttempts()
	{
		return $this->allowedAttempts;
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
		if ($this->endTime && strtotime($this->endTime) <= strtotime(Date::of('now')))
		{
			return 'expired';
		}

		return (!$this->startTime || strtotime($this->startTime) <= strtotime(Date::of('now'))) ? 'active' : 'pending';
	}

	/**
	 * Generates a new form deployment crumb
	 *
	 * @return string
	 **/
	public function genNewCrumb()
	{
		$this->crumb = str_replace(array('/', '+'), array('-', '-'), substr(base64_encode(openssl_random_pseudo_bytes(self::CRUMB_LEN + 1)), 0, self::CRUMB_LEN));
	}

	/**
	 * Check if there are errors
	 *
	 * @return bool
	 **/
	public function hasErrors($col = null, $update = false)
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
			if (!$update && $this->endTime && strtotime($this->endTime) <= strtotime(Date::of('now')))
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
			if ($this->allowedAttempts <= 0)
			{
				$this->errors['allowedAttempts'] = array('Number of attempts cannot be less than 1');
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
		$dep = new self;
		$dep->formId = $fid;
		foreach (array('resultsOpen', 'resultsClosed', 'timeLimit', 'allowedAttempts') as $key)
		{
			if (!isset($data[$key]))
			{
				App::abort(422, 'expected a value to be supplied for '.$key);
				return;
			}

			if (($key == 'endTime' || $key == 'startTime') && !empty($data[$key]))
			{
				$data[$key] = Date::of(strtotime($data[$key]))->toSql();
			}

			$dep->$key = $data[$key];
		}

		$dep->genNewCrumb();

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
			$dbh = \App::get('db');
		}

		return $dbh;
	}

	/**
	 * Save a deployment
	 *
	 * @return int
	 **/
	public function save($id = null)
	{
		$dbh = self::getDbh();

		if (is_null($id))
		{
			$dbh->setQuery(
				'INSERT INTO `#__courses_form_deployments`(form_id, start_time, end_time, results_open, results_closed, time_limit, crumb, user_id, allowed_attempts) VALUES ('.
					(int)$this->formId.', '.
					($this->startTime ? $dbh->quote($this->startTime) : 'NULL').', '.
					($this->endTime   ? $dbh->quote($this->endTime) : 'NULL').', '.
					$dbh->quote($this->resultsOpen).', '.
					$dbh->quote($this->resultsClosed).', '.
					(int)$this->timeLimit.', '.
					$dbh->quote($this->crumb).', '.
					(int)User::get('id').', '.
					(int)$this->allowedAttempts.
				')'
			);
			$dbh->query();

			$this->id = $dbh->insertid();

			// Update related asset, if applicable
			$this->updateAsset();

			return $this->id;
		}

		$dbh->setQuery(
			'UPDATE #__courses_form_deployments SET '.
				'start_time = '.($this->startTime ? $dbh->quote($this->startTime) : 'NULL').', '.
				'end_time = '.($this->endTime     ? $dbh->quote($this->endTime) : 'NULL').', '.
				'results_open = '.$dbh->quote($this->resultsOpen).', '.
				'results_closed = '.$dbh->quote($this->resultsClosed).', '.
				'time_limit = '.(int)$this->timeLimit.', '.
				'allowed_attempts = '.(int)$this->allowedAttempts.
			' WHERE id = '.(int)$id
		);
		$dbh->query();

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

		if ($result = $dbh->loadResult())
		{
			// Get our asset object
			require_once(__DIR__ . DS . 'asset.php');
			$asset = new Asset($result);
			$asset->set('url', $this->crumb);
			$asset->store();
		}
	}
}
