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

namespace Modules\MyWishes;

use Hubzero\Module\Module;
use User;

/**
 * Module class for displaying a user's wishes
 */
class Helper extends Module
{
	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		$database = \App::get('db');

		$limit = intval($this->params->get('limit', 10));

		// Find the user's most recent wishes
		$database->setQuery(
			"(
				SELECT id, wishlist, subject, about, proposed, status, accepted, assigned,
					(SELECT wl.title FROM #__wishlist as wl WHERE wl.id=w.wishlist) as listtitle
				FROM #__wishlist_item as w WHERE w.proposed_by='" . User::get('id') . "' AND (w.status=0 or w.status=3)
				ORDER BY proposed DESC
				LIMIT $limit
			)
			UNION
			(
				SELECT id, wishlist, subject, about, proposed, status, accepted, assigned,
					(SELECT wl.title FROM #__wishlist as wl WHERE wl.id=w.wishlist) as listtitle
				FROM #__wishlist_item as w WHERE w.assigned='" . User::get('id') . "' AND (w.status=0 or w.status=3)
				ORDER BY proposed DESC
				LIMIT $limit
			)"
		);
		$this->rows = $database->loadObjectList();
		if ($database->getErrorNum())
		{
			$this->setError($database->stderr());
			$this->rows = array();
		}

		$rows1 = array();
		$rows2 = array();

		if ($this->rows)
		{
			foreach ($this->rows as $row)
			{
				if ($row->assigned == User::get('id'))
				{
					$rows2[] = $row;
				}
				else
				{
					$rows1[] = $row;
				}
			}
		}

		$this->rows1 = $rows1;
		$this->rows2 = $rows2;

		// Push the module CSS to the template
		$this->css();

		require $this->getLayoutPath();
	}
}
