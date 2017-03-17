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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;

require_once PATH_CORE . '/components/com_menus/admin/helpers/menus.php';
require_once PATH_CORE . '/components/com_languages/admin/helpers/multilangstatus.php';

/**
 * Language Filter Plugin
 */
class plgSystemLanguageFilter extends \Hubzero\Plugin\Plugin
{
	/**
	 * SEF mode
	 *
	 * @var  bool
	 */
	protected static $mode_sef;

	/**
	 * Language tag
	 *
	 * @var  string
	 */
	protected static $tag;

	/**
	 * SEFs
	 *
	 * @var  array
	 */
	protected static $sefs;

	/**
	 * Language codes
	 *
	 * @var  array
	 */
	protected static $lang_codes;

	/**
	 * Homes
	 *
	 * @var  array
	 */
	protected static $homes;

	/**
	 * Default language
	 *
	 * @var  string
	 */
	protected static $default_lang;

	/**
	 * Default SEF
	 *
	 * @var  string
	 */
	protected static $default_sef;

	/**
	 * Cookie
	 *
	 * @var  string
	 */
	protected static $cookie;

	/**
	 * User language code
	 *
	 * @var  string
	 */
	private static $_user_lang_code;

	/**
	 * Constructor
	 *
	 * @param   object  $subject
	 * @param   array   $config
	 * @return  void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Ensure that constructor is called one time
		self::$cookie = SID == '';

		if (!self::$default_lang)
		{
			$router = App::get('router');

			if (App::isSite())
			{
				// setup language data
				self::$mode_sef     = true;
				self::$sefs         = Lang::available('sef');
				self::$lang_codes   = Lang::available('lang_code');
				self::$default_lang = Component::params('com_languages')->get('site', 'en-GB');
				self::$default_sef  = self::$lang_codes[self::$default_lang]->sef;
				self::$homes        = MultilangstatusHelper::getHomepages();

				$levels = User::getAuthorisedViewLevels();
				foreach (self::$sefs as $sef => &$language)
				{
					if (isset($language->access) && $language->access && !in_array($language->access, $levels))
					{
						unset(self::$sefs[$sef]);
					}
				}

				App::forget('language.filter');
				App::set('language.filter', true);
				$uri = Hubzero\Utility\Uri::getInstance();

				if (self::$mode_sef)
				{
					// Get the route path from the request.
					$path = substr($uri->toString(), strlen($uri->base()));

					// Apache mod_rewrite is Off
					$path = Config::get('sef_rewrite') ? $path : substr($path, 10);

					// Trim any spaces or slashes from the ends of the path and explode into segments.
					$path  = trim($path, '/ ');
					$parts = explode('/', $path);

					// The language segment is always at the beginning of the route path if it exists.
					$sef = $uri->getVar('lang');

					if (!empty($parts) && empty($sef))
					{
						$sef = reset($parts);
					}
				}
				else
				{
					$sef = $uri->getVar('lang');
				}
				if (isset(self::$sefs[$sef]))
				{
					$lang_code = self::$sefs[$sef]->lang_code;
					// Create a cookie
					$cookie_domain = Config::get('cookie_domain', '');
					$cookie_path   = Config::get('cookie_path', '/');
					setcookie(App::hash('language'), $lang_code, $this->getLangCookieTime(), $cookie_path, $cookie_domain);
					// set the request var
					Request::setVar('language', $lang_code);
				}
			}

			parent::__construct($subject, $config);

			// 	Detect browser feature
			if (App::isSite())
			{
				//$app->setDetectBrowser($this->params->get('detect_browser', '1')=='1');
			}
		}
	}

	/**
	 * Event hook after system has been initialized
	 *
	 * @return  void
	 */
	public function onAfterInitialise()
	{
		App::set('menu_associations', $this->params->get('menu_associations', 0));

		if (App::isSite())
		{
			self::$tag = Lang::getTag();

			$router = App::get('router');
			// attach build rules for language SEF
			$router->attachBuildRule(array($this, 'buildRule'));

			// attach parse rules for language SEF
			$router->attachParseRule(array($this, 'parseRule'));

			// Adding custom site name
			$languages = Lang::available('lang_code');
			if (isset($languages[self::$tag]) && $languages[self::$tag]->sitename)
			{
				Config::set('sitename', $languages[self::$tag]->sitename);
			}
		}
	}

