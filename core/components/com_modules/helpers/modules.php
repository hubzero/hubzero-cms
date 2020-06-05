<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Modules\Helpers;

use Hubzero\Base\Obj;
use Hubzero\Access\Access;
use Html;
use Lang;
use User;
use App;

/**
 * Modules component helper.
 */
abstract class Modules
{
	/**
	 * Extension name
	 *
	 * @var  string
	 */
	public static $extension = 'com_modules';

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  object
	 */
	public static function getActions()
	{
		$result    = new Obj;
		$assetName = self::$extension;

		$actions = Access::getActionsFromFile(\Component::path($assetName) . '/config/access.xml');

		foreach ($actions as $action)
		{
			$result->set($action->name, User::authorise($action->name, 'com_modules'));
		}

		return $result;
	}

	/**
	 * Get a list of filter options for the state of a module.
	 *
	 * @return  array  An array of option elements.
	 */
	public static function getStateOptions()
	{
		// Build the filter options.
		$options = array();
		$options[] = Html::select('option', '1', Lang::txt('JPUBLISHED'));
		$options[] = Html::select('option', '0', Lang::txt('JUNPUBLISHED'));
		$options[] = Html::select('option', '-2', Lang::txt('JTRASHED'));

		return $options;
	}

	/**
	 * Get a list of filter options for the application clients.
	 *
	 * @return  array  An array of option elements.
	 */
	public static function getClientOptions()
	{
		// Build the filter options.
		$options = array();
		$options[] = Html::select('option', '0', Lang::txt('JSITE'));
		$options[] = Html::select('option', '1', Lang::txt('JADMINISTRATOR'));

		return $options;
	}

	/**
	 * Get a list of postions
	 *
	 * @param   integer  $clientId
	 * @return  array    An array of option elements.
	 */
	public static function getPositions($clientId)
	{
		$db    = App::get('db');
		$query = $db->getQuery()
			->select('DISTINCT(position)')
			->from('#__modules')
			->whereEquals('client_id', (int) $clientId)
			->order('position', 'asc');

		$db->setQuery($query->toString());
		$positions = $db->loadColumn();
		$positions = (is_array($positions)) ? $positions : array();

		if ($error = $db->getErrorMsg())
		{
			App::abort(500, $error);
		}

		/*$positions = Module::all()
			->select('DISTINCT(position)')
			->whereEquals('client_id', (int) $clientId)
			->order('position', 'asc')
			->rows();*/

		// Build the list
		$options = array();
		foreach ($positions as $position)
		{
			if (!$position)
			{
				$options[] = Html::select('option', 'none', ':: ' . Lang::txt('JNONE') . ' ::');
			}
			else
			{
				$options[] = Html::select('option', $position, $position);
			}
		}
		return $options;
	}

	/**
	 * Get a list of templates
	 *
	 * @param   integer  $clientId
	 * @param   integer  $state
	 * @param   string   $template
	 * @return  array
	 */
	public static function getTemplates($clientId = 0, $state = '', $template='')
	{
		$db = App::get('db');
		// Get the database object and a new query object.
		$query = $db->getQuery()
			->select('element')
			->select('name')
			->select('enabled')
			->from('#__extensions')
			->whereEquals('client_id', (int) $clientId)
			->whereEquals('type', 'template');

		if ($state != '')
		{
			$query->whereEquals('enabled', $state);
		}
		if ($template != '')
		{
			$query->whereEquals('element', $template);
		}

		// Set the query and load the templates.
		$db->setQuery($query->toString());
		return $db->loadObjectList('element');
	}

	/**
	 * Get a list of the unique modules installed in the client application.
	 *
	 * @param   integer  $clientId  The client id.
	 * @return  array
	 */
	public static function getModules($clientId)
	{
		$db    = App::get('db');
		$query = $db->getQuery()
			->select('element', 'value')
			->select('name', 'text')
			->select('protected')
			->from('#__extensions', 'e')
			->whereEquals('e.client_id', (int)$clientId)
			->whereEquals('type', 'module')
			->joinRaw('#__modules as m', 'm.module=e.element AND m.client_id=e.client_id', 'left')
			->whereRaw('m.module IS NOT NULL')
			->group('element')
			->group('name')
			->group('protected');

		$db->setQuery($query->toString());
		$modules = $db->loadObjectList();

		$lang = Lang::getRoot();

		foreach ($modules as $i => $module)
		{
			$extension = $module->value;
			$path = $module->protected ? PATH_CORE : PATH_APP;
			$source = $path . "/modules/$extension";
				$lang->load("$extension.sys", $path, null, false, true)
			||	$lang->load("$extension.sys", $source, null, false, true);
			$modules[$i]->text = Lang::txt($module->text);
		}

		\Hubzero\Utility\Arr::sortObjects($modules, 'text', 1, true, $lang->getLocale());

		return $modules;
	}

