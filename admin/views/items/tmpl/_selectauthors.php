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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$authIDs = array();
$html = '';
$i = 1;
$option = $this->option;

if ($this->authNames != NULL)
{
	$html = '<ul id="author-list">'."\n";
	foreach ($this->authNames as $authname)
	{
		$authIDs[] = $authname->id;
		$name = $authname->name;

		$org = ($authname->organization)
			? $this->escape($authname->organization) : '';
		$credit = ($authname->credit)
			? $this->escape($authname->credit) : '';
		$userid = $authname->user_id ? $authname->user_id : 'unregistered';

		$html .= "\t".'<li id="author_'.$authname->id.'" class="pick reorder">'
			. '<span class="ordernum">' . $i . '</span>. ' . $name . ' (' . $userid . ')';
		$html .= $org ? ' - <span class="org">' . $org . '</span>' : '';
		$html .= ' <a class="editauthor" href="' . Route::url('index.php?option=' . $option . '&controller=items&task=editauthor&author=' . $authname->id) . '" >' . Lang::txt('COM_PUBLICATIONS_EDIT') . '</a> ';
		$html .= ' <a class="editauthor" href="' . Route::url('index.php?option=' . $option . '&controller=items&task=deleteauthor&aid=' . $authname->id) .'"  > ' . Lang::txt('COM_PUBLICATIONS_DELETE') . '</a> ';
		if ($credit)
		{
			$html .= '<br />' . Lang::txt('COM_PUBLICATIONS_CREDIT') . ': ' . $credit;
		}
		$html .= '</li>' . "\n";
		$i++;
	}
	$html.= '</ul>';
}
else
{
	$html.= '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_AUTHORS') . '</p>';
}
if (count($this->authNames) > 1)
{
	$html.= '<input type="hidden" value="" name="list" id="neworder" />';
	$html.= '<p class="tip">' . Lang::txt('COM_PUBLICATIONS_AUTHORS_REORDER_TIP') . '</p>';
	$html.= '<input type="button" onclick="submitbutton(\'saveorder\');" class="btn" value="Save Order" id="saveorder" />';
}

echo $html;
