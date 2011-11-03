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
 * Short description for 'XPollController'
 * 
 * Long description (if any) ...
 */
class XPollController extends Hubzero_Controller
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
			case 'add':       $this->edit();     break;
			case 'edit':      $this->edit();     break;
			case 'save':      $this->save();     break;
			case 'remove':    $this->remove();   break;
			case 'reset':     $this->resetit();  break;
			case 'cancel':    $this->cancel();   break;
			case 'publish':   $this->publish(1); break;
			case 'unpublish': $this->publish(0); break;
			case 'open':      $this->open(1);    break;
			case 'close':     $this->open(0);    break;
			case 'browse':    $this->browse();   break;

			default: $this->browse(); break;
		}
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
	protected function browse()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'polls') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$view->filters = array();
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = $app->getUserStateFromRequest($this->_option.'.limitstart', 'limitstart', 0, 'int');

		$p = new XPollPoll( $this->database );

		// Get a record count
		$view->total = $p->getCount( $view->filters );

		// Retrieve all the records
		$view->rows = $p->getRecords( $view->filters );

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
	 * Short description for 'edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function edit()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'poll') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Incoming (expecting an array)
		$cid = JRequest::getVar( 'cid', array(0) );
		if (!is_array( $cid )) {
			$cid = array(0);
		}
		$uid = $cid[0];

		// Load the poll
		$view->row = new XPollPoll( $this->database );
		$view->row->load( $uid );

		// Fail if not checked out by 'me'
		if ($view->row->checked_out && $view->row->checked_out <> $this->juser->get('id')) {
			$this->_redirect = 'index.php?option='. $this->_option;
			$this->_message = JText::_( 'XPOLL_ERROR_CHECKED_OUT' );
			return;
		}

		// Are we editing existing or creating new?
		if ($uid) {
			// Editing existing
			// Check it out
			$view->row->checkout( $this->juser->get('id') );

			// Load the poll's options
			$xpdata = new XPollData( $this->database );
			$view->options = $xpdata->getPollOptions( $uid, true );
		} else {
			// Creating new
			// Set the log time to the default
			$view->row->lag = 3600*24;
			$view->options = array();
		}

		// Get selected pages
		if ($uid) {
			$xpmenu = new XPollMenu( $this->database );
			$lookup = $xpmenu->getMenuIds( $view->row->id );
		} else {
			$lookup = array( JHTML::_('select.option', 0, JText::_('ALL'), 'value', 'text') );
		}

		// Build the html select list
		$view->lists = array();

		$soptions = JHTML::_('menu.linkoptions', $lookup, NULL, 1);
		if (empty( $lookup )) {
			$lookup = array( JHTML::_('select.option',  -1 ) );
		}
		$view->lists['select'] = JHTML::_('select.genericlist', $soptions, 'selections[]', 'class="inputbox" size="15" multiple="multiple"', 'value', 'text', $lookup, 'selections' );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

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

		// Incoming
		$p = JRequest::getVar( 'poll', array(), 'post' );
		$p = array_map('trim', $p);

		// Save the poll parent information
		$row = new XPollPoll( $this->database );
		if (!$row->bind( $p )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		$isNew = ($row->id == 0);
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		$row->checkin();

		// Incoming poll options
		$options = JRequest::getVar( 'polloption', array(), 'post' );

		foreach ($options as $i=>$text)
		{
			// 'slash' the options
			if (!get_magic_quotes_gpc()) {
				$text = addslashes( $text );
			}

			if (trim($text) != '') {
				$xpdata = new XPollData( $this->database );
				if (!$isNew) {
					$xpdata->id = $i;
				}
				$xpdata->pollid = $row->id;
				$xpdata->text = trim($text);
				if (!$xpdata->check()) {
					JError::raiseError( 500, $xpdata->getError() );
					return;
				}
				if (!$xpdata->store()) {
					JError::raiseError( 500, $xpdata->getError() );
					return;
				}
			}
		}

		// Remove old menu entries for this poll
		$xpmenu = new XPollMenu( $this->database );
		$xpmenu->deleteEntries( $row->id );

		// Update the menu visibility
		$selections = JRequest::getVar( 'selections', array(), 'post' );

		for ($i=0, $n=count($selections); $i < $n; $i++)
		{
			$xpmenu->insertEntry( $row->id, $selections[$i] );
		}

		// Redirect
		$this->_redirect = 'index.php?option='. $this->_option;
	}

	/**
	 * Short description for 'resetit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function resetit()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit( 'Invalid Token' );

		// Incoming (we're expecting an array)
		$ids = JRequest::getVar( 'cid', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}

		// Make sure we have IDs to work with
		if (!is_array( $ids ) || count( $ids ) < 1) {
			echo XPollHtml::alert( JText::_( 'XPOLL_ERROR_NO_SELECTION_TO_RESET' ) );
			exit();
		}

		// Loop through the IDs
		$xpdate = new XPollDate( $this->database );
		foreach ($ids as $id)
		{
			// Load the poll
			$row = new XPollPoll( $this->database );
			$row->load( $id );

			// Only alter items not checked out or checked out by 'me'
			if ($row->checked_out == 0 || $row->checked_out == $this->juser->get('id')) {
				// Delete the Date entries
				$xpdate->deleteEntries( $id );

				// Reset voters to 0 and save
				$row->voters = 0;
				if (!$row->check()) {
					JError::raiseError( 500, $row->getError() );
					return;
				}
				if (!$row->store()) {
					JError::raiseError( 500, $row->getError() );
					return;
				}
				$row->checkin( $id );
			}
		}

		// Redirect
		$this->_redirect = 'index.php?option='. $this->_option;
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
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming (expecting an array)
		$ids = JRequest::getVar( 'cid', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}

		// Make sure we have IDs to work with
		if (count($ids) > 0) {
			$poll = new XPollPoll( $this->database );

			// Loop through the array of IDs and delete
			foreach ($ids as $id)
			{
				if (!$poll->delete( $id )) {
					$this->_message .= $poll->getError();
				}
			}
		}

		// Redirect
		$this->_redirect = 'index.php?option='. $this->_option;
	}

	/**
	 * Short description for 'publish'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $publish Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function publish( $publish=1 )
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming (we're expecting an array)
		$ids = JRequest::getVar( 'cid', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}

		// Make sure we have IDs to work with
		if (!is_array( $ids ) || count( $ids ) < 1) {
			if ($publish) {
				echo XPollHtml::alert( JText::_( 'XPOLL_ERROR_NO_SELECTION_TO_PUBLISH' ) );
			} else {
				echo XPollHtml::alert( JText::_( 'XPOLL_ERROR_NO_SELECTION_TO_UNPUBLISH' ) );
			}
			exit;
		}

		// Loop through the IDs
		foreach ($ids as $id)
		{
			// Load the poll
			$row = new XPollPoll( $this->database );
			$row->load( $id );

			// Only alter items not checked out or checked out by 'me'
			if ($row->checked_out == 0 || $row->checked_out == $this->juser->get('id')) {
				// Reset voters to 0 and save
				$row->published = $publish;
				if (!$row->check()) {
					JError::raiseError( 500, $row->getError() );
					return;
				}
				if (!$row->store()) {
					JError::raiseError( 500, $row->getError() );
					return;
				}
				$row->checkin( $id );
			}
		}

		// Redirect
		$this->_redirect = 'index.php?option='. $this->_option;
	}

	/**
	 * Short description for 'open'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $open Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function open( $open=1 )
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit( 'Invalid Token' );

		// Incoming (we're expecting an array)
		$ids = JRequest::getVar( 'cid', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}

		// Make sure we have IDs to work with
		if (!is_array( $ids ) || count( $ids ) < 1) {
			if ($publish) {
				echo XPollHtml::alert( JText::_( 'XPOLL_ERROR_NO_SELECTION_TO_OPEN' ) );
			} else {
				echo XPollHtml::alert( JText::_( 'XPOLL_ERROR_NO_SELECTION_TO_CLOSE' ) );
			}
			exit;
		}

		// Loop through the IDs
		foreach ($ids as $id)
		{
			// Load the poll
			$row = new XPollPoll( $this->database );
			$row->load( $id );

			// Only alter items not checked out or checked out by 'me'
			if ($row->checked_out == 0 || $row->checked_out == $this->juser->get('id')) {
				// Reset voters to 0 and save
				$row->open = $open;
				if (!$row->check()) {
					JError::raiseError( 500, $row->getError() );
					return;
				}
				if (!$row->store()) {
					JError::raiseError( 500, $row->getError() );
					return;
				}
				$row->checkin( $id );
			}
		}

		// Redirect
		$this->_redirect = 'index.php?option='. $this->_option;
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
		$p = JRequest::getVar( 'poll', array(), 'post' );

		// Check the poll in
		$row = new XPollPoll( $this->database );
		$row->bind( $p );
		$row->checkin();

		// Redirect
		$this->_redirect = 'index.php?option='. $this->_option;
	}
}

