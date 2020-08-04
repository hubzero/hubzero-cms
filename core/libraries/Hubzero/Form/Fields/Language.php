<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use App;

/**
 * Supports a list of installed application languages
 */
class Language extends Select
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Language';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Initialize some field attributes.
		$client = (string) $this->element['client'];
		if ($client != 'site' && $client != 'administrator')
		{
			$client = 'site';
		}

		$client_id = 0;

		if ($client == 'administrator')
		{
			$client_id = 1;
		}

		$path = PATH_APP . DS . 'bootstrap' . DS . $client;
		if (!is_dir($path))
		{
			$path = PATH_CORE . DS . 'bootstrap' . DS . $client;
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(
			parent::getOptions(),
			App::get('language')->getList($this->value, $path, true, true, $client_id)
		);

		return $options;
	}
}
