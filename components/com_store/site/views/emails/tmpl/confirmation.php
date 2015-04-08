<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$emailbody  = Lang::txt('COM_STORE_THANKYOU') . ' ' . Lang::txt('COM_STORE_IN_THE') . ' ' . $this->sitename . ' ' . Lang::txt(strtolower($this->option)) . '!' . "\n\n";
$emailbody .= Lang::txt('COM_STORE_EMAIL_KEEP') . "\n";
$emailbody .= '----------------------------------------------------------' . "\n";
$emailbody .= ' ' . Lang::txt('COM_STORE_ORDER') . ' ' . Lang::txt('COM_STORE_NUM') . ': ' . $this->orderid . "\n";
$emailbody .= ' ' . Lang::txt('ORDER') . ' ' . Lang::txt('TOTAL') . ': ' . $this->cost . ' ' . Lang::txt('POINTS') . "\n";
$emailbody .= ' ' . Lang::txt('PLACED') . ' ' .Date::of($this->now)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . "\n";
$emailbody .= ' ' . Lang::txt('STATUS') . ': ' . Lang::txt('RECEIVED') . "\n";
$emailbody .= '----------------------------------------------------------' . "\n";
$emailbody .= $this->details . "\n";
$emailbody .= '----------------------------------------------------------' . "\n\n";
$emailbody .= Lang::txt('COM_STORE_EMAIL_ORDER_PROCESSED') . '. ';
$emailbody .= Lang::txt('COM_STORE_EMAIL_QUESTIONS') . '.' . "\n\n";
$emailbody .= Lang::txt('COM_STORE_EMAIL_THANKYOU') . "\n";

echo $emailbody;
