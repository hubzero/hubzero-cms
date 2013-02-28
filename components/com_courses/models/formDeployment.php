<?php

class PdfFormDeployment
{
	const CRUMB_LEN = 20;
	private static $resultViews = array('confirmation', 'score', 'details');
	private $id, $formId, $startTime, $endTime, $resultsOpen, $resultsClosed, $timeLimit, $crumb, $userId, $userName, $errors = array();

	public static function forForm($formId) {
		$rv = array();
		
		$dbh = self::getDbh();
		$dbh->setQuery('SELECT id, form_id AS formId, start_time AS startTime, end_time AS endTime, results_open AS resultsOpen, time_limit AS timeLimit, crumb, results_closed AS resultsClosed, (SELECT name FROM #__users WHERE id = user_id) AS userName FROM #__courses_form_deployments WHERE form_id = '.(int)$formId);
		foreach ($dbh->loadAssocList() as $depData) {
			$dep = new PdfFormDeployment;
			foreach ($depData as $k=>$v) {
				$dep->$k = $v;
			}
			$rv[] = $dep;
		}
		$uid = JFactory::getUser()->id;
		usort($rv, function($a, $b) use($uid) {
			$au = $a->getUserId();
			$bu = $b->getUserId();
			if ($au == $uid && $bu != $uid) {
				return -1;
			}
			if ($bu == $uid && $au != $uid) {
				return 1;
			}
			$stateA = $a->getState();
			$stateB = $b->getState();
			if ($stateA == 'active' && $stateB != 'active') {
				return -1;
			}
			if ($stateB == 'active' && $stateA != 'active') {
				return 1;
			}
			if ($stateA == 'pending' && $stateB == 'expired') {
				return -1;
			}
			if ($stateB == 'pending' && $stateA == 'expired') {
				return 1;
			}
			$timeA = $a->getStartTime();
			$timeB = $b->getStartTime();
			if ($timeA == NULL || $timeB == NULL) {
				return 0;
			}
			$timeA = strtotime($timeA);
			$timeB = strtotime($timeB);
			return $timeA > $timeB ? -1 : 1;
		});
		return $rv;
	}

	public function getUserId() { 
		return $this->userId; 
	}

	public function getResults() {
		$dbh = self::getDBH();
		$dbh->setQuery(
			'SELECT name, email, started, finished, version, u.id as user_id, count(pfa.id)*100/count(pfr2.id) AS score
			FROM #__courses_form_respondents pfr 
			INNER JOIN #__users u ON u.id = pfr.user_id 
			LEFT JOIN #__courses_form_latest_responses_view pfr2 ON pfr2.respondent_id = pfr.id
			INNER JOIN #__courses_form_questions pfq ON pfq.id = pfr2.question_id
			LEFT JOIN #__courses_form_answers pfa ON pfa.id = pfr2.answer_id AND pfa.correct
			WHERE deployment_id = '.$this->id.' 
			GROUP BY name, email, started, finished, version'
		);
		return $dbh->loadAssocList();
	}
	
	public function getForm() {
		static $form;
		if (!$form && $this->formId) {
			$form = new PdfForm($this->formId);
		}
		return $form;
	}

	public function getRespondent($uid=NULL) {
		// @FIXME: should this have a static instance?  this causes a problem when loading a grade book type scenario
		static $resp;
		if (!$resp && $this->id) {
			$resp = new PdfFormRespondent($this->id, $uid);
		}
		return new PdfFormRespondent($this->id, $uid);
	}

	public static function load($id) {
		return self::find('id = '.((int)$id));
	}

	public static function fromCrumb($crumb) {
		return self::find('crumb = '.self::getDbh()->quote($crumb));
	}

	private static function find($where) {
		$dep = new PdfFormDeployment;
		$dbh = self::getDbh();
		$dbh->setQuery('SELECT id, form_id AS formId, start_time AS startTime, end_time AS endTime, results_open AS resultsOpen, time_limit AS timeLimit, crumb, results_closed AS resultsClosed FROM #__courses_form_deployments WHERE '.$where);
		if (!($res = $dbh->loadAssoc())) {
			throw new NotFoundError('no such deployment');
		}
		foreach ($res as $k=>$v) {
			$dep->$k = $v;
		}
		return $dep;
	}

