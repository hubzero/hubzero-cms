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

namespace Hubzero\Pagination;

use Hubzero\Base\Object;

/**
 * Pagination object representing a particular item in the pagination lists.
 *
 * @since  1.3.1
 */
class Item extends Object
{
	/**
	 * The link text.
	 *
	 * @var  string
	 */
	public $text;

	/**
	 * The number of rows as a base offset.
	 *
	 * @var  integer
	 */
	public $base;

	/**
	 * The link URL.
	 *
	 * @var  string
	 */
	public $link;

	/**
	 * The prefix used for request variables.
	 *
	 * @var  integer
	 */
	public $prefix;

	/**
	 * The prefix used for request variables.
	 *
	 * @var  integer
	 */
	public $rel;

	/**
	 * Class constructor.
	 *
	 * @param   string   $text    The link text.
	 * @param   integer  $prefix  The prefix used for request variables.
	 * @param   integer  $base    The number of rows as a base offset.
	 * @param   string   $link    The link URL.
	 * @return  void
	 */
	public function __construct($text, $prefix = '', $base = null, $link = null)
	{
		$this->text   = $text;
		$this->prefix = $prefix;
		$this->base   = $base;
		$this->link   = $link;
	}
}