	/**
	 * Build rule
	 *
	 * @param   object  $router
	 * @param   object  $uri
	 * @return  void
	 */
	public function buildRule(&$router, &$uri)
	{
		$sef = $uri->getVar('lang');
		if (empty($sef))
		{
			$sef = self::$lang_codes[self::$tag]->sef;
		}
		elseif (!isset(self::$sefs[$sef]))
		{
			$sef = self::$default_sef;
		}

		$Itemid = $uri->getVar('Itemid');
		if (!is_null($Itemid))
		{
			if ($item = App::get('menu')->getItem($Itemid))
			{
				if ($item->home && $uri->getVar('option')!='com_search')
				{
					$link  = $item->link;
					$parts = parse_url($link);
					if (isset ($parts['query']) && strpos($parts['query'], '&amp;'))
					{
						$parts['query'] = str_replace('&amp;', '&', $parts['query']);
					}
					parse_str($parts['query'], $vars);

					// test if the url contains same vars as in menu link
					$test = true;
					foreach ($uri->getQuery(true) as $key=>$value)
					{
						if (!in_array($key, array('format', 'Itemid', 'lang')) && !(isset($vars[$key]) && $vars[$key] == $value))
						{
							$test = false;
							break;
						}
					}
					if ($test)
					{
						foreach ($vars as $key=>$value)
						{
							$uri->delVar($key);
						}
						$uri->delVar('Itemid');
					}
				}
			}
			else
			{
				$uri->delVar('Itemid');
			}
		}

		if (self::$mode_sef)
		{
			$uri->delVar('lang');
			if (
				$this->params->get('remove_default_prefix', 0) == 0 ||
				$sef != self::$default_sef ||
				$sef != self::$lang_codes[self::$tag]->sef ||
				$this->params->get('detect_browser', 1) && Lang::detect() != self::$tag && !self::$cookie
			)
			{
				$uri->setPath($uri->getPath() . '/' . $sef . '/');
			}
			else
			{
				$uri->setPath($uri->getPath());
			}
		}
		else
		{
			$uri->setVar('lang', $sef);
		}
	}

	/**
	 * Parse rule
	 *
	 * @param   object  $router
	 * @param   object  $uri
	 * @return  void
	 */
	public function parseRule(&$router, &$uri)
	{
		$array = array();
		$lang_code = Request::getString(App::hash('language'), null , 'cookie');

		// No cookie - let's try to detect browser language or use site default
		if (!$lang_code)
		{
			if ($this->params->get('detect_browser', 1))
			{
				$lang_code = Lang::detect();
			}
			else
			{
				$lang_code = self::$default_lang;
			}
		}
		if (self::$mode_sef)
		{
			$path = $uri->getPath();
			$parts = explode('/', $path);

			$sef = $parts[0];

			// Redirect only if not in post
			$post = Request::get('POST');
			if (!empty($lang_code) && (Request::method() != 'POST' || count($post) == 0))
			{
				if ($this->params->get('remove_default_prefix', 0) == 0)
				{
					// redirect if sef does not exists
					if (!isset(self::$sefs[$sef]))
					{
						// Use the current language sef or the default one
						$sef = isset(self::$lang_codes[$lang_code]) ? self::$lang_codes[$lang_code]->sef : self::$default_sef;
						$uri->setPath($sef . '/' . $path);

						if (Config::get('sef_rewrite'))
						{
							App::redirect($uri->base() . $uri->toString(array('path', 'query', 'fragment')));
						}
						else
						{
							$path = $uri->toString(array('path', 'query', 'fragment'));
							App::redirect($uri->base() . 'index.php' . ($path ? ('/' . $path) : ''));
						}
					}
				}
				else
				{
					// redirect if sef does not exists and language is not the default one
					if (!isset(self::$sefs[$sef]) && $lang_code != self::$default_lang)
					{
						$sef = isset(self::$lang_codes[$lang_code]) && empty($path) ? self::$lang_codes[$lang_code]->sef : self::$default_sef;
						$uri->setPath($sef . '/' . $path);

						if (Config::get('sef_rewrite'))
						{
							App::redirect($uri->base() . $uri->toString(array('path', 'query', 'fragment')));
						}
						else
						{
							$path = $uri->toString(array('path', 'query', 'fragment'));
							App::redirect($uri->base() . 'index.php' . ($path ? ('/' . $path) : ''));
						}
					}
					// redirect if sef is the default one
					elseif (isset(self::$sefs[$sef]) &&
						self::$default_lang == self::$sefs[$sef]->lang_code &&
						(!$this->params->get('detect_browser', 1) || Lang::detect() == self::$tag || self::$cookie)
					)
					{
						array_shift($parts);
						$uri->setPath(implode('/' , $parts));

						if (Config::get('sef_rewrite'))
						{
							App::redirect($uri->base() . $uri->toString(array('path', 'query', 'fragment')));
						}
						else
						{
							$path = $uri->toString(array('path', 'query', 'fragment'));
							App::redirect($uri->base() . 'index.php' . ($path ? ('/' . $path) : ''));
						}
					}
				}
			}

			$lang_code = isset(self::$sefs[$sef]) ? self::$sefs[$sef]->lang_code : '';
			if ($lang_code && Lang::exists($lang_code))
			{
				array_shift($parts);
				$uri->setPath(implode('/', $parts));
			}
		}
		else
		{
			$sef = $uri->getVar('lang');
			if (!isset(self::$sefs[$sef]))
			{
				$sef = isset(self::$lang_codes[$lang_code]) ? self::$lang_codes[$lang_code]->sef : self::$default_sef;
				$uri->setVar('lang', $sef);
				$post = Request::get('POST');
				if (Request::method() != "POST" || count($post) == 0)
				{
					App::redirect(Request::base(true) . '/index.php?' . $uri->getQuery());
				}
			}
		}

		$array = array('lang' => $sef);
		return $array;
	}

