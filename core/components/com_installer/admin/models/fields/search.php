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
 * Form Field Search class.
 */
class Search extends Field
{
	/**
	 * The field type.
	 *
	 * @var  string
	 */
	protected $type = 'Search';

	/**
	 * Method to get the field input.
	 *
	 * @return  string  The field input.
	 */
	protected function getInput()
	{
		$html  = '';
		$html .= '<input type="text" name="' . $this->name . '" id="' . $this->id . '" class="filter" value="' . htmlspecialchars($this->value) . '" title="' . Lang::txt('JSEARCH_FILTER') . '" />';
		$html .= '<button type="submit" class="btn">' . Lang::txt('JSEARCH_FILTER_SUBMIT') . '</button>';
		$html .= '<button type="button" class="btn filter-clear">' . Lang::txt('JSEARCH_FILTER_CLEAR') . '</button>';
		return $html;
	}
}
