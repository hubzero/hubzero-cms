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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_xmessage_email' );

//-----------

class plgXMessageEmail extends JPlugin
{
	public function plgXMessageEmail(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xmessage', 'email' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function onMessageMethods()
	{
		return 'email';
	}

	//-----------

	public function onMessage( $from, $xmessage, $user, $action )
	{
		if ($this->onMessageMethods() != $action) {
			return true;
		}
		
		if ($user->get('emailConfirmed') <= 0) {
			return false;
		}
		
		$email = $user->get('email');
		
		if (!$email) {
			return false;
		}
		
		$jconfig =& JFactory::getConfig();
		
		if (!isset($from['name']) || $from['name'] == '') {
			$from['name'] = $jconfig->getValue('config.sitename') . ' Administrator';
		}
		if (!isset($from['email']) || $from['email'] == '') {
			$from['email'] = $jconfig->getValue('config.mailfrom');
		}

		$args = "-f '" . $from['email'] . "'";
		$headers = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/plain; charset=utf-8\n";
		$headers .= "From: " . $from['name'] . " <" . $from['email'] . ">\n";
		$headers .= "Reply-To: " . $from['name'] . " <" . $from['email'] . ">\n";
		$headers .= "X-Priority: 3\n";
		$headers .= "X-MSMail-Priority: High\n";
		$headers .= "X-Mailer: " . $from['name'] . "\n";
		
		if (mail($email, $jconfig->getValue('config.sitename').' '.$xmessage->subject, $xmessage->message, $headers, $args)) {
			return true;
		}
    
		return false;
	}
}
