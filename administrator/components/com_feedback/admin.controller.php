<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

class FeedbackController extends JObject
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
	
	private function getTask()
	{
		$task = strtolower(JRequest::getVar('task', '','request'));
		$type = JRequest::getVar( 'type', '', 'post' );
		if (!$type) {
			$type = JRequest::getVar( 'type', 'regular', 'get' );
		}
		$this->_task = $task;
		$this->type = $type;
		return $task;
	}
	
	//-----------
	
	public function display()
	{
		// Load the component config
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		
		switch ( $this->getTask() ) 
		{
			case 'new':       $this->edit();      break;
			case 'add':       $this->edit();      break;
			case 'edit':      $this->edit();      break;
			case 'save':      $this->save();      break;
			case 'remove':    $this->remove();    break;
			case 'cancel':    $this->cancel();    break;
			case 'upload':    $this->upload();    break;
			case 'img':       $this->img();       break;
			case 'deleteimg': $this->deleteimg(); break;
	
			default: $this->quotes(); break;
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

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function quotes()
	{
		$app =& JFactory::getApplication();
		$database =& JFactory::getDBO();

		// Get site configuration
		$config = JFactory::getConfig();

		// Incoming
		$filters = array();
		$filters['search'] = urldecode(JRequest::getString('search'));
		$filters['sortby'] = JRequest::getVar( 'sortby', 'date' );
		$filters['start']  = JRequest::getInt('limitstart', 0);
		$filters['limit']  = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');

		if ($this->type == 'regular') {
			$obj = new FeedbackQuotes( $database );
		} else {
			$obj = new SelectedQuotes( $database );
		}
		
		// Get a record count
		$total = $obj->getCount( $filters );
		
		// Get records
		$rows = $obj->getResults( $filters );

		// Initiate paging class
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// output HTML
		FeedbackHtml::quotes( $rows, $pageNav, $this->_option, $filters, $this->type );
	}

	//-----------

	protected function create()
	{
		FeedbackHtml::create( $this->_option );
	}

	//-----------

	protected function edit() 
	{
		$database =& JFactory::getDBO();

		// Incoming ID
		$id = JRequest::getInt( 'id', 0 );

		// Initiate database class and load info
		if ($this->type == 'regular') {
			$row = new FeedbackQuotes( $database );
		} else {
			$row = new SelectedQuotes( $database );
		}
		$row->load( $id );

		$username = trim(JRequest::getVar( 'username', '' ));
		if ($username) {
			ximport('xprofile');
			
			$profile = new XProfile();
			$profile->load( $username );

			$row->fullname = $profile->get('name');
			$row->org      = $profile->get('organization');
			$row->userid   = $profile->get('uidNumber');
		}
		
		if ($id) {
			$action = JText::_('Edit');
		} else {
			$action = JText::_('New');
			$row->date = date( 'Y-m-d H:i:s');
		}
		
		if ($this->type == 'regular') {
			$row->notable_quotes = 0;
			$row->flash_rotation = 0;
		}

		// Ouput HTML
		FeedbackHtml::edit( $row, $action, $this->_option, $this->type );
	}

	//----------------------------------------------------------
	// Processors
	//----------------------------------------------------------
	
	protected function save() 
	{
		$database =& JFactory::getDBO();
	
		// Incoming
		$replacequote   = JRequest::getInt( 'replacequote', 0 );
		$notable_quotes = JRequest::getInt( 'notable_quotes', 0 );
		$flash_rotation = JRequest::getInt( 'flash_rotation', 0 );
	
		if ($replacequote) {
			// Replace original quote

			// Initiate class and bind posted items to database fields
			$row = new FeedbackQuotes( $database );
			if (!$row->bind($_POST)) {
				echo FeedbackHtml::alert( $row->getError() );
				exit();
			}
	
			// Code cleaner for xhtml transitional compliance
			$row->quote = str_replace( '<br>', '<br />', $row->quote );
	
			// Check new content
			if (!$row->check()) {
				echo FeedbackHtml::alert( $row->getError() );
				exit();
			}
			
			// Store new content
			if (!$row->store()) {
				echo FeedbackHtml::alert( $row->getError() );
				exit();
			}
	
			$msg = JText::sprintf('FEEDBACK_QUOTE_SAVED',  $row->fullname);
		}


		if ($this->type == 'selected' || $notable_quotes || $flash_rotation) {
			// Initiate class and bind posted items to database fields
			$rowselected = new SelectedQuotes( $database );
			if (!$rowselected->bind($_POST)) {
				echo FeedbackHtml::alert( $rowselected->getError() );
				exit();
			}
			
			// Use new id if already exists under selected quotes
			if ($this->type == 'regular') {
				$rowselected->id = 0;
			}

			// Code cleaner for xhtml transitional compliance
			$rowselected->quote = str_replace( '<br>', '<br />', $rowselected->quote );
			
			// Trim the text to create a short quote
			$rowselected->short_quote = ($rowselected->short_quote) ? $rowselected->short_quote : substr($rowselected->quote, 0, 270);
			if (strlen($rowselected->short_quote)>=271) {
				$rowselected->short_quote .= '...';
			}
			
			// Store new content
			if (!$rowselected->store()) {
				echo FeedbackHtml::alert( $rowselected->getError() );
				exit();
			}

			$msg = '';
		}
	
		if ($flash_rotation) {
			$msg .= JText::_('FEEDBACK_QUOTE_SELECTED_FOR_ROTATION');
		}
		if ($notable_quotes) {
			$msg .= JText::_('FEEDBACK_QUOTE_SELECTED_FOR_QUOTES');
		}
		
		$this->_redirect = 'index.php?option='.$this->_option.'&type='.$this->type;
		$this->_message = $msg;
	}
	
	//-----------

	protected function remove() 
	{
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
	
		// Check for an ID
		if (!$id) {
			echo FeedbackHtml::alert( JText::_('FEEDBACK_SELECT_QUOTE_TO_DELETE') );
			exit;
		}

		$database =& JFactory::getDBO();

		// Load the quote
		if ($this->type == 'regular') {
			$row = new FeedbackQuotes( $database );
		} else {
			$row = new SelectedQuotes( $database );
		}
		$row->load( $id );
		
		// Delete associated files
		$row->deletePicture( $this->config );
		
		// Delete the quote
		$row->delete();
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&type='.$type;
		$this->_message = JText::_('FEEDBACK_REMOVED');
	}

	//-----------

	protected function cancel()
	{
		$this->_redirect = 'index.php?option='.$this->_option.'&type='.$this->type;
	}

	//----------------------------------------------------------
	//  Image handling
	//----------------------------------------------------------

	public function upload()
	{
		// Load the component config
		$config = $this->config;

		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('FEEDBACK_NO_ID') );
			$this->img( '', $id );
			return;
		}
		
		// Incoming file
		$file = JRequest::getVar( 'upload', '', 'files', 'array' );
		if (!$file['name']) {
			$this->setError( JText::_('FEEDBACK_NO_FILE') );
			$this->img( '', $id );
			return;
		}
		
		// Build upload path
		ximport('fileuploadutils');
		$dir  = FileUploadUtils::niceidformat( $id );
		$path = JPATH_ROOT;
		if (substr($config->get('uploadpath'), 0, 1) != DS) {
			$path .= DS;
		}
		if (substr($config->get('uploadpath'), -1, 1) == DS) {
			$path = substr($config->get('uploadpath'), 0, (strlen($config->get('uploadpath')) - 1));
		}
		$path .= $config->get('uploadpath').DS.$dir;
		
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
			$this->setError( JText::_('FEEDBACK_NO_ID') );
			$this->img( '', $id );
		}
		
		// Incoming file
		$file = JRequest::getVar( 'file', '' );
		if (!$file) {
			$this->setError( JText::_('FEEDBACK_NO_FILE') );
			$this->img( '', $id );
		}
		
		// Build the file path
		ximport('fileuploadutils');
		$dir  = FileUploadUtils::niceidformat( $id );
		$path = JPATH_ROOT;
		if (substr($config->get('uploadpath'), 0, 1) != DS) {
			$path .= DS;
		}
		if (substr($config->get('uploadpath'), -1, 1) == DS) {
			$path = substr($config->get('uploadpath'), 0, (strlen($config->get('uploadpath')) - 1));
		}
		$path .= $config->get('uploadpath').DS.$dir;

		if (!file_exists($path.DS.$file) or !$file) { 
			$this->setError( JText::_('FILE_NOT_FOUND') ); 
		} else {
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path.DS.$file)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
				$this->img( $file, $id );
			}

			$file = '';
		}
	
		// Push through to the image view
		$this->img( $file, $id );
	}

	//-----------

	protected function img( $file='', $id=0 )
	{
		// Load the component config
		$config = $this->config;
		
		// Get the app
		$app =& JFactory::getApplication();
		
		// Do have an ID or do we need to get one?
		if (!$id) {
			$id = JRequest::getInt( 'id', 0 );
		}
		ximport('fileuploadutils');
		$dir = FileUploadUtils::niceidformat( $id );
		
		// Do we have a file or do we need to get one?
		$file = ($file) 
			  ? $file 
			  : JRequest::getVar( 'file', '' );
			  
		// Build the directory path
		$path = $config->get('uploadpath').DS.$dir;

		FeedbackHtml::writeImage( $app, $this->_option, $config->get('uploadpath'), $config->get('defaultpic'), $dir, $file, $path, $id, $this->getErrors() );
	}
}
?>
