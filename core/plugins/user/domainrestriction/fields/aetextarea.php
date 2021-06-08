<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;

class AETextarea extends Textarea
{
	protected $type = 'AETextarea';

	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return) {
			$td = ($element->attributes()->translate_default == 'true')?true:false;
			$default = ($this->value == $element->attributes()->default)?$element->attributes()->default:false;

			if ($td && $default)
			{
				$this->value = Lang::txt($default);
			}
		}
		return $return;
	}
}
