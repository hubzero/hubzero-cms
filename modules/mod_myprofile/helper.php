<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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

		$profile = new Hubzero_User_Profile();
		$profile->load($this->id);

		if (!$profile->get('name')) {
			$name  = $profile->get('givenName').' ';
			$name .= ($profile->get('middleName')) ? $profile->get('middleName').' ' : '';
			$name .= $profile->get('surname');
			$profile->set('name', $name);
		}

		// Get the member's picture (if it exist)
		if ($profile->get('picture')) {
			ximport('Hubzero_View_Helper_Html');

			$dir = Hubzero_View_Helper_Html::niceidformat( $this->id );
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
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet('mod_myprofile');
	}
}

