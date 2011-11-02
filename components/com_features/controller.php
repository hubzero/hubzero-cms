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
 * Short description for 'FeaturesController'
 * 
 * Long description (if any) ...
 */
class FeaturesController extends Hubzero_Controller
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
		$this->_task = JRequest::getVar( 'task', '' );

		switch ($this->_task)
		{
			case 'delete': $this->_delete(); break;
			case 'add':    $this->_edit();   break;
			case 'edit':   $this->_edit();   break;
			case 'save':   $this->_save();   break;
			case 'browse': $this->_browse(); break;
			case 'login':  $this->_login();  break;

			default: $this->_browse(); break;
		}
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------


	/**
	 * Short description for '_login'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function _login()
	{
		$view = new JView( array('name'=>'login') );
		$view->title = JText::_(strtoupper($this->_option)).': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for '_browse'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function _browse()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'browse') );
		$view->title = JText::_(strtoupper($this->_option));
		$view->option = $this->_option;

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Incoming
		$view->filters = array();
		$view->filters['limit']  = JRequest::getInt( 'limit', $jconfig->getValue('config.list_limit'), 'request' );
		$view->filters['start']  = JRequest::getInt( 'limitstart', 0, 'get' );
		$view->filters['type']   = JRequest::getVar( 'type', '' );

		// Check if the user is authorized to make changes
		$view->authorized = $this->_authorize();

		// Instantiate a FeaturesHistory object
		$obj = new FeaturesHistory( $this->database );

		// Get a record count
		$view->total = $obj->getCount( $view->filters, $view->authorized );

		// Get records
		$view->rows = $obj->getRecords( $view->filters, $view->authorized );

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Push some styles to the template
		$this->_getStyles();

		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_(strtoupper($this->_option)));

		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_option)),'index.php?option='.$this->_option);
		}

		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for '_add'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function _add()
	{
		$this->_edit();
	}

	/**
	 * Short description for '_edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function _edit()
	{
		// Check if they are authorized to make changes
		if (!$this->_authorize()) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}

		// Instantiate a new view
		$view = new JView( array('name'=>'edit') );
		$view->title = JText::_(strtoupper($this->_option)).': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
		$view->option = $this->_option;

		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );

		// Load the object
		$view->row = new FeaturesHistory( $this->database );
		$view->row->load( $id );

		if ($view->row->note == 'tools') {
			$view->row->tbl = 'tools';
		} else if ($view->row->note == 'nontools') {
			$view->row->tbl = 'resources';
		}

		if (!$view->row->featured) {
			$view->row->featured = date("Y").'-'.date("m").'-'.date("d").' 00:00:00';
		}

		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_(strtoupper($this->_option)).': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)));

		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_option)),'index.php?option='.$this->_option);
		}

		// Push some styles to the template
		$this->_getStyles();

		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for '_save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function _save()
	{
		// Check if they are authorized to make changes
		if (!$this->_authorize()) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}

		// Instantiate an object and bind the incoming data
		$row = new FeaturesHistory( $this->database );
		if (!$row->bind( $_POST )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		if ($row->tbl == 'tools') {
			$row->note = 'tools';
			$row->tbl = 'resources';
		} else if ($row->tbl == 'resources') {
			$row->note = 'nontools';
			$row->tbl = 'resources';
		}

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

		// Redirect
		$this->_redirect = JRoute::_('index.php?option='.$this->_option);
	}

	/**
	 * Short description for '_delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function _delete()
	{
		// Check if they are authorized to make changes
		if (!$this->_authorize()) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}

		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );

		if ($id) {
			// Delete the object
			$row = new FeaturesHistory( $this->database );
			$row->delete( $id );
		}

		// Redirect
		$this->_redirect = JRoute::_('index.php?option='.$this->_option);
	}
}

