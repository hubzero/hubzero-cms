<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\Poll;

use Hubzero\Module\Module;

/**
 * Module class for displaying a poll
 */
class Helper extends Module
{
	/**
	 * Get poll data
	 *
	 * @return  object
	 */
	public function getPoll($id)
	{
		$db     = \App::get('db');
		$result = null;

		if ($id)
		{
			$query = 'SELECT id, title,'
				. ' CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug '
				. ' FROM #__polls'
				. ' WHERE id = '.(int) $id
				. ' AND published = 1'
				;
		}
		else
		{
			$query = 'SELECT id, title,'
				. ' CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug '
				. ' FROM #__polls'
				. ' WHERE published = 1 AND open = 1 ORDER BY id DESC Limit 1'
				;
		}
		$db->setQuery($query);
		$result = $db->loadObject();

		if ($db->getErrorNum())
		{
			throw new Exception($db->stderr(), 500);
		}

		return $result;
	}

	/**
	 * Get poll options
	 *
	 * @return  array
	 */
	public function getPollOptions($id)
	{
		$db = \App::get('db');

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
	 * @return  void
	 */
	public function display()
	{
		$tabclass_arr = array('sectiontableentry2', 'sectiontableentry1');

		$menu   = \App::get('menu');
		$items  = $menu->getItems('link', 'index.php?option=com_poll&view=poll');
		$itemid = isset($items[0]) ? '&Itemid=' . $items[0]->id : '';

		$poll   = $this->getPoll($this->params->get( 'id', 0 ));

		if ($poll && $poll->id)
		{
			$tabcnt  = 0;
			$options = $this->getPollOptions($poll->id);

			require $this->getLayoutPath();
		}
	}
}
