<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$config = JFactory::getConfig();

if ($config->getValue('config.debug')) {
	error_reporting(E_ALL);
	@ini_set('display_errors','1');
}

// Editor usertype check
$jacl =& JFactory::getACL();
$jacl->addACL( $option, 'manage', 'users', 'super administrator' );
$jacl->addACL( $option, 'manage', 'users', 'administrator' );
$jacl->addACL( $option, 'manage', 'users', 'manager' );

ximport('Hubzero_User_Helper');

include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'attachment.php');
include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'author.php');
include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'comment.php');
include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'log.php');
include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'page.php');
include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'revision.php');

//include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'config.php');
include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'differenceengine.php');
include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'html.php');
//include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'macro.php');
//include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'math.php');
//include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'parser.php');
include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'sanitizer.php');
include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'setup.php');
include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'stringutils.php');
include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'tags.php');
include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'utfnormalutil.php');

include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'controller.php');

// Instantiate controller
$controller = new WikiController( array('name'=>'wiki') );
$controller->mainframe = $mainframe;
$controller->execute();
$controller->redirect();

