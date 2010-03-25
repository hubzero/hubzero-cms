<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

class ToolsController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	
	//-----------
	
	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		//Set the controller name
		if (empty( $this->_name ))
		{
			if (isset($config['name']))  {
				$this->_name = $config['name'];
			}
			else
			{
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		$this->_option = 'com_'.$this->_name;
	}
	
	//-----------
	
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
	
	//-----------

	public function execute()
	{
		// Get the task
		$this->_task = JRequest::getVar( 'task', '' );
		
		// Get the component config
		$this->config = JComponentHelper::getParams( $this->_option );
		
		// Check if middleware is enabled
		if (!$this->config->get('mw_on') && $this->_task != 'image' && $this->_task != 'css') {
			// Redirect to home page
			$this->_redirect = '/home';
			return;
		}
		
		// Are we banking?
		$upconfig =& JComponentHelper::getParams( 'com_userpoints' );
		$banking = $upconfig->get('bankAccounts');
		$this->banking = ($banking && $this->config->get('banking') ) ? 1: 1;
		
		if ($banking) {
			ximport( 'bankaccount' );
		}
		
		// Push some styles to the template
		$this->_getStyles();
		
		// Push some scripts to the template
		$this->_getScripts();

		switch ($this->_task) 
		{
			case 'login':     $this->login();     break;
			case 'tools':   	$this->tools();    	break;
			case 'image':   	$this->image();    	break;
			case 'css':   	$this->css();    	break;
			
			// Error views
			case 'accessdenied':    $this->accessdenied();    	break;
			case 'quotaexceeded':   $this->quotaexceeded();   	break;
			case 'storageexceeded': $this->storage(true); 		break;
			case 'storage': 		$this->storage(); 			break;
			
			// Tasks typically called via AJAX
			case 'rename':    		$this->renames();   		break;
			case 'diskusage': 		$this->diskusage(); 		break;
			case 'purge':     		$this->purge();     		break;
			
			// Session tasks
			case 'share':     		$this->share();     		break;
			case 'unshare':   		$this->unshare();   		break;
			case 'invoke':    		$this->invoke();    		break;
			case 'session':      	$this->view();      		break;
			case 'view':      		$this->view();      		break;
			case 'stop':      		$this->stop();      		break;
			
			// Media manager
			case 'listfiles':    	$this->listfiles();     	break;
			case 'download':      	$this->download();      	break;
			//case 'upload':       	$this->upload();        	break;
			case 'deletefolder': 	$this->deletefolder();  	break;
			case 'deletefile':   	$this->deletefile();    	break;

			default: $this->tools(); break;
		}
	}

	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message );
		}
	}

	//-----------
	
	private function _getStyles() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
	}

	//-----------
	
	private function _getScripts($option='',$name='')
	{
		$document =& JFactory::getDocument();
		if ($option) {
			$name = ($name) ? $name : $option;
			if (is_file(JPATH_ROOT.DS.'components'.DS.'com_'.$option.DS.$name.'.js')) {
				$document->addScript('components'.DS.'com_'.$option.DS.$name.'.js');
			}
		} else {
			if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
				$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
			}
		}
	}
	
	//-----------

	private function _buildPathway($session=null) 
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option='.$this->_option
			);
		}
		if ($this->app && $this->app['name']) {
			if (strstr( $this->app['name'], '_dev' ) || strstr( $this->app['name'], '_r' )) {
				$bits = explode('_',$this->app['name']);
				$bit = array_pop($bits);
				$appname = implode('_',$bits);
			} else {
				$appname = $this->app['name'];
			}
			$pathway->addItem(
				$this->app['caption'],
				'index.php?option='.$this->_option.'&app='.$appname
			);
			$pathway->addItem(
				JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
				'index.php?option='.$this->_option.'&task='.$this->_task.'&app='.$appname.'&version='.$this->app['version']
			);
		} else {
			if ($this->_task && $this->_task != 'tools') {
				$pathway->addItem(
					JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
					'index.php?option='.$this->_option.'&task='.$this->_task
				);
			}
		}
		if (is_object($session)) {
			$pathway->addItem(
				$title,
				'index.php?option='.$this->_option.'&tag='.$lnk
			);
		}
	}
	
	//-----------
	
	private function _buildTitle($session=null) 
	{
		$this->_title = JText::_(strtoupper($this->_option));
		if ($this->app && $this->app['name']) {
			$this->_title .= ': '.$this->app['caption'];
		}
		if ($this->_task && $this->_task != 'tools') {
			$this->_title .= ': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
		}
		if (is_object($session)) {
			$title .= ': ';
		}
		$document =& JFactory::getDocument();
		$document->setTitle( $this->_title );
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function tools() 
	{
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Push some CSS to the template
		$this->_getStyles();
		
		$xhub  =& XFactory::getHub();
		//$model =& $this->getModel();
		include_once( JPATH_COMPONENT.DS.'models'.DS.'tools.php' );
		$model = new ToolsModelTools();
		
		// Get some vars to fill in text
		$forgeName = $xhub->getCfg('forgeName');
		$forgeURL = $xhub->getCfg('forgeURL');
		$hubShortName = $xhub->getCfg('hubShortName');
		$hubShortURL = $xhub->getCfg('hubShortURL');
		$hubLongURL = $xhub->getCfg('hubLongURL');
		
		// Get the tool list
		$appTools = $model->getApplicationTools();
		
		// Get the forge image
		ximport('xdocument');
		$image = XDocument::getComponentImage('com_projects', 'forge.png', 1);
		
		// Instantiate the view
		$view = new JView( array('name'=>'tools') );
		$view->option = $this->_option;
		$view->title = $this->_title;
		$view->forgeName = $forgeName;
		$view->forgeURL = $forgeURL;
		$view->hubShortURL = $hubShortURL;
		$view->hubLongURL = $hubLongURL;
		$view->hubShortName = $hubShortName;
		$view->appTools = $appTools;
		$view->image = $image;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	protected function image() 
	{
		ximport('xdocument');
		$image = JPATH_SITE . XDocument::getComponentImage('com_projects', 'forge.png', 1);

		if (is_readable($image)) {
			ob_clean();
			header("Content-Type: image/png");
			readfile($image);
			ob_end_flush();
			exit;
		}
	}
	
	//-----------
	
	protected function css() 
	{
		ximport('xdocument');
		$file = JPATH_SITE . XDocument::getComponentStylesheet('com_tools', 'site_css.cs');

		if (is_readable($file)) {
			ob_clean();
			header("Content-Type: text/css");
			readfile($file);
			ob_end_flush();
			exit;
		}
	}
	
	//-----------

	protected function login() 
	{
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Instantiate the view
		$view = new JView( array('name'=>'login') );
		$view->option = $this->_option;
		$view->title = $this->_title;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------

	protected function accessdenied() 
	{
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();

		// Instantiate the view
		$view = new JView( array('name'=>'accessdenied') );
		$view->option = $this->_option;
		$view->title = $this->_title;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	protected function quotaexceeded() 
	{
		// Check that the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		// Build the page title
		$title  = JText::_('Members');
		$title .= ': '.JText::_('View');
		$title .= ': '.stripslashes($juser->get('name'));
		$title .= ': '.JText::_(strtoupper($this->_option.'_'.$this->_task));
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Set the pathway
		$japp =& JFactory::getApplication();
		$pathway =& $japp->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_('Members'), 'index.php?option=com_members' );
		}
		$pathway->addItem( stripslashes($juser->get('name')), 'index.php?option=com_members&id='.$juser->get('id') );
		$pathway->addItem( JText::_(strtoupper($this->_option.'_'.$this->_task)), 'index.php?option='.$this->_option.'&task='.$this->_task );
		
		// Check if the user is an admin.
		$authorized = $this->_authorize();
		
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		
		// Get the user's sessions
		$ms = new MwSession( $mwdb );
		$sessions = $ms->getRecords( $juser->get('username'), '', false );
		
		// Instantiate the view
		$view = new JView( array('name'=>'quotaexceeded') );
		$view->option = $this->_option;
		$view->sessions = $sessions;
		if ($authorized) {
			$view->allsessions = $ms->getRecords( $juser->get('username'), '', $authorized );
		}
		$view->active = JRequest::getVar( 'active', '' );
		$view->authorized = $authorized;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	protected function storage( $exceeded=false )
	{
		// Check that the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		// Build the page title
		/*$title  = JText::_(strtoupper($this->_name));
		$title .= ': '.JText::_('MW_STORAGE_MANAGEMENT');*/
		$title  = JText::_('Members');
		$title .= ': '.JText::_('View');
		$title .= ': '.stripslashes($juser->get('name'));
		$title .= ': '.JText::_(strtoupper($this->_option.'_'.$this->_task));
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Set the pathway
		$japp =& JFactory::getApplication();
		$pathway =& $japp->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			//$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
			$pathway->addItem( JText::_('Members'), 'index.php?option=com_members' );
		}
		$pathway->addItem( stripslashes($juser->get('name')), 'index.php?option=com_members&id='.$juser->get('id') );
		$pathway->addItem( JText::_(strtoupper($this->_option.'_'.$this->_task)), 'index.php?option='.$this->_option.'&task=storage' );
		
		// Output from purging
		$output = $this->__get('output');
			
		// Get their disk space usage
		$this->percent = 0;
		$monitor = '';
		if ($this->config->get('show_storage')) {
			$this->getDiskUsage();
			$this->_redirect = '';
			
			$view = new JView( array('name'=>'monitor') );
			$view->option = $this->_option;
			$view->amt = $this->percent;
			$view->du = '';
			$view->percent = 0;
			$view->msgs = 0;
			$view->ajax = 0;
			$view->writelink = 0;
			$monitor = $view->loadTemplate();
		}
		
		// Instantiate the view
		$view = new JView( array('name'=>'storage') );
		$view->option = $this->_option;
		$view->exceeded = $exceeded;
		$view->output = $output;
		$view->percentage = $this->percent;
		$view->monitor = $monitor;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------

	protected function invoke()
	{
		ximport('Hubzero_Ldap');

		// Check that the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		// Needed objects
		$juser =& JFactory::getUser();
		$xhub =& XFactory::getHub();
		$url = $_SERVER['REQUEST_URI'];
		$xlog =& XFactory::getLogger();

		// Incoming
		$app = array();
		$app['name']    = JRequest::getVar( 'app', '' );
		$app['name']    = str_replace(':','-',$app['name']);
		$app['number']  = 0;
		$app['version'] = JRequest::getVar( 'version', 'default' );
		
		// Get the user's IP address
		$ip = JRequest::getVar( 'REMOTE_ADDR', '', 'server' );

		$xlog->logDebug("mw::invoke URL: $url : " . $app['name'] . " by " . $juser->get('username') . " from " . $ip);
		$xlog->logDebug("mw::invoke REFERER:" . (array_key_exists('HTTP_REFERER',$_SERVER)) ? $_SERVER['HTTP_REFERER'] : 'none');

		// Make sure we have an app to invoke
		if (trim($app['name']) == '') {
			$this->_redirect = JRoute::_( 'index.php?option=com_myhub' );
			return;
		}
		
		// Get the parent toolname (appname without any revision number "_r423")
		$database =& JFactory::getDBO();
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );
		$tv = new ToolVersion( $database );
		
		switch ($app['version'])
		{
			case 1:
			case 'default':
				$app['name'] = $tv->getCurrentVersionProperty($app['name'], 'instance');
			break;
			case 'test':
			case 'dev':
				$app['name'] = $app['name'].'_dev';
			break;
			default:
				$app['name'] = $app['name'].'_r'.$app['version'];
			break;
		}
		
		$parent_toolname = $tv->getToolname($app['name']);
		$toolname = ($parent_toolname) ? $parent_toolname : $app['name'];
		
		// Check of the toolname has a revision indicator
		$bits = explode('_',$app['name']);
		$r = end($bits);
		if (substr($r,0,1) != 'r' && substr($r,0,3) != 'dev') {
			$r = '';
		}
		// No version passed and no revision
		if ((!$app['version'] || $app['version'] == 'default') && !$r) {
			// Get the latest version
			$app['version'] = $tv->getCurrentVersionProperty( $toolname, 'revision' );
			$app['name'] = $toolname.'_r'.$app['version'];
		}

		// Get the caption/session title
		$tv->loadFromInstance( $app['name'] );
		$app['caption'] = stripslashes($tv->title);
		$app['title'] = stripslashes($tv->title);

		// Check if they have access to run this tool
		$hasaccess = $this->_getToolAccess($app['name']);
		$status2 = ($hasaccess) ? "PASSED" : "FAILED";

		$xlog->logDebug("mw::invoke " . $app['name'] . " by " . $juser->get('username') . " from " . $ip . " _getToolAccess " . $status2);

		if ($this->getError()) {
			echo '<!-- '.$this->getError().' -->';
		}
		if (!$hasaccess) {
			//$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'task=accessdenied');
			$this->app = $app;
			$this->accessdenied();
			return;
		}

		// Check authorization
		$authorized = $this->_authorize();

		// Log the launch attempt
		$this->recordUsage($toolname, $juser->get('id'));
		
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		
		// Find out how many sessions the user is running.
		$ms = new MwSession( $mwdb );
		$appcount = $ms->getCount( $juser->get('username') );

		// Find out how many sessions the user is ALLOWED to run.
		$xprofile =& XFactory::getProfile();
		$remain = $xprofile->get('jobsAllowed') - $appcount;

		if (!Hubzero_Ldap::user_exists($xprofile->get('username'))) {
		       $xlog->logDebug("mw::invoke create ldap user for this account");
		       $xprofile->create('ldap');
		}

		// Have they reached their session quota?
		if ($remain <= 0) {
			//$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'task=quotaexceeded');
			$this->quotaexceeded();
			return;
		}
		
		// Get their disk space usage
		$app['percent'] = 0;
		if ($this->config->get('show_storage')) {
			$this->getDiskUsage();
			$app['percent'] = $this->percent;
		}
		
		// We've passed all checks so let's actually start the session
		$sess = $this->middleware("start user=" . $juser->get('username') . " ip=$ip app=".$app['name']." version=".$app['version'], $output);

		// Get a count of the number of sessions of this specific tool
		$appcount = $ms->getCount( $juser->get('username'), $app['name'] );
		// Do we have more than one session of this tool?
		if ($appcount > 1) {
			// We do, so let's append a number to the caption
			//$appcount++;
			$app['caption'] .= ' ('.date("g:i a").')';
		}

		// Save the changed caption
		$ms->load( $sess );
		$ms->sessname = $app['caption'];
		if (!$ms->store()) {
			echo $ms->getError();
		}
		
		$app['sess'] = $sess;
		$app['ip'] = $ip;
		$app['username'] = $juser->get('username');
		
		// Build and display the HTML
		//$this->session( $app, $authorized, $output, $toolname );
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&app='.$toolname.'&task=session&sess='.$sess);
	}

	//-----------

	protected function share()
	{
		// Check that the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		$mwdb =& MwUtils::getMWDBO();
		
		// Incoming
		$sess     = JRequest::getVar( 'sess', '' );
		$username = trim(JRequest::getVar( 'username', '' ));
		$readonly = JRequest::getVar( 'readonly', '' );
		
		$users = array();
		if (strstr($username,',')) {
			$users = explode(',',$username);
			$users = array_map('trim',$users);
		} elseif (strstr($username,' ')) {
			$users = explode(' ',$username);
			$users = array_map('trim',$users);
		} else {
			$users[] = $username;
		}
		
		// Check authorization
		$authorized = $this->_authorize();
		
		// Double-check that the user can access this session.
		$ms = new MwSession( $mwdb );
		$row = $ms->checkSession( $sess, $juser->get('username') );
		
		// Ensure we found an active session
		if (!$row->sesstoken) {
			JError::raiseError( 500, JText::_('MW_ERROR_SESSION_NOT_FOUND').': '.$sess );
			return;
		}

		//$row = $rows[0];
		$owner = $row->viewuser;

		if ($readonly != 'Yes') {
			$readonly = 'No';
		}

		$mv = new MwViewperm( $mwdb );
		$rows = $mv->loadViewperm( $sess, $owner );
		if (count($rows) != 1) {
			JError::raiseError( 500, JText::sprintf('Unable to get entry for %s, %s', $sess, $owner) );
			break;
		}

		foreach ($users as $user) 
		{
			// Check for invalid characters
			if (!eregi("^[0-9a-zA-Z]+[_0-9a-zA-Z]*$", $user)) {
				$this->setError( JText::_('MW_ERROR_INVALID_USERNAME').': '.$user );
				continue;
			}
			
			// Check that the user exist
			$zuser =& JUser::getInstance( $user );
			if (!$zuser || !is_object($zuser) || !$zuser->get('id')) {
				$this->setError( JText::_('MW_ERROR_INVALID_USERNAME').': '.$user );
				continue;
			}
			
			$mv = new MwViewperm( $mwdb );
			$checkrows = $mv->loadViewperm( $sess, $user );

			// If there are no matching entries in viewperm, add a new entry,
			// Otherwise, update the existing entry (e.g. readonly).
			if (count($checkrows) == 0) {
				$mv->sessnum   = $sess;
				$mv->viewuser  = $user;
				$mv->viewtoken = md5(rand());
				$mv->geometry  = $rows[0]->geometry;
				$mv->fwhost    = $rows[0]->fwhost;
				$mv->fwport    = $rows[0]->fwport;
				$mv->vncpass   = $rows[0]->vncpass;
				$mv->readonly  = $readonly;
				$mv->insert();
			} else {
				$mv->sessnum   = $checkrows[0]->sessnum;
				$mv->viewuser  = $checkrows[0]->viewuser;
				$mv->viewtoken = $checkrows[0]->viewtoken;
				$mv->geometry  = $checkrows[0]->geometry;
				$mv->fwhost    = $checkrows[0]->fwhost;
				$mv->fwport    = $checkrows[0]->fwport;
				$mv->vncpass   = $checkrows[0]->vncpass;
				$mv->readonly  = $readonly;
				$mv->update();
			}

			if ($mv->getError()) {
				JError::raiseError( 500, $mv->getError() );
				return;
			}
		}

		// Drop through and re-view the session...
		$this->view();
	}
	
	//-----------
	
	protected function unshare()
	{
		// Check that the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		// Needed objects
		$mwdb =& MwUtils::getMWDBO();
		
		// Incoming
		$sess = JRequest::getVar( 'sess', '' );
		$user = JRequest::getVar( 'username', '' );
		
		// If a username is given, check that the user owns this session.
		if ($user != '') {
			$ms = new MwSession( $mwdb );
			$ms->load( $sess, $juser->get('username') );

			if (!$ms->sesstoken) {
				JError::raiseError( 500, JText::_('COM_TOOLS_ERROR_SESSION_NOT_FOUND').': '.$sess );
				return;
			}
		} else {
			// Otherwise, assume that the user wants to disconnect a session that's been shared with them.
			$user = $juser->get('username');
		}

		// Delete the viewperm
		$mv = new MwViewperm( $mwdb );
		$mv->deleteViewperm( $sess, $user );
		
		if ($user == $juser->get('username')) {
			// Take us back to the main page...
			$this->_redirect = JRoute::_( 'index.php?option=com_myhub' );
			return;
		}
		
		// Drop through and re-view the session...
		$this->view();
	}
	
	//-----------
	
	protected function view()
	{
		// Check that the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		// Incoming
		$app = array();
		$app['sess'] = JRequest::getVar( 'sess', '' );
		
		// Make sure we have an app to invoke
		if (trim($app['sess']) == '') {
			$this->_redirect = JRoute::_( 'index.php?option=com_myhub' );
			return;
		}
		
		// Get the user's IP address
		$ip = JRequest::getVar( 'REMOTE_ADDR', '', 'server' );
		
		// Check authorization
		$authorized = $this->_authorize();
		
		// Double-check that the user can view this session.
		$mwdb =& MwUtils::getMWDBO();
		
		$ms = new MwSession( $mwdb );
		$row = $ms->loadSession( $app['sess'], $authorized );

		if (!is_object($row) || !$row->appname) {
			JError::raiseError( 500, JText::_('COM_TOOLS_ERROR_SESSION_NOT_FOUND').': '.$app['sess'] );
			return;
		}

		if (strstr($row->appname,'_')) {
			$bits = explode('_',$row->appname);
			$v = str_replace('r','',end($bits));
			JRequest::setVar( 'version', $v );
		}
		
		// Get parent tool name - to write correct links
		$database =& JFactory::getDBO();
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );
		$tv = new ToolVersion( $database );
		$parent_toolname = $tv->getToolname($row->appname);
		$toolname = ($parent_toolname) ? $parent_toolname : $row->appname;

		// Get the tool's name
		$tv->loadFromInstance( $row->appname );
		$app['title'] = stripslashes($tv->title);

		// Ensure we found an active session
		if (!$row->sesstoken) {
			JError::raiseError( 500, JText::_('MW_ERROR_SESSION_NOT_FOUND').': '.$app['sess'].'. '.JText::_('MW_SESSION_NOT_FOUND_EXPLANATION') );
			return;
		}
		
		// Get their disk space usage
		$app['percent'] = 0;
		if ($this->config->get('show_storage')) {
			$this->getDiskUsage();
			$app['percent'] = $this->percent;
		}
		
		// Build the view command
		if ($authorized === 'admin') {
			$command = "view user=$row->username ip=$ip sess=".$app['sess'];
		} else {
			$juser =& JFactory::getUser();
			
			$command = "view user=" . $juser->get('username') . " ip=$ip sess=".$app['sess'];
		}

		// Check if we have access to run this tool.
		// If not, force view to be read-only.
		// This will happen in the event of sharing.
		$noaccess = ($this->_getToolAccess($row->appname) == false);
		if ($this->getError()) {
			echo '<!-- '.$this->getError().' -->';
		}
		if ($noaccess) {
		//if (!$noaccess) {
			$command .= " readonly=1";
		}
		
		$app['caption'] = $row->sessname;
		$app['name'] = $row->appname;
		$app['ip'] = $ip;
		$app['username'] = $row->username;
		
		// Call the view command
		$status = $this->middleware($command, $output);

		// Build and display the HTML
		$this->session( $app, $authorized, $output, $toolname );
	}
	
	//-----------

	private function session( $app, $authorized, $output, $toolname ) 
	{
		// Build the page title
		/*$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';
		$title .= ($app['caption']) ? ': '.$app['caption'] : $app['name'];*/
		$title  = JText::_('Tools');
		$title .= ($app['title']) ? ': '.$app['title'] : ': '.$app['name'];
		$title .= ': '.JText::_('Session');
		$title .= ($app['caption']) ? ': '.$app['sess'].' "'.$app['caption'].'"' : ': '.$app['sess'];
		
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		$japp =& JFactory::getApplication();
		$pathway =& $japp->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			//$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
			$pathway->addItem( JText::_('Tools'), 'index.php?option=com_tools' );
		}
		$t = ($app['caption']) ? $app['sess'].' "'.$app['caption'].'"' : $app['sess'];
		$pathway->addItem( $app['title'], 'index.php?option='.$this->_option.'&app='.$toolname );
		if ($this->_task) {
			//$pathway->addItem( JText::_(strtoupper('view')), 'index.php?option='.$this->_option.a.'task=view'.a.'sess='.$app['sess'] );
			$pathway->addItem( JText::_('Session: '.$t), 'index.php?option='.$this->_option.'&app='.$toolname.'&task=session&sess='.$app['sess'] );
		}
		//$t = ($app['caption']) ? $app['caption'] : $app['name'];
		//$pathway->addItem( $t, 'index.php?option='.$this->_option.a.'task=view'.a.'sess='.$app['sess'] );
		
		// Get plugins
		JPluginHelper::importPlugin( 'mw' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Get the active tab (section)
		$tab = JRequest::getVar( 'active', 'session' );
		
		// Trigger the functions that return the areas we'll be using
		$cats = $dispatcher->trigger( 'onMwAreas', array($authorized) );
		
		// Get the sections
		$sections = $dispatcher->trigger( 'onMw', array($toolname, $this->_option, $authorized, array($tab)) );
		
		// Add the default "Profile" section to the beginning of the lists
		/*$body = '';
		if ($tab == 'session') {
			$body = $view->loadTemplate();
		}*/
		
		$cat = array();
		$cat['session'] = JText::_('COM_TOOLS_SESSION');
		array_unshift($cats, $cat);
		//array_unshift($sections, array('html'=>$body,'metadata'=>''));

		// Instantiate the view
		$view = new JView( array('name'=>'session') );
		$view->option = $this->_option;
		$view->app = $app;
		$view->authorized = $authorized;
		$view->cats = $cats;
		$view->sections = $sections;
		$view->config = $this->config;
		$view->tab = $tab;
		$view->output = $output;
		$view->toolname = $toolname;
		if ($app['sess']) {
			// Get the middleware database
			$mwdb =& MwUtils::getMWDBO();

			// Load the viewperm
			$ms = new MwViewperm( $mwdb );
			$view->shares = $ms->loadViewperm( $app['sess'] );
		}
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function stop() 
	{
		// Check that the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		// Incoming
		$sess = JRequest::getVar( 'sess', '' );

		// Ensure we have a session
		if (!$sess) {
			$this->_redirect = JRoute::_('index.php?option=com_myhub');
			return;
		}

		// Check the authorization
		$authorized = $this->_authorize();
		
		// Double-check that the user owns this session.
		$mwdb =& MwUtils::getMWDBO();
		
		$ms = new MwSession( $mwdb );
		if ($authorized === 'admin') {
			$ms->load( $sess );
		} else {
			$ms->load( $sess, $juser->get('username') );
		}
		
		// Did we get a result form the database?
		if (!$ms->username) {
			$this->_redirect = JRoute::_('index.php?option=com_myhub');
			return;
		}
		
		// Stop the session
		$status = $this->middleware("stop $sess", $output);
		if ($status == 0) {
			echo '<p>Stopping '.$sess.'<br />';
			foreach ($output as $line) 
			{
				echo $line."\n";
			}
			echo '</p>'."\n";
		}

		// Take us back to the main page...
		$this->_redirect = JRoute::_('index.php?option=com_myhub');
	}

	//-----------

	protected function purge()
	{
		// Check that the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		//$no_html = JRequest::getInt( 'no_html', 0 );
		$shost = $this->config->get('storagehost');
		
		if (!$shost) {
			$this->_redirect = JRoute::_('index.php?option=com_myhub' );
		}
		
		$juser =& JFactory::getUser();
		
		$degree = JRequest::getVar('degree','default');
		
		$info = array();
		$msg = '';
		$fp = stream_socket_client($shost, $errno, $errstr, 30);
		if (!$fp) {
			$info[] = "$errstr ($errno)\n";
			$this->setError( "$errstr ($errno)\n" );
		} else {
			fwrite($fp, "purge user=". $juser->get('username') .",degree=$degree \n");
			while (!feof($fp)) 
			{
				//$msg .= fgets($fp, 1024)."\n";
				$info[] = fgets($fp, 1024)."\n";
			}
			fclose($fp);
		}
		
		foreach ($info as $line) 
		{
			if (trim($line) !='') {
				$msg .= $line.'<br />';
			}
		}
	
		// Output HTML
		$this->__set('output', $msg);
		$this->storage();
		
		// Take us back to the main page...
		//$this->_redirect = JRoute::_('index.php?option=com_myhub' );
	}
	
	//-----------

	private function getDiskUsage() 
	{
		// Check that the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
	
		bcscale(6);
	
		$du = MwUtils::getDiskUsage($juser->get('username'));
		if (isset($du['space'])) {
			$val = ($du['softspace'] != 0) ? bcdiv($du['space'], $du['softspace']) : 0;
		} else {
			$val = 0;
		}
		$percent = round( $val * 100 );
		$percent = ($percent > 100) ? 100 : $percent;
		
		$this->remaining = (isset($du['remaining'])) ? $du['remaining'] : 0;
		$this->percent = $percent;
		
		if ($this->percent >= 100 && $du['remaining'] == 0) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=storageexceeded');
		}
	}

	//----------------------------------------------------------
	// Views called through AJAX
	//----------------------------------------------------------
	
	protected function renames()
	{
		$mwdb =& MwUtils::getMWDBO();

		$id = JRequest::getInt( 'id', 0 );
		$name = trim(JRequest::getVar( 'name', '' ));
		
		if ($id && $name) {
			$ms = new MwSession( $mwdb );
			$ms->load( $id );
			$ms->sessname = $name;
			$ms->store();
		}
		
		echo $name;
	}

	//-----------

	protected function diskusage()
	{
		// Check that the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		$msgs = JRequest::getInt( 'msgs', 0 );
		
		$du = MwUtils::getDiskUsage( $juser->get('username') );
		if (count($du) <=1) {
			// error
			$percent = 0;
		} else {
			bcscale(6);
			$val = ($du['softspace'] != 0) ? bcdiv($du['space'], $du['softspace']) : 0;
			$percent = round( $val * 100 );
		}

		$amt = ($percent > 100) ? '100' : $percent;
		
		// Instantiate the view
		$view = new JView( array('name'=>'monitor') );
		$view->option = $this->_option;
		$view->amt = $amt;
		$view->du = $du;
		$view->percent = $percent;
		$view->msgs = $msgs;
		$view->ajax = 1;
		$view->writelink = 1;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//----------------------------------------------------------
	// Record the usage of a tool
	//----------------------------------------------------------

	private function recordUsage( $app, $uid ) 
	{
		$database =& JFactory::getDBO();
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );
		$tool = new ToolVersion( $database );
		$tool->loadFromName( $app );
		
		// Ensure a tool is published before recording it
		//if ($tool->state == 1) {
			$created = date( 'Y-m-d H:i:s', time() );
			
			// Get a list of all their recent tools
			$rt = new RecentTool( $database );
			$rows = $rt->getRecords( $uid );

			$thisapp = 0;
			for ($i=0, $n=count( $rows ); $i < $n; $i++) 
			{
				if ($app == trim($rows[$i]->tool)) {
					$thisapp = $rows[$i]->id;
				}
			}
			
			// Get the oldest entry. We may need this later.
			$oldest = end($rows);
		
			// Check if any recent tools are the same as the one just launched
			if ($thisapp) {
				// There was one, so just update its creation time
				$rt->id = $thisapp;
				$rt->uid = $uid;
				$rt->tool = $app;
				$rt->created = $created;
			} else {
				// Check if we've reached 5 recent tools or not
				if (count($rows) < 5) {
					// Still under 5, so insert a new record
					$rt->uid = $uid;
					$rt->tool = $app;
					$rt->created = $created;
				} else {
					// We reached the limit, so update the oldest entry effectively replacing it
					$rt->id = $oldest->id;
					$rt->uid = $uid;
					$rt->tool = $app;
					$rt->created = $created;
				}
			}

			if (!$rt->store()) {
				JError::raiseError( 500, $rt->getError() );
				return;
			}
		//}
	}
	
	//----------------------------------------------------------
	// Invoke the Python script to do real work.
	//----------------------------------------------------------

	protected function middleware( $comm, &$fnoutput ) 
	{
		$retval = 1; // Assume success.
		$fnoutput = array();
		$cmd = "/bin/sh components/".$this->_option."/mw $comm 2>&1 </dev/null";
		exec($cmd,$output,$status);

		$outln = 0;
		if ($status != 0) {
			$retval = 0;
		}

		// Print out the applet tags or the error message, as the case may be.
		foreach ($output as $line) 
		{
			// If it's a new session, catch the session number...
			if (($retval == 1) && preg_match("/^Session is ([0-9]+)/",$line,$sess)) {
				$retval = $sess[1];
			} else {
				if ($status != 0) {
					$fnoutput[$outln] = $line;
				} else {
					$fnoutput[$outln] = $line;
				}
				$outln++;
			}
		}
		
		return $retval;
	}

	//----------------------------------------------------------
	// Authorization checks
	//----------------------------------------------------------

	private function _authorize($uid=0)
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($this->_option, 'manage')) {
			return 'admin';
		}
		
		$xprofile = &XFactory::getProfile();
		if (is_object($xprofile)) {
			// Check if they're a site admin (from LDAP)
			$app =& JFactory::getApplication();
			if (in_array(strtolower($app->getCfg('sitename')), $xprofile->get('admin'))) {
				return 'admin';
			}
		}

		// Check if they're the member
		if ($juser->get('id') == $uid) {
			return true;
		}

		return false;
	}
	
	//-----------
	
	private function _getToolExportControl($exportcontrol)
	{
		$xlog =& XFactory::getLogger();
	    $exportcontrol = strtolower($exportcontrol);

        switch ($exportcontrol)
        {
            case 'us':
                if (GeoUtils::ipcountry($_SERVER['REMOTE_ADDR']) != 'us') {
                    $this->setError('This tool may only be accessed from within the U.S. Your current location could not be confirmed.');
                    $xlog->logDebug("mw::_getToolExportControl($exportcontrol) FAILED US export control check");
                    return false;
                }
                break;

            case 'd1':
                if (GeoUtils::is_d1nation(GeoUtils::ipcountry($_SERVER['REMOTE_ADDR']))) {
                    $this->setError('This tool may not be accessed from your current location due to export restrictions.');
                    $xlog->logDebug("mw::_getToolExportControl($exportcontrol) FAILED D1 export control check");
                    return false;
                } 
                break;

            case 'pu':
                if (!GeoUtils::is_iplocation($_SERVER['REMOTE_ADDR'], $exportcontrol)) {
                    $this->setError('This tool may only be accessed by authorized users while on the West Lafayette campus of Purdue University due to license restrictions.');
                    $xlog->logDebug("mw::_getToolExportControl($exportControl) FAILED PURDUE export control check");
                    return false;
                }
            	break;
        }

        $xlog->logDebug("mw::_getToolExportControl($exportcontrol) PASSED");
		return true;
	}
	
	//-----------

	private function _getToolAccess($tool, $login='') 
	{
		ximport('xuserhelper');
		ximport('xgeoutils');
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.tool.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.toolgroup.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );
		
		$xhub =& XFactory::getHub();
		$xlog =& XFactory::getLogger();
		$database =& JFactory::getDBO();
	    
		// Ensure we have a tool
		if (!$tool) {
			$this->setError('No tool provided.');
			$xlog->logDebug("mw::_getToolAccess($tool,$login) FAILED null tool check");
			return false;
		}

		// Ensure we have a login
		if ($login == '') {
			$juser =& JFactory::getUser();
			$login = $juser->get('username');
			if ($login == '') {
				$xlog->logDebug("mw::_getToolAccess($tool,$login) FAILED null user check");
			    	return false;
			}
		}
		
		$tv = new ToolVersion( $database );
		$tv->loadFromInstance( $tool );

		if (empty($tv)) {
			$xlog->logDebug("mw::_getToolAccess($tool,$login) FAILED null tool version check");
			return false;
		}

		$tg = new ToolGroup( $database );
		$database->setQuery( "SELECT * FROM ".$tg->getTableName()." WHERE toolid=".$tv->toolid );
		$toolgroups = $database->loadObjectList();
		if (empty($toolgroups)) {
			$xlog->logDebug("mw::_getToolAccess($tool,$login) WARNING: no tool member groups");
		}

		$xgroups = XUserHelper::getGroups($juser->get('id'), 'members');
		if (empty($xgroups)) {
			$xlog->logDebug("mw::_getToolAccess($tool,$login) WARNING: user not in any groups");
		}

		// Check if the user is in any groups for this app
		$ingroup = false;
		$groups = array();
		$indevgroup = false;
		if ($xgroups) {
			foreach ($xgroups as $xgroup) 
			{
				$groups[] = $xgroup->cn;
			}
			if ($toolgroups) {
				foreach ($toolgroups as $toolgroup) 
				{
					if (in_array($toolgroup->cn, $groups)) {
						$ingroup = true;
						if ($toolgroup->role == 1)
							$indevgroup = true;
						break;
					}
				}
			}
		}

		$admin = false;
		$ctconfig =& JComponentHelper::getParams( 'com_contribtool' );
		if ($ctconfig->get('admingroup') != '' && in_array($ctconfig->get('admingroup'), $groups)) {
			$admin = true;
		}

		$exportAllowed = $this->_getToolExportControl($tv->exportControl);
		$tisPublished = ($tv->state == 1);
		$tisDev = ($tv->state == 3);
	    $tisGroupControlled = ($tv->toolaccess == '@GROUP');	

		if ($tisDev) {
			if ($indevgroup) {
				$xlog->logDebug("mw::_getToolAccess($tool,$login): DEV TOOL ACCESS GRANTED (USER IN DEVELOPMENT GROUP)");
				return true;
			}
 			else if ($admin) {
				$xlog->logDebug("mw::_getToolAccess($tool,$login): DEV TOOL ACCESS GRANTED (USER IN ADMIN GROUP)");
				return true;
			}
			else
			{
				$xlog->logDebug("mw::_getToolAccess($tool,$login): DEV TOOL ACCESS DENIED (USER NOT IN DEVELOPMENT OR ADMIN GROUPS)");
        		$this->setError("The development version of this tool may only be accessed by members of it's development group.");
				return false;
			}
		}		
		else if ($tisPublished) {
			if ($tisGroupControlled) {
				if ($ingroup) {
					$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED (USER IN ACCESS GROUP)");
					return true;
				}
				else if ($admin) {
					$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED (USER IN ADMIN GROUP)");
					return true;
				}
				else {
					$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS DENIED (USER NOT IN ACCESS OR ADMIN GROUPS)");
        			$this->setError("This tool may only be accessed by members of it's access control groups.");
					return false;
				}
			}
			else {
				if (!$exportAllowed) {
					$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS DENIED (EXPORT DENIED)");
					return false;
				}
				else if ($admin) {
					$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED (USER IN ADMIN GROUP)");
					return true;
				}
				else if ($indevgroup) {
					$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED (USER IN DEVELOPMENT GROUP)");
					return true;
				}
				else {
					$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED");
					return true;
				}
			}
		}
		else {
				$xlog->logDebug("mw::_getToolAccess($tool,$login): UNPUBLISHED TOOL ACCESS DENIED (TOOL NOT PUBLISHED)");
				$this->setError('This tool version is not published.');
				return false;
		}

		return false;
	}
	
	//-----------
 
	protected function listfiles() 
	{
		// Get the app
		$app =& JFactory::getApplication();
		
		$listdir = JRequest::getVar( 'listdir', '' );

		// Build the path
		$path = $this->buildUploadPath($listdir);
		
		$juser =& JFactory::getUser();
		
		// Get the configured upload path
		$base_path  = $this->config->get('storagepath') ? $this->config->get('storagepath') : 'webdav'.DS.'home';
		$base_path .= DS.$juser->get('username');
		
		$dirtree = array();
		$subdir = $listdir;	
		
		if ($subdir) {
			// Make sure the path doesn't end with a slash
			if (substr($subdir, -1) == DS) { 
				$subdir = substr($subdir, 0, strlen($subdir) - 1);
			}
			// Make sure the path doesn't start with a slash
			if (substr($subdir, 0, 1) == DS) { 
				$subdir = substr($subdir, 1, strlen($subdir));
			}
			
			$dirtree = explode(DS, $subdir);
		}
		
		// Get the directory we'll be reading out of
		$d = @dir($path);
		
		$images  = array();
		$folders = array();
		$docs    = array();

		if ($d) {
			// Read the directory contents and sort by type
			while (false !== ($entry = $d->read())) 
			{
				$img_file = $entry; 

				if (is_file($path.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "bmp|gif|jpg|png", $img_file )) {
						$images[$entry] = $img_file;
					} else {
						$docs[$entry] = $img_file;
					}
				} else if (is_dir($path.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'cvs') {
					$folders[$entry] = $img_file;
				}
			}
			$d->close();	

			ksort($images);
			ksort($folders);
			ksort($docs);
		} else {
			$this->setError( JText::sprintf('ERROR_MISSING_DIRECTORY', $path) );
		}
		
		// Instantiate a view
		$view = new JView( array('name'=>'storage', 'layout'=>'filelist') );
		$view->option = $this->_option;
		$view->dirtree = $dirtree;
		$view->docs = $docs;
		$view->folders = $folders;
		$view->images = $images;
		$view->config = $this->config;
		$view->listdir = $listdir;
		$view->path = $path;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	private function buildUploadPath( $listdir, $subdir='' ) 
	{
		if ($subdir) {
			// Make sure the path doesn't end with a slash
			if (substr($subdir, -1) == DS) { 
				$subdir = substr($subdir, 0, strlen($subdir) - 1);
			}
			// Ensure the path starts with a slash
			if (substr($subdir, 0, 1) != DS) { 
				$subdir = DS.$subdir;
			}
		}
		
		$juser =& JFactory::getUser();
		 
		// Get the configured upload path
		$base_path  = $this->config->get('storagepath') ? $this->config->get('storagepath') : 'webdav'.DS.'home';
		$base_path .= DS.$juser->get('username');
		
		if ($base_path) {
			// Make sure the path doesn't end with a slash
			if (substr($base_path, -1) == DS) { 
				$base_path = substr($base_path, 0, strlen($base_path) - 1);
			}
			// Ensure the path starts with a slash
			if (substr($base_path, 0, 1) != DS) { 
				$base_path = DS.$base_path;
			}
		}
		
		// Make sure the path doesn't end with a slash
		if (substr($listdir, -1) == DS) { 
			$listdir = substr($listdir, 0, strlen($listdir) - 1);
		}
		// Ensure the path starts with a slash
		if (substr($listdir, 0, 1) != DS) { 
			$listdir = DS.$listdir;
		}
		// Does the beginning of the $listdir match the config path?
		if (substr($listdir, 0, strlen($base_path)) == $base_path) {
			// Yes - ... this really shouldn't happen
		} else {
			// No - append it
			$listdir = $base_path.$listdir;
		}

		// Build the path
		return $listdir.$subdir;
	}
	
	//-----------

	protected function deletefolder() 
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->listfiles();
			return;
		}
		
		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = urldecode(JRequest::getVar( 'listdir', '' ));
		if (!$listdir) {
			$this->setError( JText::_('Directory not found.') );
			$this->listfiles();
			return;
		}
		
		// Build the path
		$path = $this->buildUploadPath( $listdir);
		
		// Incoming directory to delete
		$folder = urldecode(JRequest::getVar( 'delFolder', '' ));
		if (!$folder) {
			$this->setError( JText::_('Directory not found.') );
			$this->listfiles();
			return;
		}
		
		if (substr($folder,0,1) != DS) {
			$folder = DS.$folder;
		}
		
		// Check if the folder even exists
		if (!is_dir($path.$folder) or !$folder) { 
			$this->setError( JText::_('Directory not found.') ); 
		} else {
			// Attempt to delete the file
			jimport('joomla.filesystem.folder');
			if (!JFolder::delete($path.$folder)) {
				$this->setError( JText::_('Unable to delete directory.') );
			}
		}
		
		// Push through to the media view
		$this->listfiles();
	}

	//-----------

	protected function deletefile() 
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->listfiles();
			return;
		}
		
		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = urldecode(JRequest::getVar( 'listdir', '' ));
		
		// Build the path
		$path = $this->buildUploadPath( $listdir );
		
		// Incoming file to delete
		$file = urldecode(JRequest::getVar( 'delFile', '' ));
		if (!$file) {
			$this->setError( JText::_('File not found.') );
			$this->listfiles();
			return;
		}
		
		// Check if the file even exists
		if (!file_exists($path.DS.$file) or !$file) { 
			$this->setError( JText::_('File not found.') ); 
		} else {
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path.DS.$file)) {
				$this->setError( JText::_('Unable to delete file') );
			}
		}
		
		// Push through to the media view
		$this->listfiles();
	}
}
?>
