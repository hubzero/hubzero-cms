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
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------

error_reporting(E_ALL);
@ini_set('display_errors','1');

jimport('joomla.application.component.helper');

// Include scripts
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'admin.contribtool.html.php' );
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'admin.controller.php' );
include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'contribtool.config.php' );
include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'contribtool.tool.php' );
include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'contribtool.version.php' );
include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'contribtool.helper.php' );
include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'contribtool.toolgroup.php' );
include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'contribtool.author.php' );

include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'helpers'..'utilities.php' );
include_once( JPATH_ROOT.DS.'components'.DS.'com_support'.DS.'support.tags.php' );
include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'ticket.php' );
include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'comment.php' );


require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php');
include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.doi.php' );
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.type.php');
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.assoc.php');
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.contributor.php');
require_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.extended.php' );
require_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.tags.php' );

ximport('textfilter');
ximport('fileuploadutils');
ximport('xmodule');
ximport('xgroup');
ximport('xprofile');
ximport('misc_func');
ximport('xuserhelper');

// Initiate controller
$controller = new ContribtoolController();
$controller->execute();
$controller->redirect();
?>
