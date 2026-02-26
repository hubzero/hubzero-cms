<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

// Check to see if the timezone is set by the event.
// $this->row->time_zone is the event timezone
if (!isset($this->row->time_zone) || $this->row->time_zone == '') {
    // Get the timezone preferred by the USER, if not use HUB's
    $timezone = \Hubzero\Facades\Config::get('offset');
    // Handle daylight savings time
    if (date('I', strtotime($this->row->publish_up))) {
        // Add 1 hour
        $publish_up = strtotime($this->row->publish_up . '+ 1 hour');
        //$this->row->publish_up = $publish_up;
    }
    // Handle daylight savings time
    if (date('I', strtotime($this->row->publish_down))) {
        // Add 1 hour
        $publish_down = strtotime($this->row->publish_down . '+ 1 hour');
        //$this->row->publish_down = $publish_down;
    }
} else {
    // else use the one provided by the event
    $timezone = timezone_name_from_abbr(
        '',
        intval($this->row->time_zone) * 3600,
        -1
    );
}

// If not timezone is found or cannot be ascertained from above
if ($timezone === false) {
    $timezone = null;

    $offset = intval($this->row->time_zone) * 3600; // convert hour offset to seconds
    $abbrarray = timezone_abbreviations_list();
    foreach ($abbrarray as $abbr) {
        // match a city to the offset
        foreach ($abbr as $city) {
            if ($city['offset'] == $offset) {
                $timezone = $city['timezone_id'];
            }
        }
    }
}

$this->row->content = stripslashes($this->row->content);
$this->row->content = str_replace('<br />', '', $this->row->content);

//$fields = $config->getCfg('fields');
if (!empty($this->fields)) {
    for ($i = 0, $n = count($this->fields); $i < $n; $i++) {
        // explore the text and pull out all matches
        array_push(
            $this->fields[$i],
            \Components\Events\Site\Controllers\Events::parseTag(
                $this->row->content,
                $this->fields[$i][0]
            )
        );
        // clean the original text of any matches
        $this->row->content = str_replace(
            '<ef:' . $this->fields[$i][0] . '>' . end($this->fields[$i]) . '</ef:' . $this->fields[$i][0] . '>',
            '',
            $this->row->content
        );
    }
    $this->row->content = trim($this->row->content);
}

$start_date = Date::of($this->row->publish_up, $timezone)->toLocal();
$stop_date  = Date::of($this->row->publish_down, $timezone)->toLocal();
$current_date = Date::of()->toLocal();

//determine if the event is in the past or not
if (strtotime($stop_date) - strtotime($current_date) < 0) {
    $html  = "\t" . '<li id="event' . $this->row->id . '" class="prior">' . "\n";
} else {
    $html  = "\t" . '<li id="event' . $this->row->id . '">' . "\n";
}

$html .= "\t\t" . '<dl class="event-details">' . "\n";

$dateFormat = Lang::txt('DATE_FORMAT_HZ1');
$upDate = Date::of($this->row->publish_up, $timezone);
$downDate = Date::of($this->row->publish_down, $timezone);
$startFmt = $upDate->format('g:i A T', true);
$endFmt = $downDate->format('g:i A T', true);
$toLower = strtolower(Lang::txt('EVENTS_CAL_LANG_TO'));

if ($start_date == $stop_date) {
    if ($this->showdate) {
        $html .= "\t\t\t" . '<dt>' . $upDate->toLocal($dateFormat) . '</dt>' . "\n";
    }
    $html .= "\t\t\t" . '<dd class="starttime">' . $startFmt . '&nbsp;' . '</dd>' . "\n";
    $html .= "\t\t\t" . '<dd class="endtime">' . $toLower . ' ' . $endFmt . '&nbsp;' . '</dd>' . "\n";
} else {
    if ($this->showdate) {
        $html .= "\t\t\t" . '<dt class="starttime">' . $upDate->toLocal($dateFormat) . '</dt>' . "\n";
    }
    $html .= "\t\t\t" . '<dd class="starttime">' . $startFmt . '&nbsp;' . '</dd>' . "\n";
    if ($this->showdate) {
        $html .= "\t\t\t" . '<dt class="endtime">' . $toLower . ' ' . $downDate->toLocal($dateFormat) . '</dt>' . "\n";
    }
    $html .= "\t\t\t" . '<dd class="endtime">' . $endFmt . '&nbsp;' . '</dd>' . "\n";
}
$html .= "\t\t" . '</dl><div class="ewrap">' . "\n";
$detailUrl = Route::url(
    'index.php?option=' . $this->option . '&task=details&id=' . $this->row->id
);
$titleEsc = $this->escape(stripslashes($this->row->title));
$html .= "\t\t" . '<p class="title"><a href="' . $detailUrl . '">' . $titleEsc . '</a></p>' . "\n";
if (isset($this->categories[$this->row->catid])) {
    $catName = stripslashes($this->categories[$this->row->catid]);
    $html .= "\t\t" . '<p class="category"><strong>Category:</strong> ' . $catName . '</p>' . "\n";
}
$info = '';
foreach ($this->fields as $field) {
    if ($field[4] == 1 && end($field) != '') {
        $info .= "\t\t" . '<p class="' . $field[0] . '">'
            . '<strong>' . $field[1] . ':</strong> ' . end($field) . '</p>' . "\n";
    }
}
$html .= $info;
if (!$info) {
    $truncated = \Hubzero\Utility\Str::truncate(
        strip_tags($this->row->content),
        300
    );
    $html .= "\t\t" . '<p class="description">' . $truncated . '</p>' . "\n";
}
$html .= "\t" . '</div></li>' . "\n";

echo $html;
