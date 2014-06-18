<?php
/**
* @version		$Id: poll.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @subpackage	Polls
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

/**
* @package		Joomla
* @subpackage	Polls
*/
class PollModelPoll extends JModel
{
	/**
	 * Add vote
	 * @param int The id of the poll
	 * @param int The id of the option selected
	 */
	function vote( $poll_id, $option_id )
	{
		$db = $this->getDBO();
		$poll_id	= (int) $poll_id;
		$option_id	= (int) $option_id;

		$query = 'UPDATE #__poll_data'
			. ' SET hits = hits + 1'
			. ' WHERE pollid = ' . (int) $poll_id
			. ' AND id = ' . (int) $option_id
			;
		$db->setQuery( $query );
		$db->query();

		$query = 'UPDATE #__polls'
			. ' SET voters = voters + 1'
			. ' WHERE id = ' . (int) $poll_id
			;
		$db->setQuery( $query );
		$db->query();

		$date = JFactory::getDate();

		$query = 'INSERT INTO #__poll_date'
			. ' SET date = ' . $db->Quote($date->toMySQL())
			. ', vote_id = ' . (int) $option_id
			. ', poll_id = ' . (int) $poll_id
		;
		$db->setQuery( $query );
		$db->query();
	}

	function getLatest()
	{
		$db		= JFactory::getDBO();
		$result	= null;

		$query = 'SELECT id'
			.' FROM #__polls'
			.' WHERE published = 1 AND open = 1 ORDER BY id DESC Limit 1'
			;
		$db->setQuery($query);
		$result = $db->loadResult();

		if ($db->getErrorNum()) {
			JError::raiseWarning( 500, $db->stderr() );
		}

		$poll = JTable::getInstance('poll', 'Table');
		$poll->load($result);

		return $poll;
	}

	function getPollOptions($id)
	{
		$db	= JFactory::getDBO();

		$query = 'SELECT id, text' .
			' FROM #__poll_data' .
			' WHERE pollid = ' . (int) $id .
			' AND text <> ""' .
			' ORDER BY id';
		$db->setQuery($query);

		if (!($options = $db->loadObjectList())) {
			//echo "MD ".$db->stderr();
			return array();
		}

		return $options;
	}
}
