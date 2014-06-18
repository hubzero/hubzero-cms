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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Abstract model class for following
 */
abstract class CollectionsModelFollowingAbstract extends \Hubzero\Base\Object
{
	/**
	 * Varies
	 *
	 * @var object
	 */
	private $_obj = NULL;

	/**
	 * File path
	 *
	 * @var string
	 */
	private $_image = NULL;

	/**
	 * URL
	 *
	 * @var string
	 */
	private $_baselink = 'index.php';

	/**
	 * Constructor
	 *
	 * @param      integer $id Member ID
	 * @return     void
	 */
	public function __construct($oid=null)
	{
	}

	/**
	 * Returns a reference to a model object
	 *
	 * This method must be invoked as:
	 *     $inst = CollectionsModelFollowMember::getInstance($oid);
	 *
	 * @param      integer $oid User ID
	 * @return     object CollectionsModelFollowingAbstract
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new CollectionsModelFollowingAbstract($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @return     mixed
	 */
	public function creator()
	{
		return null;
	}

	/**
	 * Get this item's image
	 *
	 * @return     string
	 */
	public function image()
	{
		return $this->_image;
	}

	/**
	 * Get this item's alias
	 *
	 * @return     string
	 */
	public function alias()
	{
		return '';
	}

	/**
	 * Get this item's title
	 *
	 * @return     string
	 */
	public function title()
	{
		return '';
	}

	/**
	 * Get the URL for this item
	 *
	 * @return     string
	 */
	public function link($what='base')
	{
		return $this->_baselink;
	}
}
