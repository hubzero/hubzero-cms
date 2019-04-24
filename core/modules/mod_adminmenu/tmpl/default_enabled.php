<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Modules\AdminMenu\Node;

defined('_HZEXEC_') or die;

$shownew = (boolean) $params->get('shownew', 1);
$lang = Lang::getRoot();

$active = Request::getCmd('option');

//
// Site SubMenu
//
$menu->addChild(
	new Node(Lang::txt('JSITE'), '#', 'class:site', in_array($active, ['com_system', 'com_cpanel', 'com_config', 'com_checkin', 'com_cache', 'com_redirect'])), true
);
$menu->addChild(
	new Node(Lang::txt('MOD_MENU_CONTROL_PANEL'), 'index.php', 'class:cpanel', ($active == 'com_cpanel'))
);

$menu->addSeparator();

if (User::authorise('core.admin'))
{
	$menu->addChild(new Node(Lang::txt('MOD_MENU_CONFIGURATION'), 'index.php?option=com_config', 'class:config', ($active == 'com_config')));
	$menu->addSeparator();
}

$chm = User::authorise('core.admin', 'com_checkin');
$cam = User::authorise('core.manage', 'com_cache');

if ($chm || $cam)
{
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_MAINTENANCE'), 'index.php?option=com_checkin', 'class:maintenance', ($active == 'com_checkin')), true
	);

	if ($chm)
	{
		$menu->addChild(new Node(Lang::txt('MOD_MENU_GLOBAL_CHECKIN'), 'index.php?option=com_checkin', 'class:checkin', ($active == 'com_checkin')));
		$menu->addSeparator();
	}
	if ($cam)
	{
		$menu->addChild(new Node(Lang::txt('MOD_MENU_CLEAR_CACHE'), 'index.php?option=com_cache', 'class:clear', ($active == 'com_cache')));
		$menu->addChild(new Node(Lang::txt('MOD_MENU_PURGE_EXPIRED_CACHE'), 'index.php?option=com_cache&view=purge', 'class:purge', ($active == 'com_cache')));
		$menu->addSeparator();
	}

	$menu->addChild(new Node(Lang::txt('MOD_MENU_SYS_LDAP'), 'index.php?option=com_system&controller=ldap', 'class:ldap', ($active == 'com_system')));
	$menu->addChild(new Node(Lang::txt('MOD_MENU_SYS_GEO'), 'index.php?option=com_system&controller=geodb', 'class:geo', ($active == 'com_system')));
	$menu->addChild(new Node(Lang::txt('MOD_MENU_SYS_APC'), 'index.php?option=com_system&controller=apc', 'class:apc', ($active == 'com_system')));
	$menu->addChild(new Node(Lang::txt('MOD_MENU_SYS_ROUTES'), 'index.php?option=com_redirect', 'class:routes', ($active == 'com_redirect')));

	$menu->getParent();
}

$menu->addSeparator();
if (User::authorise('core.admin'))
{
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_SYSTEM_INFORMATION'), 'index.php?option=com_system&controller=info', 'class:info', ($active == 'com_system'))
	);
	$menu->addSeparator();
}

$menu->addChild(
	new Node(Lang::txt('MOD_MENU_LOGOUT'), 'index.php?option=com_login&task=logout&' . Session::getFormToken() . '=1', 'class:logout')
);

$menu->getParent();

