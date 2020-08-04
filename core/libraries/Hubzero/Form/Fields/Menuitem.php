<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\Html\Builder\Select as Dropdown;

/**
 * Supports an HTML grouped select list of menu item grouped by menu
 */
class Menuitem extends Groupedlist
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	public $type = 'Menuitem';

	/**
	 * Method to get the field option groups.
	 *
	 * @return  array  The field option objects as a nested array in groups.
	 */
	protected function getGroups()
	{
		// Initialize variables.
		$groups = array();

		// Initialize some field attributes.
		$menuType  = (string) $this->element['menu_type'];
		$published = $this->element['published'] ? explode(',', (string) $this->element['published']) : array();
		$disable   = $this->element['disable'] ? explode(',', (string) $this->element['disable']) : array();
		$language  = $this->element['language'] ? explode(',', (string) $this->element['language']) : array();

		// Get the menu items.
		$items = array();
		if (file_exists(PATH_CORE . '/components/com_menus/helpers/menus.php'))
		{
			// Import the com_menus helper.
			require_once PATH_CORE . '/components/com_menus/helpers/menus.php';

			$items = \Components\Menus\Helpers\Menus::getMenuLinks($menuType, 0, 0, $published, $language);
		}

		// Build group for a specific menu type.
		if ($menuType)
		{
			// Initialize the group.
			$groups[$menuType] = array();

			// Build the options array.
			foreach ($items as $link)
			{
				$groups[$menuType][] = Dropdown::option($link->value, $link->text, 'value', 'text', in_array($link->type, $disable));
			}
		}
		// Build groups for all menu types.
		else
		{
			// Build the groups arrays.
			foreach ($items as $menu)
			{
				// Initialize the group.
				$groups[$menu->menutype] = array();

				// Build the options array.
				foreach ($menu->links as $link)
				{
					$groups[$menu->menutype][] = Dropdown::option(
						$link->value, $link->text, 'value', 'text',
						in_array($link->type, $disable)
					);
				}
			}
		}

		// Merge any additional groups in the XML definition.
		$groups = array_merge(parent::getGroups(), $groups);

		return $groups;
	}
}
