<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Helpers;

use Hubzero\Config\Registry;
use Hubzero\User\Group;
use Component;
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

	/**
	 * Whether the page views should list the page's ancestors as crumbs
	 *
	 * A group wiki follows the group's Wiki Settings; every other wiki
	 * follows the component's configuration. Both default to off, since
	 * a site or group template may already show the pathway.
	 *
	 * @return  bool
	 */
	protected function showPageCrumbs()
	{
		if ($this->page->get('scope') == 'group')
		{
			$group = Group::getInstance($this->page->get('scope_id'));
			if (!$group)
			{
				return false;
			}

			$params = new Registry($group->get('params'));

			return (bool) $params->get('wiki_crumbs', 0);
		}

		return (bool) Component::params('com_wiki')->get('wiki_crumbs', 0);
	}
}
