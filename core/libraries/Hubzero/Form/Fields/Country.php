<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Geocode\Geocode;
use Hubzero\Html\Builder\Select as Dropdown;
use App;

/**
 * Supports a list of country options.
 */
class Country extends Select
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Country';

	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected static $countries = null;

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		if (!self::$countries)
		{
			self::$countries = array();

			$countries = Geocode::countries();

			if ($countries && !empty($countries))
			{
				self::$countries = $countries;
			}
		}

		if ($this->element['option_blank'])
		{
			$options[] = Dropdown::option('', App::get('language')->txt('- Select -'), 'value', 'text');
		}

		foreach (self::$countries as $option)
		{
			// Create a new option object based on the <option /> element.
			$tmp = Dropdown::option(
				(string) $option->code,
				App::get('language')->alt(trim((string) $option->name), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text'
			);

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
