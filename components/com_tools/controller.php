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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

ximport('Hubzero_Controller');

/**
 * Short description for 'ToolsController'
 * 
 * Long description (if any) ...
 */
class ToolsController extends Hubzero_Controller
{

	/**
	 * Short description for 'execute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function execute()
	{
		// Get the task
		$this->_task = JRequest::getVar( 'task', '' );

		// Check if middleware is enabled
		if ($this->_task != 'image'
		 && $this->_task != 'css'
		 && (!$this->config->get('mw_on') || ($this->config->get('mw_on') > 1 && $this->_authorize() != 'admin'))) {
			// Redirect to home page
			$this->_redirect = ($this->config->get('mw_redirect')) ? $this->config->get('mw_redirect') : '/home';
			return;
		}

		// Are we banking?
		$upconfig =& JComponentHelper::getParams( 'com_userpoints' );
		$banking = $upconfig->get('bankAccounts');
		$this->banking = ($banking && $this->config->get('banking') ) ? 1: 1;

		if ($banking) {
			ximport('Hubzero_Bank');
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

	/**
	 * Short description for '_buildPathway'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $session Parameter description (if any) ...
	 * @return     void
	 */
	protected function _buildPathway($session=null)
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

	/**
	 * Short description for '_buildTitle'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $session Parameter description (if any) ...
	 * @return     void
	 */
	protected function _buildTitle($session=null)
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

	/**
	 * Short description for 'tools'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function tools()
	{
		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Push some CSS to the template
		$this->_getStyles();

		$xhub  =& Hubzero_Factory::getHub();
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
		ximport('Hubzero_Document');
		$image = Hubzero_Document::getComponentImage('com_tools', 'forge.png', 1);

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

	/**
	 * Short description for 'image'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function image()
	{
		ximport('Hubzero_Document');
		$image = JPATH_SITE . Hubzero_Document::getComponentImage('com_tools', 'forge.png', 1);

		if (is_readable($image)) {
			ob_clean();
			header("Content-Type: image/png");
			readfile($image);
			ob_end_flush();
			exit;
		}
	}

	/**
	 * Short description for 'css'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function css()
	{
		ximport('Hubzero_Document');
		$file = JPATH_SITE . Hubzero_Document::getComponentStylesheet('com_tools', 'site_css.cs');

		if (is_readable($file)) {
			ob_clean();
			header("Content-Type: text/css");
			readfile($file);
			ob_end_flush();
			exit;
		}
	}

	/**
	 * Short description for 'login'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
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

	/**
	 * Short description for 'accessdenied'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
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

	/**
	 * Short description for 'quotaexceeded'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function quotaexceeded()
	{
		// Check that the user is logged in
		if ($this->juser->get('guest')) {
			$this->login();
			return;
		}

		// Build the page title
		$title  = JText::_('Members');
		$title .= ': '.JText::_('View');
		$title .= ': '.stripslashes($this->juser->get('name'));
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
		$pathway->addItem( stripslashes($this->juser->get('name')), 'index.php?option=com_members&id='.$this->juser->get('id') );
		$pathway->addItem( JText::_(strtoupper($this->_option.'_'.$this->_task)), 'index.php?option='.$this->_option.'&task='.$this->_task );

		// Check if the user is an admin.
		$authorized = $this->_authorize();

		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();

		// Get the user's sessions
		$ms = new MwSession( $mwdb );
		$sessions = $ms->getRecords( $this->juser->get('username'), '', false );

		// Instantiate the view
		$view = new JView( array('name'=>'quotaexceeded') );
		$view->option = $this->_option;
		$view->sessions = $sessions;
		if ($authorized) {
			$view->allsessions = $ms->getRecords( $this->juser->get('username'), '', $authorized );
		}
		$view->active = JRequest::getVar( 'active', '' );
		$view->authorized = $authorized;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'storage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $exceeded Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function storage( $exceeded=false )
	{
		// Check that the user is logged in
		if ($this->juser->get('guest')) {
			$this->login();
			return;
		}

		// Build the page title
		/*$title  = JText::_(strtoupper($this->_name));
		$title .= ': '.JText::_('MW_STORAGE_MANAGEMENT');*/
		$title  = JText::_('Members');
		$title .= ': '.JText::_('View');
		$title .= ': '.stripslashes($this->juser->get('name'));
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
		$pathway->addItem( stripslashes($this->juser->get('name')), 'index.php?option=com_members&id='.$this->juser->get('id') );
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
			$view->total = $this->total;
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

