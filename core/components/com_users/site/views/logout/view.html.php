<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * This file incorporates work covered by the following copyright and
 * permission notice:
 *
 * @package   hubzero-cms
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright (c) 2010-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

jimport('joomla.application.component.view');

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
		$image    = '';

		$menu = \App::get('menu');

		$item = $menu->getActive();
		if ($item)
		{
			$params	= $menu->getParams($item->id);
		}
		else
		{
			$params = new \Hubzero\Config\Registry('');
			$template = App::get('template')->template;
			$inifile = App::get('template')->path . DS .  'html' . DS . 'com_user' . DS . 'logout' . DS . 'config.ini';
			if (file_exists($inifile))
			{
				$params->parse(file_get_contents($inifile));
			}

			$params->def('page_title', Lang::txt( 'Logout' ));
		}

		$type = 'logout';

		// Set some default page parameters if not set
		$params->def( 'show_page_title', 1 );
		if (!$params->get( 'page_title'))
		{
			$params->set('page_title', Lang::txt( 'Logout' ));
		}

		if (!$item)
		{
			$params->def( 'header_logout', '' );
		}

		$params->def( 'pageclass_sfx', '' );
		$params->def( 'logout', '/');
		$params->def( 'description_logout', 1 );
		$params->def( 'description_logout_text', Lang::txt( 'LOGOUT_DESCRIPTION' ) );
		$params->def( 'image_logout', 'key.jpg' );
		$params->def( 'image_logout_align', 'right' );
		$usersConfig =  Component::params( 'com_users' );
		$params->def( 'registration', $usersConfig->get( 'allowUserRegistration' ) );

		$title = Lang::txt( 'Logout');

		// Set page title
		Document::setTitle($title);

		// Build logout image if enabled
		if ($params->get( 'image_'.$type) != -1)
		{
			$image = '/images/stories/'.$params->get( 'image_'.$type );
			$image = '<img src="'. $image  .'" align="'. $params->get( 'image_'.$type.'_align' ) .'" hspace="10" alt="" />';
		}

		// Get the return URL
		if (!$url = Request::getVar('return', '', 'method', 'base64'))
		{
			$url = base64_encode($params->get($type));
		}

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
	function attach()
	{
	}
}

