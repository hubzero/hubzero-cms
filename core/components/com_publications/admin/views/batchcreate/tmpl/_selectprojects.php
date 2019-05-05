<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$html  = '<select name="projectid" id="projectid">'."\n";
$html .= "\t".'<option value="0" selected="selected">' . Lang::txt('COM_PUBLICATIONS_SELECT_PROJECT') . '</option>';
foreach ($this->projects as $project)
{
	$html .= "\t".'<option value="' . $project->id . '"';
	$html .= '>' . \Hubzero\Utility\Str::truncate($project->title, 60) . ' (' . $project->alias . ')</option>'."\n";
}
$html .= '</select>'."\n";
echo $html;
