<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\View\Helper;

/**
 * Create a pagination object and return it
 */
class Pagination extends AbstractHelper
{
	/**
	 * Instantiate the paginator and return it
	 *
	 * @param   integer  $total  Total number of records
	 * @param   integer  $start  Where to start
	 * @param   integer  $limit  Number of records per page
	 * @return  object
	 */
	public function __invoke($total, $start, $limit)
	{
		$start = $start ?: 0;
		$limit = $limit ?: \JFactory::getConfig()->get('list_limit');

		jimport('joomla.html.pagination');

		return new \JPagination($total, $start, $limit);
	}
}
