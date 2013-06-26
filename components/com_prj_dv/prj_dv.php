<?php
/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2010-2012 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2010-2012 by Purdue Research Foundation, West Lafayette, IN 47906.
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

defined('_JEXEC') or die('Restricted access');

/*
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
*/

mb_internal_encoding('UTF-8');

jimport('joomla.application.component.helper');

require_once(JPATH_COMPONENT . DS . 'controller.php');
require_once(JPATH_COMPONENT . DS . 'dv_config.php');
require_once(JPATH_COMPONENT . DS . 'util/db.php');
require_once(JPATH_COMPONENT . DS . 'util/dl.php');
require_once(JPATH_COMPONENT . DS . 'util/files.php');

$document = &JFactory::getDocument();

global $html_path, $com_name, $dv_conf;

$html_path = str_replace(JPATH_BASE, '', JPATH_COMPONENT) . '/html';
$com_name = str_replace(JPATH_BASE.'/components/', '', JPATH_COMPONENT);
$com_name = str_replace('com_', '' , $com_name);
$dv_conf['settings']['com_name'] = $com_name;

$document->addScript($html_path . '/util.js');
$document->addScript($html_path . '/jquery.js');
$document->addScript($html_path . '/jquery.dv.js');
$document->addScript($html_path . '/ui/jquery-ui.min.js');
$document->addStyleSheet($html_path . '/ui/themes/smoothness/jquery-ui.css');


// Instantiate controller
$controller = new Controller();
$mainframe = &JFactory::getApplication();
$controller->mainframe = $mainframe;
$controller->execute();
$controller->redirect();
?>
