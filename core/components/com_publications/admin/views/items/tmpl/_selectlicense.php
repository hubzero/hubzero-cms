<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$value = $this->selected ? $this->selected->id : 1;
$html  = '<select name="license_type" id="license_type">' . "\n";

foreach ($this->licenses as $license)
{
	$html .= "\t".'<option value="' . $license->id . '"';
	if ($value == $license->id)
	{
		$html .= ' selected="selected"';
	}
	$html .= '>' . trim($license->name) . '</option>' . "\n";
}
$html .= '</select>' . "\n";

echo $html;
