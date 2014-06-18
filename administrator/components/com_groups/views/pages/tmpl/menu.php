<?php
JSubMenuHelper::addEntry(
	JText::_('Pages'),
	'index.php?option=com_groups&controller=pages&gid=' . $this->group->get('cn'),
	JRequest::getVar('controller', 'pages') == 'pages'
);

JSubMenuHelper::addEntry(
	JText::_('Categories'),
	'index.php?option=com_groups&controller=categories&gid=' . $this->group->get('cn'),
	JRequest::getVar('controller', 'pages') == 'categories'
);

// load group params
$config = JComponentHelper::getParams('com_groups');

// only show modules if Super group
if ($this->group->isSuperGroup() || $config->get('page_modules', 0))
{
	JSubMenuHelper::addEntry(
		JText::_('Modules'),
		'index.php?option=com_groups&controller=modules&gid=' . $this->group->get('cn'),
		JRequest::getVar('controller', 'pages') == 'modules'
	);
}
?>