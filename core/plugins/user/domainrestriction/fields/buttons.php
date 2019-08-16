<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Lang;

class Buttons extends Field
{
	protected $type = 'Buttons';

	protected function getInput()
	{
		$html   = array();
		$html[] = '<ul>';
		$html[] = '<li><label> </label>';
		$html[] = '<fieldset class="radio">';
		$html[] = '<button id="'.$this->id.'">'.Lang::txt('PLG_USER_DOMAINRESTRICTION_EDIT').'</button>';
		$html[] = '<button id="'.$this->id.'">'.Lang::txt('PLG_USER_DOMAINRESTRICTION_REMOVE').'</button>';
		$html[] = '</fieldset>';
		$html[] = '</li></ul>';

		return implode("\n", $html);
	}

	protected function getLabel()
	{
		return '';
	}
}
