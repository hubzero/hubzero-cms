<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\WishVoters;

use Hubzero\Module\Module;
use Components\Wishlist\Models\Wishlist;
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
		include_once Component::path('com_wishlist') . DS . 'models' . DS . 'wishlist.php';

		// Which list is being viewed?
		$listid   = Request::getInt('id', 0);
		$refid    = Request::getInt('rid', 0);
		$category = Request::getVar('category', '');

		// Figure list id
		if ($category && $refid)
		{
			$listid = Wishlist::onebyReference($refid, $category)->get('id');
		}

		// Cannot rank a wish if list/wish is not found
		if (!$listid)
		{
			echo '<p class="warning">' . Lang::txt('MOD_WISHVOTERS_ERROR_LOADING') . '</p>';
			return;
		}

		$database = \App::get('db');
		$database->setQuery(
			"SELECT DISTINCT v.userid, SUM(v.importance) as imp, COUNT(v.wishid) as times
			FROM `#__wishlist_vote` as v
			INNER JOIN `#__wishlist_item` as w ON w.id=v.wishid
			WHERE w.wishlist=" . $database->quote($listid) . "
			GROUP BY v.userid ORDER BY times DESC, v.voted DESC "
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
