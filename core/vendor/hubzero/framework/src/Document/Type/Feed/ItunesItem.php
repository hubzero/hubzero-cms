<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Hubzero\Document\Type\Feed;

/**
 * Item is an internal class that stores feed item information
 */
class ItunesItem extends Item
{
	/**
	 * iTunes summary
	 *
	 * @var  string
	 */
	public $itunes_summary = '';

	/**
	 * iTunes 'explicit content' flag
	 *
	 * @var  string
	 */
	public $itunes_explicit = "no";

	/**
	 * iTunes keywords
	 *
	 * @var  string
	 */
	public $itunes_keywords = '';

	/**
	 * iTunes author
	 *
	 * @var  string
	 */
	public $itunes_author = '';

	/**
	 * iTunes image
	 *
	 * @var  string
	 */
	public $itunes_image = '';

	/**
	 * iTunes duration (video, sound)
	 *
	 * @var  string
	 */
	public $itunes_duration = '';

	/**
	 * iTunes category
	 *
	 * @var  string
	 */
	public $itunes_category = '';

	/**
	 * iTunes subcategories
	 *
	 * @var  string
	 */
	public $itunes_subcategories = null;
}

