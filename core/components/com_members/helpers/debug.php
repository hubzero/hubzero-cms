<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Helpers;

use Component;
use Html;
use Lang;
use App;

/**
 * Users component debugging helper.
 */
class Debug
{
	/**
	 * Get a list of the components.
	 *
	 * @return  array
	 */
	public static function getComponents()
	{
		// Initialise variable.
		$db    = App::get('db');
		$query = $db->getQuery()
			->select('name', 'text')
			->select('element', 'value')
			->from('#__extensions')
			->where('enabled', '>=', '1')
			->whereEquals('type', 'component');

		$items = $db->setQuery($query->toString())->loadObjectList();

		if (count($items))
		{
			$lang = Lang::getRoot();

			foreach ($items as &$item)
			{
				// Load language
				$extension = $item->value;
				$source    = Component::path($extension) . '/admin';
				$lang->load("$extension.sys", PATH_APP, null, false, true) ||
				$lang->load("$extension.sys", $source, null, false, true);

				// Translate component name
				$item->text = Lang::txt($item->text);
			}

			// Sort by component name
			\Hubzero\Utility\Arr::sortObjects($items, 'text', 1, true, $lang->getLocale());
		}

		return $items;
	}

	/**
	 * Get a list of the actions for the component or code actions.
	 *
	 * @param   string  $component  The name of the component.
	 * @return  array
	 */
	public static function getActions($component = null)
	{
		$actions = array();

		// Try to get actions for the component
		if (!empty($component))
		{
			$path = Component::path($component) . '/config/access.xml';

			$component_actions = \Hubzero\Access\Access::getActionsFromFile($path);
			$component_actions ?: array();

			if (!empty($component_actions))
			{
				foreach ($component_actions as &$action)
				{
					$actions[$action->title] = array($action->name, $action->description);
				}
			}
		}

		// Use default actions from configuration if no component selected or component doesn't have actions
		if (empty($actions))
		{
			$filename = Component::path('com_config') . '/admin/models/forms/application.xml';

			if (is_file($filename))
			{
				$xml = simplexml_load_file($filename);

				foreach ($xml->children()->fieldset as $fieldset)
				{
					if ('permissions' == (string) $fieldset['name'])
					{
						foreach ($fieldset->children() as $field)
						{
							if ('rules' == (string) $field['name'])
							{
								foreach ($field->children() as $action)
								{
									$actions[(string) $action['title']] = array(
										(string) $action['name'],
										(string) $action['description']
									);
								}
								break;
								break;
								break;
							}
						}
					}
				}

				// Load language
				$lang      = Lang::getRoot();
				$extension = 'com_config';
				$source    = Component::path($extension) . '/admin';

				$lang->load("$extension.sys", PATH_APP, null, false, true) ||
				$lang->load("$extension.sys", $source, null, false, true);
			}
		}

		return $actions;
	}

	/**
	 * Get a list of filter options for the levels.
	 *
	 * @return  array  An array of Option elements.
	 */
	static function getLevelsOptions()
	{
		// Build the filter options.
		$options = array();
		$options[] = Html::select('option', '1', Lang::txt('COM_MEMBERS_OPTION_LEVEL_COMPONENT', 1));
		$options[] = Html::select('option', '2', Lang::txt('COM_MEMBERS_OPTION_LEVEL_CATEGORY', 2));
		$options[] = Html::select('option', '3', Lang::txt('COM_MEMBERS_OPTION_LEVEL_DEEPER', 3));
		$options[] = Html::select('option', '4', '4');
		$options[] = Html::select('option', '5', '5');
		$options[] = Html::select('option', '6', '6');

		return $options;
	}
}