	/**
	 * Short description for 'invoke'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function invoke()
	{
		ximport('Hubzero_Ldap');

		// Check that the user is logged in
		if ($this->juser->get('guest')) {
			$this->login();
			return;
		}

		// Needed objects
		$xhub =& Hubzero_Factory::getHub();
		$url = JRequest::getVar('REQUEST_URI','none','server');
		$xlog =& Hubzero_Factory::getLogger();

		// Incoming
		$app = array();
		$app['name']    = JRequest::getVar( 'app', '' );
		$app['name']    = str_replace(':','-',$app['name']);
		$app['number']  = 0;
		$app['version'] = JRequest::getVar( 'version', 'default' );

		// Get the user's IP address
		$ip = JRequest::getVar( 'REMOTE_ADDR', '', 'server' );

		//$xlog->logDebug("mw::invoke URL: $url : " . $app['name'] . " by " . $this->juser->get('username') . " from " . $ip);
		//$xlog->logDebug("mw::invoke REFERER:" . (array_key_exists('HTTP_REFERER',$_SERVER)) ? $_SERVER['HTTP_REFERER'] : 'none');

		// Make sure we have an app to invoke
		if (trim($app['name']) == '') {
			$this->_redirect = JRoute::_( 'index.php?option=com_myhub' );
			return;
		}

		// Get the parent toolname (appname without any revision number "_r423")
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );
		$tv = new ToolVersion( $this->database );

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

		//$xlog->logDebug("mw::invoke " . $app['name'] . " by " . $this->juser->get('username') . " from " . $ip . " _getToolAccess " . $status2);

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
		$this->recordUsage($toolname, $this->juser->get('id'));

		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();

		// Find out how many sessions the user is running.
		$ms = new MwSession( $mwdb );
		$appcount = $ms->getCount( $this->juser->get('username') );

		// Find out how many sessions the user is ALLOWED to run.
		$xprofile =& Hubzero_Factory::getProfile();
		$remain = $xprofile->get('jobsAllowed') - $appcount;

		if (!Hubzero_Ldap::user_exists($xprofile->get('username'))) {
			//$xlog->logDebug("mw::invoke create ldap user for this account");
			$xprofile->create('ldap');
		}

		// Have they reached their session quota?
		if ($remain <= 0) {
			//$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'task=quotaexceeded');
			$this->quotaexceeded();
			return;
		}

		// Get their disk space usage
		$this->getDiskUsage('hard');  // Check their hardspace limit instead of the softspace
		$this->_redirect = '';

		$app['percent'] = 0;
		if ($this->config->get('show_storage')) {
			$app['percent'] = $this->percent;
		}
		//if ($this->_redirect != '' && $this->juser->get('username') == 'zooley') {
		if ($this->percent >= 100) {
			$this->storage(true);
			return;
		}

		// Get plugins
		JPluginHelper::importPlugin( 'mw', $toolname );
		$dispatcher =& JDispatcher::getInstance();

		// Trigger any events that need to be called before session invoke
		$dispatcher->trigger( 'onBeforeSessionInvoke', array($toolname, $app['version']) );

		// We've passed all checks so let's actually start the session
		$sess = $this->middleware("start user=" . $this->juser->get('username') . " ip=$ip app=".$app['name']." version=".$app['version'], $output);

		// Trigger any events that need to be called after session invoke
		$dispatcher->trigger( 'onAfterSessionInvoke', array($toolname, $app['version']) );

		// Get a count of the number of sessions of this specific tool
		$appcount = $ms->getCount( $this->juser->get('username'), $app['name'] );
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
		$app['username'] = $this->juser->get('username');

		$rtrn = JRequest::getVar('return', '');
		// Build and display the HTML
		//$this->session( $app, $authorized, $output, $toolname );
		//$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&app='.$toolname.'&task=session&sess='.$sess);
		$xhub =& Hubzero_Factory::getHub();
		$xhub->redirect( JRoute::_('index.php?option='.$this->_option.'&app='.$toolname.'&task=session&sess='.$sess.'&return='.$rtrn) );
	}

	/**
	 * Short description for 'share'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function share()
	{
		// Check that the user is logged in
		if ($this->juser->get('guest')) {
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
		$row = $ms->checkSession( $sess, $this->juser->get('username') );

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

	/**
	 * Short description for 'unshare'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function unshare()
	{
		// Check that the user is logged in
		if ($this->juser->get('guest')) {
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
			$ms->load( $sess, $this->juser->get('username') );

			if (!$ms->sesstoken) {
				JError::raiseError( 500, JText::_('COM_TOOLS_ERROR_SESSION_NOT_FOUND').': '.$sess );
				return;
			}
		} else {
			// Otherwise, assume that the user wants to disconnect a session that's been shared with them.
			$user = $this->juser->get('username');
		}

		// Delete the viewperm
		$mv = new MwViewperm( $mwdb );
		$mv->deleteViewperm( $sess, $user );

		if ($user == $this->juser->get('username')) {
			// Take us back to the main page...
			$this->_redirect = JRoute::_( 'index.php?option=com_myhub' );
			return;
		}

		// Drop through and re-view the session...
		$this->view();
	}

	/**
	 * Short description for 'view'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function view()
	{
		// Check that the user is logged in
		if ($this->juser->get('guest')) {
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

		$rtrn = JRequest::getVar('return', '');

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
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );
		$tv = new ToolVersion( $this->database );
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
			$command = "view user=" . $this->juser->get('username') . " ip=$ip sess=".$app['sess'];
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

		// Get plugins
		JPluginHelper::importPlugin( 'mw', $app['name'] );
		$dispatcher =& JDispatcher::getInstance();

		// Trigger any events that need to be called before session start
		$dispatcher->trigger( 'onBeforeSessionStart', array($toolname, $tv->revision) );

		// Call the view command
		$status = $this->middleware($command, $output);

		// Trigger any events that need to be called after session start
		$dispatcher->trigger( 'onAfterSessionStart', array($toolname, $tv->revision) );

		// Build and display the HTML
		$this->session( $app, $authorized, $output, $toolname, $rtrn );
	}

	/**
	 * Short description for 'session'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $app Parameter description (if any) ...
	 * @param      unknown $authorized Parameter description (if any) ...
	 * @param      unknown $output Parameter description (if any) ...
	 * @param      string $toolname Parameter description (if any) ...
	 * @param      unknown $rtrn Parameter description (if any) ...
	 * @return     void
	 */
	private function session( $app, $authorized, $output, $toolname, $rtrn=NULL )
	{
		// Build the page title
		/*$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';
		$title .= ($app['caption']) ? ': '.$app['caption'] : $app['name'];*/
		$title  = JText::_('Resources').': '.JText::_('Tools');
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
			$pathway->addItem( JText::_('Resources'), 'index.php?option=com_resources' );
		}
		$pathway->addItem( JText::_('Tools'), 'index.php?option=com_resources&type=tools' );
		$t = ($app['caption']) ? $app['sess'].' "'.$app['caption'].'"' : $app['sess'];
		$pathway->addItem( $app['title'], 'index.php?option='.$this->_option.'&app='.$toolname );
		if ($this->_task) {
			//$pathway->addItem( JText::_(strtoupper('view')), 'index.php?option='.$this->_option.a.'task=view'.a.'sess='.$app['sess'] );
			$pathway->addItem( JText::_('Session: '.$t), 'index.php?option='.$this->_option.'&app='.$toolname.'&task=session&sess='.$app['sess'] );
		}
		//$t = ($app['caption']) ? $app['caption'] : $app['name'];
		//$pathway->addItem( $t, 'index.php?option='.$this->_option.a.'task=view'.a.'sess='.$app['sess'] );

