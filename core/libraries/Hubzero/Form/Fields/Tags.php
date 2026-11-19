<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Html\Builder\Behavior;
use Hubzero\Facades\App;

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
        $element = $this->fieldElement();

        // Initialize variables.
        $attributes = [
            'type'         => 'text',
            'value'        => htmlspecialchars($this->value == null ? '' : $this->value, ENT_COMPAT, 'UTF-8'),
            'name'         => $this->name,
            'id'           => $this->id,
            'size'         => ($element['size'] ? (int) $element['size'] : ''),
            'maxlength'    => ($element['maxlength'] ? (int) $element['maxlength'] : ''),
            'class'        => ($element['class'] ? (string) $element['class'] : ''),
            'readonly'     => ((string) $element['readonly'] == 'true' ? 'readonly' : ''),
            'disabled'     => ((string) $element['disabled'] == 'true' ? 'disabled' : ''),
        ];

        Behavior::framework(true);

        $results = App::get('dispatcher')->trigger(
            'hubzero.onGetMultiEntry',
            [
                [
                    'tags',
                    $this->name,
                    $this->id,
                    $attributes['class'],
                    $this->value,
                    null,
                    null,
                    'multi',
                    ($attributes['disabled'] ? true : null),
                ],
            ],
        );

        if (count($results) > 0) {
            $results = implode("\n", $results);
        } else {
            $results = self::getInput();
        }

        return $results;
    }
}
