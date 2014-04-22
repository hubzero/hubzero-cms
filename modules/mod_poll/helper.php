<?php
/**
* @version		$Id: helper.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for displaying a poll
 */
class modPoll extends \Hubzero\Module\Module
{
	/**
	 * Get poll data
	 * 
	 * @return     object
	 */
	public function getPoll($id)
	{
		$db		= JFactory::getDBO();
		$result	= null;

		if ($id)
		{
			$query = 'SELECT id, title,'
				.' CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug '
				.' FROM #__polls'
				.' WHERE id = '.(int) $id
				.' AND published = 1'
				;
		}
		else 
		{
			$query = 'SELECT id, title,'
				.' CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug '
				.' FROM #__polls'
				.' WHERE published = 1 AND open = 1 ORDER BY id DESC Limit 1'
				;
		}
		$db->setQuery($query);
		$result = $db->loadObject();

		if ($db->getErrorNum()) {
			JError::raiseWarning( 500, $db->stderr() );
		}

		return $result;
	}

	/**
	 * Get poll options
	 * 
	 * @return     array
	 */
	public function getPollOptions($id)
	{
		$db	= JFactory::getDBO();

		$query = 'SELECT id, text' .
			' FROM #__poll_data' .
			' WHERE pollid = ' . (int) $id .
			' AND text <> ""' .
			' ORDER BY id';
		$db->setQuery($query);

		if (!($options = $db->loadObjectList())) 
		{
			echo "MD " . $db->stderr();
			return;
		}

		return $options;
	}

	/**
	 * Display module content
	 * 
	 * @return     void
	 */
	public function display()
	{
		$this->run();
	}

	/**
	 * Build module contents
	 * 
	 * @return     void
	 */
	public function run()
	{
		$tabclass_arr = array('sectiontableentry2', 'sectiontableentry1');

		$menu 	= JFactory::getApplication()->getMenu();
		$items	= $menu->getItems('link', 'index.php?option=com_poll&view=poll');
		$itemid = isset($items[0]) ? '&Itemid=' . $items[0]->id : '';

		$poll   = $this->getPoll($this->params->get( 'id', 0 ));

		if ($poll && $poll->id) 
		{
			$tabcnt = 0;
			$options = $this->getPollOptions($poll->id);

			require(JModuleHelper::getLayoutPath($this->module->module));
		}
	}
}
