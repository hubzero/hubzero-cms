<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Item;

/**
 * Table class for comment votes
 */
class Vote extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__item_votes', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->item_id = intval($this->item_id);
		if (!$this->item_id)
		{
			$this->setError(\Lang::txt('Missing item ID.'));
			return false;
		}

		$this->item_type = strtolower(preg_replace("/[^a-zA-Z0-9\-]/", '', trim($this->item_type)));
		if (!$this->item_type)
		{
			$this->setError(\Lang::txt('Missing item type.'));
			return false;
		}

		if (!$this->created_by)
		{
			$this->created_by = \User::get('id');
		}

		switch ($this->vote)
		{
			case 'down':
			case 'dislike':
			case 'negative':
			case 'minus':
			case '-':
			case '-1':
				$this->vote = -1;
			break;

			case 'up':
			case 'like':
			case 'positive':
			case 'plus':
			case '+':
			case '1':
			default:
				$this->vote = 1;
			break;
		}

		if (!$this->id)
		{
			$this->created = \Date::toSql();
		}

		return true;
	}
}
