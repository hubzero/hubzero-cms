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
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Hubzero\Document\Type\Feed;

use Hubzero\Base\Object;

/**
 * Image is an internal class that stores feed image information
 *
 * Inspired by Joomla's JFeedImage class
 */
class Image extends Object
{
	/**
	 * Title image attribute
	 *
	 * @var	 string
	 */
	public $title = '';

	/**
	 * URL image attribute
	 *
	 * @var	 string
	 */
	public $url = '';

	/**
	 * Link image attribute
	 *
	 * @var	 string
	 */
	public $link = '';

	/**
	 * Image width attribute
	 *
	 * optional
	 *
	 * @var	 string
	 */
	public $width;

	/**
	 * Image height attribute
	 *
	 * optional
	 *
	 * @var	 string
	 */
	public $height;

	/**
	 * Image description attribute
	 *
	 * optional
	 *
	 * @var	 string
	 */
	public $description;
}
