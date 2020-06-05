<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Lang;

/**
 * Renders input for a widget
 */
class Widget extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Widget';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$html = array();
		$html[] = '<ul>';
		$html[] = '<li>';
		$html[] = '<label for="'.$this->id.'"> </label>';
		$html[] = '<fieldset class="radio">';
		$html[] = '<button id="'.$this->id.'">'.Lang::txt('PLG_USER_DOMAINRESTRICTION_ADD').'</button>';
		$html[] = '</fieldset>';
		$html[] = '</li>';
		$html[] = '</ul>';

		return implode("\n", $html);
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 */
	protected function getLabel()
	{
		return '';
	}
}
