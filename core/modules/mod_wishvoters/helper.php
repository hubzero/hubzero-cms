<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\WishVoters;

use Hubzero\Module\Module;
use Components\Wishlist\Tables\Wishlist;
use Component;
use Request;
use Lang;

/**
 * Module class for displaying top wish voters
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		$database = \App::get('db');

		include_once(Component::path('com_wishlist') . DS . 'models' . DS . 'wishlist.php');

		$objWishlist = new Wishlist($database);

		// Which list is being viewed?
		$listid   = Request::getInt('id', 0);
		$refid    = Request::getInt('rid', 0);
		$category = Request::getVar('category', '');

		// Figure list id
		if ($category && $refid)
		{
			$listid = $objWishlist->get_wishlistID($refid, $category);
		}

		// Cannot rank a wish if list/wish is not found
		if (!$listid)
		{
			echo '<p class="warning">' . Lang::txt('MOD_WISHVOTERS_ERROR_LOADING') . '</p>';
			return;
		}

		$database->setQuery(
			"SELECT DISTINCT v.userid, SUM(v.importance) as imp, COUNT(v.wishid) as times "
			. " FROM #__wishlist_vote as v JOIN #__wishlist_item as w ON w.id=v.wishid WHERE w.wishlist='" . $listid . "'"
			. " GROUP BY v.userid ORDER BY times DESC, v.voted DESC "
		);
		$this->rows = $database->loadObjectList();
		if ($database->getErrorNum())
		{
			$this->setError($database->stderr());
			return '<p class="error">' . Lang::txt('MOD_WISHVOTERS_ERROR_RETRIEVING') . '</p>';
		}

		require $this->getLayoutPath();
	}
}
