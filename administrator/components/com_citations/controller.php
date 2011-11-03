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
 * Short description for 'CitationsController'
 * 
 * Long description (if any) ...
 */
class CitationsController extends Hubzero_Controller
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
	// Views
	//----------------------------------------------------------

	/**
	 * Short description for 'browse'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function browse()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();

		// Instantiate a new view
		$view = new JView( array('name'=>'citations') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get filters
		$view->filters = array();
		$view->filters['search'] = urldecode($app->getUserStateFromRequest($this->_option.'.search', 'search', ''));
		$view->filters['sort']   = $app->getUserStateFromRequest($this->_option.'.sort', 'sort', 'created DESC');

		// Get paging variables
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = $app->getUserStateFromRequest($this->_option.'.limitstart', 'limitstart', 0, 'int');

		$obj = new CitationsCitation( $this->database );

		// Get a record count
		$view->total = $obj->getCount( $view->filters );

		// Get records
		$view->rows = $obj->getRecords( $view->filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		//get the dynamic citation types
		$ct = new CitationsType( $this->database );
		$view->types = $ct->getType();

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
	private function add()
	{
		$this->edit();
	}

	/**
	 * Short description for 'edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	private function edit()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'citation') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->config = $this->config;

		// Incoming - expecting an array id[]=4232
		$id = JRequest::getVar( 'id', array() );

		// Get the single ID we're working with
		if (is_array($id) && !empty($id)) {
			$id = $id[0];
		} else {
			$id = 0;
		}

		// Load the object
		$view->row = new CitationsCitation( $this->database );
		$view->row->load( $id );

		// Load the associations object
		$assoc = new CitationsAssociation( $this->database );

		// No ID, so we're creating a new entry
		// Set the ID of the creator
		if (!$id) {
			$juser =& JFactory::getUser();
			$view->row->uid = $juser->get('id');

			// It's new - no associations to get
			$view->assocs = array();
		} else {
			// Get the associations
			$view->assocs = $assoc->getRecords( array('cid'=>$id) );
		}

		//get the citations tags
		$database =& JFactory::getDBO();
		$sql = "SELECT t.*
				FROM #__tags_object to1 
				INNER JOIN #__tags t ON t.id = to1.tagid 
				WHERE to1.tbl='citations' 
				AND to1.objectid={$id}
				AND to1.label=''";
		$database->setQuery( $sql );
		$view->tags = $database->loadAssocList();

		//get the badges
		$sql = "SELECT t.*
				FROM #__tags_object to1 
				INNER JOIN #__tags t ON t.id = to1.tagid 
				WHERE to1.tbl='citations' 
				AND to1.objectid={$id}
				AND to1.label='badge'";
		$database->setQuery( $sql );
		$view->badges = $database->loadAssocList();

		$ct = new CitationsType( $this->database );
		$view->types = $ct->getType();

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'stats'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	private function stats()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'stats') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Load the object
		$row = new CitationsCitation( $this->database );
		$view->stats = $row->getStats();

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	//----------------------------------------------------------
	// Citation Types
	//----------------------------------------------------------

	protected function types()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'types') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		$ct = new CitationsType( $this->database );
		$view->types = $ct->getType();

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	protected function addtype()
	{
		$this->edittype();
	}

	protected function edittype()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'type') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->config = $this->config;

		//
		$id = JRequest::getVar( 'id', "" );

		//
		if($id) {
			$ct = new CitationsType( $this->database );
			$ct->load($id);
			$view->type = $ct;
		} else {
			$view->type = NULL;
		}

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	protected function deletetype()
	{
		$id = JRequest::getVar("id","");

		if(!$id) {
			JError::raiseError( 500, "You are missing the citation identifier." );
			return $this->types();
		}

		$ct = new CitationsType( $this->database );
		if(!$ct->delete( $id )) {
			return JError::raiseError( 500, "An error occurred while trying to delete the citation type." );
		}

		$this->_redirect = 'index.php?option=com_citations&task=types';
		$this->_message = JText::_( 'The citation type was successfully deleted.', 'passed');
	}

	protected function savetype()
	{
		$type = JRequest::getVar("type", array());

		$ct = new CitationsType( $this->database );

		if(!$ct->save( $type )) {
			return JError::raiseError( 500, "An error occurred while trying to save the citation type." );
		}

		$this->_redirect = 'index.php?option=com_citations&task=types';
		$this->_message = JText::_( 'The citation type was successfully saved.', 'passed');
	}

	//----------------------------------------------------------
	// Processors
	//----------------------------------------------------------

	/**
	 * Short description for 'save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$juser =& JFactory::getUser();

		$citation = JRequest::getVar('citation', array(), 'post');
		$citation = array_map('trim', $citation);

		// Bind incoming data to object
		$row = new CitationsCitation( $this->database );
		if (!$row->bind( $citation )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// New entry so set the created date
		if (!$row->id) {
			$row->created = date( 'Y-m-d H:i:s', time() );
		}

		// Check content for missing required data
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Store new content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Incoming associations
		$arr = JRequest::getVar( 'assocs', array(), 'post' );

		$ignored = array();

		foreach ($arr as $a)
		{
			$a = array_map('trim',$a);

			// Initiate extended database class
			$assoc = new CitationsAssociation( $this->database );

			if (!$this->_isempty($a, $ignored)) {
				$a['cid'] = $row->id;

				// bind the data
				if (!$assoc->bind( $a )) {
					JError::raiseError( 500, $assoc->getError() );
					return;
				}

				// Check content
				if (!$assoc->check()) {
					JError::raiseError( 500, $assoc->getError() );
					return;
				}

				// Store new content
				if (!$assoc->store()) {
					JError::raiseError( 500, $assoc->getError() );
					return;
				}
			} elseif ($this->_isEmpty($a, $ignored) && !empty($a['id'])) {
				// Delete the row
				if (!$assoc->delete( $a['id'] )) {
					JError::raiseError( 500, $assoc->getError() );
					return;
				}
			}
		}

		//citation tags object
		$ct = new CitationTags( $this->database );

		//get the tags
		$tags = trim(JRequest::getVar("tags", ""));

		//get the badges
		$badges = trim(JRequest::getVar("badges", ""));

		//add tags
		$ct->tag_object( $juser->get("id"), $row->id, $tags, 1, false, "");

		//add badges
		$ct->tag_object( $juser->get("id"), $row->id, $badges, 1, false, "badge");

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_( 'CITATION_SAVED' );
	}

	/**
	 * Short description for '_isEmpty'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $b Parameter description (if any) ...
	 * @param      array $ignored Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	private function _isEmpty($b, $ignored=array())
	{
		foreach ($ignored as $ignore)
		{
			if (array_key_exists($ignore,$b)) {
				$b[$ignore] = NULL;
			}
		}
		if (array_key_exists('id',$b)) {
			$b['id'] = NULL;
		}
		$values = array_values($b);
		$e = true;
		foreach ($values as $v)
		{
			if ($v) {
				$e = false;
			}
		}
		return $e;
	}

	/**
	 * Short description for 'remove'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function remove()
	{
		// Incoming (we're expecting an array)
		$ids = JRequest::getVar('id', array());
		if (!is_array($ids)) {
			$ids = array();
		}

		// Make sure we have IDs to work with
		if (count($ids) > 0) {
			// Loop through the IDs and delete the citation
			$citation = new CitationsCitation( $this->database );
			$assoc = new CitationsAssociation( $this->database );
			$author = new CitationsAuthor( $this->database );
			foreach ($ids as $id)
			{
				// Fetch and delete all the associations to this citation
				$assocs = $assoc->getRecords( array('cid'=>$id) );
				foreach ($assocs as $a)
				{
					$assoc->delete( $a->id );
				}

				// Fetch and delete all the authors to this citation
				$authors = $author->getRecords( array('cid'=>$id) );
				foreach ($authors as $a)
				{
					$author->delete( $a->id );
				}

				// Delete the citation
				$citation->delete( $id );

				//citation tags
				$ct = new CitationTags( $this->database );
				$ct->remove_all_tags( $id );
			}

			$this->_message = JText::_('CITATION_REMOVED');
		} else {
			$this->_message = JText::_('NO_SELECTION');
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	public function getformat()
	{
		//get the format being sent via json
		$format = JRequest::getVar("format", "apa");

		//include citations format class
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'citations.format.php');

		//new citations format object
		$cf = new CitationFormat();

		//get the default template for the format being passed in
		$format_template = $cf->getDefaultFormat( $format );

		//return the template
		echo $format_template;
	}

	public function gettemplatekeys()
	{
		//include citations format class
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'citations.format.php');

		//new citations format object
		$cf = new CitationFormat();

		//get the keys
	 	$keys = $cf->getTemplateKeys();

		//var to hold html data
		$html = "";

		//create row for each key pair
		foreach($keys as $k => $v) {
			$html .= "<tr><td>{$v}</td><td>{$k}</td></tr>";
		}

		//return html
		echo $html;
	}

	//-----------
}

