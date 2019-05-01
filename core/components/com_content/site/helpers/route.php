<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
namespace Components\Content\Site\Helpers;

use Components\Categories\Helpers\Node;
use Component;
use Lang;
use App;

require_once __DIR__ . '/category.php';

/**
 * Content Component Route Helper
 */
abstract class Route
{
	/**
	 * Lookup
	 *
	 * @var  array
	 */
	protected static $lookup;

	/**
	 * Language Lookup
	 *
	 * @var  array
	 */
	protected static $lang_lookup = array();

	/**
	 * Get category route
	 *
	 * @param   integer  $id
	 * @param   integer  $catid
	 * @param   integer  $language
	 * @return  string
	 */
	public static function getArticleRoute($id, $catid = 0, $language = 0)
	{
		$needles = array(
			'article' => array((int) $id)
		);

		// Create the link
		$link = 'index.php?option=com_content&view=article&id='. $id;
		if ((int)$catid > 1)
		{
			$categories = new Category;
			$category = $categories->get((int)$catid);
			if ($category)
			{
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid=' . $catid;
			}
		}
		if ($language && $language != '*' && Lang::isMultilang())
		{
			self::buildLanguageLookup();

			if (isset(self::$lang_lookup[$language]))
			{
				$link .= '&lang=' . self::$lang_lookup[$language];
				$needles['language'] = $language;
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Get category route
	 *
	 * @param   integer  $catid
	 * @return  string
	 */
	public static function getCategoryRoute($catid)
	{
		if ($catid instanceof Node)
		{
			$id = $catid->id;
			$category = $catid;
		}
		else
		{
			$id = (int) $catid;
			$categories = new Category;
			$category = $categories->get($id);
		}

		if ($id < 1 || !($category instanceof Node))
		{
			$link = '';
		}
		else
		{
			$needles = array();

			$link = 'index.php?option=com_content&view=category&id=' . $id;

			$catids = array_reverse($category->getPath());
			$needles['category']   = $catids;
			$needles['categories'] = $catids;

			if ($item = self::_findItem($needles))
			{
				$link .= '&Itemid=' . $item;
			}
		}

		return $link;
	}

	/**
	 * Get form route
	 *
	 * @param   integer  $id
	 * @return  string
	 */
	public static function getFormRoute($id)
	{
		// Create the link
		if (!$id)
		{
			$id = 0;
		}

		$link = 'index.php?option=com_content&task=article.edit&a_id=' . $id;

		return $link;
	}

	/**
	 * Build language lookup
	 *
	 * @return  void
	 */
	protected static function buildLanguageLookup()
	{
		if (count(self::$lang_lookup) == 0)
		{
			$db = App::get('db');
			$query = $db->getQuery()
				->select('a.sef', 'sef')
				->select('a.lang_code', 'lang_code')
				->from('#__languages', 'a')
				->toString();

			$db->setQuery($query);
			$langs = $db->loadObjectList();

			foreach ($langs as $lang)
			{
				self::$lang_lookup[$lang->lang_code] = $lang->sef;
			}
		}
	}

	/**
	 * Find an item in a haystack
	 *
	 * @param   array  $needles
	 * @return  mixed
	 */
	protected static function _findItem($needles = null)
	{
		$menus = App::get('menu');
		$language = isset($needles['language']) ? $needles['language'] : '*';

		// Prepare the reverse lookup array.
		if (!isset(self::$lookup[$language]))
		{
			self::$lookup[$language] = array();

			$component = Component::load('com_content');

			$items = $menus->getItems('component_id', $component->id);
			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']) && $item->language == $language)
				{
					$view = $item->query['view'];
					if (!isset(self::$lookup[$language][$view]))
					{
						self::$lookup[$language][$view] = array();
					}
					if (isset($item->query['id']))
					{
						self::$lookup[$language][$view][$item->query['id']] = $item->id;
					}
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$language][$view]))
				{
					foreach ($ids as $id)
					{
						if (isset(self::$lookup[$language][$view][(int)$id]))
						{
							return self::$lookup[$language][$view][(int)$id];
						}
					}
				}
			}
		}

		if ($language != '*')
		{
			$needles['language'] = '*';
			return self::_findItem($needles);
		}

		$active = $menus->getActive();
		if ($active && $active->component == 'com_content')
		{
			return $active->id;
		}

		return null;
	}
}
