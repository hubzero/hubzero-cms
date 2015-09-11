<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
