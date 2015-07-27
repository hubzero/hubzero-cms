<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
