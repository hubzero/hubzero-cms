<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

//namespace Hubzero\Html\Builder;

//use Lang;
//use Html;
//use App;

include_once dirname(__DIR__) . '/menus.php';

/**
 * Menus HTML helper
 */
class HtmlMenus
{
	/**
	 * @param   integer  $itemid  The menu item id
	 * @return  string
	 */
	public static function association($itemid)
	{
		// Get the associations
		$associations = \Components\Menus\Helpers\Menus::getAssociations($itemid);

		// Get the associated menu items
		$db = App::get('db');

		$query = $db->getQuery();
		$query->select('m.*');
		$query->select('mt.title', 'menu_title');
		$query->from('#__menu', 'm');
		$query->join('#__menu_types as mt', 'mt.menutype', 'm.menutype', 'left');
		$query->whereIn('m.id', array_values($associations));
		$query->join('#__languages as l', 'm.language', 'l.lang_code', 'left');
		$query->select('l.image');
		$query->select('l.title', 'language_title');

		$db->setQuery($query->toString());
		$items = $db->loadObjectList('id');

		// Check for a database error.
		if ($error = $db->getErrorMsg())
		{
			throw new Exception($error, 500);
		}

		// Construct html
		$text = array();
		foreach ($associations as $tag => $associated)
		{
			if ($associated != $itemid)
			{
				$text[] = Lang::txt('COM_MENUS_TIP_ASSOCIATED_LANGUAGE', Html::asset('image', 'mod_languages/'.$items[$associated]->image.'.gif', $items[$associated]->language_title, array('title'=>$items[$associated]->language_title), true), $items[$associated]->title, $items[$associated]->menu_title);
			}
		}
		return Html::behavior('tooltip', implode('<br />', $text), Lang::txt('COM_MENUS_TIP_ASSOCIATION'), 'menu/icon-16-links.png');
	}

	/**
	 * Returns a published state on a grid
	 *
	 * @param   integer  $value     The state value.
	 * @param   integer  $i         The row index
	 * @param   boolean  $enabled   An optional setting for access control on the action.
	 * @param   string   $checkbox  An optional prefix for checkboxes.
	 * @return  string   The Html code
	 */
	public static function state($value, $i, $enabled = true, $checkbox = 'cb')
	{
		$states = array(
			7 => array(
				'unpublish',
				'',
				'COM_MENUS_HTML_UNPUBLISH_SEPARATOR',
				'',
				false,
				'publish',
				'publish'
			),
			6 => array(
				'publish',
				'',
				'COM_MENUS_HTML_PUBLISH_SEPARATOR',
				'',
				false,
				'unpublish',
				'unpublish'
			),
			5 => array(
				'unpublish',
				'',
				'COM_MENUS_HTML_UNPUBLISH_ALIAS',
				'',
				false,
				'publish',
				'publish'
			),
			4 => array(
				'publish',
				'',
				'COM_MENUS_HTML_PUBLISH_ALIAS',
				'',
				false,
				'unpublish',
				'unpublish'
			),
			3 => array(
				'unpublish',
				'',
				'COM_MENUS_HTML_UNPUBLISH_URL',
				'',
				false,
				'publish',
				'publish'
			),
			2 => array(
				'publish',
				'',
				'COM_MENUS_HTML_PUBLISH_URL',
				'',
				false,
				'unpublish',
				'unpublish'
			),
			1 => array(
				'unpublish',
				'COM_MENUS_EXTENSION_PUBLISHED_ENABLED',
				'COM_MENUS_HTML_UNPUBLISH_ENABLED',
				'COM_MENUS_EXTENSION_PUBLISHED_ENABLED',
				true,
				'publish',
				'publish'
			),
			0 => array(
				'publish',
				'COM_MENUS_EXTENSION_UNPUBLISHED_ENABLED',
				'COM_MENUS_HTML_PUBLISH_ENABLED',
				'COM_MENUS_EXTENSION_UNPUBLISHED_ENABLED',
				true,
				'unpublish',
				'unpublish'
			),
			-1 => array(
				'unpublish',
				'COM_MENUS_EXTENSION_PUBLISHED_DISABLED',
				'COM_MENUS_HTML_UNPUBLISH_DISABLED',
				'COM_MENUS_EXTENSION_PUBLISHED_DISABLED',
				true,
				'warning',
				'warning'
			),
			-2 => array(
				'publish',
				'COM_MENUS_EXTENSION_UNPUBLISHED_DISABLED',
				'COM_MENUS_HTML_PUBLISH_DISABLED',
				'COM_MENUS_EXTENSION_UNPUBLISHED_DISABLED',
				true,
				'unpublish',
				'unpublish'
			),
		);

		return Html::grid('state', $states, $value, $i, 'items.', $enabled, true, $checkbox);
	}
}