//
// Users Submenu
//
if (User::authorise('core.manage', 'com_members'))
{
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_COM_USERS_USERS'), '#', 'class:users', ($active == 'com_members' || $active == 'com_groups')), true
	);
	$createUser = $shownew && User::authorise('core.create', 'com_members');
	$createGrp  = User::authorise('core.admin', 'com_members');

	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_COM_MEMBERS'), 'index.php?option=com_members', 'class:members', ($active == 'com_members')), $createUser
	);
	if ($createUser)
	{
		$menu->addChild(
			new Node(Lang::txt('MOD_MENU_COM_MEMBERS_ADD_MEMBER'), 'index.php?option=com_members&task=add', 'class:newuser')
		);
		$menu->getParent();
	}

	if (User::authorise('core.manage', 'com_groups'))
	{
		$menu->addChild(
			new Node(Lang::txt('MOD_MENU_COM_GROUPS'), 'index.php?option=com_groups', 'class:groups', ($active == 'com_groups'))
		);
	}

	if ($createGrp)
	{
		$menu->addSeparator();
		$menu->addChild(
			new Node(Lang::txt('MOD_MENU_COM_USERS_GROUPS'), 'index.php?option=com_members&controller=accessgroups', 'class:groups'), $createUser
		);
		if ($createUser)
		{
			$menu->addChild(
				new Node(Lang::txt('MOD_MENU_COM_USERS_ADD_GROUP'), 'index.php?option=com_members&controller=accessgroups&task=add', 'class:newarticle')
			);
			$menu->getParent();
		}

		$menu->addChild(
			new Node(Lang::txt('MOD_MENU_COM_USERS_LEVELS'), 'index.php?option=com_members&controller=accesslevels', 'class:levels'), $createUser
		);

		if ($createUser)
		{
			$menu->addChild(
				new Node(Lang::txt('MOD_MENU_COM_USERS_ADD_LEVEL'), 'index.php?option=com_members&controller=accesslevels&task=add', 'class:newarticle')
			);
			$menu->getParent();
		}
	}

	$menu->addSeparator();
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_COM_USERS_NOTES'), 'index.php?option=com_members&controller=notes', 'class:user-note'), $createUser
	);

	if ($createUser)
	{
		$menu->addChild(
			new Node(Lang::txt('MOD_MENU_COM_USERS_ADD_NOTE'), 'index.php?option=com_members&controller=notes&task=add', 'class:newarticle')
		);
		$menu->getParent();
	}

	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_COM_USERS_NOTE_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_users', 'class:category'),
		$createUser
	);

	if ($createUser)
	{
		$menu->addChild(
			new Node(Lang::txt('MOD_MENU_COM_CONTENT_NEW_CATEGORY'), 'index.php?option=com_categories&task=category.add&extension=com_users', 'class:newarticle')
		);
		$menu->getParent();
	}

	$menu->addSeparator();
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_MASS_MAIL_USERS'), 'index.php?option=com_members&controller=mail', 'class:massmail')
	);

	$menu->getParent();
}

//
// Menus Submenu
//
if (User::authorise('core.manage', 'com_menus'))
{
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_MENUS'), '#', 'class:menus', ($active == 'com_menus')), true
	);
	$createMenu = $shownew && User::authorise('core.create', 'com_menus');

	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_MENU_MANAGER'), 'index.php?option=com_menus&view=menus', 'class:menumgr'), $createMenu
	);
	if ($createMenu)
	{
		$menu->addChild(
			new Node(Lang::txt('MOD_MENU_MENU_MANAGER_NEW_MENU'), 'index.php?option=com_menus&view=menu&layout=edit', 'class:newmenu')
		);
		$menu->getParent();
	}
	$menu->addSeparator();

	// Menu Types
	foreach ($this->getMenus() as $menuType)
	{
		$alt = '*' . $menuType->sef . '*';
		if ($menuType->home == 0)
		{
			$titleicon = '';
		}
		elseif ($menuType->home == 1 && $menuType->language == '*')
		{
			$titleicon = ' <span class="home" title="' . Lang::txt('MOD_MENU_HOME_DEFAULT') . '">' . '*' . '</span>';
		}
		elseif ($menuType->home > 1)
		{
			$titleicon = ' <span class="home multiple" title="' . Lang::txt('MOD_MENU_HOME_MULTIPLE') . '">' . $menuType->home . '</span>';
		}
		else
		{
			$titleicon = ' <span title="' . $menuType->title_native . '">' . $alt . '</span>';
		}
		$menu->addChild(
			new Node($menuType->title, 'index.php?option=com_menus&view=items&menutype=' . $menuType->menutype, 'class:menu', null, null, $titleicon), $createMenu
		);

		if ($createMenu)
		{
			$menu->addChild(
				new Node(Lang::txt('MOD_MENU_MENU_MANAGER_NEW_MENU_ITEM'), 'index.php?option=com_menus&view=item&layout=edit&menutype=' . $menuType->menutype, 'class:newarticle')
			);
			$menu->getParent();
		}
	}
	$menu->getParent();
}

