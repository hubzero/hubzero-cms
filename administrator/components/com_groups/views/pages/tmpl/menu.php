<?php
JSubMenuHelper::addEntry(
	JText::_('COM_GROUPS_PAGES'),
	'index.php?option=com_groups&controller=pages&gid=' . $this->group->get('cn'),
	JRequest::getVar('controller', 'pages') == 'pages'
);

JSubMenuHelper::addEntry(
	JText::_('COM_GROUPS_PAGES_CATEGORIES'),
	'index.php?option=com_groups&controller=categories&gid=' . $this->group->get('cn'),
	JRequest::getVar('controller', 'pages') == 'categories'
);

// load group params
$config = JComponentHelper::getParams('com_groups');

// only show modules if Super group
if ($this->group->isSuperGroup() || $config->get('page_modules', 0))
{
	JSubMenuHelper::addEntry(
		JText::_('COM_GROUPS_PAGES_MODULES'),
		'index.php?option=com_groups&controller=modules&gid=' . $this->group->get('cn'),
		JRequest::getVar('controller', 'pages') == 'modules'
	);
}
?>