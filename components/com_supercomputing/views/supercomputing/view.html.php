<?php

define('NEW_REQUEST_TEXT', 'Apply for new supercomputing allocation');
define('NEW_SUBMIT_LABEL', 'Submit allocation request');
define('RENEW_REQUEST_TEXT', 'Renew an existing supercomputing allocation');
define('RENEW_SUBMIT_LABEL', 'Submit allocation renewal request');
define('ADD_USERS_REQUEST_TEXT', 'Add users to an existing allocation');
define('ADD_USERS_SUBMIT_LABEL', 'Add users to allocation');

define('BAD_AUTHENTICITY_TOKEN', 'The authenticity token attached to your request did not match what the system was expecting. Try submitting the form again.');
define('BAD_CAPTCHA', 'The anti-spam security code you entered was not correct, please try again.');
define('MISSING_FIRST_NAME', 'Enter the PI\'s first name.');
define('MISSING_LAST_NAME', 'Enter the PI\'s last name.');
define('MISSING_EMAIL', 'Enter the PI\'s email address.');
define('MISSING_TELEPHONE', 'Enter the PI\'s telephone number.');
define('MISSING_ORGANIZATION', 'Enter the PI\'s organization.');
define('MISSING_MAILING_ADDRESS', 'Enter the PI\'s mailing address.');
define('MISSING_COMPUTING_TIME', 'Enter the number of CPU hours you are requesting to be allocated.');
define('MISSING_ASSOCIATION', 'Please provide information about your project\'s affiliation.');
define('MISSING_PROJECT_INFO', 'Please provide the additional information about your project appropriate for its association.');
define('MISSING_SOFTWARE', 'Please select the type of software you plan to use or fill in the \'Other\' field.');
define('GENERIC_ERROR', 'There was a problem submitting your request. Check below to make sure everything is in order.');

jimport('joomla.application.component.view');
require_once JPATH_ROOT.'/administrator/components/com_support/tables/ticket.php';

abstract class SuperComputingView extends JView
{
	private static $injected_resources = false;
	protected $request_type, $errors = array(), $fields = array();

	protected function set_title($title)
	{
		JFactory::getDocument()->setTitle($title);
	}

	protected function push_breadcrumb($name, $link)
	{
		JFactory::getApplication()->getPathway()->addItem($name, $link);
	}

	public function set_fields($fields) { $this->fields = $fields; return $this; }
	public function inherit_properties($view) 
	{
		$this->request_type = $view->request_type;
		$this->fields = $view->fields;
		$this->errors = $view->errors;
		return $this;
	}
	public function set_errors($errors) 
	{
		if (count($errors))
			if (array_key_exists('form', $errors))
				$errors['form'][] = GENERIC_ERROR;
			else
				$errors['form'] = array(GENERIC_ERROR);
		$this->errors = $errors; return $this; 
	}
	public function set_request_type($type) { $this->request_type = $type; return $this; }
	

	protected function error_class($field)
	{
		if (array_key_exists($field, $this->errors))
			echo ' class="error-field" ';
	}

	protected function add_error_class($field)
	{
		if (array_key_exists($field, $this->errors))
			echo ' error-field ';
	}
	
	protected function errors_on($field)
	{
		if (array_key_exists($field, $this->errors))
			if (count($this->errors[$field]) > 1)
			{
?>
<ul class="errors">
	<?php foreach ($this->errors[$field] as $error): ?>
		<li class="error"><?php echo $error; ?></li>
	<?php endforeach; ?>
</ul>
<?php
			}
			else
				echo '<p class="error">'.$this->errors[$field][0].'</p>';
	}

	protected function get_partial($name, $type = 'html')
	{
		require_once dirname(__FILE__).'/../'.$name.'/view.'.$type.'.php';
		$class = 'SuperComputingView'.$name;
		return new $class;
	}

	protected function attr($name, $default = '')
	{
		echo htmlentities(JRequest::getVar($name, $default));
	}

	protected function selected_if($name, $val)
	{
		if (JRequest::getVar($name, NULL) === $val)
			echo ' checked="checked" ';
	}

	protected function checked_if($name)
	{
		if (strtolower(JRequest::getVar($name, 'off')) === 'on')
			echo ' checked="checked" ';
	}
	public function display($tpl = NULL)
	{
		if ($tpl)
			parent::display($tpl);
		else
		{
			if (!self::$injected_resources)
			{
				$doc =& JFactory::getDocument();
				$doc->addStyleSheet('/components/com_supercomputing/supercomputing.css');
				$doc->addScript('/components/com_supercomputing/supercomputing.js');
				self::$injected_resources = true;
			}
			parent::display();
		}
	}
}

abstract class SuperComputingTicketView extends SuperComputingView
{
	private $body;

	abstract public function get_title();

	public function get_body()
	{
		if (!$this->body)
		{
			ob_start();
			$this->display('ticket');
			$this->body = ob_get_clean();
		}
		return $this->body;
	}

	public function send()
	{
		$title = $this->get_title();
		$body = $this->get_body();
		$user =& JFactory::getUser();
		$data = array(
			'id'        => NULL,
			'status'    => 0,
			'created'   => date('Y-m-d H:i:s'),
			'login'     => $user->guest ? NULL : $user->get('username'),
			'severity'  => 'normal',
			'owner'     => NULL,
			'category'  => 'Supercomputing allocation request',
			'summary'   => htmlentities($title),
			'report'    => htmlentities($body),
			'resolved'  => NULL,
			'email'     => $this->fields['pi']['email'],
			'name'      => $this->fields['pi']['first-name'] . ' ' . $this->fields['pi']['last-name'],
			'os'        => 'N/A',
			'browser'   => 'N/A',
			'ip'        => 'N/A',
			'hostname'  => 'N/A',
			'uas'       => 'N/A',
			'referrer'  => 'N/A',
			'cookies'   => 0,
			'instances' => 1,
			'section'   => 1,
			'group'     => 'supercomputingallocation'
		);
		$dbh =& JFactory::getDBO();
		$ticket = new SupportTicket($dbh);
		$ticket->bind($data);
		$ticket->store();
		$xhub =& XFactory::getHub();
		ximport('xhubhelper');
		XHubHelper::send_email($xhub->getCfg('hubSupportEmail'), 'NeesHUB Support, Ticket #'.$ticket->id.': '.$title, $body);
	}
}

class SuperComputingViewSuperComputing extends SuperComputingView
{
}
