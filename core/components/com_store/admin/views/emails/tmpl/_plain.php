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

$emailbody  = Lang::txt('COM_STORE_THANKYOU') . ' ' . Lang::txt('COM_STORE_IN_THE') . ' ' . Config::get('sitename') . ' ' . Lang::txt('COM_STORE_STORE') . '!' . "\n\n";
$emailbody .= Lang::txt('COM_STORE_EMAIL_UPDATE') . "\n";
$emailbody .= '----------------------------------------------------------' . "\n";
$emailbody .= ' ' . Lang::txt('COM_STORE_ORDER_NUMBER') . ': ' . $this->orderid . "\n";
$emailbody .= ' ' . Lang::txt('COM_STORE_ORDER_TOTAL') . ': ' . $this->cost . ' ' . Lang::txt('COM_STORE_POINTS') . "\n";
$emailbody .= ' ' . Lang::txt('COM_STORE_PLACED') . ' ' . Date::of($this->row->ordered)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . "\n";
$emailbody .= ' ' . Lang::txt('COM_STORE_STATUS') . ': ';

switch ($this->row->status)
{
	case 0:
	default:
		$emailbody .= ' ' . Lang::txt('COM_STORE_IN_PROCESS') . "\r\n";
		break;
	case 1:
		$emailbody .= ' ' . strtolower(Lang::txt('COM_STORE_COMPLETED')) . ' ' . Lang::txt('COM_STORE_ON') . ' ' . Date::of($this->row->status_changed)->toLocal(Lang::txt('COM_STORE_DATE_FORMAT_HZ1')) . "\r\n\r\n";
		$emailbody .= Lang::txt('COM_STORE_EMAIL_PROCESSED') . '.' . "\r\n";
		break;
	case 2:
		$emailbody .= ' ' . strtolower(Lang::txt('COM_STORE_CANCELLED')) . ' ' . Lang::txt('COM_STORE_ON') . ' ' . Date::of($this->row->status_changed)->toLocal(Lang::txt('COM_STORE_DATE_FORMAT_HZ1')) . "\r\n\r\n";
		$emailbody .= Lang::txt('COM_STORE_EMAIL_CANCELLED') . '.' . "\r\n";
		break;
}
if ($this->message)
{
	$emailbody .= $this->message;
}

echo $emailbody;
