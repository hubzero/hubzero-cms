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
	$html .= "\t".'<dt>' . Lang::txt('COM_TOOLS_STORAGE') . ' (<a href="'.Route::url('index.php?option='.$this->option.'&task=storage').'">' . Lang::txt('COM_TOOLS_STORAGE_MANAGE') . '</a>)</dt>'."\n";
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
