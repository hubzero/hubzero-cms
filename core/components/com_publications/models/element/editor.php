<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Element;

use Components\Publications\Models\Element as Base;

/**
 * Renders an editor element
 */
class Editor extends Base
{
    /**
  * Element name
  *
  * @var  string
  */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_name = 'Editor';

    /**
     * Return any options this element may have
     *
     * @param   string  $name          Name of the field
     * @param   string  $value         Value to check against
     * @param   object  $element       Data Source Object.
     * @param   string  $control_name  Control name (eg, control[fieldname])
     * @return  string  HTML
     */
    public function fetchElement($name, $value, &$element, $control_name)
    {
        $rows = isset($element->rows) ? $element->rows : 6;
        $cols = isset($element->cols) ? $element->cols : 50;
        $editorMacros  = isset($element->editorMacros)
                        ? $element->editorMacros : 0;
        $editorMinimal = isset($element->editorMinimal)
                        ? $element->editorMinimal : 1;
        $editorImages  = isset($element->editorImages)
                        ? $element->editorImages : 0;

        $classes  = $editorMinimal == 1 ? 'minimal ' : '';
        $classes .= ' no-footer ';
        $classes .= $editorImages == 1 ? 'images ' : '';
        $classes .= $editorMacros == 1 ? 'macros ' : '';

        $fieldName = $control_name . '[' . $name . ']';
        $fieldId = $control_name . '-' . $name;
        $editorHtml = \App::get('editor')->display(
            $fieldName,
            $value,
            '',
            '',
            $cols,
            $rows,
            false,
            $fieldId,
            null,
            null,
            array('class' => $classes)
        );
        return '<span class="field-wrap">' . $editorHtml . '</span>';
    }
}
