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

class modMyProfile
{
	private $attributes = array();

	//-----------

	public function __construct( $params ) 
	{
		$this->params = $params;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	//-----------

	// TODO: needs a lot of work, esp w/r/t configuration
	public function display()
	{
		$config =& JComponentHelper::getParams( 'com_members' );
		
		$this->id = JFactory::getUser()->get('id');
		
		$profile = new XProfile();
		$profile->load($this->id);
		
		if (!$profile->get('name')) {
			$name  = $profile->get('givenName').' ';
			$name .= ($profile->get('middleName')) ? $profile->get('middleName').' ' : '';
			$name .= $profile->get('surname');
			$profile->set('name', $name);
		}
		
		// Get the member's picture (if it exist)
		if ($profile->get('picture')) {
			ximport('fileuploadutils');
			
			$dir = FileUploadUtils::niceidformat( $this->id );
			if (!file_exists(JPATH_ROOT.$config->get('webpath').DS.$dir.DS.$profile->get('picture'))) {
				$profile->set('picture', $config->get('defaultpic'));
			} else {
				$profile->set('picture', $config->get('webpath').DS.$dir.DS.stripslashes($profile->get('picture')));
			}
		} else {
			if (!file_exists(JPATH_ROOT.$config->get('defaultpic'))) {
				$profile->set('picture', '');
			} else {
				$profile->set('picture', $config->get('defaultpic'));
			}	
		}
		
		$this->profile = $profile;
		
		// Push the module CSS to the template
		ximport('xdocument');
		XDocument::addModuleStyleSheet('mod_myprofile');
	}
}
