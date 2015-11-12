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

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=ticket&id=';
$site = rtrim(Request::base(), '/');

$message = '----------------------------'."\n\n";

$cells = array();
$cells[] = '"Number"';
$cells[] = '"Issue"';
$cells[] = '"Created"';
$cells[] = '"Creator"';
$cells[] = '"Assigned"';
$cells[] = '"Severity"';
$cells[] = '"URL"';

$message .= implode(", ", $cells) . "\n";

if (isset($this->tickets) && count($this->tickets) > 0)
{
	foreach ($this->tickets as $ticket)
	{
		if (!$ticket->summary)
		{
			$ticket->summary = substr($ticket->report, 0, 70);
			if (strlen($ticket->summary) >= 70)
			{
				$ticket->summary .= '...';
			}
			if (!trim($ticket->summary))
			{
				$ticket->summary = Lang::txt('(no content found)');
			}
		}
		$ticket->summary = str_replace("\r", "", $ticket->summary);
		$ticket->summary = str_replace("\t", " ", $ticket->summary);
		$ticket->summary = str_replace("\n", " ", $ticket->summary);

		$sef = Route::url($base . $ticket->id);
		if (substr($site, -13) == 'administrator')
		{
			$sef = 'support/ticket/' . $ticket->id;
		}
		$link = $site . '/' . trim($sef, '/');
		$link = str_replace('/administrator', '', $link);

		$cells = array();
		$cells[] = '"#' . addslashes($ticket->id) . '"';
		$cells[] = '"' . addslashes($ticket->summary) . '"';
		$cells[] = '"' . addslashes($ticket->created) . '"';
		$cells[] = '"' . ($ticket->name ? addslashes($ticket->name) : '--') . ($ticket->login ? ' (' . addslashes($ticket->login) . ')' : ' (--)') . '"';
		$cells[] = '"' . ($ticket->owner ? addslashes($ticket->owner_name . ' (' . $ticket->owner . ')') : '--') . '"';
		$cells[] = '"' . addslashes($ticket->severity) . '"';
		$cells[] = '"' . rtrim(Request::base(), '/') . '/' . trim($sef, '/') . '"';

		$message .= implode(", ", $cells) . "\n";
	}
}

$message .= '----------------------------'."\n";

echo $message . "\n";