	/**
	 * before store user method
	 *
	 * Method is called before user data is stored in the database
	 *
	 * @param   array    $user   Holds the old user data.
	 * @param   boolean  $isnew  True if a new user is stored.
	 * @param   array    $new    Holds the new user data.
	 * @return  void
	 */
	public function onUserBeforeSave($user, $isnew, $new)
	{
		if ($this->params->get('automatic_change', '1') =='1' && array_key_exists('params', $user))
		{
			$registry = new Hubzero\Config\Registry($user['params']);

			self::$_user_lang_code = $registry->get('language');

			if (empty(self::$_user_lang_code))
			{
				self::$_user_lang_code = self::$default_lang;
			}
		}
	}

	/**
	 * after store user method
	 *
	 * Method is called after user data is stored in the database
	 *
	 * @param   array    $user     Holds the new user data.
	 * @param   boolean  $isnew    True if a new user is stored.
	 * @param   boolean  $success  True if user was succesfully stored in the database.
	 * @param   string   $msg      Message.
	 * @return  void
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		if ($this->params->get('automatic_change', '1')=='1' && key_exists('params', $user) && $success)
		{
			$registry = new Hubzero\Config\Registry($user['params']);

			$lang_code = $registry->get('language');
			if (empty($lang_code))
			{
				$lang_code = self::$default_lang;
			}

			if ($lang_code == self::$_user_lang_code || !isset(self::$lang_codes[$lang_code]))
			{
				if (App::isSite())
				{
					User::setState('com_users.edit.profile.redirect', null);
				}
			}
			else
			{
				if (App::isSite())
				{
					User::setState('com_users.edit.profile.redirect', 'index.php?Itemid=' . App::get('menu')->getDefault($lang_code)->id . '&lang=' . self::$lang_codes[$lang_code]->sef);
					self::$tag = $lang_code;
					// Create a cookie
					$cookie_domain = Config::get('cookie_domain', '');
					$cookie_path   = Config::get('cookie_path', '/');
					setcookie(App::hash('language'), $lang_code, $this->getLangCookieTime(), $cookie_path, $cookie_domain);
				}
			}
		}
	}

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @param   array    $user     Holds the user data
	 * @param   array    $options  Array holding options (remember, autoregister, group)
	 * @return  boolean  True on success
	 */
	public function onUserLogin($user, $options = array())
	{
		$menu = App::get('menu');

		if (App::isSite() && $this->params->get('automatic_change', 1))
		{
			// Load associations
			$assoc = App::has('menu_associations') ? App::get('menu_associations') : 0;

			if ($assoc)
			{
				$active = $menu->getActive();
				if ($active)
				{
					$associations = MenusHelper::getAssociations($active->id);
				}
			}

			$lang_code = $user['language'];
			if (empty($lang_code))
			{
				$lang_code = self::$default_lang;
			}
			if ($lang_code != self::$tag)
			{
				// Change language
				self::$tag = $lang_code;

				// Create a cookie
				$cookie_domain = Config::get('cookie_domain', '');
				$cookie_path   = Config::get('cookie_path', '/');
				setcookie(App::hash('language'), $lang_code, $this->getLangCookieTime(), $cookie_path, $cookie_domain);

				// Change the language code
				Lang::setLanguage($lang_code);

				// Change the redirect (language have changed)
				if (isset($associations[$lang_code]) && $menu->getItem($associations[$lang_code]))
				{
					$itemid = $associations[$lang_code];
					User::setState('users.login.form.return', 'index.php?&Itemid=' . $itemid);
				}
				else
				{
					$itemid = isset(self::$homes[$lang_code]) ? self::$homes[$lang_code]->id : self::$homes['*']->id;
					User::setState('users.login.form.return', 'index.php?&Itemid=' . $itemid);
				}
			}
		}
	}

