<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\BreadCrumbs;

use Hubzero\Module\Module;
use Route;
use Lang;
use Html;
use stdClass;

/**
 * Module class for displaying breadcrumbs
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
		// Legacy support in case old view overrides reference
		// $params instead of $this->params
		$params = $this->params;

		// Get the breadcrumbs
		$list   = $this->getList();
		$count  = count($list);

		// Set the default separator
		$separator = $this->setSeparator($this->params->get('separator'));
		$moduleclass_sfx = htmlspecialchars($this->params->get('moduleclass_sfx'));

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}

	/**
	 * Get the list of crumbs
	 *
	 * @return  array
	 */
	public function getList()
	{
		$items = \Pathway::items();

		$count = count($items);

		// Don't use $items here as it references Pathway properties directly
		$crumbs = array();
		for ($i = 0; $i < $count; $i ++)
		{
			$crumbs[$i] = new stdClass();
			$crumbs[$i]->name = stripslashes(htmlspecialchars($items[$i]->name, ENT_COMPAT, 'UTF-8'));
			$crumbs[$i]->link = Route::url($items[$i]->link);
		}

		if ($this->params->get('showHome', 1))
		{
			$item = new stdClass();
			$item->name = htmlspecialchars($this->params->get('homeText', Lang::txt('MOD_BREADCRUMBS_HOME')));
			$item->link = Route::url('index.php?Itemid=' . \App::get('menu')->getDefault()->id);

			array_unshift($crumbs, $item);
		}

		return $crumbs;
	}

	/**
	 * Set the breadcrumbs separator for the breadcrumbs display.
	 *
	 * @param   string  $custom  Custom xhtml complient string to separate the items of the breadcrumbs
	 * @return  string  Separator string
	 */
	public function setSeparator($custom = null)
	{
		// If a custom separator has not been provided we try to load a template
		// specific one first, and if that is not present we load the default separator
		if ($custom == null)
		{
			if (Lang::isRTL())
			{
				$_separator = '&lsaquo;';
			}
			else
			{
				$_separator = '&rsaquo;';
			}
		}
		else
		{
			$_separator = htmlspecialchars($custom);
		}

		return $_separator;
	}
}
