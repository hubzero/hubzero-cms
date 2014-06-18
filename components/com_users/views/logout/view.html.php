<?php
/**
 * HUBzero CMS
 *                       *
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
 * This file incorporates work covered by the following copyright and
 * permission notice:
 *
 * @package   hubzero-cms-joomla
 * @file      components/com_user/views/logout/view.html.php
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright (c) 2010-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * User component logout view class
 *
 * @package		Joomla
 * @subpackage	Users
 * @since	1.0
 */
class UsersViewLogout extends JViewLegacy
{
	/**
	 * Short description for 'display'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $tpl Parameter description (if any) ...
	 * @return     void
	 */
	function display($tpl = null)
	{
		// Initialize variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$image		= '';

		$menu = JFactory::getApplication()->getMenu();

		$item   = $menu->getActive();
		if($item)
			$params	= $menu->getParams($item->id);
		else {
			$params = new JParameter( '' );
			$template = JFactory::getApplication()->getTemplate();
            $inifile = JPATH_SITE . DS . 'templates' . DS . $template . DS .  'html' . DS . 'com_user' . DS . 'logout' . DS . 'config.ini';
			if (file_exists($inifile)) {
				$params->loadINI( file_get_contents($inifile) );
			}

			$params->def('page_title',	JText::_( 'Logout' ));
		}

		$type = 'logout';

		// Set some default page parameters if not set
		$params->def( 'show_page_title', 				1 );
		if (!$params->get( 'page_title')) {
				$params->set('page_title',	JText::_( 'Logout' ));
			}
		if(!$item)
		{
			$params->def( 'header_logout', 			'' );
		}

		$params->def( 'pageclass_sfx', 			'' );
		$params->def( 'logout', 				'/');
		$params->def( 'description_logout', 		1 );
		$params->def( 'description_logout_text',	JText::_( 'LOGOUT_DESCRIPTION' ) );
		$params->def( 'image_logout', 				'key.jpg' );
		$params->def( 'image_logout_align', 		'right' );
		$usersConfig =  JComponentHelper::getParams( 'com_users' );
		$params->def( 'registration', 				$usersConfig->get( 'allowUserRegistration' ) );

		$title = JText::_( 'Logout');

		// Set page title
		$document->setTitle( $title );

		// Build logout image if enabled
		if ( $params->get( 'image_'.$type ) != -1 ) {
			$image = '/images/stories/'.$params->get( 'image_'.$type );
			$image = '<img src="'. $image  .'" align="'. $params->get( 'image_'.$type.'_align' ) .'" hspace="10" alt="" />';
		}

		// Get the return URL
		if (!$url = JRequest::getVar('return', '', 'method', 'base64')) {
			$url = base64_encode($params->get($type));
		}

		$errors = JError::getErrors();

		$this->assign('image' , $image);
		$this->assign('type'  , $type);
		$this->assign('return', $url);

		$this->assignRef('params', $params);

		parent::display($tpl);
	}

	/**
	 * Short description for 'attach'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function attach() {}
}