	/**
	 * This method adds alternate meta tags for associated menu items
	 *
	 * @return  nothing
	 */
	public function onAfterDispatch()
	{
		if (App::isSite() && $this->params->get('alternate_meta') && Document::getType() == 'html')
		{
			// Get active menu item
			$active = App::get('menu')->getActive();
			if (!$active)
			{
				return;
			}

			// Get menu item link
			if (Config::get('sef'))
			{
				$active_link = Route::url('index.php?Itemid=' . $active->id, false);
			}
			else
			{
				$active_link = Route::url($active->link . '&Itemid=' . $active->id, false);
			}
			if ($active_link == Request::base(true) . '/')
			{
				$active_link .= 'index.php';
			}

			// Get current link
			$current_link = Request::getUri();
			if ($current_link == Request::base(true) . '/')
			{
				$current_link .= 'index.php';
			}

			// Check the exact menu item's URL
			if ($active_link == $current_link)
			{
				// Get menu item associations
				require_once PATH_CORE . '/components/com_menus/admin/helpers/menus.php';
				$associations = MenusHelper::getAssociations($active->id);

				// Remove current menu item
				unset($associations[$active->language]);

				// Associated menu items in other languages
				if ($associations && $this->params->get('menu_associations'))
				{
					$menu   = App::get('menu');
					$server = Hubzero\Utility\Uri::getInstance()->toString(array('scheme', 'host', 'port'));

					foreach (Lang::available() as $language)
					{
						if (isset($associations[$language->lang_code]))
						{
							$item = $menu->getItem($associations[$language->lang_code]);
							if ($item && Lang::exists($language->lang_code))
							{
								if (Config::get('sef'))
								{
									$link = Route::url('index.php?Itemid='.$associations[$language->lang_code].'&lang='.$language->sef);
								}
								else
								{
									$link = Route::url($item->link.'&Itemid='.$associations[$language->lang_code].'&lang='.$language->sef);
								}

								// Check if language is the default site language and remove url language code is on
								if ($language->sef == self::$default_sef && $this->params->get('remove_default_prefix') == '1')
								{
									$relLink = preg_replace('|/' . $language->sef . '/|', '/', $link, 1);
									Document::addHeadLink($server . $relLink, 'alternate', 'rel', array('hreflang' => $language->lang_code));
								}
								else
								{
									Document::addHeadLink($server . $link, 'alternate', 'rel', array('hreflang' => $language->lang_code));
								}
							}
						}
					}
				}
				// Homepages in other languages
				elseif ($active->home)
				{
					$menu   = App::get('menu');
					$server = Hubzero\Utility\Uri::getInstance()->toString(array('scheme', 'host', 'port'));

					foreach (Lang::available() as $language)
					{
						$item = $menu->getDefault($language->lang_code);
						if ($item && $item->language != $active->language && $item->language != '*' && JLanguage::exists($language->lang_code))
						{
							if (Config::get('sef'))
							{
								$link = Route::url('index.php?Itemid='.$item->id.'&lang='.$language->sef);
							}
							else
							{
								$link = Route::url($item->link.'&Itemid='.$item->id.'&lang='.$language->sef);
							}

							// Check if language is the default site language and remove url language code is on
							if ($language->sef == self::$default_sef && $this->params->get('remove_default_prefix') == '1')
							{
								$relLink = preg_replace('|/' . $language->sef . '/|', '/', $link, 1);
								Document::addHeadLink($server . $relLink, 'alternate', 'rel', array('hreflang' => $language->lang_code));
							}
							else
							{
								Document::addHeadLink($server . $link, 'alternate', 'rel', array('hreflang' => $language->lang_code));
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Getting the Language Cookie settings
	 *
	 * @return  string  The cookie time.
	 */
	private function getLangCookieTime()
	{
		$lang_cookie = 0;

		if ($this->params->get('lang_cookie', 1) == 1)
		{
			$lang_cookie = time() + 365 * 86400;
		}

		return $lang_cookie;
	}
}
