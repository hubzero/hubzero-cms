<?php

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : 'index';
if ($task == 'index' && preg_match('#^/pdf2form/([-a-zA-Z0-9]{20})(?:$|\?)#', isset($_SERVER['SCRIPT_URL']) ? $_SERVER['SCRIPT_URL'] : $_SERVER['REDIRECT_SCRIPT_URL'], $ma))
{
	$params = $_SERVER['argv'];
	$location = 'index.php?option=com_pdf2form&task=complete&crumb=' . $ma[1];
	if ($params)
	{
		$location .= '&' . implode('&', $params);
	}
	header('Location: ' . JRoute::_($location));
	exit();
}

$doc = JFactory::getDocument();
$doc->addStyleSheet('/components/com_pdf2form/resources/pdf2form.css');
$doc->addScript('/media/system/js/jquery.ui.js');

$path = JFactory::getApplication()->getPathway();
$path->addItem('PDF Forms', '/pdf2form');

$errors = array();

function authzFaculty()
{
	if (!($uid = JFactory::getUser()->id))
	{
		throw new NeedLoginError;
	}
	$dbh = JFactory::getDBO();
	$dbh->setQuery('SELECT 1 FROM #__user_roles WHERE role = \'faculty\' AND user_id = '.((int)$uid));
	if (!$dbh->loadResult())
	{
		throw new ForbiddenError;
	}
}

function assertFormId()
{
	if (isset($_POST['formId']))
	{
		return $_POST['formId'];
	}
	if (isset($_GET['formId']))
	{
		return $_GET['formId'];
	}
	throw new UnprocessableEntityError('No form identifier supplied');
}

function assertExistentForm()
{
	$pdf = new PdfForm(assertFormId());
	if (!$pdf->isStored())
	{
		throw new NotFoundError('No form matches identifier');
	}
	return $pdf;
}

