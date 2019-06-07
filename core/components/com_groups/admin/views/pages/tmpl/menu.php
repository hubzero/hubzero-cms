<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Submenu::addEntry(
	Lang::txt('COM_GROUPS_PAGES'),
	Route::url('index.php?option=com_groups&controller=pages&gid=' . $this->group->get('cn')),
	Request::getCmd('controller', 'pages') == 'pages'
);

Submenu::addEntry(
	Lang::txt('COM_GROUPS_PAGES_CATEGORIES'),
	Route::url('index.php?option=com_groups&controller=categories&gid=' . $this->group->get('cn')),
	Request::getCmd('controller', 'pages') == 'categories'
);

// load group params
$config = Component::params('com_groups');

// only show modules if Super group
if ($this->group->isSuperGroup() || $config->get('page_modules', 0))
{
	Submenu::addEntry(
		Lang::txt('COM_GROUPS_PAGES_MODULES'),
		Route::url('index.php?option=com_groups&controller=modules&gid=' . $this->group->get('cn')),
		Request::getCmd('controller', 'pages') == 'modules'
	);
}
