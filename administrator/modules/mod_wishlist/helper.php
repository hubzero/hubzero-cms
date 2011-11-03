<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class modWishlist
{
	private $_attributes = array();

	//-----------

	public function __construct($params, $module) 
	{
		$this->params = $params;
		$this->module = $module;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->_attributes[$property] = $value;
	}

	//-----------
	
	public function __get($property)
	{
		if (isset($this->_attributes[$property])) 
		{
			return $this->_attributes[$property];
		}
	}

	//-----------
	
	public function __isset($property)
	{
		return isset($this->_attributes[$property]);
	}

	//-----------

	public function display()
	{
		$this->database = JFactory::getDBO();

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_wishlist' . DS . 'tables' . DS . 'wishlist.php');
		$obj = new Wishlist($this->database);
		$wishlist = $this->params->get('wishlist', '');
		if (!$wishlist)
		{
			$wishlist = $obj->get_wishlistID(1, 'general');
			if (!$wishlist) 
			{
				$wishlist = $obj->createlist('general', 1);	
			}
		}

		$this->database->setQuery("SELECT count(*) FROM #__wishlist_item WHERE wishlist='$wishlist' AND status=1");
		$this->granted = $this->database->loadResult();

		$this->database->setQuery("SELECT count(*) FROM #__wishlist_item WHERE wishlist='$wishlist' AND accepted=0 AND status=0");
		$this->pending = $this->database->loadResult();

		$this->database->setQuery("SELECT count(*) FROM #__wishlist_item WHERE wishlist='$wishlist' AND accepted=1 AND status=0");
		$this->accepted = $this->database->loadResult();

		$this->database->setQuery("SELECT count(*) FROM #__wishlist_item WHERE wishlist='$wishlist' AND status=3");
		$this->rejected = $this->database->loadResult();

		$this->database->setQuery("SELECT count(*) FROM #__wishlist_item WHERE wishlist='$wishlist' AND status=4");
		$this->withdrawn = $this->database->loadResult();

		$this->database->setQuery("SELECT count(*) FROM #__wishlist_item WHERE wishlist='$wishlist' AND status=2");
		$this->removed = $this->database->loadResult();

		if ($this->params->get('showMine', 0))
		{
			$juser =& JFactory::getUser();
			$this->username = $juser->get('username');

			$this->database->setQuery("SELECT count(*) FROM #__wishlist_item WHERE wishlist='$wishlist' AND status=1 AND proposed_by=" . $juser->get('id'));
			$this->granted = $this->database->loadResult();

			$this->database->setQuery("SELECT count(*) FROM #__wishlist_item WHERE wishlist='$wishlist' AND accepted=0 AND status=0 AND proposed_by=" . $juser->get('id'));
			$this->pending = $this->database->loadResult();

			$this->database->setQuery("SELECT count(*) FROM #__wishlist_item WHERE wishlist='$wishlist' AND accepted=1 AND status=0 AND proposed_by=" . $juser->get('id'));
			$this->accepted = $this->database->loadResult();
		}

		$document =& JFactory::getDocument();
		$document->addStyleSheet('/administrator/modules/' . $this->module->module . '/' . $this->module->module . '.css');

		// Get the view
		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
