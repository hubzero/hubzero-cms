<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;

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
        $element = $this->fieldElement();

        $onchange = $element['onchange'] ? ' onchange="' . (string) $element['onchange'] . '"' : '';
        $options = [];
        foreach ($element->children() as $option) {
            $options[] = Html::select('option', (string) $option['value'], Lang::txt(trim((string) $option)));
        }
        $options[] = Html::select('option', '0', Lang::txt('JSITE'));
        $options[] = Html::select('option', '1', Lang::txt('JADMINISTRATOR'));
        return Html::select(
            'genericlist',
            $options,
            $this->name,
            $onchange,
            'value',
            'text',
            $this->value,
            $this->id,
        );
    }
}
