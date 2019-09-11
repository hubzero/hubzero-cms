<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Lang;

/**
 * Renders input for a Blacklist notification
 */
class Blacklist extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Blacklist';

	/**
	 * App
	 *
	 * @var  object
	 */
	protected $app;

	/**
	 * Database
	 *
	 * @var  object
	 */
	protected $db;

	/**
	 * Form fields
	 *
	 * @var  array
	 */
	protected $formfields;

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 */
	protected function getLabel()
	{
		return '';
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$return = '<p class="info">'.Lang::txt('PLG_USER_DOMAINRESTRICTION_IPV4').'</p>';

		if (function_exists('gmp_pow'))
		{
			$return = '<p class="info">'.Lang::txt('PLG_USER_DOMAINRESTRICTION_IPV46').'</p>';
		}

		return $return;
	}

	/**
	 * Get the value from a field
	 *
	 * @param   string  $name
	 * @return  string
	 */
	private function _getField($name)
	{
		foreach ($this->formfields as $field)
		{
			if ($field->name == 'fields[params]['.$name.']'
			 || $field->name == 'fields[params]['.$name.'][]')
			{
				return $field->value;
			}
		}
	}
}