try
{
	switch ($task)
	{
		case 'index':
			authzFaculty();
			require 'views/select.php';
		break;
		case 'upload':
			authzFaculty();
			$pdf = PdfForm::fromPostedFile('pdf');
			if (!$pdf->hasErrors())
			{
				$pdf->renderPageImages($form);
			}
			if ($pdf->hasErrors())
			{
				$errors = $pdf->getErrors();
				require 'views/select.php';
			}
			else
			{
				header('Location: ' . JRoute::_('index.php?option=com_pdf2form&task=layout&formId=' . $pdf->getId()));
				exit();
			}
		break;
		case 'layout':
			authzFaculty();
			$pdf = new PdfForm(assertFormId());
			require 'views/layout.php';
		break;
		case 'saveLayout':
			authzFaculty();
			$pdf = assertExistentForm();
			$pdf->setTitle($_POST['title']);
			if (isset($_POST['pages']))
			{
				$pdf->setPageLayout($_POST['pages']);
			}
			header('Content-type: application/json');
			echo '{"result":"success"}';
		exit();
		case 'deploy':
			authzFaculty();
			$pdf = assertExistentForm();
			$dep = new PdfFormDeployment;
			require 'views/deploy.php';
		break;
		case 'createDeployment':
			if (!isset($_POST['deployment']))
			{
				throw new UnprocessableEntityError();
			}
			$pdf = assertExistentForm();
			$dep = PdfFormDeployment::fromFormData($pdf->getId(), $_POST['deployment']);
			if ($dep->hasErrors())
			{
				require 'views/deploy.php';
			}
			else
			{
				header('Location: ' . JRoute::_('index.php?option=com_pdf2form&task=showDeployment&id=' . $dep->save() . '&formId=' . $pdf->getId()));
				exit();
			}
		break;
		case 'updateDeployment':
			if (!isset($_POST['deployment']) || !isset($_POST['deploymentId']))
			{
				throw new UnprocessableEntityError();
			}
			$pdf = assertExistentForm();
			$dep = PdfFormDeployment::fromFormData($pdf->getId(), $_POST['deployment']);
			echo 'check';
			if ($dep->hasErrors(NULL, TRUE))
			{
				require 'views/show_deployment.php';
			}
			else
			{
				header('Location: ' . JRoute::_('index.php?option=com_pdf2form?task=showDeployment&id=' . $dep->save($_POST['deploymentId']) . '&formId=' . $pdf->getId()));
				exit();
			}
		break;
		case 'showDeployment':
			if (!isset($_GET['id']))
			{
				throw new UnprocessableEntityError('No form identifier supplied');
			}
			$pdf = assertExistentForm();
			$dep = PdfFormDeployment::load($_GET['id']);
			require 'views/show_deployment.php';
		break;
		case 'complete':
			if (!isset($_GET['crumb']))
			{
				throw new UnprocessableEntityError();
			}
			$dep = PdfFormDeployment::fromCrumb($_GET['crumb']);
			$dbg = isset($_GET['dbg']);
			switch ($dep->getState())
			{
				case 'pending':
					throw new ForbiddenError('This deployment is not yet available');
				case 'expired':
					require 'views/results/'.$dep->getResultsClosed().'.php';
				break;
				case 'active':
					$incomplete = array();
					$resp = $dep->getRespondent();
					require $resp->getEndTime() ? 'views/results/'.$dep->getResultsOpen().'.php' : 'views/complete.php';
			}
		break;
		case 'startWork':
			if (!isset($_POST['crumb']))
			{
				throw new UnprocessableEntityError();
			}
			PdfFormDeployment::fromCrumb($_POST['crumb'])->getRespondent()->markStart();
			header('Location: ' . JRoute::_('index.php?option=com_pdf2form?task=complete&crumb='.$_POST['crumb'].(isset($_POST['tmpl']) ? '&tmpl='.$_POST['tmpl'] : '')));
		exit();
		case 'saveProgress':
			if (!isset($_POST['crumb']) || !isset($_POST['question']) || !isset($_POST['answer']))
			{
				throw new UnprocessableEntityError();
			}
			PdfFormDeployment::fromCrumb($_POST['crumb'])->getRespondent()->saveProgress($_POST['question'], $_POST['answer']);
			header('Content-type: application/json');
			echo '{"result":"success"}';
		exit();
		case 'submit':
			if (!isset($_POST['crumb']))
			{
				throw new UnprocessableEntityError();
			}
			$dep = PdfFormDeployment::fromCrumb($_POST['crumb']);
			list($complete, $answers) = $dep->getForm()->getQuestionAnswerMap($_POST);
			if ($complete)
			{
				$resp = $dep->getRespondent();
				$resp->saveAnswers($_POST)->markEnd();

				header('Location: ' . JRoute::_('index.php?option=com_pdf2form?task=complete&crumb='.$_POST['crumb']));
				exit();
			}
			else
			{
				$incomplete = array_filter($answers, function($ans)
				{
					return is_null($ans[0]);
				});
				require 'views/complete.php';
			}
		break;
		default:
			throw new NotFoundError('Not found');
	}
}
catch (NeedLoginError $ex)
{
	header('Location: ' . JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($_SERVER['REQUEST_URI'])));
	exit();
}
catch (HttpCodedError $ex)
{
	JError::raiseError($ex->getHttpCode(), $ex->getMessage());
}
catch (Exception $ex)
{
	error_log(var_export($ex, 1));
	JError::raiseError(500, 'Internal Server Error');
}

class HttpCodedError extends Exception
{
	private $httpCode;

	public function __construct($code, $msg = '')
	{
		parent::__construct($msg);
		$this->httpCode = $code;
	}

	public function getHttpCode()
	{
		return $this->httpCode;
	}
}
class NotFoundError extends HttpCodedError
{
	public function __construct($msg = '')
	{
		parent::__construct(404, $msg);
	}
}
class ForbiddenError extends HttpCodedError
{
	public function __construct($msg = '')
	{
		parent::__construct(403, $msg);
	}
}
class NeedLoginError extends ForbiddenError
{
}

class UnprocessableEntityError extends HttpCodedError
{
	public function __construct($msg = '')
	{
		parent::__construct(422, $msg);
	}
}

class PdfFormRespondent
{
	private $id, $started, $finished;

	public function __construct($depId)
	{
		if (!($uid = JFactory::getUser()->id))
		{
			throw new NeedLoginError;
		}
		$dbh = JFactory::getDBO();
		$dbh->setQuery('SELECT id, started, finished FROM #__pdf_form_respondents WHERE deployment_id = '.(int)$depId.' AND user_id = '.(int)$uid);
		if (($res = $dbh->loadAssoc()))
		{
			$this->id = $res['id'];
			$this->started = $res['started'];
			$this->finished = $res['finished'];
		}
		else
		{
			$dbh->execute('INSERT INTO #__pdf_form_respondents(deployment_id, user_id) VALUES ('.(int)$depId.', '.(int)$uid.')');
			$this->id = $dbh->insertid();
		}
	}

	public function getConfirmationCode($crumb)
	{
		return md5($this->id . $crumb);
	}

