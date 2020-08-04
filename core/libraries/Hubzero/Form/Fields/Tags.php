<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Html\Builder\Behavior;
use App;

/**
 * Supports a URL text field
 */
class Tags extends Text
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Tags';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Initialize variables.
		$attributes = array(
			'type'         => 'text',
			'value'        => htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'),
			'name'         => $this->name,
			'id'           => $this->id,
			'size'         => ($this->element['size']      ? (int) $this->element['size']      : ''),
			'maxlength'    => ($this->element['maxlength'] ? (int) $this->element['maxlength'] : ''),
			'class'        => ($this->element['class']     ? (string) $this->element['class']  : ''),
			'readonly'     => ((string) $this->element['readonly'] == 'true'    ? 'readonly' : ''),
			'disabled'     => ((string) $this->element['disabled'] == 'true'    ? 'disabled' : '')
		);

		Behavior::framework(true);

		$results = App::get('dispatcher')->trigger(
			'hubzero.onGetMultiEntry',
			array(
				array('tags', $this->name, $this->id, $attributes['class'], $this->value, null, null, 'multi', ($attributes['disabled'] ? true : null))
			)
		);

		if (count($results) > 0)
		{
			$results = implode("\n", $results);
		}
		else
		{
			$results = self::getInput();
		}

		return $results;
	}
}
