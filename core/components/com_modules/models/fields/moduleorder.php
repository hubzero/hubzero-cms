<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;

/**
 * Form Field class for module ordering
 */
class ModuleOrder extends Field
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'ModuleOrder';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        $element = $this->fieldElement();

        // Initialize variables.
        $html = [];
        $attr = '';

        // Initialize some field attributes.
        $attr .= $element['class'] ? ' class="' . (string) $element['class'] . '"' : '';
        $attr .= ((string) $element['disabled'] == 'true') ? ' disabled="disabled"' : '';
        $attr .= $element['size'] ? ' size="' . (int) $element['size'] . '"' : '';

        // Initialize JavaScript field attributes.
        $attr .= $element['onchange'] ? ' onchange="' . (string) $element['onchange'] . '"' : '';

        $html[] = '<script type="application/json" id="moduleorder">';

        $ordering = $this->formValue('ordering');
        $position = $this->formValue('position');
        $clientId = $this->formValue('client_id');

        $data = new \stdClass();
        $data->originalOrder = $ordering;
        $data->originalPos = $position;
        $data->orders = [];
        $data->name = $this->name;
        $data->id = $this->id;
        $data->attr = $attr;

        $db = App::get('db');
        $query = $db
            ->getQuery()
            ->select('position')
            ->select('ordering')
            ->select('title')
            ->from('#__modules')
            ->whereEquals('client_id', (int) $clientId)
            ->order('ordering', 'asc');

        $db->setQuery($query->toString());
        $orders = $db->loadObjectList();

        $orders2 = [];
        for ($i = 0, $n = count($orders); $i < $n; $i++) {
            if (!isset($orders2[$orders[$i]->position])) {
                $orders2[$orders[$i]->position] = 0;
            }
            $orders2[$orders[$i]->position]++;
            $ord = $orders2[$orders[$i]->position];
            $title = Lang::txt('COM_MODULES_OPTION_ORDER_POSITION', $ord, addslashes($orders[$i]->title));

            $data->orders[$i] = [$orders[$i]->position, $ord, $title];
        }

        $html[] = json_encode($data);
        $html[] = '</script>';

        return implode("\n", $html);
    }
}
