<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Facades\Lang;

class AETextarea extends Textarea
{
    protected $type = 'AETextarea';

    /**
     * Set up the field, translating its default if asked to
     *
     * @param   object       &$element  The field's XML element
     * @param   mixed        $value     The field's value
     * @param   string|null  $group     The field's group
     * @return  bool
     */
    public function setup(&$element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);

        if ($return && $element instanceof \SimpleXMLElement) {
            $td = ($element->attributes()->translate_default == 'true') ? true : false;
            $default = ($this->value == $element->attributes()->default) ? $element->attributes()->default : false;

            if ($td && $default) {
                $this->value = Lang::txt($default);
            }
        }
        return $return;
    }
}
