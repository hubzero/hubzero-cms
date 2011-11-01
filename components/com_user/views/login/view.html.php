<?php
/**
* @version		$Id: view.html.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @subpackage	Login
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * User component login view class
 *
 * @package		Joomla
 * @subpackage	Users
 * @since	1.0
 */
class UserViewLogin extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		// Initialize variables
		$document	=& JFactory::getDocument();
		$user		=& JFactory::getUser();
		$pathway	=& $mainframe->getPathway();
		$image		= '';

		$menu   =& JSite::getMenu();
		$item   = $menu->getActive();
		if($item)
			$params	=& $menu->getParams($item->id);
		else {
			$params = new JParameter( '' );
			$template = JFactory::getApplication()->getTemplate();
			$inifile = JPATH_SITE . DS . 'templates' . DS . $template . DS .  'html' . DS . 'com_user' . DS . 'login' . DS . 'config.ini';
			if (file_exists($inifile)) {
				$params->loadINI( file_get_contents($inifile) );
			}

			$params->def('page_title',	JText::_( 'Login' ));
		}

		$type = 'login';

		// Set some default page parameters if not set
		$params->def( 'show_page_title', 				1 );
		if (!$params->get( 'page_title')) {
				$params->set('page_title',	JText::_( 'Login' ));
			}
		if(!$item)
		{
			$params->def( 'header_login', 			'' );
		}

		$params->def( 'pageclass_sfx', 			'' );
		$params->def( 'login', 					'/');
		$params->def( 'description_login', 		1 );
		$params->def( 'description_login_text', 	JText::_( 'LOGIN_DESCRIPTION' ) );
		$params->def( 'image_login', 				'key.jpg' );
		$params->def( 'image_login_align', 			'right' );
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		$params->def( 'registration', 				$usersConfig->get( 'allowUserRegistration' ) );

		$title = JText::_( 'Login');

		// pathway item
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem($title, '' );
		}
		// Set page title
		$document->setTitle( $title );

		// Build login image if enabled
		if ( $params->get( 'image_'.$type ) != -1 ) {
			$image = '/images/stories/'.$params->get( 'image_'.$type );
			$image = '<img src="'. $image  .'" align="'. $params->get( 'image_'.$type.'_align' ) .'" hspace="10" alt="" />';
		}

		// Get the return URL
		if (!$url = JRequest::getVar('return', '', 'method', 'base64')) {
			$url = base64_encode($params->get($type));
		}

		$uri = JURI::getInstance();
		$furl = base64_encode($uri->toString());
		$this->assign('freturn', $furl);

		$errors =& JError::getErrors();

		$this->assign('image' , $image);
		$this->assign('type'  , $type);
		$this->assign('return', $url);

		$this->assignRef('params', $params);

		// if authenticator is specified call plugin display method, otherwise (or if method does not exist) use default
		
		$authenticator = JRequest::getVar('authenticator', '', 'method');

		JPluginHelper::importPlugin('authentication');

		$plugins = JPluginHelper::getPlugin('authentication');

		foreach ($plugins as $plugin)
		{	
			$className = 'plg'.$plugin->type.$plugin->name;

			if (class_exists($className))
			{
				$myplugin = new $className($this,(array)$plugin);

				if (method_exists($className,'status'))
				{
					$status[$plugin->name] = $myplugin->status();
					$this->assign('status', $status);
				}
					
				if ($plugin->name != $authenticator)
					continue;

				if (method_exists($className,'display'))
				{
					$result = $myplugin->display($this, $tpl);

					return $result;
				}
			}
		}

		parent::display($tpl);
	}
	
	function attach() {}
}

