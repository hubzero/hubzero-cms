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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Courses controller class
 */
class CoursesControllerForm extends Hubzero_Controller {
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		// @FIXME: what's this? Is this when a test is completed?
		if ($this->_task == 'index' && preg_match('#^/pdf2form/([-a-zA-Z0-9]{20})(?:$|\?)#', isset($_SERVER['SCRIPT_URL']) ? $_SERVER['SCRIPT_URL'] : $_SERVER['REDIRECT_SCRIPT_URL'], $ma))
		{
			$params = $_SERVER['argv']; 
			$location = "/courses?controller=form&task=complete&crumb=".$ma[1];
			if($params)
			{
				$location .= "&".implode("&", $params);
			}
			header("Location: {$location}");
			exit();
		}

		// Get the user
		$this->juser = JFactory::getUser();

		parent::execute();
	}

	/**
	 * Method to set the document path
	 * 
	 * @return     void
	 */
	public function _buildPathway()
	{
		$pathway =& JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		$pathway->addItem(
			JText::_(strtoupper('Forms')),
			'index.php?option=' . $this->_option . '&controller=forms&task=index'
		);

	}

	/**
	 * Method to build and set the document title
	 * 
	 * @return     void
	 */
	public function _buildTitle()
	{
		// Set the title used in the view
		$this->_title = JText::_('Forms');

		//set title of browser window
		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Default index view of all forms
	 * 
	 * @return     void
	 */
	public function indexTask()
	{
		// Check authorization
		// @FIXME: only admins should see ALL exams
		$this->authorize();

		// Add stylesheets and scripts
		$this->_getStyles($this->_option, $this->_controller . '.css');

		$this->_getScripts('assets/js/' . $this->_task);
		$this->_getScripts('/assets/js/select');

		// Add tablesorter
		Hubzero_Document::addSystemStylesheet('tablesorter.themes.blue.css');
		Hubzero_Document::addSystemScript('jquery.tablesorter.min');

		// Set the title and pathway
		$this->_buildTitle();
		$this->_buildPathway();

		if(!isset($this->view->errors))
		{
			$this->view->errors = array();
		}

		// Display
		$this->view->display();
	}

	/**
	 * Upload a PDF and render images
	 * 
	 * @return     void
	 */
	public function uploadTask()
	{
		// Check authorization
		$this->authorize();
		$pdf = PdfForm::fromPostedFile('pdf');

		// No error, then render the images
		if (!$pdf->hasErrors())
		{
			$pdf->renderPageImages();
		}

		// If there were errors, jump back to the index view and display them
		if ($pdf->hasErrors())
		{
			// @FIXME: get these errors back to the index view
			$this->view->errors = $pdf->getErrors();
			$this->indexTask();
		}
		else
		{
			// Just return JSON
			if (JRequest::getInt('no_html', false))
			{
				echo json_encode(array('success'=>true, 'id'=>$pdf->getId()));
				exit();
			}
			else // Otherwise, redirect
			{
				$this->setRedirect(
					JRoute::_('index.php?option=com_courses&controller=form&task=layout&formId=' . $pdf->getId(), false),
					JText::_('PDF upload successfull'),
					'passed'
				);
				return;
			}
		}
	}

	public function layoutTask()
	{
		// Check authorization
		$this->authorize();

		// Add stylesheets and scripts
		$this->_getStyles($this->_option, $this->_controller . '.css');
		$this->_getScripts('assets/js/' . $this->_task);

		// Set the title and pathway
		$this->_buildTitle();
		$this->_buildPathway();

		$this->view->pdf = new PdfForm($this->assertFormId());

		$this->view->title = $this->view->pdf->getTitle();
		//$path->addItem(($title ? htmlentities($title) : 'Layout: new form'), $_SERVER['REQUEST_URI']);

		$this->view->display();
	}

	public function saveLayoutTask()
	{
		// Check authorization
		$this->authorize();

		$pdf = $this->assertExistentForm(); 
		$pdf->setTitle($_POST['title']);

		if (isset($_POST['pages']))
		{
			$pdf->setPageLayout($_POST['pages']);
		}

		echo json_encode(array('result'=>'success'));
		exit();
	}

	public function deployTask()
	{
		// Check authorization
		$this->authorize();

		// Add stylesheets and scripts
		$this->_getStyles($this->_option, $this->_controller . '.css');
		$this->_getScripts('assets/js/timepicker');
		$this->_getScripts('assets/js/' . $this->_task);

		$this->view->pdf = $this->assertExistentForm();
		$this->view->dep = new PdfFormDeployment;

		$this->view->title = $this->view->pdf->getTitle();

		//$path->addItem('Deploy: '.htmlentities($title), $_SERVER['REQUEST_URI']);

		$this->view->display();
	}

	public function createDeploymentTask()
	{
		if(!$deployment = JRequest::getVar('deployment'))
		{
			JError::raiseError(422, 'No deployment provided');
		}

		$pdf = $this->assertExistentForm(); 
		$dep = PdfFormDeployment::fromFormData($pdf->getId(), $deployment);

		if ($dep->hasErrors())
		{
			$this->deployTask();
		}
		else
		{
			if ($_GET['tmpl'] == 'component')
			{
				echo json_encode(array('success'=>true, 'id'=>$dep->save(), 'formId'=>$pdf->getId()));
				exit();
			}
			else
			{
				$this->setRedirect(
					JRoute::_('index.php?option=com_courses&controller=form&task=showDeployment&id='.$dep->save().'&formId='.$pdf->getId(), false),
					JText::_('Deployment successfully created'),
					'passed'
				);
				return;
			}
		}
	}


	public function updateDeploymentTask()
	{
		if(!$deployment = JRequest::getVar('deployment'))
		{
			JError::raiseError(422, 'No deployment provided');
		}

		if(!$deploymentId = JRequest::getInt('deploymentId'))
		{
			JError::raiseError(422, 'No deployment ID provided');
		}

		$pdf = $this->assertExistentForm(); 
		$dep = PdfFormDeployment::fromFormData($pdf->getId(), $deployment);

		if ($dep->hasErrors(NULL, TRUE))
		{
			$this->showDeployment();
		}
		else
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_courses&controller=form&task=showDeployment&id='.$dep->save($deploymentId).'&formId='.$pdf->getId(), false),
				JText::_('Deployment successfully updated'),
				'passed'
			);
			return;
		}
	}

	public function showDeploymentTask()
	{
		if(!$id = JRequest::getInt('id', false))
		{
			JError::raiseError(422, 'No form identifier supplied');
		}

		$this->_getStyles($this->_option, $this->_controller . '.css');
		$this->_getScripts('assets/js/' . $this->_task);
		$this->_getScripts('assets/js/timepicker');

		// Add tablesorter
		Hubzero_Document::addSystemStylesheet('tablesorter.themes.blue.css');
		Hubzero_Document::addSystemScript('jquery.tablesorter.min');

		$this->view->pdf = $this->assertExistentForm();

		$this->view->title = 'Deployment: '.htmlentities($this->view->pdf->getTitle());
		//$doc->setTitle($title);
		//$path->addItem($title, $_SERVER['REQUEST_URI']);

		$this->view->dep = PdfFormDeployment::load($id);

		$this->view->display();
	}

	public function completeTask()
	{
		if(!$crumb = JRequest::getVar('crumb', false))
		{
			JError::raiseError(422);
		}

		$dep = PdfFormDeployment::fromCrumb($crumb);
		$dbg = JRequest::getVar('dbg', false);

		switch ($dep->getState())
		{
			case 'pending': 
				throw new ForbiddenError('This deployment is not yet available');
			case 'expired': 
				require '../views/results/'.$dep->getResultsClosed().'.php'; 
			break;
			case 'active':
				$incomplete = array();
				$resp = $dep->getRespondent();
				require $resp->getEndTime() ? 'views/results/'.$dep->getResultsOpen().'.php' : 'views/complete.php';
		}
	}

	public function startWorkTask()
	{
		if(!$crumb = JRequest::getVar('crumb', false))
		{
			JError::raiseError(422);
		}

		PdfFormDeployment::fromCrumb($crumb)->getRespondent()->markStart();

		header('Location: /courses?controller=form&task=complete&crumb='.$_POST['crumb'].(isset($_POST['tmpl']) ? '&tmpl='.$_POST['tmpl'] : ''));
		exit();
	}

	public function saveProgressTask()
	{
		if (!isset($_POST['crumb']) || !isset($_POST['question']) || !isset($_POST['answer'])) {
			throw new UnprocessableEntityError();
		}
		PdfFormDeployment::fromCrumb($_POST['crumb'])->getRespondent()->saveProgress($_POST['question'], $_POST['answer']);
		header('Content-type: application/json');
		echo '{"result":"success"}';
		exit();
	}

	public function submitTask()
	{
		if(!$crumb = JRequest::getVar('crumb', false))
		{
			JError::raiseError(422);
		}

		$dep = PdfFormDeployment::fromCrumb($crumb);

		list($complete, $answers) = $dep->getForm()->getQuestionAnswerMap($_POST);

		if ($complete)
		{
			$resp = $dep->getRespondent();
			$resp->saveAnswers($_POST)->markEnd();

			$this->setRedirect(JRoute::_('index.php?option=com_courses&controller=form&task=complete&crumb='.$crumb));
			return;
		}
		else
		{
			$incomplete = array_filter($answers, function($ans) { return is_null($ans[0]); });
			require '../views/complete.php';
		}
	}

	/**
	 * Check authorization
	 * 
	 * @return     void
	 */
	public function authorize()
	{
		// Make sure they're logged in
		if($this->juser->get('guest'))
		{
			$return = base64_encode(JRoute::_('index.php?option=' . $this->_option . '&controller=form'));
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . $return),
				$message,
				'warning'
			);
			return;
		}

		// Check for super admins
		if($this->juser->usertype == 'Super Administrator')
		{
			// Let them through
		}
		else
		{
			// Otherwise, a course id should be provided, and we need to make sure they are authorized
			JError::raiseError(403, 'Not authorized');
		}
	}

	public function assertFormId()
	{
		if (isset($_POST['formId']))
		{
			return $_POST['formId'];
		}
		if (isset($_GET['formId']))
		{
			return $_GET['formId'];
		}

		JError::raiseError(422, 'No form identifier supplied');
	}

	public function assertExistentForm()
	{
		$pdf = new PdfForm($this->assertFormId());

		if (!$pdf->isStored())
		{
			JError::raiseError(404, 'No form matches identifier');
		}

		return $pdf;
	}
}

