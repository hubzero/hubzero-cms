<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_newsfeeds
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_HZEXEC_') or die();

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Newsfeeds Component Route Helper
 *
 * @package		Joomla.Site
 * @subpackage	com_newsfeeds
 * @since		1.5
 */
abstract class NewsfeedsHelperRoute
{
	protected static $lookup;

	/**
	 * @param	int	The route of the newsfeed
	 */
	public static function getNewsfeedRoute($id, $catid)
	{
		$needles = array(
			'newsfeed'  => array((int) $id)
		);

		//Create the link
		$link = 'index.php?option=com_newsfeeds&view=newsfeed&id='. $id;

		if ((int)$catid > 1)
		{
			$categories = JCategories::getInstance('Newsfeeds');
			$category = $categories->get((int)$catid);

			if ($category)
			{
				//TODO Throw error that the category either not exists or is unpublished
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	public static function getCategoryRoute($catid)
	{
		if ($catid instanceof JCategoryNode)
		{
			$id = $catid->id;
			$category = $catid;
		}
		else
		{
			$id = (int) $catid;
			$category = JCategories::getInstance('Newsfeeds')->get($id);
		}

		if ($id < 1 || !($category instanceof JCategoryNode))
		{
			$link = '';
		}
		else
		{
			$needles = array();

			//Create the link
			$link = 'index.php?option=com_newsfeeds&view=category&id='.$id;

			$catids = array_reverse($category->getPath());
			$needles['category'] = $catids;
			$needles['categories'] = $catids;

			if ($item = self::_findItem($needles))
			{
				$link .= '&Itemid='.$item;
			}
		}

		return $link;
	}

	protected static function _findItem($needles = null)
	{
		$app   = JFactory::getApplication();
		$menus = $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();

			$component = \Component::params('com_newsfeeds');
			$items     = $menus->getItems('component_id', $component->id);
			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];
					if (!isset(self::$lookup[$view]))
					{
						self::$lookup[$view] = array();
					}
					if (isset($item->query['id']))
					{
						self::$lookup[$view][$item->query['id']] = $item->id;
					}
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view]))
				{
					foreach ($ids as $id)
					{
						if (isset(self::$lookup[$view][(int)$id]))
						{
							return self::$lookup[$view][(int)$id];
						}
					}
				}
			}
		}

		$active = $menus->getActive();
		if ($active)
		{
			return $active->id;
		}

		return null;
	}
}
