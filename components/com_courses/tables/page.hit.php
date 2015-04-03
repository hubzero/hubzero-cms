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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Courses\Tables;

/**
 * Table class for course page
 */
Class PageHit extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_page_hits', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->offering_id = intval($this->offering_id);
		if (!$this->offering_id)
		{
			$this->setError(Lang::txt('COM_COURSES_LOGS_MUST_HAVE_OFFERING_ID'));
			return false;
		}

		$this->page_id = intval($this->page_id);
		if (!$this->page_id)
		{
			$this->setError(Lang::txt('COM_COURSES_LOGS_MUST_HAVE_PAGE_ID'));
			return false;
		}

		$this->ip = trim($this->ip);

		if (!$this->id)
		{
			$this->timestamp = \JFactory::getDate()->toSql();
			if (!$this->ip)
			{
				$this->ip = Request::ip();
			}
			if (!$this->user_id)
			{
				$this->user_id = User::get('id');
			}
		}

		$this->user_id = intval($this->user_id);
		if (!$this->user_id)
		{
			$this->setError(Lang::txt('COM_COURSES_LOGS_MUST_HAVE_USER_ID'));
			return false;
		}

		return true;
	}

	/**
	 * Record a page hit
	 *
	 * @param      integer $pid Page ID
	 * @return     void
	 */
	public function hit($offering_id, $page_id, $user_id=0)
	{
		if (!$user_id)
		{
			$user_id = User::get('id');
		}
		$this->offering_id = $offering_id;
		$this->page_id     = $page_id;
		$this->user_id     = $user_id;

		if (!$this->check())
		{
			return false;
		}
		if (!$this->store())
		{
			return false;
		}
		return true;
	}
}