		// Instantiate the view
		$view = new JView( array('name'=>'session') );
		$view->option = $this->_option;
		$view->app = $app;
		$view->authorized = $authorized;
		$view->config = $this->config;
		$view->output = $output;
		$view->toolname = $toolname;
		$view->rtrn = $rtrn;
		$view->total = $this->total;
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

	/**
	 * Short description for 'stop'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function stop()
	{
		// Check that the user is logged in
		if ($this->juser->get('guest')) {
			$this->login();
			return;
		}

		// Incoming
		$sess = JRequest::getVar( 'sess', '' );
		$rtrn = base64_decode( JRequest::getVar('return', '', 'method', 'base64') );

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
			$ms->load( $sess, $this->juser->get('username') );
		}

		// Did we get a result form the database?
		if (!$ms->username) {
			$this->_redirect = JRoute::_('index.php?option=com_myhub');
			return;
		}

		// Get plugins
		JPluginHelper::importPlugin( 'mw', $ms->appname );
		$dispatcher =& JDispatcher::getInstance();

		// Trigger any events that need to be called before session stop
		$dispatcher->trigger( 'onBeforeSessionStop', array($ms->appname) );

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

		// Trigger any events that need to be called after session stop
		$dispatcher->trigger( 'onAfterSessionStop', array($ms->appname) );

		// Take us back to the main page...
		if ($rtrn) {
			$this->_redirect = $rtrn;
		} else {
			$this->_redirect = JRoute::_('index.php?option=com_myhub');
		}
	}

	/**
	 * Short description for 'purge'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function purge()
	{
		// Check that the user is logged in
		if ($this->juser->get('guest')) {
			$this->login();
			return;
		}

		//$no_html = JRequest::getInt( 'no_html', 0 );
		$shost = $this->config->get('storagehost');

		if (!$shost) {
			$this->_redirect = JRoute::_('index.php?option=com_myhub' );
		}

		$degree = JRequest::getVar('degree','default');

		$info = array();
		$msg = '';
		$fp = stream_socket_client($shost, $errno, $errstr, 30);
		if (!$fp) {
			$info[] = "$errstr ($errno)\n";
			$this->setError( "$errstr ($errno)\n" );
		} else {
			fwrite($fp, "purge user=". $this->juser->get('username') .",degree=$degree \n");
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

	/**
	 * Short description for 'getDiskUsage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $type Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function getDiskUsage($type='soft')
	{
		// Check that the user is logged in
		if ($this->juser->get('guest')) {
			$this->login();
			return;
		}

		bcscale(6);

		$du = MwUtils::getDiskUsage($this->juser->get('username'));
		if (isset($du['space'])) {
			if ($type == 'hard') {
				$val = ($du['hardspace'] != 0) ? bcdiv($du['space'], $du['hardspace']) : 0;
			} else {
				$val = ($du['softspace'] != 0) ? bcdiv($du['space'], $du['softspace']) : 0;
			}
		} else {
			$val = 0;
		}
		$percent = round( $val * 100 );
		$percent = ($percent > 100) ? 100 : $percent;

		if (isset($du['softspace']))
		{
			$total = $du['softspace'] / 1024000000;
		}
		else
		{
			$total = 0;
		}

		$this->remaining = (isset($du['remaining'])) ? $du['remaining'] : 0;
		$this->percent = $percent;
		$this->total = $total;

		//if ($this->percent >= 100 && $this->remaining == 0) {
		if ($this->percent >= 100) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=storageexceeded');
		}
	}

	//----------------------------------------------------------
	// Views called through AJAX
	//----------------------------------------------------------

	/**
	 * Short description for 'renames'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
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

	/**
	 * Short description for 'diskusage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function diskusage()
	{
		// Check that the user is logged in
		if ($this->juser->get('guest')) {
			$this->login();
			return;
		}

		$msgs = JRequest::getInt( 'msgs', 0 );

		$du = MwUtils::getDiskUsage( $this->juser->get('username') );
		if (count($du) <=1) {
			// error
			$percent = 0;
		} else {
			bcscale(6);
			$val = ($du['softspace'] != 0) ? bcdiv($du['space'], $du['softspace']) : 0;
			$percent = round( $val * 100 );
		}

		$amt = ($percent > 100) ? '100' : $percent;
		$total = $du['softspace'] / 1024000000;

		// Instantiate the view
		$view = new JView( array('name'=>'monitor') );
		$view->option = $this->_option;
		$view->amt = $amt;
		$view->total = $total;
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

	/**
	 * Short description for 'recordUsage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $app Parameter description (if any) ...
	 * @param      unknown $uid Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function recordUsage( $app, $uid )
	{
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );
		$tool = new ToolVersion( $this->database );
		$tool->loadFromName( $app );

		// Ensure a tool is published before recording it
		//if ($tool->state == 1) {
			$created = date( 'Y-m-d H:i:s', time() );

			// Get a list of all their recent tools
			$rt = new RecentTool( $this->database );
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

	/**
	 * Short description for 'middleware'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $comm Parameter description (if any) ...
	 * @param      array &$fnoutput Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
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

	/**
	 * Short description for '_authorize'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $uid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	protected function _authorize($uid=0)
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) {
			return false;
		}

		// Check if they're a site admin (from Joomla)
		if ($this->juser->authorize($this->_option, 'manage')) {
			return 'admin';
		}

		$xprofile = &Hubzero_Factory::getProfile();
		if (is_object($xprofile)) {
			// Check if they're a site admin (from LDAP)
			$app =& JFactory::getApplication();
			if (in_array(strtolower($app->getCfg('sitename')), $xprofile->get('admin'))) {
				return 'admin';
			}
		}

		// Check if they're the member
		if ($this->juser->get('id') == $uid) {
			return true;
		}

		return false;
	}

	/**
	 * Short description for '_getToolExportControl'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $exportcontrol Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	private function _getToolExportControl($exportcontrol)
	{
		$xlog =& Hubzero_Factory::getLogger();
	    $exportcontrol = strtolower($exportcontrol);

        switch ($exportcontrol)
        {
            case 'us':
                if (Hubzero_Geo::ipcountry($_SERVER['REMOTE_ADDR']) != 'us') {
                    $this->setError('This tool may only be accessed from within the U.S. Your current location could not be confirmed.');
                    $xlog->logDebug("mw::_getToolExportControl($exportcontrol) FAILED US export control check");
                    return false;
                }
                break;

            case 'd1':
                if (Hubzero_Geo::is_d1nation(Hubzero_Geo::ipcountry($_SERVER['REMOTE_ADDR']))) {
                    $this->setError('This tool may not be accessed from your current location due to export restrictions.');
                    $xlog->logDebug("mw::_getToolExportControl($exportcontrol) FAILED D1 export control check");
                    return false;
                }
                break;

            case 'pu':
                if (!Hubzero_Geo::is_iplocation($_SERVER['REMOTE_ADDR'], $exportcontrol)) {
                    $this->setError('This tool may only be accessed by authorized users while on the West Lafayette campus of Purdue University due to license restrictions.');
                    $xlog->logDebug("mw::_getToolExportControl($exportControl) FAILED PURDUE export control check");
                    return false;
                }
            	break;
        }

        //$xlog->logDebug("mw::_getToolExportControl($exportcontrol) PASSED");
		return true;
	}

	/**
	 * Short description for '_getToolAccess'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $tool Parameter description (if any) ...
	 * @param      string $login Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	private function _getToolAccess($tool, $login='')
	{
		ximport('Hubzero_User_Helper');
		ximport('Hubzero_Geo');
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.tool.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.toolgroup.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );

		$xhub =& Hubzero_Factory::getHub();
		$xlog =& Hubzero_Factory::getLogger();

		// Ensure we have a tool
		if (!$tool) {
			$this->setError('No tool provided.');
			$xlog->logDebug("mw::_getToolAccess($tool,$login) FAILED null tool check");
			return false;
		}

		// Ensure we have a login
		if ($login == '') {
			$login = $this->juser->get('username');
			if ($login == '') {
				$xlog->logDebug("mw::_getToolAccess($tool,$login) FAILED null user check");
				return false;
			}
		}

		$tv = new ToolVersion( $this->database );
		$tv->loadFromInstance( $tool );

		if (empty($tv)) {
			$xlog->logDebug("mw::_getToolAccess($tool,$login) FAILED null tool version check");
			return false;
		}

		$tg = new ToolGroup( $this->database );
		$this->database->setQuery( "SELECT * FROM ".$tg->getTableName()." WHERE toolid=".$tv->toolid );
		$toolgroups = $this->database->loadObjectList();
		if (empty($toolgroups)) {
			//$xlog->logDebug("mw::_getToolAccess($tool,$login) WARNING: no tool member groups");
		}

		$xgroups = Hubzero_User_Helper::getGroups($this->juser->get('id'), 'members');
		if (empty($xgroups)) {
			//$xlog->logDebug("mw::_getToolAccess($tool,$login) WARNING: user not in any groups");
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
				//$xlog->logDebug("mw::_getToolAccess($tool,$login): DEV TOOL ACCESS GRANTED (USER IN DEVELOPMENT GROUP)");
				return true;
			}
 			else if ($admin) {
				//$xlog->logDebug("mw::_getToolAccess($tool,$login): DEV TOOL ACCESS GRANTED (USER IN ADMIN GROUP)");
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
					//$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED (USER IN ACCESS GROUP)");
					return true;
				}
				else if ($admin) {
					//$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED (USER IN ADMIN GROUP)");
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
					//$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED (USER IN ADMIN GROUP)");
					return true;
				}
				else if ($indevgroup) {
					//$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED (USER IN DEVELOPMENT GROUP)");
					return true;
				}
				else {
					//$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED");
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

	/**
	 * Short description for 'listfiles'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function listfiles()
	{
		// Get the app
		$app =& JFactory::getApplication();

		$listdir = JRequest::getVar( 'listdir', '' );

		// Build the path
		$path = $this->buildUploadPath($listdir);

		// Get the configured upload path
		$base_path  = $this->config->get('storagepath') ? $this->config->get('storagepath') : 'webdav'.DS.'home';
		$base_path .= DS.$this->juser->get('username');

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

				if (is_file($path.DS.$img_file) && substr($entry,0,1) != '.' && substr($entry,0,1) != '..' && strtolower($entry) !== 'index.html') {
					if (eregi( "bmp|gif|jpg|png", $img_file )) {
						$images[$entry] = $img_file;
					} else {
						$docs[$entry] = $img_file;
					}
				} else if (is_dir($path.DS.$img_file) && substr($entry,0,1) != '.' && substr($entry,0,1) != '..' && strtolower($entry) !== 'cvs') {
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

	/**
	 * Short description for 'buildUploadPath'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $listdir Parameter description (if any) ...
	 * @param      string $subdir Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

		// Get the configured upload path
		$base_path  = $this->config->get('storagepath') ? $this->config->get('storagepath') : 'webdav'.DS.'home';
		$base_path .= DS.$this->juser->get('username');

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

	/**
	 * Short description for 'deletefolder'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function deletefolder()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) {
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

	/**
	 * Short description for 'deletefile'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function deletefile()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) {
			$this->listfiles();
			return;
		}

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = urldecode(JRequest::getVar( 'listdir', '' ));

		// Build the path
		$path = $this->buildUploadPath( $listdir );

		// Incoming file to delete
		$file = urldecode(JRequest::getVar( 'file', '' ));
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