	public function saveAnswers($answers)
	{
		$dbh = JFactory::getDBO();
		foreach ($answers as $key=>$val)
		{
			if (!preg_match('/^question-(\d+)$/', $key, $qid))
			{
				continue;
			}
			$dbh->execute('INSERT INTO #__pdf_form_responses(respondent_id, question_id, answer_id) VALUES ('.$this->id.', '.$qid[1].', '.(int)$val.')');
		}
		return $this;
	}

	public function getAnswers()
	{
		$dbh = JFactory::getDBO();
		$dbh->setQuery('SELECT pfr.question_id, answer_id, pfa.id AS correct_answer_id, version FROM #__pdf_form_latest_responses_view pfr INNER JOIN #__pdf_form_questions pfq ON pfq.id = pfr.question_id INNER JOIN #__pdf_form_answers pfa ON pfr.question_id = pfa.question_id AND pfa.correct WHERE pfr.respondent_id = '.$this->id);
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
		$rv['summary']['score'] = number_format($rv['summary']['correct']*100/$rv['summary']['total'], 1);
		return $rv;
	}

	public function saveProgress($qid, $aid)
	{
		$dbh = JFactory::getDBO();
		$dbh->execute('DELETE FROM #__pdf_form_respondent_progress WHERE respondent_id = '.(int)$this->id.' AND question_id = '.(int)$qid);
		$dbh->execute('INSERT INTO #__pdf_form_respondent_progress(respondent_id, question_id, answer_id) VALUES ('.(int)$this->id.', '.(int)$qid.', '.(int)$aid.')');
		return $this;
	}

	public function getProgress()
	{
		$dbh = JFactory::getDBO();
		$dbh->setQuery('SELECT question_id, answer_id FROM #__pdf_form_respondent_progress WHERE respondent_id = '.(int)$this->id);
		return $dbh->loadAssocList('question_id');
	}

	public function getStartTime()
	{
		return $this->started;
	}

	public function getEndTime()
	{
		return $this->finished;
	}

	public function markStart()
	{
		$this->started = date('Y-m-d H:i:s');
		JFactory::getDBO()->execute('UPDATE #__pdf_form_respondents SET started = \''.$this->started.'\' WHERE started IS NULL AND id = '.(int)$this->id);
		return $this;
	}

	public function markEnd()
	{
		$this->started = date('Y-m-d H:i:s');
		JFactory::getDBO()->execute('UPDATE #__pdf_form_respondents SET finished = \''.$this->started.'\' WHERE id = '.(int)$this->id);
		return $this;
	}
}

class PdfFormDeployment
{
	const CRUMB_LEN = 20;
	private static $resultViews = array('confirmation', 'score', 'details');
	private $id, $formId, $startTime, $endTime, $resultsOpen, $resultsClosed, $timeLimit, $crumb, $userId, $userName, $errors = array();

