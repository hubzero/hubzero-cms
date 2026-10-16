<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Helpers;

use Pathway;

/**
 * Breadcrumb helpers shared by the wiki site controllers
 */
trait PagePathway
{
	/**
	 * Append the current page's ancestors and the page itself to the pathway
	 *
	 * @return  array  The page's ancestors, root first, for views that list them as crumbs
	 */
	protected function appendPagePathway()
	{
		$parents = array();

		if ($this->page->get('parent'))
		{
			$parents = $this->page->ancestors();

			foreach ($parents as $p)
			{
				Pathway::append(
					$p->get('title'),
					$p->link()
				);
			}
		}

		Pathway::append(
			$this->page->title,
			$this->page->link()
		);

		return $parents;
	}
}
