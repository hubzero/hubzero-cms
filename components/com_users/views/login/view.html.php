<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Login view class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.5
 */
class UsersViewLogin extends JViewLegacy
{
	protected $form;
	protected $params;
	protected $state;
	protected $user;

	/**
	 * Method to display the view.
	 *
	 * @param	string	The template file to include
	 * @since	1.5
	 */
	public function display($tpl = null)
	{
		// Get the view data.
		$this->user		= JFactory::getUser();
		$this->form		= $this->get('Form');
		$this->state	= $this->get('State');
		$this->params	= $this->state->get('params');

		// Make sure we're using a secure connection
		if (!isset( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] == 'off')
		{
			JFactory::getApplication()->redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			die('insecure connection and redirection failed');
		}

		// Get and add the js and extra css to the page
		\Hubzero\Document\Assets::addComponentStylesheet('com_users', 'login.css');
		\Hubzero\Document\Assets::addComponentStylesheet('com_users', 'providers.css');
		\Hubzero\Document\Assets::addComponentScript('com_users', 'assets/js/login');

		\Hubzero\Document\Assets::addSystemStylesheet('uniform.css');
		\Hubzero\Document\Assets::addSystemScript('jquery.uniform');
		\Hubzero\Document\Assets::addSystemScript('jquery.hoverIntent');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Check for layout override
		$active = JFactory::getApplication()->getMenu()->getActive();
		if (isset($active->query['layout'])) {
			$this->setLayout($active->query['layout']);
		}

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		$this->prepareDocument();

		$uri = JURI::getInstance();
		$furl = base64_encode($uri->toString());
		$this->freturn = $furl;

		// HUBzero: If we have a return set with an authenticator in it, we're linking an existing account
		// Parse the return to retrive the authenticator, and remove it from the list below
		$auth = '';
		if ($return = JRequest::getVar('return', null))
		{
			$decoded_return = base64_decode($return);
			$query  = parse_url($decoded_return);
			if (is_array($query) && isset($query['query']))
			{
				$query  = $query['query'];
				$query  = explode('&', $query);
				$auth   = '';
				foreach ($query as $q)
				{
					$n = explode('=', $q);
					if ($n[0] == 'authenticator')
					{
						$auth = $n[1];
					}
				}
			}
		}

		// Set return if is isn't already
		if (is_null($return) && is_object($active))
		{
			$return = $active->params->get('login_redirect_url', JRoute::_('index.php?option=com_members&task=myaccount'));
			$return = base64_encode($return);
		}

		// Figure out whether or not any of our third party auth plugins are turned on
		// Don't include the 'hubzero' plugin, or the $auth plugin as described above
		$multiAuth      = false;
		$plugins        = JPluginHelper::getPlugin('authentication');
		$authenticators = array();
		$remember_me_default = 0;

		foreach ($plugins as $p)
		{
			if ($p->name != 'hubzero' && $p->name != $auth)
			{
				$pparams = new JRegistry($p->params);
				$display = $pparams->get('display_name', ucfirst($p->name));
				$authenticators[] = array('name' => $p->name, 'display' => $display);
				$multiAuth = true;
			}
			else if ($p->name == 'hubzero')
			{
				$pparams = new JRegistry($p->params);
				$remember_me_default = $pparams->get('remember_me_default', 0);
			}
		}

		// Override $multiAuth if authenticator is set to hubzero
		if (JRequest::getWord('authenticator') == 'hubzero')
		{
			$multiAuth = false;
		}

		// Set the return if we have it...
		$this->returnQueryString = (!empty($return)) ? "&return={$return}" : '';

		$this->multiAuth           = $multiAuth;
		$this->return              = $return;
		$this->authenticators      = $authenticators;
		$this->remember_me_default = $remember_me_default;

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
					$this->status = $status;
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

	/**
	 * Prepares the document
	 * @since	1.6
	 */
	protected function prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$user		= JFactory::getUser();
		$login		= $user->get('guest') ? true : false;
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', $login ? JText::_('JLOGIN') : JText::_('JLOGOUT'));
		}

		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}

	function attach()
	{
	}
}
