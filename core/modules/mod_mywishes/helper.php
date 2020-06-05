<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
