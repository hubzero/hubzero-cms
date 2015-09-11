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

$base = rtrim(Request::base(), '/');
if (substr($base, -13) == 'administrator')
{
	$base = substr($base, 0, strlen($base)-13);
	$sef = 'support/ticket/new?case=' . $this->report->id;
}
else
{
	$sef = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new&case=' . $this->report->id);
}
$link = rtrim($base, '/') . '/' . trim($sef, '/');

$base = rtrim(str_replace('/administrator', '', $base), '/');

$this->commentor = User::getInstance($this->reported->author);

$message  = '----------------------------' . "\n";
$message .= strtoupper(Lang::txt('Case #')) . ': ' . $this->report->id . "\n";
$message .= strtoupper(Lang::txt('Reason')) . ': ' . $this->report->subject . "\n";
$message .= strtoupper(Lang::txt('Reported')) . ': ' . $this->report->created . "\n";
if (!$this->author)
{
	$reporter = User::getInstance($this->report->created_by);

	$message .= strtoupper(Lang::txt('Reported by')) . ': ' . $this->escape($reporter->get('name')) . '(' . $this->escape($reporter->get('username')) . ')' . "\n";
	$message .= strtoupper(Lang::txt('Comments')) . ': "' . $this->escape($this->report->report) . '"' . "\n";
}
$message .= strtoupper(Lang::txt('Reviewed')) . ': ' . $this->report->reviewed . "\n";
$message .= strtoupper(Lang::txt('Status')) . ': removed' . "\n";
if ($this->report->note && !$this->author)
{
	$message .= strtoupper(Lang::txt('Note')) . ': ' . $this->report->note . "\n";
}
//$message .= strtoupper(Lang::txt('Status')) . ': removed' . "\n";
if ($this->author)
{
$message .= strtoupper(Lang::txt('Dispute')) . ': ' . Lang::txt('The content marked as inappropriate is presented below in its entirety. If you wish to dispute the report, please file a ticket with our support center and reference the case #.') . "\n";
}
$message .= '----------------------------'."\n\n";

$message .= Lang::txt('Created by') . ': ' . stripslashes($this->commentor->get('name')) . ' (' . $this->commentor->get('username') . ')' . "\n";
$message .= Lang::txt('Created') . ': ' . $this->reported->created . "\n\n";

$message .= str_replace('<br />', '', $this->reported->text);
$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n";
