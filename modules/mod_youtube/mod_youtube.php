<?php
/**
* @version		$Id: mod_feed.php 11371 2008-12-30 01:31:50Z ian $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

error_reporting(E_ALL);
@ini_set('display_errors','1');

//Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$youtube = new modYoutubeHelper($params, $module);
$youtube->render();

require(JModuleHelper::getLayoutPath('mod_youtube'));
