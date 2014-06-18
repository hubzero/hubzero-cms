<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'log.php');

/**
 * Model class for a tag
 */
class TagsModelLog extends \Hubzero\Base\Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'TagsTableLog';

	/**
	 * JUser
	 *
	 * @var object
	 */
	protected $_creator = NULL;

	/**
	 * JUser
	 *
	 * @var object
	 */
	protected $_actor = NULL;

	/**
	 * Constructor
	 *
	 * @param      integer $id Tag ID or raw tag
	 * @return     void
	 */
	public function __construct($oid)
	{
		// Set the database object
		$this->_db = JFactory::getDBO();

		// Set the table object
		$this->_tbl = new TagsTableLog($this->_db);

		// Load record
		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_string($oid))
		{
			$this->_tbl->loadTag($oid);
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}
	}

	/**
	 * Returns a reference to a tag model
	 *
	 * @param      mixed $oid Tag ID or raw tag
	 * @return     object TagsModelTag
	 */
	static function &getInstance($oid=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid;
		}
		else if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new TagsModelLog($oid);
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
	public function creator($property=null)
	{
		if (!isset($this->_creator) || !is_object($this->_creator))
		{
			$this->_creator = JUser::getInstance($this->get('user_id'));
		}
		if ($property && $this->_creator instanceof JUser)
		{
			return $this->_creator->get($property);
		}
		return $this->_creator;
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
	public function actor($property=null)
	{
		if (!isset($this->_actor) || !is_object($this->_actor))
		{
			$this->_actor = JUser::getInstance($this->get('actorid'));
		}
		if ($property && $this->_actor instanceof JUser)
		{
			return $this->_actor->get($property);
		}
		return $this->_actor;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function created($rtrn='')
	{
		switch (strtolower($rtrn))
		{
			case 'date':
				return JHTML::_('date', $this->get('timestamp'), JText::_('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return JHTML::_('date', $this->get('timestamp'), JText::_('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('timestamp');
			break;
		}
	}
}
