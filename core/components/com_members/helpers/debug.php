<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Helpers;

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
		$query = $db->getQuery(true);

		$query->select('name AS text, element AS value')
			->from('#__extensions')
			->where('enabled >= 1')
			->where('type ='.$db->Quote('component'));

		$items = $db->setQuery($query)->loadObjectList();

		if (count($items))
		{
			$lang = Lang::getRoot();

			foreach ($items as &$item)
			{
				// Load language
				$extension = $item->value;
				$source    = PATH_CORE . '/components/' . $extension . '/admin';
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
			$component_actions = \JAccess::getActions($component);

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
			$filename = PATH_CORE . '/components/com_config/admin/models/forms/application.xml';

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
				$source    = PATH_CORE . '/components/' . $extension . '/admin';

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