	public static function forForm($formId)
	{
		$rv = array();

		$dbh = self::getDbh();
		$dbh->setQuery('SELECT id, form_id AS formId, start_time AS startTime, end_time AS endTime, results_open AS resultsOpen, time_limit AS timeLimit, crumb, results_closed AS resultsClosed, (SELECT name FROM #__users WHERE id = user_id) AS userName FROM #__pdf_form_deployments WHERE form_id = '.(int)$formId);
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

	public function getUserId()
	{
		return $this->userId;
	}

	public function getResults()
	{
		$dbh = self::getDBH();
		$dbh->setQuery(
			'SELECT name, email, started, finished, version, count(pfa.id)*100/count(pfr2.id) AS score
			FROM #__pdf_form_respondents pfr
			INNER JOIN #__users u ON u.id = pfr.user_id
			LEFT JOIN #__pdf_form_latest_responses_view pfr2 ON pfr2.respondent_id = pfr.id
			INNER JOIN #__pdf_form_questions pfq ON pfq.id = pfr2.question_id
			LEFT JOIN #__pdf_form_answers pfa ON pfa.id = pfr2.answer_id AND pfa.correct
			WHERE deployment_id = '.$this->id.'
			GROUP BY name, email, started, finished, version'
		);
		return $dbh->loadAssocList();
	}

	public function getForm()
	{
		static $form;
		if (!$form && $this->formId)
		{
			$form = new PdfForm($this->formId);
		}
		return $form;
	}

	public function getRespondent()
	{
		static $resp;
		if (!$resp && $this->id)
		{
			$resp = new PdfFormRespondent($this->id);
		}
		return $resp;
	}

	public static function load($id)
	{
		return self::find('id = '.((int)$id));
	}

	public static function fromCrumb($crumb)
	{
		return self::find('crumb = '.self::getDbh()->quote($crumb));
	}

	private static function find($where)
	{
		$dep = new PdfFormDeployment;
		$dbh = self::getDbh();
		$dbh->setQuery('SELECT id, form_id AS formId, start_time AS startTime, end_time AS endTime, results_open AS resultsOpen, time_limit AS timeLimit, crumb, results_closed AS resultsClosed FROM #__pdf_form_deployments WHERE '.$where);
		if (!($res = $dbh->loadAssoc()))
		{
			throw new NotFoundError('no such deployment');
		}
		foreach ($res as $k=>$v)
		{
			$dep->$k = $v;
		}
		return $dep;
	}

	public function getUserName()
	{
		return $this->userName;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getLink()
	{
		return 'https://'.$_SERVER['HTTP_HOST'].'/pdf2form/'.$this->crumb;
	}

	public function getStartTime()
	{
		return $this->startTime;
	}

	public function getEndTime()
	{
		return $this->endTime;
	}

	public function getActiveResults()
	{
		return $this->getState() == 'active' ? $this->resultsOpen : $this->resultsClosed;
	}

	public function getResultsOpen()
	{
		return $this->resultsOpen;
	}

	public function getResultsClosed()
	{
		return $this->resultsClosed;
	}

	public function getTimeLimit()
	{
		return $this->timeLimit;
	}

	public function getRealTimeLimit()
	{
		if (!$this->endTime)
		{
			return $this->timeLimit;
		}
		return min($this->timeLimit, (strtotime($this->endTime) - time())/60);
	}

	public function getCrumb()
	{
		return $this->crumb;
	}

	public function getState()
	{
		if ($this->endTime && strtotime($this->endTime) <= time())
		{
			return 'expired';
		}
		return (!$this->startTime || strtotime($this->startTime) <= time()) ? 'active' : 'pending';
	}

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
			if (!$update && $this->endTime && $this->startTime && strtotime($this->endTime) <= strtotime($this->startTime))
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

	public function getErrors($field)
	{
		return isset($this->errors[$field]) ? $this->errors[$field] : array();
	}

	public static function fromFormData($fid, $data)
	{
		$dep = new PdfFormDeployment;
		$dep->formId = $fid;
		foreach (array('startTime', 'endTime', 'resultsOpen', 'resultsClosed', 'timeLimit') as $key)
		{
			if (!isset($data[$key]))
			{
				throw new UnprocessableEntityError('expected a value to be supplied for '.$key);
			}
			$dep->$key = $data[$key];
		}
		$dep->crumb = str_replace(array('/', '+'), array('-', '-'), substr(base64_encode(openssl_random_pseudo_bytes(self::CRUMB_LEN + 1)), 0, self::CRUMB_LEN));
		return $dep;
	}

	private static function getDbh()
	{
		static $dbh;
		if (!$dbh)
		{
			$dbh = JFactory::getDBO();
		}
		return $dbh;
	}

	public function save($id = NULL)
	{
		$dbh = self::getDbh();
		if (is_null($id))
		{
			$dbh->execute(
				'INSERT INTO #__pdf_form_deployments(form_id, start_time, end_time, results_open, results_closed, time_limit, crumb, user_id) VALUES ('.
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
			'UPDATE #__pdf_form_deployments SET '.
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

class PdfForm
{
	private $errors = array(), $fname = NULL, $id = NULL, $pages = NULL, $title = NULL;

	private static function getDbh()
	{
		static $dbh;
		if (!$dbh)
		{
			$dbh = JFactory::getDBO();
		}
		return $dbh;
	}

	private static function imageBase()
	{
		$args = func_get_args();
		array_unshift($args,  JPATH_BASE.'/site/pdf2form/images');
		return implode('/', $args);
	}

	public static function getActiveList()
	{
		$dbh = self::getDbh();
		$dbh->setQuery(
			'SELECT pf.id, title, pf.created, (SELECT MAX(created) FROM #__pdf_form_questions WHERE form_id = pf.id) AS updated
			FROM #__pdf_forms pf
			WHERE title IS NOT NULL AND title != \'\' AND active = 1
			ORDER BY title'
		);
		return $dbh->loadAssocList();
	}

	public static function anyArchived()
	{
		$dbh = self::getDbh();
		$dbh->setQuery('SELECT 1 FROM #__pdf_forms WHERE title IS NOT NULL AND title != \'\' AND active = 0');
		return (bool)$dbh->loadResult();
	}

	public function __construct($id = NULL)
	{
		$this->id = (int)$id;
	}

	public function isStored()
	{
		if (!$this->id)
		{
			return false;
		}
		$dbh = self::getDbh();
		$dbh->setQuery('SELECT 1 FROM #__pdf_forms WHERE id = '.$this->id);
		return (bool)$dbh->loadResult();
	}

	public function hasErrors()
	{
		return (bool)$this->errors;
	}

	public function eachPage($fun)
	{
		if (!$this->id)
		{
			throw new UnprocessableEntityError('No pages exist for equally nonexistent form');
		}
		$base = self::imageBase($this->id);
		$dir = opendir($base);
		$images = array();
		while (($file = readdir($dir)))
		{
			if (preg_match('/^\d+[.]png$/', $file))
			{
				$images[] = $file;
			}
		}
		closedir($dir);
		natsort($images);
		$base = preg_replace('#^'.preg_quote(JPATH_BASE).'#', '', $base);
		$idx = 0;
		foreach ($images as $img)
		{
			$fun($base.'/'.$img, ++$idx);
		}
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function getId()
	{
		if ($this->id)
		{
			return $this->id;
		}
		$dbh = self::getDbh();
		$dbh->execute('INSERT INTO #__pdf_forms() VALUES ()');
		return ($this->id = $dbh->insertid());
	}

	public function renderPageImages()
	{
		try
		{
			$fid = $this->getId();
			mkdir(self::imageBase($fid));
			for ($this->pages = 1; ; ++$this->pages)
			{
				$im = new imagick($this->fname.'['.($this->pages - 1).']');
				$im->setImageFormat('png');
				$im->trimImage(0);
				$im->scaleImage(582,0);
				$im->sharpenImage(1,.5);
				$im->borderImage('white', 15, 15);
				$im->writeImage(self::imageBase($fid, $this->pages.'.png'));
			}
		}
		catch (ImagickException $ex)
		{
		}
	}

	public static function fromPostedFile($name)
	{
		$pdf = new PdfForm;
		if (!isset($_FILES[$name]))
		{
			$pdf->errors[] = 'Upload not posted (server error)';
		}
		else if ($_FILES[$name]['error'])
		{
			switch ($_FILES[$name]['error'])
			{
				case UPLOAD_ERR_INI_SIZE: case UPLOAD_ERR_FORM_SIZE:
					$pdf->errors[] = 'Upload failed, the file exceeds the maximum allowable size';
				break;
				case UPLOAD_ERR_PARTIAL:
					$pdf->errors[] = 'The upload did not complete. Please try again';
				break;
				case UPLOAD_ERR_NO_FILE:
					$pdf->errors[] = 'Please select a file to upload';
				break;
				case UPLOAD_ERR_NO_TMP_DIR: case UPLOAD_ERR_CANT_WRITE: case UPLOAD_ERR_EXTENSION:
					$pdf->errors[] = 'Upload failed due to server configuration';
				break;
				default:
					$pdf->errors[] = 'Upload failed for mysterious reasons';
			}
		}
		else
		{
			$pdf->fname = $_FILES[$name]['tmp_name'];
		}
		return $pdf;
	}

	public function setTitle($title)
	{
		$dbh = self::getDbh();
		$dbh->execute('UPDATE #__pdf_forms SET title = '.$dbh->quote(stripslashes($title)).' WHERE id = '.$this->getId());
		return $this;
	}

	public function getTitle()
	{
		if ($this->title)
		{
			return $this->title;
		}
		static $checked;
		if (!$checked)
		{
			$checked = true;
			$dbh = self::getDbh();
			$dbh->setQuery('SELECT title FROM #__pdf_forms WHERE id = '.$this->getId());
			$this->title = $dbh->loadResult();
		}
		return $this->title;
	}

	public function setPageLayout($pages)
	{
		$dbh = self::getDbh();
		$fid = $this->getId();
		$dbh->setQuery('SELECT MAX(version) FROM #__pdf_form_questions WHERE form_id = '.$fid);
		$version = $dbh->loadResult();
		if (!$version)
		{
			$version = 1;
		}
		else
		{
			++$version;
		}
		foreach ($pages as $pageNum=>$page)
		{
			foreach ($page as $groupNum=>$group)
			{
				$dbh->execute('INSERT INTO #__pdf_form_questions(form_id, page, top_dist, left_dist, height, width, version) VALUES ('.$fid.', '.((int)$pageNum).', '.((int)$group['top']).', '.((int)$group['left']).', '.((int)$group['height']).', '.((int)$group['width']).', '.$version.')');
				$groupId = $dbh->insertid();
				foreach ($group['answers'] as $answer)
				{
					$dbh->execute('INSERT INTO #__pdf_form_answers(question_id, top_dist, left_dist, correct) VALUES ('.$groupId.', '.((int)$answer['top']).', '.((int)$answer['left']).', '.($answer['correct'] == 'true' ? 1 : 0).')');
				}
			}
		}
		return $this;
	}

	public function getPageLayout($version = NULL)
	{
		$dbh = self::getDbh();
		$fid = $this->getId();
		if (!is_null($date))
		{
			$date = date('Y-m-d H:i:s', strtotime($date));
		}
		$dbh->setQuery(
			'SELECT pfa.id AS answer_id, question_id, page, correct, pfa.left_dist AS a_left, pfa.top_dist AS a_top, pfq.left_dist AS q_left, pfq.top_dist AS q_top, height, width
			FROM #__pdf_form_questions pfq
			LEFT JOIN #__pdf_form_answers pfa ON pfa.question_id = pfq.id
			WHERE form_id = '.$fid.' AND version = '.($version ? (int)$version : '(SELECT MAX(version) FROM #__pdf_form_questions WHERE form_id = '.$fid.')').'
			ORDER BY page, question_id'
		);
		$rv = array();
		foreach ($dbh->loadAssocList() as $answer)
		{
			if (!isset($rv[$answer['page']]))
			{
				$rv[$answer['page']] = array();
			}
			if (!isset($rv[$answer['page']][$answer['question_id']]))
			{
				$rv[$answer['page']][$answer['question_id']] = array(
					'left'    => $answer['q_left'],
					'top'     => $answer['q_top'],
					'height'  => $answer['height'],
					'width'   => $answer['width'],
					'answers' => array()
				);
			}
			$rv[$answer['page']][$answer['question_id']]['answers'][] = array('left' => $answer['a_left'], 'top' => $answer['a_top'], 'correct' => (bool)$answer['correct'], 'id' => $answer['answer_id']);
		}
		return $rv;
	}

	public function getQuestionAnswerMap($answers)
	{
		$dbh = self::getDBH();
		$fid = $this->getId();
		$dbh->setQuery(
			'SELECT pfq.id, pfa.id AS answer_id
			FROM #__pdf_form_questions pfq
			INNER JOIN #__pdf_form_answers pfa ON pfa.question_id = pfq.id AND pfa.correct
			WHERE form_id = '.$fid.' AND version = (SELECT max(version) FROM #__pdf_form_questions WHERE form_id = '.$fid.')');
		$rv = array();
		$complete = TRUE;
		foreach ($dbh->loadAssocList() as $row)
		{
			if (isset($answers['question-'.$row['id']]))
			{
				$rv[$row['id']] = array($answers['question-'.$row['id']], $row['answer_id']);
			}
			else
			{
				$rv[$row['id']] = array(NULL, $row['answer_id']);
				$complete = FALSE;
			}
		}
		return array($complete, $rv);
	}

	public function getQuestionCount()
	{
		$dbh = self::getDBH();
		$fid = $this->getId();
		$dbh->setQuery(
			'SELECT COUNT(*) FROM #__pdf_form_questions pfq WHERE form_id = '.$fid.' AND version = (SELECT max(version) FROM #__pdf_form_questions WHERE form_id = '.$fid.') AND (SELECT 1 FROM #__pdf_form_answers WHERE question_id = pfq.id LIMIT 1)'
		);
		return $dbh->loadResult();
	}
}

function timeDiff($secs)
{
	$seconds = array(1, 'second');
	$minutes = array(60 * $seconds[0], 'minute');
	$hours   = array(60 * $minutes[0], 'hour');
	$days    = array(24 * $hours[0],   'day');
	$weeks   = array(7  * $days[0],    'week');
	$rv = array();

	foreach (array($weeks, $days, $hours, $minutes, $seconds) as $step)
	{
		list($sec, $unit) = $step;
		$times = floor($secs / $sec);
		if ($times > 0)
		{
			$secs -= $sec * $times;
			$rv[] = $times . ' ' . $unit . ($times == 1 ? '' : 's');
			if (count($rv) == 2)
			{
				break;
			}
		}
		else if (count($rv))
		{
			break;
		}
	}
	return join(', ', $rv);
}

