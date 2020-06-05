<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Login;

use Hubzero\Module\Module;
use Hubzero\Config\Registry;
use Hubzero\Utility\Uri;
use Request;
use Plugin;
use User;
use App;

/**
 * Module class for displaying a login form
 */
class Helper extends Module
{
	/**
	 * Get the redirect URL
	 *
	 * @param   object  $params  Registry The module options.
	 * @param   string  $type    Type
	 * @return  string
	 */
	static function getReturnURL($params, $type)
	{
		$url = null;
		if ($itemid =  $params->get($type))
		{
			$db = App::get('db');
			$query = $db->getQuery(true);

			$query->select($db->quoteName('link'));
			$query->from($db->quoteName('#__menu'));
			$query->where($db->quoteName('published') . '=1');
			$query->where($db->quoteName('id') . '=' . $db->quote($itemid));

			$db->setQuery($query);
			if ($link = $db->loadResult())
			{
				$url = 'index.php?Itemid=' . $itemid;
			}
		}

		if (!$url)
		{
			// stay on the same page
			$uri = clone Uri::getInstance();
			$vars = $uri->parse($uri->toString());
			unset($vars['lang']);

			if (isset($vars['Itemid']))
			{
				$itemid = $vars['Itemid'];
				$item = App::get('menu')->getItem($itemid);
				unset($vars['Itemid']);
				if (isset($item) && $vars == $item->query)
				{
					$url = 'index.php?Itemid=' . $itemid;
				}
				else
				{
					$url = 'index.php?' . $uri->buildQuery($vars) . '&Itemid=' . $itemid;
				}
			}
			else
			{
				$url = 'index.php?' . $uri->buildQuery($vars);
			}
		}

		return base64_encode($url);
	}

	/**
	 * Get type
	 *
	 * @return  string
	 */
	static function getType()
	{
		return (!User::isGuest()) ? 'logout' : 'login';
	}

	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		// [!] Legacy compatibility for older view overrides
		$params = $this->params;
		$module = $this->module;

		// Make sure we're using a secure connection
		if (!isset( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] == 'off')
		{
			App::redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			die('insecure connection and redirection failed');
		}

		// Get and add the js and extra css to the page
		$this->css('login.css', 'com_login');
		$this->css('providers.css', 'com_login');
		$this->js('login', 'com_login');

		$this->js('jquery.hoverIntent', 'system');

		$type    = self::getType();
		$return  = Request::getString('return', null);

		$uri = \Hubzero\Utility\Uri::getInstance();
		if ($rtrn = $uri->getVar('return'))
		{
			if (preg_match('/[^A-Za-z0-9\+\/\=]/', $rtrn))
			{
				// This isn't a base64 string and most likely is someone trying to do something nasty (XSS)
				$uri->setVar('return', base64_encode($uri->toString(array('path'))));
			}
		}
		$freturn = base64_encode($uri->toString());

		//$freturn = base64_encode($_SERVER['REQUEST_URI']);

		// If we have a return set with an authenticator in it, we're linking an existing account
		// Parse the return to retrive the authenticator, and remove it from the list below
		$auth = '';
		if ($areturn = Request::getString('return', null))
		{
			if (preg_match('/[^A-Za-z0-9\+\/\=]/', $return))
			{
				// This isn't a base64 string and most likely is someone trying to do something nasty (XSS)
				$return = null;
				Request::setVar('return', null);
			}
			else
			{
				$areturn = base64_decode($areturn);
				$query   = parse_url($areturn);
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
		}

		// Figure out whether or not any of our third party auth plugins are turned on
		// Don't include the 'hubzero' plugin, or the $auth plugin as described above
		$multiAuth      = false;
		$plugins        = Plugin::byType('authentication');
		$authenticators = array();

		foreach ($plugins as $p)
		{
			if ($p->name != 'hubzero' && $p->name != $auth)
			{
				$pparams = new Registry($p->params);
				$display = $pparams->get('display_name', ucfirst($p->name));
				$authenticators[$p->name] = array('name' => $p->name, 'display' => $display);
				$multiAuth = true;
			}
			elseif ($p->name == 'hubzero')
			{
				$pparams = new Registry($p->params);
				$remember_me_default = $pparams->get('remember_me_default', 0);
			}
		}

		Plugin::import('authentication');

		// Set the return if we have it...
		$returnQueryString = ($return) ? "&return={$return}" : '';

		require $this->getLayoutPath();
	}
}
