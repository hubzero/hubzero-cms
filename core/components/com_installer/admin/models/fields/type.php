<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Components\Installer\Admin\Models\Extension;
use Hubzero\Form\Field;
use Html;
use Lang;

/**
 * Form Field Place class.
 */
class Type extends Field
{
	/**
	 * The field type.
	 *
	 * @var  string
	 */
	protected $type = 'Type';

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
			$options[] = Html::select('option', $option->attributes('value'), Lang::txt(trim((string) $option)));
		}

		$types = Extension::all()
			->select('DISTINCT type')
			->order('type', 'asc')
			->rows()
			->fieldsByKey('type');

		$types = array_unique($types);

		foreach ($types as $type)
		{
			$options[] = Html::select('option', $type, Lang::txt('COM_INSTALLER_TYPE_'. strtoupper($type)));
		}

		$return = Html::select('genericlist', $options, $this->name, $onchange, 'value', 'text', $this->value, $this->id);

		return $return;
	}
}
