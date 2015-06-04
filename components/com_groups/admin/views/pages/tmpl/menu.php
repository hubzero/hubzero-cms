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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

Submenu::addEntry(
	Lang::txt('COM_GROUPS_PAGES'),
	Route::url('index.php?option=com_groups&controller=pages&gid=' . $this->group->get('cn')),
	Request::getVar('controller', 'pages') == 'pages'
);

Submenu::addEntry(
	Lang::txt('COM_GROUPS_PAGES_CATEGORIES'),
	Route::url('index.php?option=com_groups&controller=categories&gid=' . $this->group->get('cn')),
	Request::getVar('controller', 'pages') == 'categories'
);

// load group params
$config = Component::params('com_groups');

// only show modules if Super group
if ($this->group->isSuperGroup() || $config->get('page_modules', 0))
{
	Submenu::addEntry(
		Lang::txt('COM_GROUPS_PAGES_MODULES'),
		Route::url('index.php?option=com_groups&controller=modules&gid=' . $this->group->get('cn')),
		Request::getVar('controller', 'pages') == 'modules'
	);
}
