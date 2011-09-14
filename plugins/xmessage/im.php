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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_xmessage_im' );

/**
 * Short description for 'plgXMessageIm'
 * 
 * Long description (if any) ...
 */
class plgXMessageIm extends JPlugin
{

	/**
	 * Short description for 'plgXMessageIm'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgXMessageIm(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xmessage', 'im' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	/**
	 * Short description for 'onMessageMethods'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	public function onMessageMethods()
	{
		return 'im';
	}

	/**
	 * Short description for 'onMessage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $from Parameter description (if any) ...
	 * @param      unknown $xmessage Parameter description (if any) ...
	 * @param      unknown $user Parameter description (if any) ...
	 * @param      unknown $action Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function onMessage( $from, $xmessage, $user, $action )
	{
		if ($this->onMessageMethods() != $action) {
			return true;
		}

		return false;
	}
}
