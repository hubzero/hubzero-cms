<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Modules\AdminMenu\Node;

defined('_HZEXEC_') or die;

//
// Site SubMenu
//
$menu->addChild(new Node(Lang::txt('JSITE'), null, 'class:site disabled'));

//
// Users Submenu
//
if ($user->authorise('core.manage', 'com_members'))
{
	$menu->addChild(new Node(Lang::txt('MOD_MENU_COM_USERS'), null, 'class:users disabled'));
}

//
// Menus Submenu
//
if ($user->authorise('core.manage', 'com_menus'))
{
	$menu->addChild(new Node(Lang::txt('MOD_MENU_MENUS'), null, 'class:menus disabled'));
}

//
// Content Submenu
//
if ($user->authorise('core.manage', 'com_content'))
{
	$menu->addChild(new Node(Lang::txt('MOD_MENU_COM_CONTENT'), null, 'class:articles disabled'));
}

//
// Components Submenu
//
// Get the authorised components and sub-menus.
// Check if there are any components, otherwise, don't display the components menu item
if ($components = $this->getComponents(true))
{
	$menu->addChild(new Node(Lang::txt('MOD_MENU_COMPONENTS'), null, 'class:components disabled'));
}

//
// Extensions Submenu
//
$im = $user->authorise('core.manage', 'com_installer');
$mm = $user->authorise('core.manage', 'com_modules');
$pm = $user->authorise('core.manage', 'com_plugins');
$tm = $user->authorise('core.manage', 'com_templates');
$lm = $user->authorise('core.manage', 'com_languages');

if ($im || $mm || $pm || $tm || $lm)
{
	$menu->addChild(new Node(Lang::txt('MOD_MENU_EXTENSIONS_EXTENSIONS'), null, 'class:extensions disabled'));
}

//
// Help Submenu
//
if ($params->get('showhelp', 0) == 1)
{
	$menu->addChild(new Node(Lang::txt('MOD_MENU_HELP'), null, 'class:help disabled'));
}
