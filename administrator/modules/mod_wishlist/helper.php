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

namespace Modules\Wishlist;

use Hubzero\Module\Module;

/**
 * Module class for com_wishlist data
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return     void
	 */
	public function display()
	{
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wishlist' . DS . 'models' . DS . 'wishlist.php');

		$wishlist = intval($this->params->get('wishlist', 0));
		if (!$wishlist)
		{
			$model = \WishlistModelWishlist::getInstance(1, 'general');
			if (!$model->exists())
			{
				return false;
			}
			$wishlist = $model->get('id');
		}
		$this->wishlist = $wishlist;

		$queries = array(
			'granted'   => 1,
			'pending'   => "0 AND accepted=0",
			'accepted'  => "0 AND accepted=1",
			'rejected'  => 3,
			'withdrawn' => 4,
			'removed'   => 2
		);

		$database = \JFactory::getDBO();

		foreach ($queries as $key => $state)
		{
			$database->setQuery("SELECT count(*) FROM `#__wishlist_item` WHERE wishlist='$wishlist' AND status=" . $state);
			$this->$key = $database->loadResult();
		}

		// Get the view
		parent::display();
	}
}