	public function getUserName() {
		return $this->userName;
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getLink() {
		return 'https://'.$_SERVER['HTTP_HOST'].'/courses/form/complete?crumb='.$this->crumb;
	}

	public function getStartTime() {
		return $this->startTime;
	}

	public function getEndTime() {
		return $this->endTime;
	}

	public function getActiveResults() {
		return $this->getState() == 'active' ? $this->resultsOpen : $this->resultsClosed;
	}
	
	public function getResultsOpen() {
		return $this->resultsOpen;
	}

	public function getResultsClosed() {
		return $this->resultsClosed;
	}

	public function getTimeLimit() {
		return $this->timeLimit;
	}

	public function getRealTimeLimit() {
		if (!$this->endTime) {
			return $this->timeLimit;
		}
		return min($this->timeLimit, (strtotime($this->endTime) - time())/60);
	}

	public function getCrumb() {
		return $this->crumb;
	}

	public function getState() {
		if ($this->endTime && strtotime($this->endTime) <= time()) {
			return 'expired';
		}
		return (!$this->startTime || strtotime($this->startTime) <= time()) ? 'active' : 'pending';
	}

	public function hasErrors($col = NULL, $update = FALSE) {
		static $checked;
		if (!$checked) {
			$checked = true;
			if ($this->endTime && !strtotime($this->endTime)) {
				$this->errors['endTime'] = array('Invalid end time');
			}
			if ($this->startTime && !strtotime($this->startTime)) {
				$this->errors['startTime'] = array('Invalid start time');
			}
			if ($this->timeLimit != '' && (($this->timeLimit != (int)$this->timeLimit) || $this->timeLimit <= 0)) {
				$this->errors['timeLimit'] = array('Expected a positive, nonzero, integer number of minutes');
			}
			if (!$update && $this->endTime && strtotime($this->endTime) <= time()) {
				if (!isset($this->errors['endTime'])) {
					$this->errors['endTime'] = array();
				}
				$this->errors['endTime'][] = 'The deployment would already be expired with this end time';
			}
			if ($this->endTime && $this->startTime && strtotime($this->endTime) <= strtotime($this->startTime)) {
				if (!isset($this->errors['endTime'])) {
					$this->errors['endTime'] = array();
				}
				$this->errors['endTime'][] = 'The end time must be after the start time';
			}
			if ($this->resultsOpen && !in_array($this->resultsOpen, self::$resultViews)) {
				$this->errors['resultsOpen'] = array('Invalid selection');
			}
			if ($this->resultsClosed && !in_array($this->resultsClosed, self::$resultViews)) {
				$this->errors['resultsClosed'] = array('Invalid selection');
			}
		}
		return is_null($col) ? (bool)$this->errors : isset($this->errors[$col]) && (bool)$this->errors[$col];
	}

	public function getErrors($field) {
		return isset($this->errors[$field]) ? $this->errors[$field] : array();
	}

	public static function fromFormData($fid, $data) {
		$dep = new PdfFormDeployment;
		$dep->formId = $fid;
		foreach (array('startTime', 'endTime', 'resultsOpen', 'resultsClosed', 'timeLimit') as $key) {
			if (!isset($data[$key])) {
				throw new UnprocessableEntityError('expected a value to be supplied for '.$key);
			}
			$dep->$key = $data[$key];
		}
		$dep->crumb = str_replace(array('/', '+'), array('-', '-'), substr(base64_encode(openssl_random_pseudo_bytes(self::CRUMB_LEN + 1)), 0, self::CRUMB_LEN));
		return $dep;
	}
	
	private static function getDbh() {
		static $dbh;
		if (!$dbh) {
			$dbh = JFactory::getDBO();
		}
		return $dbh;
	}

	public function save($id = NULL) {
		$dbh = self::getDbh();
		if (is_null($id)) {
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
			return ($this->id = $dbh->insertid());
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
		return ($this->id = $id);
	}
}