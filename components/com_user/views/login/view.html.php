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
		$app        = JFactory::getApplication();
		$user		=& JFactory::getUser();
		$pathway	=& $mainframe->getPathway();
		$image		= '';

		// Make sure we're using a secure connection
		if (!isset( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] == 'off')
		{
			$app->redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			die('insecure connection and redirection failed');
		}

		// Get and add the js and extra css to the page
		$assets        = DS."components".DS."com_user".DS."assets";
		$template      = DS."templates".DS.JFactory::getApplication()->getTemplate().DS."html".DS."com_user".DS;
		$media         = DS."media".DS."system";
		$js            = $assets.DS."js".DS."login.jquery.js";
		$css           = $assets.DS."css".DS."login.css";
		$uniform_js    = $media.DS."js".DS."jquery.uniform.js";
		$uniform_css   = $media.DS."css".DS."uniform.css";
		$providers_css = $assets.DS."css".DS."providers.css";
		if(file_exists(JPATH_BASE . $template . "login.jquery.js"))
		{
			$document->addScript($template . "login.jquery.js");
		}
		elseif(file_exists(JPATH_BASE . $js))
		{
			$document->addScript($js);
		}
		if(file_exists(JPATH_BASE . $template . "login.css"))
		{
			$document->addStyleSheet($template . "login.css");
		}
		elseif(file_exists(JPATH_BASE . $css))
		{
			$document->addStyleSheet($css);
		}
		if(file_exists(JPATH_BASE . $uniform_js))
		{
			$document->addScript($uniform_js);
		}
		if(file_exists(JPATH_BASE . $uniform_css))
		{
			$document->addStyleSheet($uniform_css);
		}
		if(file_exists(JPATH_BASE . $providers_css))
		{
			$document->addStyleSheet($providers_css);
		}

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

		// HUBzero: If we have a return set with an authenticator in it, we're linking an existing account
		// Parse the return to retrive the authenticator, and remove it from the list below
		$auth = '';
		if($return = JRequest::getVar('return', null))
		{
			$return = base64_decode($return);
			$query  = parse_url($return);
			$query  = $query['query'];
			$query  = explode('&', $query);
			$auth   = '';
			foreach($query as $q)
			{
				$n = explode('=', $q);
				if($n[0] == 'authenticator')
				{
					$auth = $n[1];
				}
			}
		}

		// Figure out whether or not any of our third party auth plugins are turned on 
		// Don't include the 'hubzero' plugin, or the $auth plugin as described above
		$multiAuth      = false;
		$plugins        = JPluginHelper::getPlugin('authentication');
		$authenticators = array();

		foreach($plugins as $p)
		{
			if($p->name != 'hubzero' && $p->name != $auth)
			{
				$authenticators[] = $p->name;
				$multiAuth = true;
			}
		}

		// Override $multiAuth if authenticator is set to hubzero
		if(JRequest::getWord('authenticator') == 'hubzero')
		{
			$multiAuth = false;
		}

		// Set the return if we have it...
		$returnUrl = (base64_decode($url) != '/members/myaccount') ? "&return={$url}" : '';

		$this->assign('multiAuth', $multiAuth);
		$this->assign('returnUrl', $returnUrl);
		$this->assign('authenticators', $authenticators);

		// if authenticator is specified call plugin display method, otherwise (or if method does not exist) use default
		
		$authenticator = JRequest::getVar('authenticator', '', 'method');

		JPluginHelper::importPlugin('authentication');

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