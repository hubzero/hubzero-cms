INSERT INTO `jos_components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`)
VALUES
	('APC', 'option=com_apc&task=host', 0, 0, 'option=com_apc&task=host', 'APC', 'com_apc', 0, 'js/ThemeOffice/component.png', 0, '', 1),
	('Host', 'option=com_apc&task=host', 0, 127, 'option=com_apc&task=host', 'Host', 'com_apc', 1, 'js/ThemeOffice/component.png', 0, '', 1),
	('Version', 'option=com_apc&task=version', 0, 127, 'option=com_apc&task=version', 'Version', 'com_apc', 5, 'js/ThemeOffice/component.png', 0, '', 1),
	('System', 'option=com_apc&task=system', 0, 127, 'option=com_apc&task=system', 'System', 'com_apc', 2, 'js/ThemeOffice/component.png', 0, '', 1),
	('User', 'option=com_apc&task=user', 0, 127, 'option=com_apc&task=user', 'User', 'com_apc', 3, 'js/ThemeOffice/component.png', 0, '', 1),
	('Directory', 'option=com_apc&task=dircache', 0, 127, 'option=com_apc&task=dircache', 'Directory', 'com_apc', 4, 'js/ThemeOffice/component.png', 0, '', 1);