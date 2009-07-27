<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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
// Support configuration
//----------------------------------------------------------

class SupportConfig
{
	public $paramaters = array();
	public $severities = array();
	
	//-----------
	
	public function __construct( $option )
	{
		$database =& JFactory::getDBO();
		
		$database->setQuery( "SELECT params FROM #__components WHERE `option`='".$option."' AND parent=0 LIMIT 1" );
		$parameters = $database->loadResult();
		
		/*if (!$parameters) {
			$database->setQuery( "SELECT admin_menu_link FROM #__components WHERE `option`='".$option."' AND parent=0 LIMIT 1" );
			$admin_menu_link = $database->loadResult();
			
			if (!$admin_menu_link) {
				$database->setQuery( "UPDATE #__components SET `admin_menu_link`='option=com_support', `admin_menu_alt`='Support' WHERE `option`='com_support' AND `parent`=0" );
				if (!$database->query()) {
					echo $database->getErrorMsg();
					return false;
				}
				
				$database->setQuery( "INSERT INTO #__components(`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) 
				VALUES('', 'Support', 'option=com_support', 0, 0, 'option=com_support', 'Support', '".$option."', 0, 'js/ThemeOffice/component.png', 0, '', 1)" );
				if (!$database->query()) {
					echo $database->getErrorMsg();
					return false;
				}
				
				$database->setQuery( "SELECT id FROM #__components WHERE `option`='".$option."' AND parent=0 LIMIT 1" );
				$parent = $database->loadResult();
				
				$database->setQuery( "INSERT INTO #__components(`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) 
				VALUES('', 'Tickets', 'option=com_support&task=tickets', 0, $parent, 'option=com_support&task=tickets', 'Tickets', '".$option."', 0, 'js/ThemeOffice/component.png', 0, '', 1)" );
				if (!$database->query()) {
					echo $database->getErrorMsg();
					return false;
				}
				
				$database->setQuery( "INSERT INTO #__components(`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) 
				VALUES('', 'Messages', 'option=com_support&task=messages', 0, $parent, 'option=com_support&task=messages', 'Messages', '".$option."', 1, 'js/ThemeOffice/component.png', 0, '', 1)" );
				if (!$database->query()) {
					echo $database->getErrorMsg();
					return false;
				}
				
				$database->setQuery( "INSERT INTO #__components(`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) 
				VALUES('', 'Resolutions', 'option=com_support&task=resolutions', 0, $parent, 'option=com_support&task=resolutions', 'Resolutions', '".$option."', 2, 'js/ThemeOffice/component.png', 0, '', 1)" );
				if (!$database->query()) {
					echo $database->getErrorMsg();
					return false;
				}
				
				$database->setQuery( "INSERT INTO #__components(`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) 
				VALUES('', 'Abuse Reports', 'option=com_support&task=abusereports', 0, $parent, 'option=com_support&task=abusereports', 'Abuse Reports', '".$option."', 3, 'js/ThemeOffice/component.png', 0, '', 1)" );
				if (!$database->query()) {
					echo $database->getErrorMsg();
					return false;
				}
				
				$database->setQuery( "CREATE TABLE `#__abuse_reports` (
				  `id` int(11) NOT NULL auto_increment,
				  `category` varchar(50) default NULL,
				  `referenceid` int(11) default '0',
				  `report` text NOT NULL,
				  `created_by` int(11) NOT NULL default '0',
				  `created` datetime NOT NULL default '0000-00-00 00:00:00',
				  `state` int(3) default '0',
				  `subject` varchar(150) default NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;" );
				if (!$database->query()) {
					echo $database->getErrorMsg();
					return false;
				}
			}
		}*/
		
		$params = array();
		if ($parameters) {
			$ps = explode(n,$parameters);
			foreach ($ps as $p) 
			{
				$m = explode('=',$p);
				if (trim($m[0])) {
					$params[$m[0]] = (isset($m[1])) ? $m[1] : '';
				}
			}
		}
		
		$this->parameters = $params;
	}
	
	//-----------
	
	public function getSeverities() 
	{
		$s = $this->severities;
		if (!$s) {
			$severities = $this->parameters['severities'];
			if ($severities) {
				$svs = explode(',', $severities);
				foreach ($svs as $sv) 
				{
					$s[] = trim($sv);
				}
			} else {
				$s = array('critical','major','normal','minor','trivial');
			}
			$this->severities = $s;
		}
		return $s;
	}
}
?>