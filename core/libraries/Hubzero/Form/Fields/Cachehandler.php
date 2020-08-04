<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Html\Builder\Select as Dropdown;
use Hubzero\Cache\Manager;
use App;

/**
 * Provides a list of available cache handlers
 */
class Cachehandler extends Select
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	public $type = 'Cachehandler';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		// Convert to name => name array.
		foreach (Manager::getStores() as $store)
		{
			$options[] = Dropdown::option($store, App::get('language')->txt('JLIB_FORM_VALUE_CACHE_' . $store), 'value', 'text');
		}

		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
