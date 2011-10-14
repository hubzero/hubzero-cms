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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

/**
 * Short description for 'FeedbackController'
 * 
 * Long description (if any) ...
 */
class FeedbackController extends Hubzero_Controller
{

	/**
	 * Short description for 'execute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->_task = JRequest::getVar( 'task', '', 'post' );
		if (!$this->_task) {
			$this->_task = JRequest::getVar( 'task', '', 'get' );
		}

		switch ($this->_task)
		{
			// Image management
			case 'upload':          $this->upload();          break;
			case 'img':             $this->img();             break;
			case 'delete':          $this->delete();          break;

			// Processors
			case 'sendsuggestions': $this->sendsuggestions(); break;
			case 'sendstory':       $this->sendstory();       break;
			case 'sendreport':      $this->sendreport();      break;

			// Views
			case 'suggestions':     $this->suggestions();     break;
			case 'success_story':   $this->success_story();   break;
			case 'report_problems': $this->report_problems(); break;
			case 'poll':            $this->poll();            break;
			case 'main':            $this->main();            break;

			default: $this->main(); break;
		}
	}

	/**
	 * Short description for '_buildPathway'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function _buildPathway()
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();

		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option='.$this->_option
			);
		}
		if ($this->_task) {
			$pathway->addItem(
				JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
				'index.php?option='.$this->_option.'&task='.$this->_task
			);
		}
	}

	/**
	 * Short description for '_buildTitle'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function _buildTitle()
	{
		$this->_title = JText::_(strtoupper($this->_option));
		if ($this->_task) {
			$this->_title .= ': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
		}
		$document =& JFactory::getDocument();
		$document->setTitle( $this->_title );
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------


	/**
	 * Short description for 'login'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function login()
	{
		$view = new JView( array('name'=>'login') );
		$view->title = $this->_title;
		$view->msg   = $this->_msg;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'main'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function main()
	{
		// Check if wishlist component entry is there
		$this->database->setQuery( "SELECT c.id FROM #__components as c WHERE c.option='com_wishlist' AND enabled=1" );
		$wishlist = $this->database->loadResult();
		$wishlist = $wishlist ? 1 : 0;

		// Check if xpoll component entry is there
		$this->database->setQuery( "SELECT c.id FROM #__components as c WHERE c.option='com_xpoll' AND enabled=1" );
		$xpoll = $this->database->loadResult();
		$xpoll = $xpoll ? 1 : 0;

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();

		// Output HTML
		$view = new JView( array('name'=>'main') );
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->wishlist = $wishlist;
		$view->xpoll = $xpoll;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'success_story'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function success_story()
	{
		// Incoming
		$quote = array();
		$quote['long'] = JRequest::getVar('quote', '', 'post');
		$quote['short'] = JRequest::getVar('short_quote', '', 'post');

		// Generate a CAPTCHA
		$captcha = array();
		$captcha['operand1'] = rand(0,10);
		$captcha['operand2'] = rand(0,10);
		$captcha['sum'] = $captcha['operand1'] + $captcha['operand2'];
		$captcha['key'] = $this->_generate_hash($captcha['sum'],date('j'));

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();

		if ($this->juser->get('guest')) {
			$this->_msg = JText::_('To submit a success story, you need to be logged in. Please login using the form below:');
			$this->login();
			return;
		}

		// Output HTML
		$view = new JView( array('name'=>'story') );
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->user = $this->_getUser();
		$view->quote = $quote;
		$view->captcha = $captcha;
		$view->verified = ($this->juser->get('guest')) ? 0 : 1;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'poll'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function poll()
	{
		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();

		// Output HTML
		$view = new JView( array('name'=>'poll') );
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->task = $this->_task;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'suggestions'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function suggestions()
	{
		// Incoming
		$suggestion = array();
		$suggestion['for'] = JRequest::getVar( 'for', '' );
		$suggestion['idea'] = '';

		// Generate a CAPTCHA
		$suggestion['operand1'] = rand(0,10);
		$suggestion['operand2'] = rand(0,10);
		$suggestion['sum'] = $suggestion['operand1'] + $suggestion['operand2'];
		$suggestion['key'] = $this->_generate_hash($suggestion['sum'],date('j'));

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();

		// Output HTML
		$view = new JView( array('name'=>'suggestions') );
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->user = $this->_getUser();
		$view->suggestion = $suggestion;
		$view->verified = ($this->juser->get('guest')) ? 0 : 1;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'report_problems'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function report_problems()
	{
		// Get browser info
		ximport('Hubzero_Browser');
		$browser = new Hubzero_Browser();

		$problem = array(
			'os' => $browser->getOs(),
			'osver' => $browser->getOsVersion(),
			'browser' => $browser->getBrowser(),
			'browserver' => $browser->getBrowserVersion(),
			'topic' => '',
			'short' => '',
			'long' => '',
			'referer' => JRequest::getVar( 'HTTP_REFERER', NULL, 'server' ),
			'tool' => JRequest::getVar( 'tool', '' )
		);

		$view = new JView( array('name'=>'report') );
					 
		// Generate a CAPTCHA
		JPluginHelper::importPlugin('support');
		$dispatcher =& JDispatcher::getInstance();
		$view->captchas = $dispatcher->trigger('onGetComponentCaptcha');
		//$problem['operand1'] = rand(0,10);
		//$problem['operand2'] = rand(0,10);
		//$problem['sum'] = $problem['operand1'] + $problem['operand2'];
		//$problem['key'] = $this->_generate_hash($problem['sum'],date('j'));

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();

		// Output HTML
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->reporter = $this->_getUser();
		$view->problem = $problem;
		$view->verified = $this->_isVerified();
		$view->file_types = $this->config->get('file_ext');
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//----------------------------------------------------------
	// Processors
	//----------------------------------------------------------


	/**
	 * Short description for 'sendstory'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function sendstory()
	{
		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();

		// Initiate class and bind posted items to database fields
		$row = new FeedbackQuotes( $this->database );
		if (!$row->bind( $_POST )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		$user = array('uid'=>$row->userid, 'name'=>$row->fullname, 'org'=>$row->org, 'email'=>$row->useremail );

		// Check that a story was entered
		if (!$row->quote) {
			$this->setError(JText::_('COM_FEEDBACK_ERROR_MISSING_STORY'));

			// Generate a CAPTCHA
			$captcha = array();
			$captcha['operand1'] = rand(0,10);
			$captcha['operand2'] = rand(0,10);
			$captcha['sum'] = $captcha['operand1'] + $captcha['operand2'];
			$captcha['key'] = $this->_generate_hash($captcha['sum'],date('j'));

			// Output HTML
			$view = new JView( array('name'=>'story') );
			$view->title = $this->_title;
			$view->option = $this->_option;
			$view->task = $this->_task;
			$view->user = $user;
			$view->quote = $row->quote;
			$view->captcha = $captcha;
			$view->verified = JRequest::getInt( 'verified', 0 );
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
			return;
		}

		// Code cleaner for xhtml transitional compliance
		$row->quote = Hubzero_View_Helper_Html::purifyText($row->quote);
		$row->quote = str_replace( '<br>', '<br />', $row->quote );
		$row->date  = date( 'Y-m-d H:i:s', time() );
		$row->picture = basename($row->picture);

		// Check content
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Store new content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Output HTML
		$view = new JView( array('name'=>'story', 'layout'=>'thanks') );
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->user = $user;
		$view->quote = $row->quote;
		$view->picture = $row->picture;
		$view->config = $this->config;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'sendsuggestions'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function sendsuggestions()
	{
		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();

		// Incoming
		$suggester  = array_map('trim', $_POST['suggester']);
		$suggestion = array_map('trim', $_POST['suggestion']);
		$suggester  = array_map(array('Hubzero_View_Helper_Html','purifyText'), $suggester);
		$suggestion = array_map(array('Hubzero_View_Helper_Html','purifyText'), $suggestion);

		// Make sure email address is valid
		$validemail = $this->_check_validEmail($suggester['email']);

		// Prep a new math question and hash in case any form validation fails
		$suggestion['operand1'] = rand(0,10);
		$suggestion['operand2'] = rand(0,10);
		$sum = $suggestion['operand1'] + $suggestion['operand2'];
		$suggestion['key'] = $this->_generate_hash($sum,date('j'));

		if ($suggester['name'] && $suggestion['for'] && $suggestion['idea'] && $validemail) {
			// Are the logged in?
			if ($this->juser->get('guest')) {
				// No - don't trust user
				// Check CAPTCHA
				$key = JRequest::getInt( 'krhash', 0 );
				$answer = JRequest::getInt( 'answer', 0 );
				$answer = $this->_generate_hash($answer,date('j'));

				if ($answer != $key) {
					$view = new JView( array('name'=>'suggestions') );
					$view->title = $this->_title;
					$view->option = $this->_option;
					$view->task = $this->_task;
					$view->user = $suggester;
					$view->suggestion = $suggestion;
					$view->verified = ($this->juser->get('guest')) ? 0 : 1;
					$view->setError(3);
					$view->display();
					return;
				}
			}

			// Get user's IP and domain
			$ip = $this->_ip_address();
			$hostname = gethostbyaddr(JRequest::getVar('REMOTE_ADDR','','server'));

			// Quick spam filter
			$spam = $this->_detect_spam($suggestion['idea'], $ip);
			if ($spam) {
				// Output form with error messages
				$view = new JView( array('name'=>'suggestions') );
				$view->title = $this->_title;
				$view->option = $this->_option;
				$view->task = $this->_task;
				$view->user = $suggester;
				$view->suggestion = $suggestion;
				$view->verified = ($this->juser->get('guest')) ? 0 : 1;
				$view->setError(1);
				$view->display();
				return;
			}

			// Get some email settings
			$jconfig =& JFactory::getConfig();

			$admin   = $jconfig->getValue('config.mailfrom');
			$subject = $jconfig->getValue('config.sitename').' '.JText::_('COM_FEEDBACK_SUGGESTIONS');
			$from    = $jconfig->getValue('config.sitename').' '.JText::_('COM_FEEDBACK_SUGGESTIONS_FORM');
			$hub     = array('email' => $suggester['email'], 'name' => $from);

			// Generate e-mail message
			$message  = (!$this->juser->get('guest')) ? JText::_('COM_FEEDBACK_VERIFIED_USER')."\r\n" : '';
			$message .= ($suggester['login']) ? JText::_('COM_FEEDBACK_USERNAME').': '. $suggester['login'] ."\r\n" : '';
			$message .= JText::_('COM_FEEDBACK_NAME').': '. $suggester['name'] ."\r\n";
			$message .= JText::_('COM_FEEDBACK_AFFILIATION').': '. $suggester['org'] ."\r\n";
			$message .= JText::_('COM_FEEDBACK_EMAIL').': '. $suggester['email'] ."\r\n";
			$message .= JText::_('COM_FEEDBACK_FOR').': '. $suggestion['for'] ."\r\n";
			$message .= JText::_('COM_FEEDBACK_IDEA').': '. $suggestion['idea'] ."\r\n";

			// Send e-mail
			ximport('Hubzero_Toolbox');
			Hubzero_Toolbox::send_email($admin, $subject, $message);

			// Get their browser and OS
			ximport('Hubzero_Browser');
			$hbrowser = new Hubzero_Browser();

			$os = $hbrowser->getOs();
			$os_version = $hbrowser->getOsVersion();
			$browser = $hbrowser->getBrowser();
			$browser_ver = $hbrowser->getBrowserVersion();

			// Create new support ticket
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'ticket.php' );

			$data = array();
			$data['id']        = NULL;
			$data['status']    = 0;
			$data['created']   = date( "Y-m-d H:i:s" );
			$data['login']     = $suggester['login'];
			$data['severity']  = 'normal';
			$data['owner']     = NULL;
			$data['category']  = 'Suggestion';
			$data['summary']   = $suggestion['for'];
			$data['report']    = $suggestion['idea'];
			$data['resolved']  = NULL;
			$data['email']     = $suggester['email'];
			$data['name']      = $suggester['name'];
			$data['os']        = $os .' '. $os_version;
			$data['browser']   = $browser .' '. $browser_ver;
			$data['ip']        = $ip;
			$data['hostname']  = $hostname;
			$data['uas']       = JRequest::getVar('HTTP_USER_AGENT','','server');
			$data['referrer']  = NULL;
			$data['cookies']   = (JRequest::getVar('sessioncookie','','cookie')) ? 1 : 0;
			$data['instances'] = 1;
			$data['section']   = 1;

			$row = new SupportTicket( $this->database );
			if (!$row->bind( $data )) {
				$this->setError( $row->getError() );
			}
			if (!$row->check()) {
				$this->setError( $row->getError() );
			}
			if (!$row->store()) {
				$this->setError( $row->getError() );
			}

			// Output Thank You message
			$view = new JView( array('name'=>'suggestions', 'layout'=>'thanks') );
			$view->title = $this->_title;
			$view->option = $this->_option;
			$view->task = $this->_task;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
		} else {
			// Error
			$this->setError(1);
			if ($validemail == 0) {
				$this->setError(2);
			}

			// Output form with error messages
			$view = new JView( array('name'=>'suggestions') );
			$view->title = $this->_title;
			$view->option = $this->_option;
			$view->task = $this->_task;
			$view->user = $suggester;
			$view->suggestion = $suggestion;
			$view->verified = ($this->juser->get('guest')) ? 0 : 1;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
		}
	}

	/**
	 * Short description for 'sendreport'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function sendreport()
	{
		include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'attachment.php');
		include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'ticket.php');

		// Get plugins
		JPluginHelper::importPlugin('support');
		$dispatcher =& JDispatcher::getInstance();

		// Trigger any events that need to be called before session stop
		$dispatcher->trigger('onPreTicketSubmission', array());

		// Incoming
		$no_html  = JRequest::getInt('no_html', 0);
		$verified = JRequest::getInt('verified', 0);
		$reporter = array_map('trim', $_POST['reporter']);
		$problem  = array_map('trim', $_POST['problem']);

 		// Normally calling JRequest::getVar calls _cleanVar, but b/c of the way this page processes the posts
 		// (with array square brackets in the html names) against the $_POST collection, we explicitly
		// call the clean_var function on these arrays after fetching them
		$reporter = array_map(array('JRequest','_cleanVar'), $reporter);
		$problem  = array_map(array('JRequest','_cleanVar'), $problem);

		// Probably redundant after the change to call JRequest::_cleanVar change above, It is a bit hard to 
		// tell if the Joomla  _cleanvar function does enough to allow us to remove the purifyText call
		$reporter = array_map(array('Hubzero_View_Helper_Html','purifyText'), $reporter);
		//$problem  = array_map(array('Hubzero_View_Helper_Html','purifyText'), $problem);

		// Make sure email address is valid
		$validemail = $this->_check_validEmail($reporter['email']);
		
		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();


		// Trigger any events that need to be called
		$customValidation = true;
		$result = $dispatcher->trigger('onValidateTicketSubmission', array($reporter, $problem));
		$customValidation = (is_array($result) && !empty($result)) ? $result[0] : $customValidation;

		// Check for some required fields
		if (!$reporter['name'] || !$reporter['email'] || !$validemail || !$problem['long'] || !$customValidation) {
			JRequest::setVar('task', 'report_problems');
			// Output form with error messages
			$view = new JView( array('name'=>'report') );
			$view->title = $this->_title;
			$view->option = $this->_option;
			$view->task = 'report_problems';
			$view->reporter = $reporter;
			$view->problem = $problem;
			$view->verified = $verified;
			$view->captchas = $dispatcher->trigger('onGetComponentCaptcha');
			$view->file_types = $this->config->get('file_ext');
			$view->setError(2);
			$view->display();
			return;
		}

		// Get the user's IP
		$ip = $this->_ip_address();
		$hostname = gethostbyaddr(JRequest::getVar('REMOTE_ADDR','','server'));

		// Check CAPTCHA
		$validcaptchas = $dispatcher->trigger('onValidateCaptcha');
		if (count($validcaptchas) > 0) 
		{
			foreach ($validcaptchas as $validcaptcha) 
			{
				if (!$validcaptcha) 
				{
					$this->setError(JText::_('Error: Invalid CAPTCHA response.'));
				}
			}
		}

		// Are they verified?
		if (!$verified) 
		{
			// Quick spam filter
			$spam = $this->_detect_spam($problem['long'], $ip);
			if ($spam) {
				$this->setError(JText::_('Error: Message flagged as spam.'));
				return;
			}
			// Quick bot check
			$botcheck = JRequest::getVar('botcheck', '');
			if ($botcheck) {
				$this->setError(JText::_('Error: Invalid botcheck response.'));
				return;
			}
		}
		
		if ($this->getError()) {
			if ($no_html) {
				// Output error messages (AJAX)
				$view = new JView( array('name'=>'report', 'layout'=>'error') );
				if ($this->getError()) {
					$view->setError($this->getError());
				}
				$view->display();
				return;
			} else {
				JRequest::setVar('task', 'report_problems');
				// Output form with error messages
				$view = new JView( array('name'=>'report') );
				$view->title = $this->_title;
				$view->option = $this->_option;
				$view->task = 'report_problems';
				$view->reporter = $reporter;
				$view->problem = $problem;
				$view->verified = $verified;
				$view->captchas = $dispatcher->trigger('onGetComponentCaptcha');
				$view->file_types = $this->config->get('file_ext');
				$view->setError(3);
				$view->display();
				return;
			}
		}

		// Get user's city, region and location based on ip
		$source_city    = 'unknown';
		$source_region  = 'unknown';
		$source_country = 'unknown';

		ximport('Hubzero_Geo');
		$gdb =& Hubzero_Geo::getGODBO();
		if (is_object($gdb)) {
			$gdb->setQuery( "SELECT countrySHORT, countryLONG, ipREGION, ipCITY FROM ipcitylatlong WHERE INET_ATON('$ip') BETWEEN ipFROM and ipTO" );
			$rows = $gdb->loadObjectList();
			if ($rows && count($rows) > 0) {
				$source_city    = $rows[0]->ipCITY;
				$source_region  = $rows[0]->ipREGION;
				$source_country = $rows[0]->countryLONG;
			}
		}

		// Cut suggestion at 70 characters
		if (!$problem['short'] && $problem['long']) {
			$problem['short'] = substr($problem['long'], 0, 70);
			if (strlen($problem['short']) >= 70) {
				$problem['short'] .= '...';
			}
		}

		$tool = $this->_getTool( $problem['referer'] );
		if ($tool) {
			$group = $this->_getTicketGroup( trim($tool) );
		} else {
			$group = '';
		}

		// Build an array of ticket data
		$data = array();
		$data['id']        = NULL;
		$data['status']    = 0;
		$data['created']   = date( "Y-m-d H:i:s" );
		$data['login']     = $reporter['login'];
		$data['severity']  = 'normal';
		$data['owner']     = NULL;
		$data['category']  = (isset($problem['topic'])) ? $problem['topic'] : '';
		$data['summary']   = htmlentities($problem['short'], ENT_COMPAT, 'UTF-8');
		$data['report']    = htmlentities($problem['long'], ENT_COMPAT, 'UTF-8');
		$data['resolved']  = NULL;
		$data['email']     = $reporter['email'];
		$data['name']      = $reporter['name'];
		$data['os']        = $problem['os'] .' '. $problem['osver'];
		$data['browser']   = $problem['browser'] .' '. $problem['browserver'];
		$data['ip']        = $ip;
		$data['hostname']  = $hostname;
		$data['uas']       = JRequest::getVar('HTTP_USER_AGENT','','server');
		$data['referrer']  = $problem['referer'];
		$data['cookies']   = (JRequest::getVar('sessioncookie','','cookie')) ? 1 : 0;
		$data['instances'] = 1;
		$data['section']   = 1;
		$data['group']     = $group;

		// Initiate class and bind data to database fields
		$row = new SupportTicket( $this->database );
		if (!$row->bind( $data )) {
			$this->setError( $row->getError() );
		}
		// Check the data
		if (!$row->check()) {
			$this->setError( $row->getError() );
		}
		// Save the data
		if (!$row->store()) {
			$this->setError( $row->getError() );
		}
		// Retrieve the ticket ID
		if (!$row->id) {
			$row->getId();
		}

		$sconfig = JComponentHelper::getParams( 'com_support' );
		$attachment = $this->_uploadAttachment( $row->id, $sconfig );
		$row->report .= ($attachment) ? "\n\n".$attachment : '';
		$problem['long'] .= ($attachment) ? "\n\n".$attachment : '';

		// Save the data
		if (!$row->store()) {
			$this->setError( $row->getError() );
		}

		// Get some email settings
		$jconfig =& JFactory::getConfig();
		$admin   = $jconfig->getValue('config.mailfrom');
		$subject = $jconfig->getValue('config.sitename').' '.JText::_('COM_FEEDBACK_SUPPORT').', '.JText::sprintf('COM_FEEDBACK_TICKET_NUMBER',$row->id);
		$from    = $jconfig->getValue('config.sitename').' web-robot';
		$hub     = array('email' => $reporter['email'], 'name' => $from);

		// Parse comments for attachments
		$xhub =& Hubzero_Factory::getHub();
		$attach = new SupportAttachment( $this->database );
		$attach->webpath = $xhub->getCfg('hubLongURL').$sconfig->get('webpath').DS.$row->id;
		$attach->uppath  = JPATH_ROOT.$sconfig->get('webpath').DS.$row->id;
		$attach->output  = 'email';

		// Generate e-mail message
		$message  = (!$this->juser->get('guest')) ? JText::_('COM_FEEDBACK_VERIFIED_USER')."\r\n\r\n" : '';
		$message .= ($reporter['login']) ? JText::_('COM_FEEDBACK_USERNAME').': '. $reporter['login'] ."\r\n" : '';
		$message .= JText::_('COM_FEEDBACK_NAME').': '. $reporter['name'] ."\r\n";
		$message .= JText::_('COM_FEEDBACK_AFFILIATION').': '. $reporter['org'] ."\r\n";
		$message .= JText::_('COM_FEEDBACK_EMAIL').': '. $reporter['email'] ."\r\n";
		$message .= JText::_('COM_FEEDBACK_IP_HOSTNAME').': '. $ip .' ('.$hostname.')' ."\r\n";
		$message .= JText::_('COM_FEEDBACK_REGION').': '.$source_city.', '.$source_region.', '.$source_country ."\r\n\r\n";
		$message .= JText::_('COM_FEEDBACK_OS').': '. $problem['os'] .' '. $problem['osver'] ."\r\n";
		$message .= JText::_('COM_FEEDBACK_BROWSER').': '. $problem['browser'] .' '. $problem['browserver'] ."\r\n";
		$message .= JText::_('COM_FEEDBACK_UAS').': '. JRequest::getVar('HTTP_USER_AGENT','','server') ."\r\n";
		$message .= JText::_('COM_FEEDBACK_COOKIES').': ';
		$message .= (JRequest::getVar('sessioncookie','','cookie')) ? JText::_('COM_FEEDBACK_COOKIES_ENABLED')."\r\n" : JText::_('COM_FEEDBACK_COOKIES_DISABLED')."\r\n";
		$message .= JText::_('COM_FEEDBACK_REFERRER').': '. $problem['referer'] ."\r\n";
		$message .= ($problem['tool']) ? JText::_('COM_FEEDBACK_TOOL').': '. $problem['tool'] ."\r\n\r\n" : "\r\n";
		$message .= JText::_('COM_FEEDBACK_PROBLEM_DETAILS').': '. $attach->parse(stripslashes($problem['long'])) ."\r\n\r\n";

		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option=com_support&task=ticket&id='. $row->id);
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		$message .= $juri->base().$sef."\r\n";

		// Load the support config
		$params = JComponentHelper::getParams('com_support');
		
		// Get any set emails that should be notified of ticket submission
		$defs = str_replace("\r", '', $params->def('emails','{config.mailfrom}'));
		$defs = explode("\n", $defs);
		if ($defs) 
		{
			// Import our mailer
			ximport('Hubzero_Toolbox');
			
			// Loop through the addresses
			foreach ($defs As $def) 
			{
				$def = trim($def);
				// Check if the address should come from Joomla config
				if ($def == '{config.mailfrom}') 
				{
					$def = $jconfig->getValue('config.mailfrom');
				}
				// Check for a valid address
				if ($this->_check_validEmail($def))
				{
					// Send e-mail
					Hubzero_Toolbox::send_email($def, $subject, $message);
				}
			}
		}

		// Trigger any events that need to be called before session stop
		$dispatcher->trigger('onTicketSubmission', array($row));

		// Output Thank You message
		$view = new JView(array('name'=>'report', 'layout'=>'thanks'));
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->ticket = $row->id;
		$view->no_html = $no_html;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	private function _isVerified()
	{
		if (!$this->juser->get('guest')) {
			ximport('Hubzero_User_Profile');
			$profile = new Hubzero_User_Profile();
			$profile->load($this->juser->get('id'));
			if ($profile->get('emailConfirmed') == 1) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Short description for '_uploadAttachment'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $listdir Parameter description (if any) ...
	 * @param      mixed $sconfig Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function _uploadAttachment( $listdir, $sconfig )
	{
		if (!$listdir) {
			$this->setError( JText::_('SUPPORT_NO_UPLOAD_DIRECTORY') );
			return '';
		}

		// Incoming file
		$file = JRequest::getVar( 'upload', '', 'files', 'array' );
		if (!$file['name']) {
			return '';
		}

		// Incoming
		$description = ''; //JRequest::getVar( 'description', '' );

		// Construct our file path
		$path = JPATH_ROOT.$sconfig->get('webpath').DS.$listdir;

		// Build the path if it doesn't exist
		if (!is_dir( $path )) {
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path, 0777 )) {
				$this->setError( JText::_('UNABLE_TO_CREATE_UPLOAD_PATH') );
				return '';
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ','_',$file['name']);
		$ext = strtolower(JFile::getExt($file['name']));

		//make sure that file is acceptable type
		if ( !in_array($ext, explode(',',$this->config->get('file_ext'))) ) {
			$this->setError( JText::_('Incorrect file type.') );
			return '';
		}

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path.DS.$file['name'])) {
			$this->setError( JText::_('ERROR_UPLOADING') );
			return '';
		} else {
			// File was uploaded
			// Create database entry
			$description = htmlspecialchars($description);

			$row = new SupportAttachment( $this->database );
			$row->bind( array('id'=>0,'ticket'=>$listdir,'filename'=>$file['name'],'description'=>$description) );
			if (!$row->check()) {
				$this->setError( $row->getError() );
			}
			if (!$row->store()) {
				$this->setError( $row->getError() );
			}
			if (!$row->id) {
				$row->getID();
			}

			return '{attachment#'.$row->id.'}';
		}
	}

	/**
	 * Short description for '_getTool'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $referrer Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function _getTool( $referrer )
	{
		$tool = '';

		if (!$referrer) {
			return $tool;
		}

		if (substr($referrer,0,3) == '/mw') {
			$bits = explode('/', $referrer);
			if ($bits[2] == 'invoke') {
				$longbits = explode('?',$bits[3]);
				if (is_array($longbits)) {
					$tool = trim($longbits[0]);
				} else {
					$tool = trim($bits[3]);
				}
			} else if ($bits[2] == 'view') {
				$longbits = explode('=',$bits[3]);
				if (is_array($longbits)) {
					$tool = trim(end($longbits));
				} else {
					$tool = trim($bits[3]);
				}
			}
			if (strstr($tool,'_r')) {
				$version = strrchr($tool,'_r');
				$tool = str_replace($version, '', $tool);
			}
			if (strstr($tool,'_dev')) {
				$version = strrchr($tool,'_dev');
				$tool = str_replace($version, '', $tool);
			}
		} else if (substr($referrer,0,6) == '/tools' || substr($referrer,0,10) == '/resources') {
			$bits = explode('/', $referrer);
			$tool = (isset($bits[2])) ? trim($bits[2]) : '';
		} else if (substr($referrer,0,4) == 'http') {
			$bits = explode('/', $referrer);
			$tool = (isset($bits[4])) ? trim($bits[4]) : '';
		}

		return $tool;
	}

	/**
	 * Short description for '_getTicketGroup'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $tool Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function _getTicketGroup($tool)
	{
		// Do we have a tool?
		if (!$tool) {
			return '';
		}

		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'resource.php');
		$resource = new ResourcesResource( $this->database );
		$tool = str_replace(':','-',$tool);
		$resource->loadAlias( $tool );

		if (!$resource || $resource->type != 7) {
			return '';
		}

		// Get tags on the tools
		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'tags.php' );
		$rt = new ResourcesTags($this->database);
		$tags = $rt->getTags( $resource->id, 0, 0, 1 );

		if (!$tags) {
			return 'app-'.$tool;
		}

		// Get tag/group associations
		//include_once( JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'handler.php' );
		$tt = new TagsGroup( $this->database );
		$tgas = $tt->getRecords();

		if (!$tgas) {
			return 'app-'.$tool;
		}

		// Loop through the tags and make a flat array so we can search quickly
		$ts = array();
		foreach ($tags as $tag)
		{
			$ts[] = $tag->tag;
		}
		// Loop through the tag/group array and see if one of them is in the tags list
		foreach ($tgas as $tga)
		{
			if (in_array($tga->tag, $ts)) {
				// We found one! So set the group
				return $tga->cn;
				break;
			}
		}
		return 'app-'.$tool;
	}

	//----------------------------------------------------------
	//  Image handling
	//----------------------------------------------------------


	/**
	 * Short description for 'upload'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function upload()
	{
		if ($this->juser->get('guest')) {
			$this->setError( JText::_('COM_FEEDBACK_NOTAUTH') );
			$this->img( '', 0 );
			return;
		}

		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('COM_FEEDBACK_NO_ID') );
			$this->img( '', $id );
			return;
		}

		// Incoming file
		$file = JRequest::getVar( 'upload', '', 'files', 'array' );
		if (!$file['name']) {
			$this->setError( JText::_('COM_FEEDBACK_NO_FILE') );
			$this->img( '', $id );
			return;
		}

		// Build upload path
		$dir  = Hubzero_View_Helper_Html::niceidformat( $id );
		$path = JPATH_ROOT;
		if (substr($this->config->get('uploadpath'), 0, 1) != DS) {
			$path .= DS;
		}
		if (substr($this->config->get('uploadpath'), -1, 1) == DS) {
			$path = substr($this->config->get('uploadpath'), 0, (strlen($this->config->get('uploadpath')) - 1));
		}
		$path .= $this->config->get('uploadpath').DS.$dir;

		if (!is_dir( $path )) {
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path, 0777 )) {
				$this->setError( JText::_('COM_FEEDBACK_UNABLE_TO_CREATE_UPLOAD_PATH') );
				$this->img( '', $id );
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ','_',$file['name']);

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path.DS.$file['name'])) {
			$this->setError( JText::_('COM_FEEDBACK_ERROR_UPLOADING') );
			$file = $curfile;
		} else {
			// Do we have an old file we're replacing?
			$curfile = JRequest::getVar( 'currentfile', '' );

			if ($curfile != '') {
				// Yes - remove it
				if (file_exists($path.DS.$curfile)) {
					if (!JFile::delete($path.DS.$curfile)) {
						$this->setError( JText::_('COM_FEEDBACK_UNABLE_TO_DELETE_FILE') );
						$this->img( $file['name'], $id );
						return;
					}
				}
			}

			$file = $file['name'];
		}

		// Push through to the image view
		$this->img( $file, $id );
	}

	/**
	 * Short description for 'delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function delete()
	{
		if ($this->juser->get('guest')) {
			$this->setError( JText::_('COM_FEEDBACK_NOTAUTH') );
			$this->img( '', 0 );
			return;
		}

		// Incoming member ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('COM_FEEDBACK_NO_ID') );
			$this->img( '', $id );
			return;
		}

		if ($this->juser->get('id') != $id) {
			$this->setError( JText::_('COM_FEEDBACK_NOTAUTH') );
			$this->img( '', $this->juser->get('id') );
			return;
		}

		// Incoming file
		$file = JRequest::getVar( 'file', '' );
		if (!$file) {
			$this->setError( JText::_('COM_FEEDBACK_NO_FILE') );
			$this->img( '', $id );
			return;
		}

		$file = basename($file);

		// Build the file path
		$dir  = Hubzero_View_Helper_Html::niceidformat( $id );
		$path = JPATH_ROOT;
		if (substr($this->config->get('uploadpath'), 0, 1) != DS) {
			$path .= DS;
		}
		if (substr($this->config->get('uploadpath'), -1, 1) == DS) {
			$path = substr($this->config->get('uploadpath'), 0, (strlen($this->config->get('uploadpath')) - 1));
		}
		$path .= $this->config->get('uploadpath').DS.$dir;

		if (!file_exists($path.DS.$file) or !$file) {
			$this->setError( JText::_('COM_FEEDBACK_FILE_NOT_FOUND') );
		} else {
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path.DS.$file)) {
				$this->setError( JText::_('COM_FEEDBACK_UNABLE_TO_DELETE_FILE') );
				$this->img( $file, $id );
				return;
			}

			$file = '';
		}

		// Push through to the image view
		$this->img( $file, $id );
	}

	/**
	 * Short description for 'img'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $file Parameter description (if any) ...
	 * @param      integer $id Parameter description (if any) ...
	 * @return     void
	 */
	protected function img( $file='', $id=0 )
	{
		// Do have an ID or do we need to get one?
		if (!$id) {
			$id = JRequest::getInt( 'id', 0 );
		}
		$dir = Hubzero_View_Helper_Html::niceidformat( $id );

		// Do we have a file or do we need to get one?
		$file = ($file)
			  ? $file
			  : JRequest::getVar( 'file', '' );

		// Build the directory path
		$path = $this->config->get('uploadpath').DS.$dir;

		// Output form with error messages
		$view = new JView( array('name'=>'story', 'layout'=>'picture') );
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->webpath = $this->config->get('uploadpath');
		$view->default_picture = $this->config->get('defaultpic');
		$view->path = $dir;
		$view->file = $file;
		$view->file_path = $path;
		$view->id = $id;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//----------------------------------------------------------
	// Private functions
	//----------------------------------------------------------


	/**
	 * Short description for '_getUser'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     array Return description (if any) ...
	 */
	private function _getUser()
	{
		$user = array();
		$user['login'] = '';
		$user['name']  = '';
		$user['org']   = '';
		$user['email'] = '';
		$user['uid']   = '';

		if (!$this->juser->get('guest')) {
			ximport('Hubzero_User_Profile');

			$profile = new Hubzero_User_Profile();
			$profile->load( $this->juser->get('id') );

			if (is_object($profile)) {
				$user['login'] = $profile->get('username');
				$user['name']  = $profile->get('name');
				$user['org']   = $profile->get('organization');
				$user['email'] = $profile->get('email');
				$user['uid']   = $profile->get('uidNumber');
			}
		}
		return $user;
	}

	/**
	 * Short description for '_detect_spam'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $text Parameter description (if any) ...
	 * @param      unknown $ip Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	private function _detect_spam($text, $ip)
	{
		// Spammer IPs (banned)
		$ips = $this->config->get('blacklist');
		if ($ips) {
			$bl = explode(',',$ips);
			array_map('trim',$bl);
		} else {
			$bl = array();
		}

		// Bad words
		$words = $this->config->get('badwords');
		if ($words) {
			$badwords = explode(',', $words);
			array_map('trim',$badwords);
		} else {
			$badwords = array();
		}

		// Build an array of patterns to check againts
		$patterns = array('/\[url=(.*?)\](.*?)\[\/url\]/s', '/\[url=(.*?)\[\/url\]/s');
		foreach ($badwords as $badword)
		{
			if (!empty($badword))
			    	$patterns[] = '/(.*?)'.trim($badword).'(.*?)/s';
		}

		// Set the splam flag
		$spam = false;

		// Check the text against bad words
		foreach ($patterns as $pattern)
		{
			preg_match_all( $pattern, $text, $matches );
			if (count($matches[0]) >=1) {
				$spam = true;
			}
		}

		// Check the number of links in the text
		// Very unusual to have 5 or more - usually only spammers
		if (!$spam) {
			$num = substr_count($text, 'http://');
			if ($num >= 5) { // too many links
        	    $spam = true;
			}
		}

		// Check the user's IP against the blacklist
		if (in_array($ip, $bl)) {
			$spam = true;
		}

		return $spam;
	}

	/**
	 * Short description for '_generate_hash'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $input Parameter description (if any) ...
	 * @param      string $day Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function _generate_hash($input, $day)
	{
		// Add date:
		$input .= $day . date('ny');

		// Get MD5 and reverse it
		$enc = strrev(md5($input));

		// Get only a few chars out of the string
		$enc = substr($enc, 26, 1) . substr($enc, 10, 1) . substr($enc, 23, 1) . substr($enc, 3, 1) . substr($enc, 19, 1);

		return $enc;
	}

	/**
	 * Short description for '_check_validLogin'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $login Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	private function _check_validLogin($login)
	{
		if (eregi("^[_0-9a-zA-Z]+$", $login)) {
			return(1);
		} else {
			return(0);
		}
	}

	/**
	 * Short description for '_check_validEmail'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $email Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	private function _check_validEmail($email)
	{
		if (eregi("^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $email)) {
			return(1);
		} else {
			return(0);
		}
	}

	/**
	 * Short description for '_server'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $index Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	private function _server($index = '')
	{
		if (!isset($_SERVER[$index])) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Short description for '_valid_ip'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $ip Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function _valid_ip($ip)
	{
		return (!preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) ? FALSE : TRUE;
	}

	/**
	 * Short description for '_ip_address'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	private function _ip_address()
	{
		if ($this->_server('REMOTE_ADDR') AND $this->_server('HTTP_CLIENT_IP')) {
			 $ip_address = JRequest::getVar('HTTP_CLIENT_IP','','server');
		} elseif ($this->_server('REMOTE_ADDR')) {
			 $ip_address = JRequest::getVar('REMOTE_ADDR','','server');
		} elseif ($this->_server('HTTP_CLIENT_IP')) {
			 $ip_address = JRequest::getVar('HTTP_CLIENT_IP','','server');
		} elseif ($this->_server('HTTP_X_FORWARDED_FOR')) {
			 $ip_address = JRequest::getVar('HTTP_X_FORWARDED_FOR','','server');
		}

		if ($ip_address === FALSE) {
			$ip_address = '0.0.0.0';
			return $ip_address;
		}

		if (strstr($ip_address, ',')) {
			$x = explode(',', $ip_address);
			$ip_address = end($x);
		}

		if (!$this->_valid_ip($ip_address)) {
			$ip_address = '0.0.0.0';
		}

		return $ip_address;
	}
}

