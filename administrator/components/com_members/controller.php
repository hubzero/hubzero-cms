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
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

/**
 * Short description for 'MembersController'
 * 
 * Long description (if any) ...
 */
class MembersController extends Hubzero_Controller
{

	/**
	 * Short description for 'execute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
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

	//----------------------------------------------------------
	//  Views
	//----------------------------------------------------------

	/**
	 * Short description for 'browse'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function browse()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'members') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();

		// Get filters
		$view->filters = array();
		$view->filters['search'] = urldecode($app->getUserStateFromRequest($this->_option.'.search', 'search', ''));
		$view->filters['search_field'] = urldecode($app->getUserStateFromRequest($this->_option.'.search_field', 'search_field', 'name'));
		//$view->filters['sortby'] = $app->getUserStateFromRequest($this->_option.'.sortby', 'sortby', 'surname');
		$view->filters['sort']     = trim($app->getUserStateFromRequest($this->_option.'.sort', 'filter_order', 'surname'));
		$view->filters['sort_Dir'] = trim($app->getUserStateFromRequest($this->_option.'.sortdir', 'filter_order_Dir', 'ASC'));
		$view->filters['show']   = '';
		$view->filters['scope']  = '';
		$view->filters['authorized'] = true;

		$view->filters['sortby'] = $view->filters['sort'].' '.$view->filters['sort_Dir'];

		// Get paging variables
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = JRequest::getInt('limitstart', 0);

		$obj = new MembersProfile( $this->database );

		// Get a record count
		$view->total = $obj->getCount( $view->filters, true );

		// Get records
		$view->rows = $obj->getRecords( $view->filters, true );

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'add'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function add()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'add') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $id Parameter description (if any) ...
	 * @return     void
	 */
	protected function edit($id=0)
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'member') );
		$view->option = $this->_option;
		$view->task = $this->_task;

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
		$view->profile = new Hubzero_User_Profile();
		$view->profile->load( $id );

		// Get the user's interests (tags)
		include_once( JPATH_ROOT.DS.'components'.DS.$this->_option.DS.'helpers'.DS.'tags.php' );

		$mt = new MembersTags( $this->database );
		$view->tags = $mt->get_tag_string( $id );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'apply'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function apply()
	{
		$this->save(0);
	}

	/**
	 * Short description for 'save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $redirect Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	protected function save($redirect=1)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming user ID
		$id = JRequest::getInt( 'id', 0, 'post' );

		// Do we have an ID?
		if (!$id) {
			JError::raiseError( 500, JText::_('MEMBERS_NO_ID') );
			return;
		}

		// Incoming profile edits
		$p = JRequest::getVar( 'profile', array(), 'post' );

		// Load the profile
		$profile = new Hubzero_User_Profile();
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
			ximport('Hubzero_Registration_Helper');
			$confirm = Hubzero_Registration_Helper::genemailconfirm();
			$profile->set('emailConfirmed', $confirm);
		}
		$se = JRequest::getInt( 'shadowExpire', 0, 'post' );
		if ($se) {
		    $profile->set('shadowExpire','1');
		} else {
		    $profile->set('shadowExpire', null);
		}
		if (isset($p['email'])) {
			$profile->set('email', trim($p['email']));
		}
		if (isset($p['mailPreferenceOption'])) {
			$profile->set('mailPreferenceOption', trim($p['mailPreferenceOption']));
		} else {
			$profile->set('mailPreferenceOption', 0);
		}

		if (!empty($p['gender'])) {
			$profile->set('gender', trim($p['gender']));
		}
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

		if (!empty($p['disability'])) {
			if ($p['disability'] == 'yes') {
				if (!is_array($p['disabilities'])) {
					$p['disabilities'] = array();
				}
				if (count($p['disabilities']) == 1 && isset($p['disabilities']['other']) && empty($p['disabilities']['other'])) {
					$profile->set('disability',array('no'));
				} else {
					$profile->set('disability',$p['disabilities']);
				}
			} else {
				$profile->set('disability',array($p['disability']));
			}
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
		if (!empty($p['hispanic'])) {
			if ($p['hispanic'] == 'yes') {
				if (!is_array($p['hispanics'])) {
					$p['hispanics'] = array();
				}
				if (count($p['hispanics']) == 1 && isset($p['hispanics']['other']) && empty($p['hispanics']['other'])) {
					$profile->set('hispanic', array('no'));
				} else {
					$profile->set('hispanic',$p['hispanics']);
				}
			} else {
				$profile->set('hispanic',array($p['hispanic']));
			}
		}

		if (isset($p['race']) && is_array($p['race'])) {
			$profile->set('race',$p['race']);
		}

		// Do we have a new pass?
		$newpass = trim(JRequest::getVar( 'newpass', '', 'post' ));
		if ($newpass != '') {
			ximport('Hubzero_User_Helper');
			 // Encrypt the password and update the profile
			$userPassword = Hubzero_User_Helper::encrypt_password($newpass);
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
		include_once( JPATH_ROOT.DS.'components'.DS.$this->_option.DS.'helpers'.DS.'tags.php' );

		$mt = new MembersTags( $this->database );
		$mt->tag_object($id, $id, $tags, 1, 1);

		// Make sure certain changes make it back to the Joomla user table
		$juser =& JUser::getInstance($id);
		$juser->set('name', $name);
		$juser->set('email', $profile->get('email'));
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
		$profile = new Hubzero_User_Profile();
		$profile->load( $id );
		
		// Generate a new password
		$newpass = Hubzero_Registration_Helper::userpassgen();
		
		// Encrypt the password and update the profile
		$userPassword = Hubzero_User_Helper::encrypt_password($newpass);
		$profile->set('userPassword', $userPassword);
		
		// Save the changes
		if (!$profile->update()) {
			JError::raiseWarning('', $profile->getError() );
			return false;
		}
		
		// Push through to the edit view
		$this->edit($id);
	}*/

	/**
	 * Short description for 'remove'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

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
			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				// Delete any associated pictures
				$dir  = Hubzero_View_Helper_Html::niceidformat( $id );
				$path = JPATH_ROOT.DS.$config->get('webpath').DS.$dir;
				if (!file_exists($path.DS.$file) or !$file) {
					$this->setError( JText::_('FILE_NOT_FOUND') );
				} else {
					unlink($path.DS.$file);
				}

				// Remove any contribution associations
				$assoc = new MembersAssociation( $this->database );
				$assoc->authorid = $id;
				$assoc->deleteAssociations();

				// Remove the profile
				$profile = new Hubzero_User_Profile();
				$profile->load( $id );
				$profile->delete();
			}
		}

		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('MEMBER_REMOVED');
	}

	/**
	 * Short description for 'cancel'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function cancel()
	{
		$this->_redirect = 'index.php?option='.$this->_option;
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
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

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
		$dir  = Hubzero_View_Helper_Html::niceidformat( $id );
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
			$profile = new Hubzero_User_Profile();
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

	/**
	 * Short description for 'deleteimg'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function deleteimg()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit( 'Invalid Token' );

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
		$dir  = Hubzero_View_Helper_Html::niceidformat( $id );
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
			$profile = new Hubzero_User_Profile();
			$profile->load( $id );
			$profile->set('picture', '');
			if (!$profile->update()) {
				$this->setError( $profile->getError() );
			}

			$file = '';
		}

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
		// Instantiate a new view
		$view = new JView( array('name'=>'member', 'layout'=>'image') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Load the component config
		$view->config = $this->config;

		// Incoming
		if (!$id) {
			$view->id = JRequest::getInt( 'id', 0 );
		} else {
			$view->id = $id;
		}

		if (!$file) {
			$view->file = JRequest::getVar( 'file', '' );
		} else {
			$view->file = $file;
		}

		// Build the file path
		$view->dir = Hubzero_View_Helper_Html::niceidformat( $id );
		$view->path = JPATH_ROOT;
		if (substr($this->config->get('webpath'), 0, 1) != DS) {
			$view->path .= DS;
		}
		$view->path .= $this->config->get('webpath').DS.$view->dir;

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'addgroup'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function addgroup()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

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
		ximport('Hubzero_Group');
		$group = Hubzero_Group::getInstance( $gid );

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

	/**
	 * Short description for 'group'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $id Parameter description (if any) ...
	 * @return     void
	 */
	protected function group( $id=0 )
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'member', 'layout'=>'groups') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		ximport('Hubzero_Group');

		// Incoming
		if (!$id) {
			$view->id = JRequest::getInt( 'id', 0 );
		} else {
			$view->id = $id;
		}

		// Get a list of all groups
		$filters = array();
		$filters['type'] = array('all');
		$filters['limit'] = 'all';
		$filters['search'] = '';
		$filters['limit'] = 'all';
		$filters['fields'] = array('cn','description','published','gidNumber','type');

		// Get a list of all groups
		$view->rows = Hubzero_Group::find($filters);

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	//----------------------------------------------------------
	//  Hosts
	//----------------------------------------------------------

	/**
	 * Short description for 'addhost'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function addhost()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming member ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('MEMBERS_NO_ID') );
			$this->hosts();
		}

		// Load the profile
		$profile = new Hubzero_User_Profile();
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

	/**
	 * Short description for 'deletehost'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function deletehost()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit( 'Invalid Token' );

		// Incoming member ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('MEMBERS_NO_ID') );
			$this->hosts();
		}

		// Load the profile
		$profile = new Hubzero_User_Profile();
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

	/**
	 * Short description for 'hosts'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $profile Parameter description (if any) ...
	 * @return     void
	 */
	protected function hosts( $profile=null )
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'member', 'layout'=>'hosts') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Incoming
		if (!$profile) {
			$id = JRequest::getInt('id', 0, 'get');

			$profile = new Hubzero_User_Profile();
			$profile->load( $id );
		}

		// Get a list of all hosts
		$view->rows = $profile->get('host');

		$view->id = $profile->get('uidNumber');

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	//----------------------------------------------------------
	//  Managers
	//----------------------------------------------------------

	/**
	 * Short description for 'addmanager'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function addmanager()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming member ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('MEMBERS_NO_ID') );
			$this->managers();
		}

		// Load the profile
		$profile = new Hubzero_User_Profile();
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

	/**
	 * Short description for 'deletemanager'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function deletemanager()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit( 'Invalid Token' );

		// Incoming member ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('MEMBERS_NO_ID') );
			$this->managers();
		}

		// Load the profile
		$profile = new Hubzero_User_Profile();
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

	/**
	 * Short description for 'managers'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $profile Parameter description (if any) ...
	 * @return     void
	 */
	protected function managers( $profile=null )
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'member', 'layout'=>'managers') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Incoming
		if (!$profile) {
			$id = JRequest::getInt( 'id', 0, 'get' );

			$profile = new Hubzero_User_Profile();
			$profile->load( $id );
		}

		// Get a list of all hosts
		$view->rows = $profile->get('manager');

		$view->id = $profile->get('uidNumber');

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}
}

