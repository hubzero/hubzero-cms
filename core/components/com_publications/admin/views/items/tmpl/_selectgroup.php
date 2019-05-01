<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$html  = '<select name="group_owner"';
if (!$this->groups || $this->groupOwner) {
	$html .= ' disabled="disabled"';
}
$html .= '>'."\n";
$html .= ' <option value="">' . Lang::txt('Select group ...') . '</option>'."\n";
if ($this->groups)
{
	foreach ($this->groups as $group)
	{
		$html .= ' <option value="' . $group->gidNumber . '"';
		if ($this->value == $group->gidNumber)
		{
			$html .= ' selected="selected"';
		}
		$html .= '>' . \Hubzero\Utility\Str::truncate($group->description, 60) .'</option>'."\n";
	}
}
$html .= '</select>'."\n";

echo $html;
