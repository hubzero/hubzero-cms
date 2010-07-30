<?php

require_once dirname(__FILE__).'/lib/recaptcha/recaptchalib.php';
require_once dirname(__FILE__).'/views/supercomputing/view.html.php';

jimport('joomla.application.component.controller');


abstract class Recaptcha
{
	private static $public_key = '6Le_fbsSAAAAAErkFlbPNUp5n0zu_xND9GoGkff0';
	private static $private_key = '6Le_fbsSAAAAAAwzODm-Ats28z7G1ybxkmnc0MHw';

	public static function get_captcha()
	{
		return recaptcha_get_html(self::$public_key, NULL, array_key_exists('HTTPS', $_SERVER));
	}

	public static function check_response()
	{
		return recaptcha_check_answer(
			self::$private_key, 
			$_SERVER['REMOTE_ADDR'], 
			JRequest::getString('recaptcha_challenge_field', NULL), 
			JRequest::getString('recaptcha_response_field', NULL))->is_valid;
	}
}

class SuperComputingController extends JController
{
	private function redirect_guests()
	{
		$user =& JFactory::getUser();
		if ($user->guest)
		{
			$this->setRedirect('/login?return='.base64_encode('/supercomputing'));
			$this->redirect();
		}
	}

	public function display()
	{
		$this->redirect_guests();
		parent::display();	
	}

	public function request()
	{
		$this->redirect_guests();
		switch (JRequest::getString('request-type', NULL))
		{
			case NEW_REQUEST_TEXT:
				return $this->new_renew_form('new');
			CASE RENEW_REQUEST_TEXT:
				return $this->new_renew_form('renew');
			case ADD_USERS_REQUEST_TEXT:
				return $this->add_users_form();
			default:
				return $this->display();
		}
	}

	private static function validate_post()
	{
		$rv = array();
		if (!JRequest::checkToken())
			$rv['form'] = array(BAD_AUTHENTICITY_TOKEN);

		if (JFactory::getUser()->guest && !Recaptcha::check_response())
			$rv['captcha'] = array(BAD_CAPTCHA);
		
		return $rv;
	}

	private static function parse_user_info()
	{
		$errors = array();
		$people = array('pi' => array(), 'other-users' => array());
		$person_keys = array(
			'first-name' => MISSING_FIRST_NAME,
			'last-name' => MISSING_LAST_NAME,
			'email' => MISSING_EMAIL,
			'telephone' => MISSING_TELEPHONE,
			'organization' => MISSING_ORGANIZATION,
			'mailing-address' => MISSING_MAILING_ADDRESS
		);
		foreach ($person_keys as $key=>$err)
			if (($val = JRequest::getString("$key-pi", NULL)))
				$people['pi'][$key] = stripslashes($val);
			else
				$errors["$key-pi"] = array($err);
		
		$field_count = count($person_keys);
		$idx = 0;
		for ($idx = 0; array_key_exists('first-name-'.$idx, $_POST); ++$idx)
		{
			$other = array();
			foreach ($person_keys as $key=>$_err)
				if (!empty($_POST[$key.'-'.$idx]))
					$other[$key] = stripslashes($_POST[$key.'-'.$idx]);
				else
					break;
			
			if (count($other) == $field_count)
				$people['other-users'][] = $other;
		}
		return array($people, $errors);
	}

	private static function parse_allocation()
	{
		$errors = array();
		$alloc = array('software' => array());
	
		$alloc_keys = array(
			'computing-time' => MISSING_COMPUTING_TIME,
			'association' => MISSING_ASSOCIATION,
			'project-info' => MISSING_PROJECT_INFO,
		);
		foreach ($alloc_keys as $key=>$err)
			if (!empty($_POST[$key]))
				$alloc[$key] = stripslashes($_POST[$key]);
			else
				$errors[$key] = array($err);

		$software = array('abaqus', 'ansys', 'ls-dyna', 'opensees');
		foreach ($software as $sw)
			if (strtolower(JRequest::getString('software-'.$sw, 'off')) == 'on')
				$alloc['software'][] = $sw;
	
		if (!empty($_POST['software-other']))
			$alloc['software'][] = stripslashes($_POST['software-other']);
		elseif (!count($alloc['software']))
			$errors['software'] = array(MISSING_SOFTWARE);
	
		return array($alloc, $errors);
	}

	public function submit_new()
	{
		$this->redirect_guests();
		return $this->submit_full('new');
	}

	public function submit_renew()
	{
		$this->redirect_guests();
		return $this->submit_full('renew');
	}

	public function submit_full($req_type)
	{
		$this->redirect_guests();
		list($people, $user_errors) = self::parse_user_info();
		list($alloc, $alloc_errors) = self::parse_allocation();
		$errors = array_merge($user_errors, $alloc_errors, self::validate_post());
		if ($errors)
			$this->getView('allocationform', 'html')->set_request_type($req_type)->set_errors($errors)->display();	
		else
			$this->getView('success', 'html')->set_request_type($req_type)->set_fields(array_merge($people, $alloc))->display();
	}

	public function submit_add_users()
	{
		$this->redirect_guests();
		list($people, $user_errors) = self::parse_user_info();
		$errors = array_merge($user_errors, self::validate_post());
		if ($errors)
			$this->getView('addusersform', 'html')->set_errors($errors)->display();
		else
			$this->getView('adduserssuccess', 'html')->set_request_type($req_type)->set_fields($people)->display();
	}

	private function new_renew_form($req_type)
	{
		$this->getView('allocationform', 'html')->set_request_type($req_type)->display();
	}

	private function add_users_form()
	{
		$this->getView('addusersform', 'html')->display();
	}
}

$cont = new SuperComputingController();
$cont->execute(JRequest::getCmd('task'));
