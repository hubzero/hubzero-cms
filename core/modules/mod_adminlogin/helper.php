<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\AdminLogin;

use Hubzero\Module\Module;
use Hubzero\Config\Registry;
use Request;
use Route;
use Lang;
use Html;
use App;

/**
 * Module class for displaying a login form
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		if (!App::isAdmin())
		{
			return;
		}

		$return  = self::getReturnURI();
		$freturn = base64_encode(Route::url('index.php?' . Request::getQueryString()));

		$returnQueryString = (!empty($return)) ? "&return={$return}" : '';
		$authenticators    = [];
		$plugins           = \Plugin::byType('authentication');

		foreach ($plugins as $p)
		{
			$pparams = new Registry($p->params);

			// Make sure it supports admin login
			if (!$pparams->get('admin_login', false))
			{
				continue;
			}

			// If it's the default hubzero plugin, don't include it in the list (we'll include it separately)
			if ($p->name == 'hubzero')
			{
				$site_display = $pparams->get('display_name', \Config::get('sitename'));
				$basic = true;
			}
			else
			{
				$display = $pparams->get('display_name', ucfirst($p->name));
				$authenticators[$p->name] = array('name' => $p->name, 'display' => $display);
			}
		}

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}

	/**
	 * Get an HTML select list of the available languages.
	 *
	 * @return  string
	 */
	public static function getLanguageList()
	{
		$languages = array();
		$languages = Lang::getList(null, PATH_APP . DS . 'bootstrap' . DS . App::get('client')->name, false, true);
		array_unshift($languages, Html::select('option', '', Lang::txt('JDEFAULT')));

		return Html::select('genericlist', $languages, 'lang', ' class="inputbox"', 'value', 'text', null);
	}

	/**
	 * Get the redirect URI after login.
	 *
	 * @return  string
	 */
	public static function getReturnURI()
	{
		$return = 'index.php?' . Request::getQueryString();
		if ($return != 'index.php?option=com_login')
		{
			return base64_encode(Route::url($return));
		}

		return base64_encode(Route::url('index.php'));
	}
}
