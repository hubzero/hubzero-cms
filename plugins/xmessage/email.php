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