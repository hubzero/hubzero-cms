<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
		$category = Request::getString('category', '');

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
