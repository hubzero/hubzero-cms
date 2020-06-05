<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Html;
use Lang;

/**
 * Form Field Place class.
 */
class Client extends Field
{
	/**
	 * The field type.
	 *
	 * @var  string
	 */
	protected $type = 'Client';

	/**
	 * Method to get the field input.
	 *
	 * @return  string  The field input.
	 */
	protected function getInput()
	{
		$onchange = $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		$options = array();
		foreach ($this->element->children() as $option)
		{
			$options[] = Html::select('option', $option->attributes('value'), Lang::txt(trim($option->data())));
		}
		$options[] = Html::select('option', '0', Lang::txt('JSITE'));
		$options[] = Html::select('option', '1', Lang::txt('JADMINISTRATOR'));
		$return = Html::select('genericlist', $options, $this->name, $onchange, 'value', 'text', $this->value, $this->id);
		return $return;
	}
}
