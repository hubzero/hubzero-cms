<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$html  = '<select name="mastertypeid" id="mastertypeid">' . "\n";
$label = Lang::txt('COM_PUBLICATIONS_SELECT_MASTER_TYPE');
$html .= "\t" . '<option value="0" selected="selected">' . $label . '</option>';
foreach ($this->mastertypes as $mastertype) {
    $html .= "\t" . '<option value="' . $mastertype->id . '"';
    $text = \Hubzero\Utility\Str::truncate($mastertype->type, 60);
    $html .= '>' . $text . ' (' . $mastertype->alias . ')</option>' . "\n";
}
$html .= '</select>' . "\n";
echo $html;
