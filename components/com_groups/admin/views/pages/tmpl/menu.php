<?php
JSubMenuHelper::addEntry(
	Lang::txt('COM_GROUPS_PAGES'),
	'index.php?option=com_groups&controller=pages&gid=' . $this->group->get('cn'),
	Request::getVar('controller', 'pages') == 'pages'
);

JSubMenuHelper::addEntry(
	Lang::txt('COM_GROUPS_PAGES_CATEGORIES'),
	'index.php?option=com_groups&controller=categories&gid=' . $this->group->get('cn'),
	Request::getVar('controller', 'pages') == 'categories'
);

// load group params
$config = Component::params('com_groups');

// only show modules if Super group
if ($this->group->isSuperGroup() || $config->get('page_modules', 0))
{
	JSubMenuHelper::addEntry(
		Lang::txt('COM_GROUPS_PAGES_MODULES'),
		'index.php?option=com_groups&controller=modules&gid=' . $this->group->get('cn'),
		Request::getVar('controller', 'pages') == 'modules'
	);
}
?>