	/**
	 * Get a list of the assignment options for modules to menus.
	 *
	 * @param   integer  $clientId  The client id.
	 * @return  array
	 */
	public static function getAssignmentOptions($clientId)
	{
		$options = array();
		$options[] = Html::select('option', '0', 'COM_MODULES_OPTION_MENU_ALL');
		$options[] = Html::select('option', '-', 'COM_MODULES_OPTION_MENU_NONE');

		if ($clientId == 0)
		{
			$options[] = Html::select('option', '1', 'COM_MODULES_OPTION_MENU_INCLUDE');
			$options[] = Html::select('option', '-1', 'COM_MODULES_OPTION_MENU_EXCLUDE');
		}

		return $options;
	}

	/**
	 * Get a list of tmeplates
	 *
	 * @param   integer  $clientId  The client id
	 * @param   string   $state     The state of the template
	 * @return  array
	 */
	public static function templates($clientId = 0, $state = '')
	{
		$templates = self::getTemplates($clientId, $state);
		foreach ($templates as $template)
		{
			$options[] = Html::select('option', $template->element, $template->name);
		}
		return $options;
	}

	/**
	 * Get a list of template types
	 *
	 * @return  array
	 */
	public static function types()
	{
		$options = array();
		$options[] = Html::select('option', 'user', 'COM_MODULES_OPTION_POSITION_USER_DEFINED');
		$options[] = Html::select('option', 'template', 'COM_MODULES_OPTION_POSITION_TEMPLATE_DEFINED');
		return $options;
	}

	/**
	 * Get a list of template states
	 *
	 * @return  array
	 */
	public static function templateStates()
	{
		$options = array();
		$options[] = Html::select('option', '1', 'JENABLED');
		$options[] = Html::select('option', '0', 'JDISABLED');
		return $options;
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
		$states	= array(
			1 => array(
				'unpublish',
				'COM_MODULES_EXTENSION_PUBLISHED_ENABLED',
				'COM_MODULES_HTML_UNPUBLISH_ENABLED',
				'COM_MODULES_EXTENSION_PUBLISHED_ENABLED',
				true,
				'publish',
				'publish'
			),
			0 => array(
				'publish',
				'COM_MODULES_EXTENSION_UNPUBLISHED_ENABLED',
				'COM_MODULES_HTML_PUBLISH_ENABLED',
				'COM_MODULES_EXTENSION_UNPUBLISHED_ENABLED',
				true,
				'unpublish',
				'unpublish'
			),
			-1 => array(
				'unpublish',
				'COM_MODULES_EXTENSION_PUBLISHED_DISABLED',
				'COM_MODULES_HTML_UNPUBLISH_DISABLED',
				'COM_MODULES_EXTENSION_PUBLISHED_DISABLED',
				true,
				'warning',
				'warning'
			),
			-2 => array(
				'publish',
				'COM_MODULES_EXTENSION_UNPUBLISHED_DISABLED',
				'COM_MODULES_HTML_PUBLISH_DISABLED',
				'COM_MODULES_EXTENSION_UNPUBLISHED_DISABLED',
				true,
				'unpublish',
				'unpublish'
			),
		);

		return Html::grid('state', $states, $value, $i, '', $enabled, true, $checkbox);
	}

	/**
	 * Display a batch widget for the module position selector.
	 *
	 * @param   integer  $clientId  The client ID
	 * @return  string   The necessary HTML for the widget.
	 */
	public static function positions($clientId)
	{
		// Create the copy/move options.
		$options = array(
			Html::select('option', 'c', Lang::txt('JLIB_HTML_BATCH_COPY')),
			Html::select('option', 'm', Lang::txt('JLIB_HTML_BATCH_MOVE'))
		);

		// Create the batch selector to change select the category by which to move or copy.
		$lines = array(
			'<label id="batch-choose-action-lbl" for="batch-choose-action">',
				Lang::txt('COM_MODULES_BATCH_POSITION_LABEL'),
			'</label>',
			'<fieldset id="batch-choose-action" class="combo">',
				'<select name="batch[position_id]" class="inputbox" id="batch-position-id">',
					'<option value="">' . Lang::txt('JSELECT') . '</option>',
					'<option value="nochange">' . Lang::txt('COM_MODULES_BATCH_POSITION_NOCHANGE') . '</option>',
					'<option value="noposition">' . Lang::txt('COM_MODULES_BATCH_POSITION_NOPOSITION') . '</option>',
					Html::select('options',	self::positionList($clientId)),
				'</select>',
				Html::select('radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'),
			'</fieldset>'
		);

		return implode("\n", $lines);
	}

	/**
	 * Method to get the field options.
	 *
	 * @param   integer  $clientId  The client ID
	 * @return  array    The field option objects.
	 */
	public static function positionList($clientId = 0)
	{
		$db = App::get('db');
		$query = $db->getQuery()
			->select('DISTINCT(position)', 'value')
			->select('position', 'text')
			->from('#__modules')
			->whereEquals('client_id', (int) $clientId)
			->order('position', 'asc');

		// Get the options.
		$db->setQuery($query->toString());

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			App::abort(500, $db->getErrorMsg());
		}

		// Pop the first item off the array if it's blank
		if (strlen($options[0]->text) < 1)
		{
			array_shift($options);
		}

		return $options;
	}
}
