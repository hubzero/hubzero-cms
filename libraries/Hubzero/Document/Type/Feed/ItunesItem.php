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

namespace Hubzero\Document\Type\Feed;

/**
 * Item is an internal class that stores feed item information
 */
class ItunesItem extends Item
{
	/**
	 * Description for 'itunes_summary'
	 *
	 * @public string
	 */
	public $itunes_summary = '';

	/**
	 * Description for 'itunes_explicit'
	 *
	 * @public string
	 */
	public $itunes_explicit = "no";

	/**
	 * Description for 'itunes_keywords'
	 *
	 * @public string
	 */
	public $itunes_keywords = '';

	/**
	 * Description for 'itunes_author'
	 *
	 * @public string
	 */
	public $itunes_author = '';

	/**
	 * Description for 'itunes_image'
	 *
	 * @public string
	 */
	public $itunes_image = '';

	/**
	 * Description for 'itunes_duration'
	 *
	 * @public string
	 */
	public $itunes_duration = '';

	/**
	 * Description for 'itunes_category'
	 *
	 * @public string
	 */
	public $itunes_category = '';

	/**
	 * Description for 'itunes_subcategories'
	 *
	 * @public unknown
	 */
	public $itunes_subcategories = null;
}

