<?php
/**
 * @package		HUBzero CMS
 * @author		Christopher Smoak <csmoak@purdue.edu>
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

// Include the helper file  
require_once(dirname(__FILE__).DS.'helper.php');
  
// Get a parameter from the module's configuration
$moduleTitle = $params->get('moduleTitle');
$twitterID  = $params->get('twitterID');
$tweetCount = $params->get('tweetcount');
$displayLink = $params->get('displayLink');
$displayIcon = $params->get('displayIcon');

// Get the items to display from the helper
$tweets = modTwitterFeedHelper::getTweets($twitterID, $tweetCount);
  
// Include the template for display  
require(JModuleHelper::getLayoutPath('mod_twitterfeed'));
