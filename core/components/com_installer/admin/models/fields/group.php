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
class Group extends Field
{
	/**
	 * The field type.
	 *
	 * @var  string
	 */
	protected $type = 'Group';

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
			$options[] = Html::select('option', (string)$option->attributes()->value, Lang::txt(trim((string) $option)));
		}

		$folders = Extension::all()
			->select('DISTINCT folder')
			->where('folder', '!=', '')
			->order('folder', 'asc')
			->rows()
			->fieldsByKey('folder');

		$folders = array_unique($folders);

		foreach ($folders as $folder)
		{
			$options[] = Html::select('option', $folder, $folder);
		}

		$return = Html::select('genericlist', $options, $this->name, $onchange, 'value', 'text', $this->value, $this->id);

		return $return;
	}
}