//
// Content Submenu
//
if (User::authorise('core.manage', 'com_content'))
{
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_COM_CONTENT'), '#', 'class:articles', in_array($active, ['com_content', 'com_categories', 'com_media'])), true
	);
	$createContent = $shownew && User::authorise('core.create', 'com_content');
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_COM_CONTENT_ARTICLE_MANAGER'), 'index.php?option=com_content', 'class:article', ($active == 'com_content')), $createContent
	);
	if ($createContent)
	{
		$menu->addChild(
			new Node(Lang::txt('MOD_MENU_COM_CONTENT_NEW_ARTICLE'), 'index.php?option=com_content&task=article.add', 'class:newarticle')
		);
		$menu->getParent();
	}

	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_COM_CONTENT_CATEGORY_MANAGER'), 'index.php?option=com_categories&extension=com_content', 'class:category'), $createContent
	);
	if ($createContent)
	{
		$menu->addChild(
			new Node(Lang::txt('MOD_MENU_COM_CONTENT_NEW_CATEGORY'), 'index.php?option=com_categories&task=category.add&extension=com_content', 'class:newarticle')
		);
		$menu->getParent();
	}
	/*$menu->addChild(
		new Node(Lang::txt('MOD_MENU_COM_CONTENT_FEATURED'), 'index.php?option=com_content&view=featured', 'class:featured')
	);
	*/
	if (User::authorise('core.manage', 'com_media'))
	{
		$menu->addSeparator();
		$menu->addChild(new Node(Lang::txt('MOD_MENU_MEDIA_MANAGER'), 'index.php?option=com_media', 'class:media', ($active == 'com_media')));
	}

	$menu->getParent();
}

//
// Components Submenu
//

// Get the authorised components and sub-menus.
$components = $this->getComponents(true);

// Check if there are any components, otherwise, don't render the menu
if ($components)
{
	$actives = array();
	foreach ($components as $component)
	{
		if (in_array($component->element, array('com_members', 'com_groups', 'com_system')))
		{
			continue;
		}
		$actives[] = $component->element;
	}

	$menu->addChild(new Node(Lang::txt('MOD_MENU_COMPONENTS'), '#', 'class:components', in_array($active, $actives)), true);

	foreach ($components as $component)
	{
		if (in_array($component->element, array('com_members', 'com_groups', 'com_system')))
		{
			continue;
		}
		if (!empty($component->submenu))
		{
			// This component has a db driven submenu.
			$menu->addChild(new Node($component->text, $component->link, $component->img), true);
			foreach ($component->submenu as $sub)
			{
				$menu->addChild(new Node($sub->text, $sub->link, $sub->img));
			}
			$menu->getParent();
		}
		else
		{
			$menu->addChild(new Node($component->text, $component->link, $component->img, ($active == $component->element)));
		}
	}
	$menu->getParent();
}

//
// Extensions Submenu
//
$im = User::authorise('core.manage', 'com_installer');
$mm = User::authorise('core.manage', 'com_modules');
$pm = User::authorise('core.manage', 'com_plugins');
$tm = User::authorise('core.manage', 'com_templates');
$lm = User::authorise('core.manage', 'com_languages');

