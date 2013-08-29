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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Table class for project logs
 */
class ProjectLog extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         	= NULL;

	/**
	 * Project id
	 * 
	 * @var integer
	 */	
	var $projectid      = NULL;
	
	/**
	 * User id
	 * 
	 * @var integer
	 */	
	var $userid       	= NULL;
	
	/**
	 * Datetime (0000-00-00 00:00:00)
	 * 
	 * @var datetime
	 */
	var $time			= NULL;
		
	/**
	 * Major section (general / setup / project / edit / reviewer etc.)
	 * 
	 * @var string
	 */	
	var $section        = NULL;
	
	/**
	 * Plugin or layout name ( feed / todo / apps etc.)
	 * 
	 * @var string
	 */	
	var $layout        	= NULL;
	
	/**
	 * Task name (view / save etc.)
	 * 
	 * @var string
	 */	
	var $action       	= NULL;
	
	/**
	 * Request uri at time of log
	 * 
	 * @var string
	 */	
	var $request_uri    = NULL;
	
	/**
	 * Ajax call?
	 * 
	 * @var tinyint
	 */	
	var $ajax       	= NULL;
	
	/**
	 * Project owner ID
	 * 
	 * @var int
	 */	
	var $owner       	= NULL;
				
	/**
	 * IP address
	 * 
	 * @var varchar
	 */	
	var $ip       		= NULL;	
	
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db ) 
	{
		parent::__construct( '#__project_logs', 'id', $db );
	}	
}