class HttpCodedError extends Exception 
{
	private $httpCode;

	public function __construct($code, $msg = '') {
		parent::__construct($msg);
		$this->httpCode = $code;
	}

	public function getHttpCode() {
		return $this->httpCode;
	}
}
class NotFoundError extends HttpCodedError 
{
	public function __construct($msg = '') {
		parent::__construct(404, $msg);
	}
}
class ForbiddenError extends HttpCodedError
{
	public function __construct($msg = '') {
		parent::__construct(403, $msg);
	}
}
class NeedLoginError extends ForbiddenError {
}

class UnprocessableEntityError extends HttpCodedError
{
	public function __construct($msg = '') {
		parent::__construct(422, $msg);
	}
}

class PdfFormRespondent
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
			'SELECT name, email, started, finished, version, count(pfa.id)*100/count(pfr2.id) AS score
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

	public function getRespondent() {
		static $resp;
		if (!$resp && $this->id) {
			$resp = new PdfFormRespondent($this->id);
		}
		return $resp;
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

	public function getLink() {
		return 'https://'.$_SERVER['HTTP_HOST'].'/pdf2form/'.$this->crumb;
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
			if (!$update && $this->endTime && $this->startTime && strtotime($this->endTime) <= strtotime($this->startTime)) {
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

class PdfForm
{
	private $errors = array(), $fname = NULL, $id = NULL, $pages = NULL, $title = NULL;

	private static function getDbh() {
		static $dbh;
		if (!$dbh) {
			$dbh = JFactory::getDBO();
		}
		return $dbh;
	}

	public static function getActiveList() {
		$dbh = self::getDbh();
		$dbh->setQuery(
			'SELECT pf.id, title, pf.created, (SELECT MAX(created) FROM #__courses_form_questions WHERE form_id = pf.id) AS updated 
			FROM #__courses_forms pf  
			WHERE title IS NOT NULL AND title != \'\' AND active = 1 
			ORDER BY title'
		);
		return $dbh->loadAssocList();
	}

	public static function anyArchived() {
		$dbh = self::getDbh();
		$dbh->setQuery('SELECT 1 FROM #__courses_forms WHERE title IS NOT NULL AND title != \'\' AND active = 0');
		return (bool)$dbh->loadResult();
	}

	public function __construct($id = NULL) {
		$this->id = (int)$id;
	}

	public function isStored() {
		if (!$this->id) {
			return false;
		}
		$dbh = self::getDbh();
		$dbh->setQuery('SELECT 1 FROM #__courses_forms WHERE id = '.$this->id);
		return (bool)$dbh->loadResult();
	}

	public function hasErrors() {
		return (bool)$this->errors;
	}
	
	public function eachPage($fun) {
		if (!$this->id) {
			throw new UnprocessableEntityError('No pages exist for equally nonexistent form');
		}
		// @FIXME: create form base param
		$base = '/www/myhub/site/courses/forms/'.$this->id;
		$dir = opendir($base);
		$images = array();
		while (($file = readdir($dir))) {
			if (preg_match('/^\d+[.]png$/', $file)) {
				$images[] = $file;
			}
		}
		closedir($dir);
		natsort($images);
		$base = preg_replace('#^'.preg_quote(JPATH_BASE).'#', '', $base);
		$idx = 0;
		foreach ($images as $img) {
			$fun($base.'/'.$img, ++$idx);
		}
	}

	public function getErrors() { 
		return $this->errors; 
	}

	public function getId() {
		if ($this->id) {
			return $this->id;
		}
		$dbh = self::getDbh();
		$dbh->execute('INSERT INTO jos_courses_forms() VALUES ()');
		return ($this->id = $dbh->insertid());
	}

	public function renderPageImages() {
		try {
			$fid = $this->getId();
			// @FIXME: clean up folder creation
			mkdir('/www/myhub/site/courses/forms/'.$fid);
			for ($this->pages = 1; ; ++$this->pages) {
				$im = new imagick($this->fname.'['.($this->pages - 1).']');
				$im->setImageFormat('png');
				file_put_contents('/www/myhub/site/courses/forms/'.$fid.'/'.$this->pages.'.png', (string)$im);
			}
		}
		catch (ImagickException $ex) {
		}
	}

	public static function fromPostedFile($name) {
		$pdf = new PdfForm;
		if (!isset($_FILES[$name])) {
			$pdf->errors[] = 'Upload not posted (server error)';
		}
		else if ($_FILES[$name]['error']) {
			switch ($_FILES[$name]['error']) {
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
		else {
			$pdf->fname = $_FILES[$name]['tmp_name'];
		}
		return $pdf;
	}

	public function setTitle($title) {
		$dbh = self::getDbh();
		$dbh->execute('UPDATE #__courses_forms SET title = '.$dbh->quote(stripslashes($title)).' WHERE id = '.$this->getId());
		return $this;
	}

	public function getTitle() {
		if ($this->title) {
			return $this->title;
		}
		static $checked;
		if (!$checked) {
			$checked = true;
			$dbh = self::getDbh();
			$dbh->setQuery('SELECT title FROM #__courses_forms WHERE id = '.$this->getId());
			$this->title = $dbh->loadResult();
		}
		return $this->title;
	}

	public function setPageLayout($pages) {
		$dbh = self::getDbh();
		$fid = $this->getId();
		$dbh->setQuery('SELECT MAX(version) FROM #__courses_form_questions WHERE form_id = '.$fid);
		$version = $dbh->loadResult();
		if (!$version) {
			$version = 1;
		}
		else {
			++$version;
		}
		foreach ($pages as $pageNum=>$page) {
			foreach ($page as $groupNum=>$group) {
				$dbh->execute('INSERT INTO #__courses_form_questions(form_id, page, top_dist, left_dist, height, width, version) VALUES ('.$fid.', '.((int)$pageNum).', '.((int)$group['top']).', '.((int)$group['left']).', '.((int)$group['height']).', '.((int)$group['width']).', '.$version.')');
				$groupId = $dbh->insertid();
				foreach ($group['answers'] as $answer) {
					$dbh->execute('INSERT INTO #__courses_form_answers(question_id, top_dist, left_dist, correct) VALUES ('.$groupId.', '.((int)$answer['top']).', '.((int)$answer['left']).', '.($answer['correct'] == 'true' ? 1 : 0).')');
				}
			}
		}
		return $this;
	}

	public function getPageLayout($version = NULL) {
		$dbh = self::getDbh();
		$fid = $this->getId();
		if (isset($date) && !is_null($date)) {
			$date = date('Y-m-d H:i:s', strtotime($date));
		}
		$dbh->setQuery(
			'SELECT pfa.id AS answer_id, question_id, page, correct, pfa.left_dist AS a_left, pfa.top_dist AS a_top, pfq.left_dist AS q_left, pfq.top_dist AS q_top, height, width
			FROM #__courses_form_questions pfq
			LEFT JOIN #__courses_form_answers pfa ON pfa.question_id = pfq.id
			WHERE form_id = '.$fid.' AND version = '.($version ? (int)$version : '(SELECT MAX(version) FROM #__courses_form_questions WHERE form_id = '.$fid.')').'
			ORDER BY page, question_id'
		);
		$rv = array();
		foreach ($dbh->loadAssocList() as $answer) {
			if (!isset($rv[$answer['page']])) {
				$rv[$answer['page']] = array();
			}
			if (!isset($rv[$answer['page']][$answer['question_id']])) {
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

	public function getQuestionAnswerMap($answers) {
		$dbh = self::getDBH();
		$fid = $this->getId();
		$dbh->setQuery(
			'SELECT pfq.id, pfa.id AS answer_id 
			FROM #__courses_form_questions pfq 
			INNER JOIN #__courses_form_answers pfa ON pfa.question_id = pfq.id AND pfa.correct
			WHERE form_id = '.$fid.' AND version = (SELECT max(version) FROM #__courses_form_questions WHERE form_id = '.$fid.')');
		$rv = array();
		$complete = TRUE;
		foreach ($dbh->loadAssocList() as $row) {
			if (isset($answers['question-'.$row['id']])) {
				$rv[$row['id']] = array($answers['question-'.$row['id']], $row['answer_id']);
			}
			else {
				$rv[$row['id']] = array(NULL, $row['answer_id']);
				$complete = FALSE;
			}
		}
		return array($complete, $rv);
	}

	public function getQuestionCount() {
		$dbh = self::getDBH();
		$fid = $this->getId();
		$dbh->setQuery(
			'SELECT COUNT(*) FROM #__courses_form_questions pfq WHERE form_id = '.$fid.' AND version = (SELECT max(version) FROM #__courses_form_questions WHERE form_id = '.$fid.') AND (SELECT 1 FROM #__courses_form_answers WHERE question_id = pfq.id LIMIT 1)'
		);
		return $dbh->loadResult();
	}
}

function timeDiff($secs) {
	$seconds = array(1, 'second');
	$minutes = array(60 * $seconds[0], 'minute');
	$hours   = array(60 * $minutes[0], 'hour');
	$days    = array(24 * $hours[0],   'day');
	$weeks   = array(7  * $days[0],    'week');
	$rv = array();

	foreach (array($weeks, $days, $hours, $minutes, $seconds) as $step) {
		list($sec, $unit) = $step;
		$times = floor($secs / $sec);
		if ($times > 0) {
			$secs -= $sec * $times;
			$rv[] = $times . ' ' . $unit . ($times == 1 ? '' : 's');
			if (count($rv) == 2) {
				break;
			}
		}
		else if (count($rv)) {
			break;
		}
	}
	return join(', ', $rv);
}