<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$html  = '<select name="mastertypeid" id="mastertypeid">'."\n";
$html .= "\t".'<option value="0" selected="selected">' . Lang::txt('COM_PUBLICATIONS_SELECT_MASTER_TYPE') . '</option>';
foreach ($this->mastertypes as $mastertype)
{
	$html .= "\t".'<option value="' . $mastertype->id . '"';
	$html .= '>' . \Hubzero\Utility\Str::truncate($mastertype->type, 60) . ' (' . $mastertype->alias . ')</option>'."\n";
}
$html .= '</select>'."\n";
echo $html;
