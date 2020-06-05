<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Menu;

use Hubzero\Module\Module;
use Cache;
use Route;
use User;
use App;

/**
 * Module class for displaying a menu
 */
class Helper extends Module
{
	/**
	 * Display module
	 *
	 * @return  void
	 */
	public function display()
	{
		// [!] Legacy compatibility for older view overrides
		$params = $this->params;
		$module = $this->module;

		$list      = self::getList($params);
		$menu      = App::get('menu');
		$active    = $menu->getActive();
		$active_id = isset($active) ? $active->id : $menu->getDefault()->id;
		$path      = isset($active) ? $active->tree : array();
		$showAll   = $params->get('showAllChildren');
		$class_sfx = htmlspecialchars($params->get('class_sfx'));

		if (count($list))
		{
			require $this->getLayoutPath($params->get('layout', 'default'));
		}
	}

	/**
	 * Get a list of the menu items.
	 *
	 * @param   object  $params  Registry The module options.
	 * @return  array
	 */
	static function getList(&$params)
	{
		$menu = App::get('menu');

		// If no active menu, use default
		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();

		$levels = User::getAuthorisedViewLevels();
		asort($levels);

		$key = 'mod_menu.' . 'menu_items' . $params . implode(',', $levels) . '.' . $active->id;

		if (!($items = Cache::get($key)))
		{
			// Initialise variables.
			$list     = array();
			$db       = App::get('db');

			$path     = $active->tree;
			$start    = (int) $params->get('startLevel');
			$end      = (int) $params->get('endLevel');
			$showAll  = $params->get('showAllChildren');
			$items    = $menu->getItems('menutype', $params->get('menutype'));

			$lastitem = 0;

			if ($items)
			{
				foreach ($items as $i => $item)
				{
					if (($start && $start > $item->level)
						|| ($end && $item->level > $end)
						|| (!$showAll && $item->level > 1 && !in_array($item->parent_id, $path))
						|| ($start > 1 && !in_array($item->tree[$start-2], $path))
					)
					{
						unset($items[$i]);
						continue;
					}

					$item->deeper = false;
					$item->shallower = false;
					$item->level_diff = 0;

					if (isset($items[$lastitem]))
					{
						$items[$lastitem]->deeper     = ($item->level > $items[$lastitem]->level);
						$items[$lastitem]->shallower  = ($item->level < $items[$lastitem]->level);
						$items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
					}

					$item->parent = (boolean) $menu->getItems('parent_id', (int) $item->id, true);

					$lastitem = $i;
					$item->active = false;
					$item->flink  = $item->link;

					$item->title        = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
					$item->anchor_css   = htmlspecialchars($item->params->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false);
					$item->anchor_title = htmlspecialchars($item->params->get('menu-anchor_title', ''), ENT_COMPAT, 'UTF-8', false);
					$item->menu_image   = $item->params->get('menu_image', '') ? htmlspecialchars($item->params->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false) : '';

					// Reverted back for CMS version 2.5.6
					switch ($item->type)
					{
						case 'separator':
							// No further action needed.
							continue 2;
							break;

						case 'url':
							if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false))
							{
								// If this is an internal link, ensure the Itemid is set.
								$item->flink = $item->link . '&Itemid=' . $item->id;
							}
							break;

						case 'alias':
							// If this is an alias use the item id stored in the parameters to make the link.
							$item->flink = 'index.php?Itemid=' . $item->params->get('aliasoptions');
							break;

						default:
							$item->flink = 'index.php?Itemid=' . $item->id;
							break;
					}

					if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false))
					{
						$item->flink = Route::url($item->flink, true, $item->params->get('secure'));
					}
					else
					{
						$item->flink = Route::url($item->flink);
					}
				}

				if (isset($items[$lastitem]))
				{
					$items[$lastitem]->deeper     = (($start ? $start : 1) > $items[$lastitem]->level);
					$items[$lastitem]->shallower  = (($start ? $start : 1) < $items[$lastitem]->level);
					$items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start ? $start : 1));
				}
			}

			Cache::put($key, $items, intval($params->get('cache_time', 900)) / 60);
		}
		return $items;
	}
}
