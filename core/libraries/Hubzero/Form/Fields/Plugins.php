<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use App;

/**
 * Form Field class for listing plugins
 */
class Plugins extends Select
{
	/**
	 * The field type.
	 *
	 * @var  string
	 */
	protected $type = 'Plugins';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return  array  An array of Html options.
	 */
	protected function getOptions()
	{
		// Initialise variables
		$folder = $this->element['folder'];

		if (!empty($folder))
		{
			// Get list of plugins
			$db = App::get('db');
			$query = $db->getQuery()
				->select('element', 'value')
				->select('name', 'text')
				->from('#__extensions')
				->whereEquals('folder', $folder)
				->whereEquals('enabled', '1')
				->order('ordering', 'asc')
				->order('name', 'asc');
			$db->setQuery($query->toString());

			$options = $db->loadObjectList();

			$lang = App::get('language');
			foreach ($options as $i => $item)
			{
				$extension = 'plg_' . $folder . '_' . $item->value;
					$lang->load($extension . '.sys', PATH_APP . '/plugins/' . $folder . '/' . $item->value, null, false, true)
				||	$lang->load($extension . '.sys', PATH_CORE . '/plugins/' . $folder . '/' . $item->value, null, false, true);

				$options[$i]->text = $lang->txt($item->text);
			}

			if ($db->getErrorMsg())
			{
				return '';
			}
		}
		else
		{
			App::abort(500, App::get('language')->txt('JFRAMEWORK_FORM_FIELDS_PLUGINS_ERROR_FOLDER_EMPTY'));
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
