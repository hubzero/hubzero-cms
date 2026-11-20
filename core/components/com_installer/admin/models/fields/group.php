<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Components\Installer\Admin\Models\Extension;
use Hubzero\Form\Field;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;

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
        $element = $this->fieldElement();

        $onchange = $element['onchange'] ? ' onchange="' . (string) $element['onchange'] . '"' : '';
        $options = [];

        foreach ($element->children() as $option) {
            $optVal = (string) $option->attributes()->value;
            $optLabel = Lang::txt(trim((string) $option));
            $options[] = Html::select('option', $optVal, $optLabel);
        }

        $folders = Extension::all()
            ->select('DISTINCT folder')
            ->where('folder', '!=', '')
            ->order('folder', 'asc')
            ->rows()
            ->fieldsByKey('folder');

        $folders = array_unique($folders);

        foreach ($folders as $folder) {
            $options[] = Html::select('option', $folder, $folder);
        }

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
