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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Group module menu model class
 */
class GroupsModelModuleMenu extends \Hubzero\Base\Model
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'GroupsTableModuleMenu';

	/**
	 * Constructor
	 *
	 * @param   mixed $oid
	 * @return  void
	 */
	public function __construct($oid)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new GroupsTableModuleMenu($this->_db);

		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}
	}

	/**
	 * Get page title
	 * 
	 * @return  string
	 */
	public function getPageTitle()
	{
		if ($this->get('pageid') == 0)
		{
			return  JText::_('COM_GROUPS_PAGES_MODULE_INCLUDED_ON_ALL_PAGES');
		}

		if ($this->get('pageid') == -1)
		{
			return  JText::_('COM_GROUPS_PAGES_MODULE_INCLUDED_ON_NO_PAGES');
		}

		// new group page
		$tbl = new GroupsTablePage($this->_db);

		// load page
		$tbl->load($this->get('pageid'));

		// return page title
		return $tbl->get('title');
	}

}