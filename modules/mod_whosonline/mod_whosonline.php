<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_whosonline
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the whosonline functions only once
require_once dirname(__FILE__).'/helper.php';

$whoseonline = new modWhosonlineHelper($params, $module);
$whoseonline->display();

