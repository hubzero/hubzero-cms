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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\Dashboard;

use Hubzero\Module\Module;

/**
 * Module class for Administrator dashboard
 */
class Helper extends Module
{
	/**
	 * Output module HTML
	 *
	 * @return     void
	 */
	public function display()
	{
		$mosConfig_bankAccounts = 0;
		$database = \JFactory::getDBO();

		$jconfig  = \JFactory::getConfig();
		$upconfig = \JComponentHelper::getParams('com_members');
		$this->banking  = $upconfig->get('bankAccounts');
		$this->sitename = $jconfig->getValue('config.sitename');

		$threemonths = \JFactory::getDate(time() - (92 * 24 * 60 * 60))->toSql();
		$onemonth    = \JFactory::getDate(time() - (30 * 24 * 60 * 60))->toSql();

		if ($this->banking && \JComponentHelper::isEnabled('com_store'))
		{
			// get new store orders
			$database->setQuery( "SELECT count(*) FROM `#__orders` WHERE status=0");
			$this->orders = $database->loadResult();
		}

		// get open support tickets over 3 months old
		/*$sql = "SELECT count(*) FROM `#__support_tickets` WHERE status=1 AND created < '".$threemonths."' AND section!=2 AND type=0";
		$database->setQuery($sql);
		$oldtickets = $database->loadResult();

		// get unassigned support tickets
		$sql = "SELECT count(*) FROM `#__support_tickets` WHERE status=0 AND section!=2 AND type=0 AND (owner is NULL OR owner='') AND report != ''";
		$database->setQuery($sql);
		$newtickets = $database->loadResult();*/

		// get abuse reports
		$sql = "SELECT count(*) FROM `#__abuse_reports` WHERE state=0";
		$database->setQuery($sql);
		$this->reports = $database->loadResult();

		// get pending resources
		$sql = "SELECT count(*) FROM `#__resources` WHERE published=3";
		$database->setQuery($sql);
		$this->pending = $database->loadResult();

		// get contribtool entries requiring admin attention
		$sql = "SELECT count(*) FROM `#__tool` AS t JOIN jos_tool_version as v ON v.toolid=t.id AND v.mw='narwhal' AND v.state=3  WHERE t.state=1 OR t.state=3 OR t.state=5 OR t.state=6";
		$database->setQuery($sql);
		$this->contribtool = $database->loadResult();

		// get recent quotes
		$sql = "SELECT count(*) FROM `#__feedback` WHERE `date` > '".$onemonth."'";
		$database->setQuery($sql);
		$this->quotes = $database->loadResult();

		// get wishes from main wishlist - to come
		$this->wishes = 0;

		// Check if component entry is there
		$database->setQuery( "SELECT c.extension_id FROM `#__extensions` as c WHERE c.element='com_wishlist' AND type='component' AND enabled=1" );
		$found = $database->loadResult();

		if ($found)
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wishlist' . DS . 'models' . DS . 'wishlist.php');

			$obj = new \Wishlist($database);
			$objWish = new \Wish($database);
			$juser   = \JFactory::getUser();

			// Check if main wishlist exists, create one if missing
			$this->mainlist = $obj->get_wishlistID(1, 'general');
			if (!$this->mainlist)
			{
				$this->mainlist = $obj->createlist('general', 1);
			}
			$filters = array('filterby'=>'pending', 'sortby'=>'date');
			$wishes = $objWish->get_wishes($this->mainlist, $filters, 1, $juser);

			$this->wishes = count($wishes);
		}

		// Get the view
		parent::display();
	}
}
