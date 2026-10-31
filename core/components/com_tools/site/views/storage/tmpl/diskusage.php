<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$html = '';
if (!$this->ajax)
{
	$html .= '<dl id="diskusage" data-base="' . rtrim(Request::base(true), '/') . '">'."\n";
}
if ($this->writelink)
{
	// The label itself links to the storage page -- clearer than a separate
	// "manage" link, with a small open/expand glyph and a descriptive tooltip.
	$storageUrl = Route::url('index.php?option='.$this->option.'&task=storage');
	$viewTitle  = Lang::txt('COM_TOOLS_STORAGE_VIEW_TITLE');
	$html .= "\t".'<dt><a class="du-view" href="'.$storageUrl.'" title="'.$viewTitle.'" aria-label="'.$viewTitle.'" data-help="'.Lang::txt('COM_TOOLS_HELP_STORAGE').'">'
		. '<svg class="du-disk" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v6c0 1.66 3.58 3 8 3s8-1.34 8-3V5"/><path d="M4 11v6c0 1.66 3.58 3 8 3s8-1.34 8-3v-6"/></svg>'
		. '<span class="du-label">' . Lang::txt('COM_TOOLS_STORAGE') . '</span>'
		. '<span class="du-pct" aria-hidden="true">' . (int) $this->amt . '%</span>'
		. '<svg class="du-view-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 4h6v6"/><path d="M20 4 11 13"/><path d="M18 14v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4"/></svg>'
		. '</a></dt>'."\n";
}
else
{
	$html .= "\t".'<dt>' . Lang::txt('COM_TOOLS_STORAGE') . '</dt>'."\n";
}

$this->css('
#du-amount .du-amount-bar {
	width: '.$this->amt.'% !important;
}
');

$html .= "\t".'<dd id="du-amount"><div class="du-amount-bar" title="'.$this->amt.'%"><strong>&nbsp;</strong><span class="du-amount-text">'.$this->amt.'% of '.$this->total.'GB</span></div></dd>'."\n";
if ($this->msgs)
{
	if (count($this->du) <=1)
	{
		$html .= "\t".'<dd id="du-msg"><p class="error">' . Lang::txt('COM_TOOLS_STORAGE_ERROR_RETRIEVING') . '</p></dd>'."\n";
	}
	if ($this->percent == 100)
	{
		$html .= "\t".'<dd id="du-msg"><p class="warning">' . Lang::txt('COM_TOOLS_STORAGE_WARNING_REACHED_LIMIT') . ' <a href="'.Route::url('index.php?option='.$this->option.'&task=storageexceeded').'">' . Lang::txt('COM_TOOLS_STORAGE_HOW_TO_RESOLVE') . '</a>.</p></dd>'."\n";
	}
	if ($this->percent > 100)
	{
		$html .= "\t".'<dd id="du-msg"><p class="warning">' . Lang::txt('COM_TOOLS_STORAGE_WARNING_EXCEEDING_LIMIT') . ' <a href="'.Route::url('index.php?option='.$this->option.'&task=storageexceeded').'">' . Lang::txt('COM_TOOLS_STORAGE_HOW_TO_RESOLVE') . '</a>.</p></dd>'."\n";
	}
}
if (!$this->ajax)
{
	$html .= '</dl>'."\n";
}
echo $html;
