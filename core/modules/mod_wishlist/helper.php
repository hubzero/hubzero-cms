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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
