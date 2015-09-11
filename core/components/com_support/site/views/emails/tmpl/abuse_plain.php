<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
