<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_JEXEC') or die;

//
// Site SubMenu
//
$menu->addChild(new \Modules\Menu\Node(Lang::txt('JSITE'), null, 'disabled'));

//
// Users Submenu
//
if ($user->authorise('core.manage', 'com_users'))
{
	$menu->addChild(new \Modules\Menu\Node(Lang::txt('MOD_MENU_COM_USERS'), null, 'disabled'));
}

//
// Menus Submenu
//
if ($user->authorise('core.manage', 'com_menus'))
{
	$menu->addChild(new \Modules\Menu\Node(Lang::txt('MOD_MENU_MENUS'), null, 'disabled'));
}

//
// Content Submenu
//
if ($user->authorise('core.manage', 'com_content'))
{
	$menu->addChild(new \Modules\Menu\Node(Lang::txt('MOD_MENU_COM_CONTENT'), null, 'disabled'));
}

//
// Components Submenu
//
// Get the authorised components and sub-menus.
// Check if there are any components, otherwise, don't display the components menu item
if ($components = $this->getComponents(true))
{
	$menu->addChild(new \Modules\Menu\Node(Lang::txt('MOD_MENU_COMPONENTS'), null, 'disabled'));
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
	$menu->addChild(new \Modules\Menu\Node(Lang::txt('MOD_MENU_EXTENSIONS_EXTENSIONS'), null, 'disabled'));
}

//
// Help Submenu
//
if ($params->get('showhelp', 0) == 1)
{
	$menu->addChild(new \Modules\Menu\Node(Lang::txt('MOD_MENU_HELP'), null, 'disabled'));
}
