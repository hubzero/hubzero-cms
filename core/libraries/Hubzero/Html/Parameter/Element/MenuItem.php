<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;
use Hubzero\Html\Builder;
use App;

/**
 * Renders a menu item element
 */
class MenuItem extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'MenuItem';

	/**
	 * Fetch a calendar element
	 *
	 * @param   string  $name          Element name
	 * @param   string  $value         Element value
	 * @param   object  &$node         XMLElement node object containing the settings for the element
	 * @param   string  $control_name  Control name
	 * @return  string
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$db = App::get('db');

		$menuType = $this->_parent->get('menu_type');
		if (!empty($menuType))
		{
			$where = ' WHERE menutype = ' . $db->quote($menuType);
		}
		else
		{
			$where = ' WHERE 1';
		}

		// Load the list of menu types
		// TODO: move query to model
		$query = $db->getQuery()
			->select('menutype')
			->select('title')
			->from('#__menu_types')
			->order('title', 'asc');

		$db->setQuery($query->toString());
		$menuTypes = $db->loadObjectList();

		if ($state = $node->attributes('state'))
		{
			$where .= ' AND published = ' . (int) $state;
		}

		// load the list of menu items
		// TODO: move query to model
		$query = $db->getQuery()
			->select('id')
			->select('parent_id')
			->select('title')
			->select('menutype')
			->select('type')
			->from('#__menu');

		$menuType = $this->_parent->get('menu_type');
		if (!empty($menuType))
		{
			$query->whereEquals('menutype', $menuType);
		}
		if ($state = $node->attributes('state'))
		{
			$query->whereEquals('published', (int) $state);
		}

		$query
			->order('menutype', 'asc')
			->order('parent_id', 'asc')
			->order('ordering', 'asc');

		$db->setQuery($query->toString());
		$menuItems = $db->loadObjectList();

		// Establish the hierarchy of the menu
		// TODO: use node model
		$children = array();

		if ($menuItems)
		{
			// First pass - collect children
			foreach ($menuItems as $v)
			{
				$pt = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}

		// Second pass - get an indent list of the items
		$list = Builder\Menu::treerecurse(0, '', array(), $children, 9999, 0, 0);

		// Assemble into menutype groups
		$n = count($list);
		$groupedList = array();
		foreach ($list as $k => $v)
		{
			$groupedList[$v->menutype][] = &$list[$k];
		}

		// Assemble menu items to the array
		$options = array();
		$options[] = Builder\Select::option('', App::get('language')->txt('JOPTION_SELECT_MENU_ITEM'));

		foreach ($menuTypes as $type)
		{
			if ($menuType == '')
			{
				$options[] = Builder\Select::option('0', '&#160;', 'value', 'text', true);
				$options[] = Builder\Select::option($type->menutype, $type->title . ' - ' . App::get('language')->txt('JGLOBAL_TOP'), 'value', 'text', true);
			}
			if (isset($groupedList[$type->menutype]))
			{
				$n = count($groupedList[$type->menutype]);
				for ($i = 0; $i < $n; $i++)
				{
					$item = &$groupedList[$type->menutype][$i];

					// If menutype is changed but item is not saved yet, use the new type in the list
					if (App::get('request')->getString('option', '', 'get') == 'com_menus')
					{
						$currentItemArray = App::get('request')->getVar('cid', array(0), '', 'array');
						$currentItemId    = (int) $currentItemArray[0];
						$currentItemType  = App::get('request')->getString('type', $item->type, 'get');
						if ($currentItemId == $item->id && $currentItemType != $item->type)
						{
							$item->type = $currentItemType;
						}
					}

					$disable = strpos($node->attributes('disable'), $item->type) !== false ? true : false;
					$options[] = Builder\Select::option($item->id, '&#160;&#160;&#160;' . $item->treename, 'value', 'text', $disable);

				}
			}
		}

		return Builder\Select::genericlist(
			$options,
			$control_name . '[' . $name . ']',
			array(
				'id' => $control_name . $name,
				'list.attr' => 'class="inputbox"',
				'list.select' => $value
			)
		);
	}
}
