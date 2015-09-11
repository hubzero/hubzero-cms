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

$emailbody  = Lang::txt('COM_STORE_THANKYOU') . ' ' . Lang::txt('COM_STORE_IN_THE') . ' ' . Config::get('sitename') . ' ' . Lang::txt(strtolower($this->option)) . '!' . "\n\n";
$emailbody .= Lang::txt('COM_STORE_EMAIL_KEEP') . "\n";
$emailbody .= '----------------------------------------------------------' . "\n";
$emailbody .= ' ' . Lang::txt('COM_STORE_ORDER_NUMBER') . ': ' . $this->orderid . "\n";
$emailbody .= ' ' . Lang::txt('COM_STORE_ORDER') . ' ' . Lang::txt('COM_STORE_TOTAL') . ': ' . $this->cost . ' ' . Lang::txt('COM_STORE_POINTS') . "\n";
$emailbody .= ' ' . Lang::txt('COM_STORE_PLACED') . ' ' . Date::of()->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . "\n";
$emailbody .= ' ' . Lang::txt('COM_STORE_STATUS') . ': ' . Lang::txt('COM_STORE_RECEIVED') . "\n";
$emailbody .= '----------------------------------------------------------' . "\n";
$emailbody .= $this->details . "\n";
$emailbody .= '----------------------------------------------------------' . "\n\n";
$emailbody .= Lang::txt('COM_STORE_EMAIL_ORDER_PROCESSED') . '. ';
$emailbody .= Lang::txt('COM_STORE_EMAIL_QUESTIONS') . '.' . "\n\n";
$emailbody .= Lang::txt('COM_STORE_EMAIL_THANKYOU') . "\n";

echo $emailbody;
