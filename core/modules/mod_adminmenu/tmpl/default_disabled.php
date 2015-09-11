<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Modules\AdminMenu\Node;

defined('_HZEXEC_') or die;

//
// Site SubMenu
//
$menu->addChild(new Node(Lang::txt('JSITE'), null, 'disabled'));

//
// Users Submenu
//
if ($user->authorise('core.manage', 'com_users'))
{
	$menu->addChild(new Node(Lang::txt('MOD_MENU_COM_USERS'), null, 'disabled'));
}

//
// Menus Submenu
//
if ($user->authorise('core.manage', 'com_menus'))
{
	$menu->addChild(new Node(Lang::txt('MOD_MENU_MENUS'), null, 'disabled'));
}

//
// Content Submenu
//
if ($user->authorise('core.manage', 'com_content'))
{
	$menu->addChild(new Node(Lang::txt('MOD_MENU_COM_CONTENT'), null, 'disabled'));
}

//
// Components Submenu
//
// Get the authorised components and sub-menus.
// Check if there are any components, otherwise, don't display the components menu item
if ($components = $this->getComponents(true))
{
	$menu->addChild(new Node(Lang::txt('MOD_MENU_COMPONENTS'), null, 'disabled'));
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
	$menu->addChild(new Node(Lang::txt('MOD_MENU_EXTENSIONS_EXTENSIONS'), null, 'disabled'));
}

//
// Help Submenu
//
if ($params->get('showhelp', 0) == 1)
{
	$menu->addChild(new Node(Lang::txt('MOD_MENU_HELP'), null, 'disabled'));
}
