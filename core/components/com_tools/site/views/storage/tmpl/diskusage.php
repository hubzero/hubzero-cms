<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
$html .= "\t".'<dd id="du-amount"><div class="du-amount-bar" style="width:'.$this->amt.'%;"><strong>&nbsp;</strong><span class="du-amount-text">'.$this->amt.'% of '.$this->total.'GB</span></div></dd>'."\n";
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