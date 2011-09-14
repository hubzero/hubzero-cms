<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

class SefController extends Hubzero_Controller
{
	public function execute()
	{
		// Load the component config
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;

		$this->_task = JRequest::getVar( 'task', '' );
		$section = JRequest::getVar( 'section', '' );
		if ($section) {
			$this->_task = $section;
		}

		switch ($this->_task)
		{
			case 'config': $this->config(); break;
			case 'saveconfig': $this->saveconfig(); break;

			case 'add':    $this->add();    break;
			case 'new':    $this->add();    break;
			case 'edit':   $this->edit();   break;
			case 'save':   $this->save();   break;
			case 'remove': $this->remove(); break;
			case 'cancel': $this->cancel(); break;
			case 'info':   $this->info();   break;
			case 'browse': $this->browse(); break;

			default: $this->browse(); break;
		}
	}

	//----------------------------------------------------------
	// Tag functions
	//----------------------------------------------------------

	protected function info()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'info') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	protected function browse()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'entries') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get Joomla configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$view->filters = array();
		$view->filters['limit']      = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start']      = $app->getUserStateFromRequest($this->_option.'.limitstart', 'limitstart', 0, 'int');
		$view->filters['catid']      = $app->getUserStateFromRequest($this->_option.'.catid', 'catid', 0, 'int');
		$view->filters['ViewModeId'] = $app->getUserStateFromRequest($this->_option.'.viewmode', 'viewmode', 0, 'int');
		$view->filters['SortById']   = $app->getUserStateFromRequest($this->_option.'.sortby', 'sortby', 0, 'int');

		// Determine the mode
		$view->is404mode = false;
		if ($view->filters['ViewModeId'] == 1) {
			$view->is404mode = true;
		}

		$lists = array();

		// Make the select list for the filter
		$viewmode = array();
		$viewmode[] = JHTML::_('select.option', '0', JText::_('Show SEF Urls'), 'value', 'text');
		$viewmode[] = JHTML::_('select.option', '1', JText::_('Show 404 Log'), 'value', 'text');
		$viewmode[] = JHTML::_('select.option', '2', JText::_('Show Custom Redirects'), 'value', 'text');

		$view->lists['viewmode'] = JHTML::_('select.genericlist', $viewmode, 'viewmode', '', 'value', 'text', $view->filters['ViewModeId'], false, false );

		// Make the select list for the filter
		$orderby = array();
		$orderby[] = JHTML::_('select.option', '0', JText::_('SEF Url (asc)'), 'value', 'text');
		$orderby[] = JHTML::_('select.option', '1', JText::_('SEF Url (desc)'), 'value', 'text');
		if ($view->is404mode != true) {
  			$orderby[] = JHTML::_('select.option', '2', JText::_('Real Url (asc)'), 'value', 'text');
			$orderby[] = JHTML::_('select.option', '3', JText::_('Real Url (desc)'), 'value', 'text');
		}
		$orderby[] = JHTML::_('select.option', '4', JText::_('Hits (asc)'), 'value', 'text');
		$orderby[] = JHTML::_('select.option', '5', JText::_('Hits (desc)'), 'value', 'text');

		$view->lists['sortby'] = JHTML::_('select.genericlist', $orderby, 'sortby', '', 'value', 'text', $view->filters['SortById'], false, false );

		// Instantiate a new SefEntry
		$s = new SefEntry( $this->database );

		// Record count
		$view->total = $s->getCount( $view->filters );

		// Get records
		$view->rows = $s->getRecords( $view->filters );

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

	protected function add()
	{
		$this->edit();
	}

	protected function edit($row=null)
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'entry') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Load a tag object if one doesn't already exist
		if (!$row) {
			// Incoming
			$ids = JRequest::getVar('id', array());
			if (!is_array( $ids )) {
				$ids = array();
			}

			$id = (!empty($ids)) ? $ids[0] : 0;

			$view->row = new SefEntry( $this->database );
			$view->row->load( $id );

			if (!$id) {
				// do stuff for new records
				$view->row->dateadd = date("Y-m-d");
			}
		} else {
			$view->row = $row;
		}

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	protected function cancel()
	{
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	protected function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Load the tag object and bind the incoming data to it
		$row = new SefEntry( $this->database );
		if (!$row->bind( $_POST )) {
			$this->setError( $row->getError() );
			$this->edit($row);
			return;
		}

		if (substr($row->oldurl,-1) == DS) {
			$row->oldurl = substr($row->oldurl,0,strlen($row->oldurl)-1);
		}

		// Check content
		if (!$row->check()) {
			$this->setError( $row->getError() );
			$this->edit($row);
			return;
		}

		// Store new content
		if (!$row->store()) {
			$this->setError( $row->getError() );
			$this->edit($row);
			return;
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_( 'SEF saved' );
	}

	protected function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$ids = JRequest::getVar('id', array());
		if (!is_array( $ids )) {
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids)) {
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}

		// Load some needed objects
		$sef = new SefEntry( $this->database );

		foreach ($ids as $id)
		{
			// Remove the SEF
			$sef->delete( $id );
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_( 'SEF removed' );
	}
}