if ($im || $mm || $pm || $tm || $lm)
{
	$menu->addChild(new Node(Lang::txt('MOD_MENU_EXTENSIONS_EXTENSIONS'), '#', 'class:extensions', in_array($active, ['com_installer', 'com_modules', 'com_plugins', 'com_templates', 'com_languages'])), true);

	if ($im)
	{
		$menu->addChild(new Node(Lang::txt('MOD_MENU_EXTENSIONS_EXTENSION_MANAGER'), 'index.php?option=com_installer', 'class:install', ($active == 'com_installer')));
		$menu->addSeparator();
	}

	if ($mm)
	{
		$menu->addChild(new Node(Lang::txt('MOD_MENU_EXTENSIONS_MODULE_MANAGER'), 'index.php?option=com_modules', 'class:module', ($active == 'com_modules')));
	}

	if ($pm)
	{
		$menu->addChild(new Node(Lang::txt('MOD_MENU_EXTENSIONS_PLUGIN_MANAGER'), 'index.php?option=com_plugins', 'class:plugin', ($active == 'com_plugins')));
	}

	if ($tm)
	{
		$menu->addChild(new Node(Lang::txt('MOD_MENU_EXTENSIONS_TEMPLATE_MANAGER'), 'index.php?option=com_templates', 'class:themes', ($active == 'com_templates')));
	}

	if ($lm)
	{
		$menu->addChild(new Node(Lang::txt('MOD_MENU_EXTENSIONS_LANGUAGE_MANAGER'), 'index.php?option=com_languages', 'class:language', ($active == 'com_languages')));
	}
	$menu->getParent();
}

//
// Help Submenu
//
if ($params->get('showhelp', 0) == 1)
{
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_HELP'), '#', 'class:help', ($active == 'com_help')), true
	);
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_HELP_PAGES'), 'index.php?option=com_help', 'class:help')
	);
	$menu->addSeparator();

	/*$menu->addChild(
		new Node(Lang::txt('MOD_MENU_HELP_SUPPORT_OFFICIAL_FORUM'), 'http://hubzero.org/answers', 'class:help-forum', false, '_blank')
	);*/
	if ($forum_url = $params->get('forum_url'))
	{
		$menu->addChild(
			new Node(Lang::txt('MOD_MENU_HELP_SUPPORT_CUSTOM_FORUM'), $forum_url, 'class:help-forum', false, '_blank')
		);
	}
	/*$debug = $lang->setDebug(false);
	if ($lang->hasKey('MOD_MENU_HELP_SUPPORT_OFFICIAL_LANGUAGE_FORUM_VALUE') && Lang::txt('MOD_MENU_HELP_SUPPORT_OFFICIAL_LANGUAGE_FORUM_VALUE') != '')
	{
		$forum_url = 'http://hubzero.org/forum/?f=' . (int) Lang::txt('MOD_MENU_HELP_SUPPORT_OFFICIAL_LANGUAGE_FORUM_VALUE');
		$lang->setDebug($debug);
		$menu->addChild(
			new Node(Lang::txt('MOD_MENU_HELP_SUPPORT_OFFICIAL_LANGUAGE_FORUM'), $forum_url, 'class:help-forum', false, '_blank')
		);
	}
	$lang->setDebug($debug);*/
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_HELP_DOCUMENTATION'), 'http://hubzero.org/documentation', 'class:help', false, '_blank')
	);
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_HELP_HUBZERO'), 'http://hubzero.org/support', 'class:help-docs', false, '_blank')
	);
	/*$menu->addSeparator();
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_HELP_LINKS'), '#', 'class:weblinks'), true
	);
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_HELP_EXTENSIONS'), 'http://hubzero.org/extensions=', 'class:help-jed', false, '_blank')
	);
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_HELP_TRANSLATIONS'), 'http://hubzero.org/translations', 'class:help-trans', false, '_blank')
	);
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_HELP_RESOURCES'), 'http://hubzero.org/documentation', 'class:help-jrd', false, '_blank')
	);
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_HELP_COMMUNITY'), 'http://hubzero.org/community', 'class:help-community', false, '_blank')
	);
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_HELP_SECURITY'), 'http://hubzero.org/security', 'class:help-security', false, '_blank')
	);
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_HELP_DEVELOPER'), 'http://hubzero.org/developer', 'class:help-dev', false, '_blank')
	);
	$menu->addChild(
		new Node(Lang::txt('MOD_MENU_HELP_SHOP'), 'http://hubzero.org/store', 'class:help-shop', false, '_blank')
	);
	$menu->getParent();*/
	$menu->getParent();
}
