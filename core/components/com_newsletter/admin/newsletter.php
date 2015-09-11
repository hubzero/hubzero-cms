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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Admin;

if (!\User::authorise('core.manage', 'com_newsletter'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

//include models
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'newsletter.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'template.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'primary.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'secondary.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'mailinglist.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'mailinglist.email.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'mailing.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'mailing.recipient.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'mailing.recipient.action.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'helper.php');

//instantiate controller
$controllerName = \Request::getCmd('controller', 'newsletter');
require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

//menu items
$menuItems = array(
	'newsletter'  => \Lang::txt('COM_NEWSLETTER_NEWSLETTERS'),
	'mailing'     => \Lang::txt('COM_NEWSLETTER_MAILINGS'),
	'mailinglist' => \Lang::txt('COM_NEWSLETTER_LISTS'),
	'template'    => \Lang::txt('COM_NEWSLETTER_TEMPLATES'),
	'tools'       => \Lang::txt('COM_NEWSLETTER_TOOLS')
);

//add menu items
foreach ($menuItems as $k => $v)
{
	$active = (\Request::getCmd('controller', 'newsletter') == $k) ? true : false ;
	\Submenu::addEntry($v, \Route::url('index.php?option=com_newsletter&controller=' . $k), $active);
}

//execute controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
