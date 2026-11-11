<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{
 *     attributes: mixed,
 *     categories: mixed,
 *     name: mixed,
 *     showNone: mixed,
 *     value: mixed,
 * } $this
 */

$html  = '<select name="' . $this->name . '" id="' . $this->name . '"'
    . (isset($this->attributes) && $this->attributes ? ' ' . $this->attributes : '') . '>' . "\n";

if ($this->showNone != '') {
    $html .= "\t" . '<option value=""';
    $html .= ($this->value == 0 || $this->value == '') ? ' selected="selected"' : '';
    $html .= '>' . $this->showNone . '</option>' . "\n";
}

$skips = [];

foreach ($this->categories as $anode) {
    if (!in_array($anode->id, $skips)) {
        $selected = ($this->value && ($anode->id == $this->value || $anode->name == $this->value))
              ? ' selected="selected"'
              : '';
        $html .= "\t" . '<option value="' . $anode->id . '"' . $selected . '>' . $anode->name . '</option>' . "\n";
    }
}
$html .= '</select>' . "\n";

echo $html;
