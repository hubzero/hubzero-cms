#!/usr/bin/php

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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

$thispath = __DIR__;
$bits = explode('/', $thispath);
$basepath = '/'.$bits[1].'/'.$bits[2];

/** Set flag that this is a parent file */
define("_HZEXEC_", 1);

/**
 * Description for ''JPATH_BASE''
 */
define('JPATH_BASE', $basepath);

/**
 * Description for ''DS''
 */
define('DS', DIRECTORY_SEPARATOR);

require_once (PATH_CORE  . DS . 'bootstrap' . DS . 'site' . DS . 'defines.php');
require_once (PATH_CORE  . DS . 'bootstrap' . DS . 'site' . DS . 'framework.php');

$time_start = microtime(true);

/**
 * CREATE THE APPLICATION
 *
 * NOTE :
 */
$mainframe = JFactory::getApplication('site');

/**
 * INITIALISE THE APPLICATION
 *
 * NOTE :
 */
// set the language
$mainframe->initialise();

Plugin::import('system');

// trigger the onAfterInitialise events
Event::trigger('onAfterInitialise');

if (0)
{
/**
 * ROUTE THE APPLICATION
 *
 * NOTE :
 */
$mainframe->route();

// authorization
$Itemid = Request::getInt('Itemid');
$mainframe->authorize($Itemid);

// trigger the onAfterRoute events
Event::trigger('onAfterRoute');

/**
 * DISPATCH THE APPLICATION
 *
 * NOTE :
 */
$option = Request::getCmd('option');
$mainframe->dispatch($option);

// trigger the onAfterDispatch events
Event::trigger('onAfterDispatch');

/**
 * RENDER  THE APPLICATION
 *
 * NOTE :
 */
$mainframe->render();

// trigger the onAfterRender events
Event::trigger('onAfterRender');
} // (0)

include_once(PATH_CORE . DS . 'components' . DS . 'com_answers' . DS . 'helpers' . DS . 'economy.php');
include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'economy.php');
include_once(PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'helpers' . DS . 'economy.php');

include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'controllers' . DS . 'points.php');

echo "_______________________________________________\n";
echo date(DATE_RFC822)."\n";
echo "\"Royalty Payments\"\n\n";

Request::setVar('option', 'com_members');
Request::setVar('controller', 'points');
Request::setVar('task', 'royalty');

$controller = new MembersControllerPoints();
$controller->execute();

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "_______________________________________________\n";
echo "Computed in $time seconds\n";
