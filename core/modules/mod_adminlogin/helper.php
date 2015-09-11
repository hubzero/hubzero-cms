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
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\AdminLogin;

use Hubzero\Module\Module;
use Hubzero\Config\Registry;
use Request;
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
		$freturn = base64_encode('index.php?' . Request::getQueryString());

		$returnQueryString = (!empty($return)) ? "&return={$return}" : '';
		$authenticators    = [];
		$plugins           = \Plugin::byType('authentication');

		foreach ($plugins as $p)
		{
			$pparams = new Registry($p->params);

			// Make sure it supports admin login
			if (!$pparams->get('admin_login', false)) continue;

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
			return base64_encode($return);
		}

		return base64_encode('index.php');
	}
}
