<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$html  = '<select name="' . $this->name . '" id="' . $this->name . '"' . (isset($this->attributes) && $this->attributes ? ' ' . $this->attributes : '') . '>' . "\n";

if ($this->showNone != '')
{
	$html .= "\t".'<option value=""';
	$html .= ($this->value == 0 || $this->value == '') ? ' selected="selected"' : '';
	$html .= '>' . $this->showNone . '</option>'."\n";
}

$skips = array();

foreach ($this->categories as $anode)
{
	if (!in_array($anode->id, $skips))
	{
		$selected = ($this->value && ($anode->id == $this->value || $anode->name == $this->value))
			  ? ' selected="selected"'
			  : '';
		$html .= "\t".'<option value="' . $anode->id . '"' . $selected.'>' . $anode->name . '</option>' . "\n";
	}
}
$html .= '</select>'."\n";

echo $html;
