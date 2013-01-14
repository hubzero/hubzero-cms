<?php

class FormRespondent
{
	private $id, $started, $finished;

	public function __construct($depId) {
		if (!($uid = JFactory::getUser()->id)) {
			throw new NeedLoginError;
		}
		$dbh = JFactory::getDBO();
		$dbh->setQuery('SELECT id, started, finished FROM #__courses_form_respondents WHERE deployment_id = '.(int)$depId.' AND user_id = '.(int)$uid);
		if (($res = $dbh->loadAssoc())) {
			$this->id = $res['id'];
			$this->started = $res['started'];
			$this->finished = $res['finished'];
		}
		else {
			$dbh->execute('INSERT INTO #__courses_form_respondents(deployment_id, user_id) VALUES ('.(int)$depId.', '.(int)$uid.')');
			$this->id = $dbh->insertid();
		}
	}

	public function getConfirmationCode($crumb) {
		return md5($this->id.$crumb);
	}

	public function saveAnswers($answers) {
		$dbh = JFactory::getDBO();
		foreach ($answers as $key=>$val) {
			if (!preg_match('/^question-(\d+)$/', $key, $qid)) {
				continue;
			}
			$dbh->execute('INSERT INTO #__courses_form_responses(respondent_id, question_id, answer_id) VALUES ('.$this->id.', '.$qid[1].', '.(int)$val.')');
		}
		return $this;
	}

	public function getAnswers() {
		$dbh = JFactory::getDBO();
		$dbh->setQuery('SELECT pfr.question_id, answer_id, pfa.id AS correct_answer_id, version FROM #__courses_form_latest_responses_view pfr INNER JOIN #__courses_form_questions pfq ON pfq.id = pfr.question_id INNER JOIN #__courses_form_answers pfa ON pfr.question_id = pfa.question_id AND pfa.correct WHERE pfr.respondent_id = '.$this->id);
		$rv = array(
			'summary' => array('correct' => 0, 'total' => 0, 'score' => NULL, 'version' => NULL),
			'detail' => array()
		);
		foreach ($dbh->loadAssocList() as $answer) {
			if ($answer['answer_id'] == $answer['correct_answer_id']) {
				++$rv['summary']['correct'];
			}
			$rv['summary']['version'] = $answer['version'];
			++$rv['summary']['total'];
			$rv['detail'][$answer['question_id']] = $answer;
		}
		$rv['summary']['score'] = number_format($rv['summary']['correct']*100/$rv['summary']['total'], 1);
		return $rv;
	}

	public function saveProgress($qid, $aid) {
		$dbh = JFactory::getDBO();
		$dbh->execute('DELETE FROM #__courses_form_respondent_progress WHERE respondent_id = '.(int)$this->id.' AND question_id = '.(int)$qid);
		$dbh->execute('INSERT INTO #__courses_form_respondent_progress(respondent_id, question_id, answer_id) VALUES ('.(int)$this->id.', '.(int)$qid.', '.(int)$aid.')');
		return $this;
	}

	public function getProgress() {
		$dbh = JFactory::getDBO();
		$dbh->setQuery('SELECT question_id, answer_id FROM #__courses_form_respondent_progress WHERE respondent_id = '.(int)$this->id);
		return $dbh->loadAssocList('question_id');
	}

	public function getStartTime() {
		return $this->started;
	}
	
	public function getEndTime() {
		return $this->finished;
	}

	public function markStart() {
		$this->started = date('Y-m-d H:i:s');
		JFactory::getDBO()->execute('UPDATE #__courses_form_respondents SET started = \''.$this->started.'\' WHERE started IS NULL AND id = '.(int)$this->id);
		return $this;
	}
	
	public function markEnd() {
		$this->started = date('Y-m-d H:i:s');
		JFactory::getDBO()->execute('UPDATE #__courses_form_respondents SET finished = \''.$this->started.'\' WHERE id = '.(int)$this->id);
		return $this;
	}
}