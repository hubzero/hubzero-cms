<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Check to see if the timezone is set by the event.
// $this->row->time_zone is the event timezone
if (!isset($this->row->time_zone) || $this->row->time_zone == '')
{
	// Get the timezone preferred by the USER, if not use HUB's
	$timezone = \Config::get('offset');
	// Handle daylight savings time
	if (date('I', strtotime($this->row->publish_up)))
	{
		// Add 1 hour
		$publish_up = strtotime($this->row->publish_up . '+ 1 hour');
		//$this->row->publish_up = $publish_up;
	}
	// Handle daylight savings time
	if (date('I', strtotime($this->row->publish_down)))
	{
		// Add 1 hour
		$publish_down = strtotime($this->row->publish_down. '+ 1 hour');
		//$this->row->publish_down = $publish_down;
	}
}
else
{
	// else use the one provided by the event
	$timezone = timezone_name_from_abbr('', $this->row->time_zone*3600, null);
}

// If not timezone is found or cannot be ascertained from above
if ($timezone === false)
{
	$timezone = null;

	$offset = $this->row->time_zone*3600; // convert hour offset to seconds
	$abbrarray = timezone_abbreviations_list();
	foreach ($abbrarray as $abbr)
	{
		// match a city to the offset
		foreach ($abbr as $city)
		{
			if ($city['offset'] == $offset)
			{
				$timezone = $city['timezone_id'];
			}
		}
	}
}


$this->row->content = stripslashes($this->row->content);
$this->row->content = str_replace('<br />', '', $this->row->content);

//$fields = $config->getCfg('fields');
if (!empty($this->fields)) {
	for ($i=0, $n=count( $this->fields ); $i < $n; $i++)
	{
		// explore the text and pull out all matches
		array_push($this->fields[$i], \Components\Events\Site\Controllers\Events::parseTag($this->row->content, $this->fields[$i][0]));
		// clean the original text of any matches
		$this->row->content = str_replace('<ef:'.$this->fields[$i][0].'>'.end($this->fields[$i]).'</ef:'.$this->fields[$i][0].'>', '', $this->row->content);
	}
	$this->row->content = trim($this->row->content);
}

$start_date = Date::of($this->row->publish_up, $timezone)->toLocal();
$stop_date  = Date::of($this->row->publish_down, $timezone)->toLocal();
$current_date = Date::of()->toLocal();

//determine if the event is in the past or not
if (strtotime($stop_date) - strtotime($current_date) < 0)
{
	$html  = "\t".'<li id="event'.$this->row->id.'" class="prior">'."\n";
}
else
{
	$html  = "\t".'<li id="event'.$this->row->id.'">'."\n";
}

$html .= "\t\t".'<dl class="event-details">'."\n";
if ($start_date == $stop_date) {
	if ($this->showdate) {
		$html .= "\t\t\t".'<dt>'.Date::of($this->row->publish_up, $timezone)->toLocal('d M Y').'</dt>'."\n";
	}
	$html .= "\t\t\t".'<dd class="starttime">'.Date::of($this->row->publish_up, $timezone)->toLocal('g:i a T').'&nbsp;' .'</dd>'."\n";
	$html .= "\t\t\t".'<dd class="endtime">'.strtolower(Lang::txt('EVENTS_CAL_LANG_TO')).' '.Date::of($this->row->publish_down, $timezone)->toLocal('g:i a T').'&nbsp;'.'</dd>'."\n";
} else {
	if ($this->showdate) {
		$html .= "\t\t\t".'<dt class="starttime">'.Date::of($this->row->publish_up, $timezone)->toLocal(Lang::txt('DATE_FORMAT_HZ1')).'</dt>'."\n";
	}
	$html .= "\t\t\t".'<dd class="starttime">'.Date::of($this->row->publish_up, $timezone)->toLocal('g:i a T').'&nbsp;'.'</dd>'."\n";
	if ($this->showdate) {
		$html .= "\t\t\t".'<dt class="endtime">'.strtolower(Lang::txt('EVENTS_CAL_LANG_TO')).' '.Date::of($this->row->publish_down, $timezone)->toLocal(Lang::txt('DATE_FORMAT_HZ1')).'</dt>'."\n";
	}
	$html .= "\t\t\t".'<dd class="endtime">'.Date::of($this->row->publish_down, $timezone)->toLocal('g:i a T').'&nbsp;'.'</dd>'."\n";
}
$html .= "\t\t".'</dl><div class="ewrap">'."\n";
$html .= "\t\t".'<p class="title"><a href="'. Route::url('index.php?option='.$this->option.'&task=details&id='.$this->row->id) .'">'. $this->escape(stripslashes($this->row->title)) .'</a></p>'."\n";
if (isset($this->categories[$this->row->catid]))
{
	$html .= "\t\t".'<p class="category"><strong>Category:</strong> '. stripslashes($this->categories[$this->row->catid]) .'</p>'."\n";
}
$info = '';
foreach ($this->fields as $field)
{
	if ($field[4] == 1 && end($field) != '') {
		$info .= "\t\t".'<p class="'.$field[0].'"><strong>'. $field[1] .':</strong> '. end($field) .'</p>'."\n";
	}
}
$html .= $info;
if (!$info) {
	$html .= "\t\t".'<p class="description">'. \Hubzero\Utility\Str::truncate(strip_tags($this->row->content), 300) .'</p>'."\n";
}
$html .= "\t".'</div></li>'."\n";

echo $html;
