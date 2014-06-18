<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


// Include the logic only once
require_once (dirname(__FILE__) . DS . 'helper.php');

$modTwitterFeed = new modTwitterFeed($params, $module);
$modTwitterFeed->display();

/*
// Include the helper file
require_once(dirname(__FILE__).DS.'helper.php');

// Get a parameter from the module's configuration
$moduleTitle = $params->get('moduleTitle');
$twitterID   = $params->get('twitterID');
$tweetCount  = $params->get('tweetcount');
$displayLink = $params->get('displayLink');
$displayIcon = $params->get('displayIcon');

// Get the items to display from the helper
//modTwitterFeedHelper::getTweets($twitterID, $tweetCount);

// Include the template for display
require(JModuleHelper::getLayoutPath('mod_twitterfeed'));
*/