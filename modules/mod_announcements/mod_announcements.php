<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Include the logic only once
require_once (dirname(__FILE__).DS.'helper.php');

$catid	= $params->get('catid', 0);

$config = JFactory::getConfig();

if ($config->getValue('config.debug')) {
	error_reporting(E_ALL);
	@ini_set('display_errors','1');
}


//check if cache diretory is writable as cache files will be created for the announcements
$cacheDir = JPATH_BASE.DS.'cache';
if (!is_writable($cacheDir))
{
	echo '<div>';
	echo JText::_('Please make cache directory writable.');
	echo '</div>';
	return;
}

//check if category has been set
if (empty ($catid))
{
	echo '<div>';
	echo JText::_('No category specified.');
	echo '</div>';
	return;
}

$modannouncements = new modAnnouncementsHelper($params);
$modannouncements->display();
require(JModuleHelper::getLayoutPath('mod_announcements'));