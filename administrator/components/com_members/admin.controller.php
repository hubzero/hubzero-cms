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
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------

class MembersController extends JObject
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
		
		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		// Set the component name
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
		// Load the component config
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		
		$default = 'browse';
		
		$task = strtolower(JRequest::getVar('task', $default, 'default'));
		
		$thisMethods = get_class_methods( get_class( $this ) );
		if (!in_array($task, $thisMethods)) {
			$task = $default;
			if (!in_array($task, $thisMethods)) {
				return JError::raiseError( 404, JText::_('Task ['.$task.'] not found') );
			}
		}

		$this->_task = $task;
		$this->$task();
	}

	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message );
		}
	}

	//----------------------------------------------------------
	//  Views
	//----------------------------------------------------------
	
	protected function browse()
	{
		$app =& JFactory::getApplication();
		$database =& JFactory::getDBO();
		
		// Get filters
		$filters = array();
		//$filters['search'] = urldecode(JRequest::getString('search'));
		$filters['search'] = urldecode($app->getUserStateFromRequest($this->_option.'.search', 'search', ''));
		//$filters['search_field'] = urldecode(JRequest::getString('search_field', 'name'));
		$filters['search_field'] = urldecode($app->getUserStateFromRequest($this->_option.'.search_field', 'search_field', 'name'));
		//$filters['sortby'] = JRequest::getVar( 'sortby', 'surname' );
		$filters['sortby'] = $app->getUserStateFromRequest($this->_option.'.sortby', 'sortby', 'surname');
		$filters['show']   = '';
		$filters['scope']  = '';
		$filters['authorized'] = true;
		
		// Get configuration
		$config = JFactory::getConfig();
		
		// Get paging variables
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start'] = JRequest::getInt('limitstart', 0);

		$obj = new MembersProfile( $database );

		// Get a record count
		$total = $obj->getCount( $filters, true );
		
		// Get records
		$rows = $obj->getRecords( $filters, true );

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Output HTML
		MembersHtml::browse( $rows, $pageNav, $this->_option, $filters );
	}

	//-----------

	protected function add()
	{
		MembersHtml::add( $this->_option );
	}

	//-----------

	protected function edit($id=0) 
	{
		$database =& JFactory::getDBO();
		
		if (!$id) {
			// Incoming
			$ids = JRequest::getVar( 'id', array() );

			// Get the single ID we're working with
			if (is_array($ids)) {
				$id = (!empty($ids)) ? $ids[0] : 0;
			} else {
				$id = 0;
			}
		}
		
		// Initiate database class and load info
		$profile = new XProfile();
		$profile->load( $id );
		
		// Get the user's interests (tags)
		include_once( JPATH_ROOT.DS.'components'.DS.$this->_option.DS.'members.tags.php' );
		
		$mt = new MembersTags( $database );
		$tags = $mt->get_tag_string( $id );
		
		// Ouput HTML
		MembersHtml::edit( $profile, $this->_option, $tags );
	}

	//-----------
	
	protected function apply() 
	{
		$this->save(0);
	}
	
	//-----------
	
	protected function save($redirect=1) 
	{
		// Incoming user ID
		$id = JRequest::getInt( 'id', 0, 'post' );
		
		// Do we have an ID?
		if (!$id) {
			echo MembersHtml::error( JText::_('MEMBERS_NO_ID') );
			return;
		}
		
		// Incoming profile edits
		$p = JRequest::getVar( 'profile', array(), 'post' );
		
		// Load the profile
		$profile = new XProfile();
		$profile->load( $id );
		
		// Set the new info
		$profile->set('givenName', trim($p['givenName']));
		$profile->set('middleName', trim($p['middleName']));
		$profile->set('surname', trim($p['surname']));
		$name  = trim($p['givenName']).' ';
		$name .= (trim($p['middleName']) != '') ? trim($p['middleName']).' ' : '';
		$name .= trim($p['surname']);
		$profile->set('name', $name);
		if (isset($p['vip'])) {
			$profile->set('vip',$p['vip']);
		} else {
			$profile->set('vip',0);
		}
		$profile->set('url', trim($p['url']));
		$profile->set('phone', trim($p['phone']));
		$profile->set('orgtype', trim($p['orgtype']));
		$profile->set('organization', trim($p['organization']));
		$profile->set('bio', trim($p['bio']));
		if (isset($p['public'])) {
			$profile->set('public',$p['public']);
		} else {
			$profile->set('public',0);
		}
		$profile->set('modifiedDate', date( 'Y-m-d H:i:s', time() ));
		
		$profile->set('jobsAllowed', intval(trim($p['jobsAllowed'])));
		
		$ec = JRequest::getInt( 'emailConfirmed', 0, 'post' );
		if ($ec) {
				$profile->set('emailConfirmed', 1);
		} else {
			ximport('xregistrationhelper');
			$confirm = XRegistrationHelper::genemailconfirm();
			$profile->set('emailConfirmed', $confirm);
		}
		$se = JRequest::getInt( 'shadowExpire', 0, 'post' );
		if ($se) 
		    $profile->set('shadowExpire','1');
		else
		    $profile->set('shadowExpire', null);
		
		if (isset($p['email'])) {
			$profile->set('email', trim($p['email']));
		}
		if (isset($p['mailPreferenceOption'])) {
			$profile->set('mailPreferenceOption', trim($p['mailPreferenceOption']));
		} else {
			$profile->set('mailPreferenceOption', 0);
		}
		
		if (!empty($p['gender']))
			$profile->set('gender', trim($p['gender']));
		
		/*if (is_array($p['disability'])) {
			$dises = $p['disability'];
			$disabilities = array();
			foreach ($dises as $d=>$v) 
			{
				if ($d != 'yes') {
					$disabilities[] = $d;
				}
			}
			$profile->set('disability',$disabilities);
		} else {
			$profile->set('disability',array($p['disability']));
		}*/

		if (!empty($p['disability']))
		if ($p['disability'] == 'yes') {
			if (!is_array($p['disabilities'])) {
				$p['disabilities'] = array();
			}
			if (count($p['disabilities']) == 1 && isset($p['disabilities']['other']) && empty($p['disabilities']['other']))
				$profile->set('disability',array('no'));
			else
				$profile->set('disability',$p['disabilities']);
		} else {
			$profile->set('disability',array($p['disability']));
		}
		
		/*if (is_array($p['hispanic'])) {
			$hises = $p['hispanic'];
			$hispanic = array();
			foreach ($hises as $h=>$v) 
			{
				if ($h != 'yes') {
					$hispanic[] = $h;
				}
			}
			$profile->set('hispanic',$hispanic);
		} else {
			$profile->set('hispanic',array($p['hispanic']));
		}*/
		if (!empty($p['hispanic']))
		if ($p['hispanic'] == 'yes') {
			if (!is_array($p['hispanics'])) {
				$p['hispanics'] = array();
			}
			if (count($p['hispanics']) == 1 && isset($p['hispanics']['other']) && empty($p['hispanics']['other']))
				$profile->set('hispanic', array('no'));
			else
				$profile->set('hispanic',$p['hispanics']);
		} else {
			$profile->set('hispanic',array($p['hispanic']));
		}
		
		if (isset($p['race']) && is_array($p['race'])) {
			$profile->set('race',$p['race']);
		}
		
		// Do we have a new pass?
		$newpass = trim(JRequest::getVar( 'newpass', '', 'post' ));
		if ($newpass != '') {
			ximport('xuserhelper');
			 // Encrypt the password and update the profile
			$userPassword = XUserHelper::encrypt_password($newpass);
			$profile->set('userPassword', $userPassword);
		}

		// Save the changes
		if (!$profile->update()) {
			JError::raiseWarning('', $profile->getError() );
			return false;
		}
		
		// Get the user's interests (tags)
		$tags = trim(JRequest::getVar( 'tags', '' ));
		
		// Process tags
		include_once( JPATH_ROOT.DS.'components'.DS.$this->_option.DS.'members.tags.php' );
		
		$database =& JFactory::getDBO();
		$mt = new MembersTags( $database );
		$mt->tag_object($id, $id, $tags, 1, 1);
		
		// Make sure certain changes make it back to the Joomla user table
		$juser =& JUser::getInstance($id);
		$juser->set('name', $name);
		if (!$juser->save()) {
			JError::raiseWarning('', JText::_( $juser->getError() ));
			return false;
		}
		
		if ($redirect) {
			// Redirect
			$this->_redirect = JRoute::_( 'index.php?option='.$this->_option );
			$this->_message = JText::_('MEMBER_SAVED');
		} else {
			$this->edit($id);
		}
	}
	
	//-----------
	
	/*protected function resetpass() 
	{
		// Incoming user ID
		$id = JRequest::getInt( 'id', 0, 'post' );
		
		// Do we have an ID?
		if (!$id) {
			echo MembersHtml::error( JText::_('MEMBERS_NO_ID') );
			return;
		}
		
		// Load the profile
		$profile = new XProfile();
		$profile->load( $id );
		
		// Generate a new password
		$newpass = XRegistrationHelper::userpassgen();
		
		// Encrypt the password and update the profile
		$userPassword = XUserHelper::encrypt_password($newpass);
		$profile->set('userPassword', $userPassword);
		
		// Save the changes
		if (!$profile->update()) {
			JError::raiseWarning('', $profile->getError() );
			return false;
		}
		
		// Push through to the edit view
		$this->edit($id);
	}*/
	
	//-----------

	protected function remove() 
	{
		// Load the component config
		$config = $this->config;
		
		// Incoming
		$ids = JRequest::getVar( 'ids', array() );

		// Get the single ID we're working with
		if (!is_array($ids)) {
			$ids = array();
		}
		
		// Do we have any IDs?
		if (!empty($ids)) {
			$database =& JFactory::getDBO();
		
			// Loop through each ID and delete the necessary items
			foreach ($ids as $id) 
			{
				// Delete any associated pictures
				$dir  = FileUploadUtils::niceidformat( $id );
				$path = JPATH_ROOT.DS.$config->get('webpath').DS.$dir;
				if (!file_exists($path.DS.$file) or !$file) { 
					$this->setError( JText::_('FILE_NOT_FOUND') ); 
				} else {
					unlink($path.DS.$file);
				}
				
				// Remove any contribution associations
				$assoc = new MembersAssociation( $database );
				$assoc->authorid = $id;
				$assoc->deleteAssociations();
				
				// Remove the profile
				$profile = new XProfile();
				$profile->load( $id );
				$profile->delete();
			}
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('MEMBER_REMOVED');
	}
	
	//-----------

	protected function cancel()
	{
		$this->_redirect = 'index.php?option='.$this->_option;
	}
	
	//----------------------------------------------------------
	//  Image handling
	//----------------------------------------------------------

	protected function upload()
	{
		// Load the component config
		$config = $this->config;
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('MEMBERS_NO_ID') );
			$this->img( '', $id );
			return;
		}
		
		// Incoming file
		$file = JRequest::getVar( 'upload', '', 'files', 'array' );
		if (!$file['name']) {
			$this->setError( JText::_('MEMBERS_NO_FILE') );
			$this->img( '', $id );
			return;
		}
		
		// Build upload path
		$dir  = FileUploadUtils::niceidformat( $id );
		$path = JPATH_ROOT;
		if (substr($config->get('webpath'), 0, 1) != DS) {
			$path .= DS;
		}
		$path .= $config->get('webpath').DS.$dir;
	
		if (!is_dir( $path )) {
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path, 0777 )) {
				$this->setError( JText::_('UNABLE_TO_CREATE_UPLOAD_PATH') );
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
			$this->setError( JText::_('ERROR_UPLOADING') );
			$file = $curfile;
		} else {
			$ih = new MembersImgHandler();
			
			// Do we have an old file we're replacing?
			$curfile = JRequest::getVar( 'currentfile', '' );
			
			if ($curfile != '') {
				// Yes - remove it
				if (file_exists($path.DS.$curfile)) {
					if (!JFile::delete($path.DS.$curfile)) {
						$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
						$this->img( $file['name'], $id );
						return;
					}
				}
				$curthumb = $ih->createThumbName($curfile);
				if (file_exists($path.DS.$curthumb)) {
					if (!JFile::delete($path.DS.$curthumb)) {
						$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
						$this->img( $file['name'], $id );
						return;
					}
				}
			}
			
			// Instantiate a profile, change some info and save
			$profile = new XProfile();
			$profile->load( $id );
			$profile->set('picture', $file['name']);
			if (!$profile->update()) {
				$this->setError( $profile->getError() );
			}
			
			// Resize the image if necessary
			$ih->set('image',$file['name']);
			$ih->set('path',$path.DS);
			$ih->set('maxWidth', 186);
			$ih->set('maxHeight', 186);
			if (!$ih->process()) {
				$this->setError( $ih->getError() );
			}
			
			// Create a thumbnail image
			$ih->set('maxWidth', 50);
			$ih->set('maxHeight', 50);
			$ih->set('cropratio', '1:1');
			$ih->set('outputName', $ih->createThumbName());
			if (!$ih->process()) {
				$this->setError( $ih->getError() );
			}
			
			$file = $file['name'];
		}
	
		// Push through to the image view
		$this->img( $file, $id );
	}

	//-----------

	protected function deleteimg()
	{
		// Load the component config
		$config = $this->config;
		
		// Incoming member ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('MEMBERS_NO_ID') );
			$this->img( '', $id );
			return;
		}
		
		// Incoming file
		$file = JRequest::getVar( 'file', '' );
		if (!$file) {
			$this->setError( JText::_('MEMBERS_NO_FILE') );
			$this->img( '', $id );
			return;
		}
		
		// Build the file path
		$dir  = FileUploadUtils::niceidformat( $id );
		$path = JPATH_ROOT;
		if (substr($config->get('webpath'), 0, 1) != DS) {
			$path .= DS;
		}
		$path .= $config->get('webpath').DS.$dir;

		if (!file_exists($path.DS.$file) or !$file) { 
			$this->setError( JText::_('FILE_NOT_FOUND') ); 
		} else {
			$ih = new MembersImgHandler();
			
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path.DS.$file)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
				$this->img( $file, $id );
				return;
			}
			
			$curthumb = $ih->createThumbName($file);
			if (file_exists($path.DS.$curthumb)) {
				if (!JFile::delete($path.DS.$curthumb)) {
					$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
					$this->img( $file, $id );
					return;
				}
			}
			
			// Instantiate a profile, change some info and save
			$profile = new XProfile();
			$profile->load( $id );
			$profile->set('picture', '');
			if (!$profile->update()) {
				$this->setError( $profile->getError() );
			}

			$file = '';
		}
	
		$this->img( $file, $id );
	}

	//-----------

	protected function img( $file='', $id=0 )
	{
		// Load the component config
		$config = $this->config;
		
		// Get the app
		$app =& JFactory::getApplication();
		
		// Incoming
		if (!$id) {
			$id = JRequest::getInt( 'id', 0, 'get' );
		}
		$file = ($file) 
			  ? $file 
			  : JRequest::getVar( 'file', '', 'get' );
		
		// Build the file path
		$dir = FileUploadUtils::niceidformat( $id );
		$path = JPATH_ROOT;
		if (substr($config->get('webpath'), 0, 1) != DS) {
			$path .= DS;
		}
		$path .= $config->get('webpath').DS.$dir;
		
		MembersHtml::writeImage( $app, $this->_option, $config->get('webpath'), $config->get('defaultpic'), $dir, $file, $path, $id, $this->getErrors() );
	}
	
	//-----------

	protected function addgroup()
	{
		// Incoming member ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('MEMBERS_NO_ID') );
			$this->group( $id );
		}
		
		// Incoming group table
		$tbl = JRequest::getVar( 'tbl', '' );
		if (!$tbl) {
			$this->setError( JText::_('MEMBERS_NO_GROUP_TABLE') );
			$this->group( $id );
		}
		
		// Incoming group ID
		$gid = JRequest::getInt( 'gid', 0 );
		if (!$gid) {
			$this->setError( JText::_('MEMBERS_NO_GROUP_ID') );
			$this->group( $id );
		}
		
		// Load the group page
		ximport('xgroup');
		$group = new XGroup();
		$group->select( $gid );
		
		// Add the user to the group table
		$group->add( $tbl, array($id) );
		if ($tbl == 'managers') {
			// Ensure they're added to the members list as well if they're a manager
			$group->add( 'members', array($id) );
		}
		
		$group->update();
		
		// Push through to the groups view
		$this->group( $id );
	}
	
	//-----------

	protected function group( $id=0 )
	{
		// Get the app
		$app =& JFactory::getApplication();
		
		// Incoming
		if (!$id) {
			$id = JRequest::getInt( 'id', 0, 'get' );
		}
		
		// Get a list of all groups
		$filters = array();
		$filters['type'] = 'all';
		$filters['limit'] = 'all';
		$filters['search'] = '';
		$filters['limit'] = 'all';
		$filters['fields'] = array('description','published','gidNumber','type');
		
		ximport('xgroup');
		
		// Get a list of all groups
		$rows = XGroupHelper::get_groups($filters['type'], false, $filters);
		
		// Output HTML
		MembersHtml::writeGroups( $app, $this->_option, $id, $rows, $this->getErrors() );
	}
	
	//----------------------------------------------------------
	//  Hosts
	//----------------------------------------------------------
	
	protected function addhost()
	{
		// Incoming member ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('MEMBERS_NO_ID') );
			$this->hosts();
		}
		
		// Load the profile
		$profile = new XProfile();
		$profile->load( $id );
		
		// Incoming host
		$host = JRequest::getVar( 'host', '' );
		if (!$host) {
			$this->setError( JText::_('MEMBERS_NO_HOST') );
			$this->hosts( $id );
		}
		
		$hosts = $profile->get('host');
		$hosts[] = $host;
		
		// Update the hosts list
		$profile->set('host', $hosts);
		if (!$profile->update()) {
			$this->setError( $profile->getError() );
		}
		
		// Push through to the hosts view
		$this->hosts( $profile );
	}
	
	//-----------
	
	protected function deletehost()
	{
		// Incoming member ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('MEMBERS_NO_ID') );
			$this->hosts();
		}
		
		// Load the profile
		$profile = new XProfile();
		$profile->load( $id );
		
		// Incoming host
		$host = JRequest::getVar( 'host', '' );
		if (!$host) {
			$this->setError( JText::_('MEMBERS_NO_HOST') );
			$this->hosts( $profile );
		}
		
		$hosts = $profile->get('host');
		$a = array();
		foreach ($hosts as $h) 
		{
			if ($h != $host) {
				$a[] = $h;
			}
		}
		
		// Update the hosts list
		$profile->set('host', $a);
		if (!$profile->update()) {
			$this->setError( $profile->getError() );
		}
		
		// Push through to the hosts view
		$this->hosts( $profile );
	}
	
	//-----------

	protected function hosts( $profile=null )
	{
		// Get the app
		$app =& JFactory::getApplication();
		
		// Incoming
		if (!$profile) {
			$id = JRequest::getInt( 'id', 0, 'get' );
			
			$profile = new XProfile();
			$profile->load( $id );
		}
		
		// Get a list of all hosts
		$rows = $profile->get( 'host' );
		
		// Output HTML
		MembersHtml::writeHosts( $app, $this->_option, $profile->get('uidNumber'), $rows, $this->getErrors() );
	}
	
	//----------------------------------------------------------
	//  Managers
	//----------------------------------------------------------
	
	protected function addmanager()
	{
		// Incoming member ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('MEMBERS_NO_ID') );
			$this->managers();
		}
		
		// Load the profile
		$profile = new XProfile();
		$profile->load( $id );
		
		// Incoming host
		$manager = JRequest::getVar( 'manager', '' );
		if (!$manager) {
			$this->setError( JText::_('MEMBERS_NO_MANAGER') );
			$this->managers( $id );
		}
		
		$managers = $profile->get('manager');
		$managers[] = $manager;
		
		// Update the hosts list
		$profile->set('manager', $managers);
		if (!$profile->update()) {
			$this->setError( $profile->getError() );
		}
		
		// Push through to the hosts view
		$this->managers( $profile );
	}
	
	//-----------
	
	protected function deletemanager()
	{
		// Incoming member ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('MEMBERS_NO_ID') );
			$this->managers();
		}
		
		// Load the profile
		$profile = new XProfile();
		$profile->load( $id );
		
		// Incoming host
		$manager = JRequest::getVar( 'manager', '' );
		if (!$manager) {
			$this->setError( JText::_('MEMBERS_NO_MANAGER') );
			$this->managers( $profile );
		}
		
		$managers = $profile->get('manager');
		$a = array();
		foreach ($managers as $h) 
		{
			if ($h != $manager) {
				$a[] = $h;
			}
		}
		
		// Update the hosts list
		$profile->set('manager', $a);
		if (!$profile->update()) {
			$this->setError( $profile->getError() );
		}
		
		// Push through to the hosts view
		$this->managers( $profile );
	}
	
	//-----------

	protected function managers( $profile=null )
	{
		// Get the app
		$app =& JFactory::getApplication();
		
		// Incoming
		if (!$profile) {
			$id = JRequest::getInt( 'id', 0, 'get' );
			
			$profile = new XProfile();
			$profile->load( $id );
		}
		
		// Get a list of all hosts
		$rows = $profile->get( 'manager' );
		
		// Output HTML
		MembersHtml::writeManagers( $app, $this->_option, $profile->get('uidNumber'), $rows, $this->getErrors() );
	}
}
?>
