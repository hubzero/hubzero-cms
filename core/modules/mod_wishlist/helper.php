<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Wishlist;

use Hubzero\Module\Module;
use Components\Wishlist\Models\Wishlist;
use Component;

/**
 * Module class for com_wishlist data
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
		if (!\App::isAdmin())
		{
			return;
		}

		include_once Component::path('com_wishlist') . DS . 'models' . DS . 'wishlist.php';

		$wishlist = intval($this->params->get('wishlist', 0));
		if (!$wishlist)
		{
			$model = Wishlist::oneByReference(1, 'general');
			if (!$model->get('id'))
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

		$database = \App::get('db');

		foreach ($queries as $key => $state)
		{
			$database->setQuery("SELECT COUNT(*) FROM `#__wishlist_item` WHERE wishlist=" . $database->quote($wishlist) . " AND status=" . $state);
			$this->$key = $database->loadResult();
		}

		// Get the view
		parent::display();
	}
}
