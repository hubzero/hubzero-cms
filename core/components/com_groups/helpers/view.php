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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers;

use Components\Groups\Models\Page\Archive;
use Exception;
use Filesystem;
use Request;
use Event;
use Route;
use Lang;
use User;
use App;

class View
{
	private static $_tab              = null;
	private static $_sections         = null;
	private static $_sections_content = null;

	/**
	 * Get Active Group Tab
	 *
	 * @param    $group    \Hubzero\User\Group Object
	 * @return   string
	 */
	public static function getTab($group)
	{
		//do we already have an instance of tab?
		if (!isset(self::$_tab))
		{
			//get request vars
			$tab = Request::getVar('active', 'overview');

			//get group plugin access
			$pluginAccess = \Hubzero\User\Group\Helper::getPluginAccess($group);

			// If active tab not overview and an not one of available tabs
			if ($tab != 'overview' && !in_array($tab, array_keys($pluginAccess)))
			{
				$tab = 'overview';
			}

			//set instance var
			self::$_tab = $tab;
		}

		//return active tab
		return self::$_tab;
	}

	/**
	 * Get group plugins
	 *
	 * @return   array
	 */
	public static function getSections()
	{
		//do we already have an instance of categories?
		if (!isset(self::$_sections))
		{
			// Trigger the functions that return the areas we'll be using
			// then add overview to array
			$sections = Event::trigger('groups.onGroupAreas', array());
			array_unshift($sections, array(
				'name'             => 'overview',
				'title'            => 'Overview',
				'default_access'   => 'anyone',
				'display_menu_tab' => true
			));

			//set instance variable
			self::$_sections = $sections;
		}

		//return sections
		return self::$_sections;
	}


	/**
	 * Get "Before" group content
	 *
	 * @param    $group         \Hubzero\User\Group Object
	 * @param    $authorized    Authorization level
	 * @return   array
	 */
	public static function getBeforeSectionsContent($group)
	{
		// are we authorized
		$authorized = self::authorize($group);

		//get before group content
		$beforeGroupContent = Event::trigger('groups.onBeforeGroup', array(
			$group,
			$authorized
		));

		//return any before content
		return $beforeGroupContent;
	}


	/**
	 * Get group plugins sections
	 *
	 * @return   array
	 */
	public static function getSectionsContent($group)
	{
		//do we already have an instance of sections?
		if (!isset(self::$_sections_content))
		{
			// are we authorized
			$authorized = self::authorize($group);

			//get active tab
			$tab = self::getTab($group);

			//get reqest vars
			$action = Request::getVar('action', '');
			$limit  = Request::getInt('limit', 15);
			$start  = Request::getInt('limitstart', 0);

			//get group plugin access
			$pluginAccess = \Hubzero\User\Group\Helper::getPluginAccess($group);

			// Limit the records if we're on the overview page
			$limit = ($limit == 0) ? 'all' : $limit;

			// Get the sections
			$sectionsContent = Event::trigger('groups.onGroup', array(
					$group,
					'com_groups',
					$authorized,
					$limit,
					$start,
					$action,
					$pluginAccess,
					array($tab)
				)
			);

			// Push the overview content to the array of sections we're going to output
			// Empty now, gets set later so we can have some needed vars for super groups
			array_unshift($sectionsContent, array('html' => '', 'metadata' => ''));

			//set instance var
			self::$_sections_content = $sectionsContent;
		}

		//return categories
		return self::$_sections_content;
	}


	/**
	 * Display Active Tab Title, next to group name
	 *
	 * @param    $group    \Hubzero\User\Group Object
	 * @return   string
	 */
	public static function displayTab($group)
	{
		//get group categories
		$sections = self::getSections();

		//get active tab
		$tab = self::getTab($group);

		// If a sub-page of the overview tab
		$active = strtolower(Request::getVar('active', 'overview'));
		if ($tab == 'overview' && $tab != $active)
		{
			$pageArchive = Archive::getInstance();
			$pages = $pageArchive->pages('list', array(
				'gidNumber' => $group->get('gidNumber'),
				'state'     => array(0,1),
				'orderby'   => 'lft ASC'
			));

			// fetch the active page
			$activePage = Pages::getActivePage($group, $pages);

			if ($activePage)
			{
				return $activePage->get('title');
			}
		}

		//return title of active tab
		foreach ($sections as $section)
		{
			if ($tab == $section['name'])
			{
				return $section['title'];
			}
		}
	}

	/**
	 * Display group menu
	 *
	 * @return    string
	 */
	public static function displaySections($group, $classOrId = 'id="page_menu"')
	{
		// create view object
		$view = new \Hubzero\Component\View(array(
			'name'   => 'groups',
			'layout' => '_menu'
		));

		// get group pages if any
		// only get published items that arent set as the home page
		$pageArchive = Archive::getInstance();
		$pages = $pageArchive->pages('tree', array(
			'gidNumber' => $group->get('gidNumber'),
			'state'     => array(1),
			'orderby'   => 'lft ASC'
		), true);

		// pass vars to view
		$view->group           = $group;
		$view->user            = User::getRoot();
		$view->classOrId       = $classOrId;
		$view->tab             = self::getTab($group);
		$view->sections        = self::getSections();
		$view->sectionsContent = self::getSectionsContent($group);
		$view->pages           = $pages;
		$view->pluginAccess    = \Hubzero\User\Group\Helper::getPluginAccess($group);

		// return template
		return $view->loadTemplate();
	}

	/**
	 * Output menu
	 * 
	 * @param  [type] $pageArray [description]
	 * @return [type]            [description]
	 */
	public static function buildRecursivePageMenu($group, $pageArray)
	{
		// get overview section access
		$access = \Hubzero\User\Group\Helper::getPluginAccess($group, 'overview');

		$out = '';
		if (sizeof($pageArray) > 0)
		{
			$out = '<ul>';
			foreach ($pageArray as $key => $page)
			{
				// dont show page links if there isnt an approved version
				if ($page->approvedVersion() === null)
				{
					continue;
				}

				// page access settings
				$pageAccess = ($page->get('privacy') == 'default') ? $access : $page->get('privacy');

				// is this the active page?
				$cls  = (Pages::isPageActive($page)) ? 'active' : '';

				//page menu item
				if (($pageAccess == 'registered' && User::isGuest()) ||
				  ($pageAccess == 'members' && !in_array(User::get("id"), $group->get('members'))))
				{
					$out .= "<li class=\"protected\"><span class=\"page\">" . $page->get('title') . "</span></li>";
				}
				else
				{
					$out .= '<li class="' . $cls . '">';
					$out .= '<a class="page" title="' . $page->get('title') . '" href="' . $page->url() . '">' . $page->get('title') . '</a>';
				}

				// do we have child menu items
				if (!is_array($page->get('children')))
				{
					$out .= '</li>';
				}
				else
				{
					$out .= self::buildRecursivePageMenu($group, $page->get('children')) . '</li>';
				}
			}
			$out .= '</ul>';
		}
		return $out;
	}

	/**
	 * Display "Before" group content
	 *
	 * @param    $group         \Hubzero\User\Group Object
	 * @param    $authorized    Authorization level
	 * @return   void
	 */
	public static function displayBeforeSectionsContent($group)
	{
		//get before content
		$beforeGroupContent = self::getBeforeSectionsContent($group);

		//echo before group content
		foreach ($beforeGroupContent as $bgc)
		{
			echo $bgc;
		}
	}


	/**
	 * Display group content based on sections and active tab
	 *
	 * @return    string
	 */
	public static function displaySectionsContent($group, $overviewSection = null)
	{
		// create view object
		$view = new \Hubzero\Component\View(array(
			'name'   => 'groups',
			'layout' => '_content'
		));

		// need objects
		$content    = '';
		$tab        = self::getTab($group);
		$categories = self::getSections();
		$sections   = self::getSectionsContent($group);

		// add overview section to sections
		if ($overviewSection !== null)
		{
			$sections[0]['html'] = $overviewSection;
		}

		// set content for tab
		foreach ($categories as $k => $cat)
		{
			if ($tab == $cat['name'])
			{
				$content = $sections[$k]['html'];
			}
		}

		//get true tab
		$trueTab = Request::getVar('active', 'overview');

		// do overview page checks
		if ($tab == 'overview' && $trueTab != 'login')
		{
			//user has access to page
			$userHasAccess = true;

			//get overview page access
			$overviewPageAccess = \Hubzero\User\Group\Helper::getPluginAccess($group, 'overview');

			//if user isnt logged in and access level is set to registered users or members only
			if (User::isGuest() && ($overviewPageAccess == 'registered' || $overviewPageAccess == 'members'))
			{
				$userHasAccess = false;
			}

			// if the user is not a group member or site admin
			if (!in_array(User::get('id'), $group->get('members')) && $overviewPageAccess == 'members')
			{
				$userHasAccess = false;
			}

			//if user does not have access
			if (!$userHasAccess)
			{
				// if the group is not supposed to be discoverable throw 404
				if ($group->get('discoverability') == 1)
				{
					App::abort(404, Lang::txt('Group Access Denied'));
					return;
				}

				// return message letting user know they dont have access
				$content = '<p class="info">' . Lang::txt('You do not have the permissions to access this group page.') . '</p>';
			}
		}

		// pass vars to view
		$view->group   = $group;
		$view->content = $content;

		// return template
		return $view->loadTemplate();
	}


	/**
	 * Display group toolbar
	 *
	 * @return    string
	 */
	public static function displayToolbar($group, $classOrId = 'id="group_options"', $displayLogoutLink = false)
	{
		// create view object
		$view = new \Hubzero\Component\View(array(
			'name'   => 'groups',
			'layout' => '_toolbar'
		));

		// pass vars to view
		$view->group      = $group;
		$view->user       = User::getRoot();
		$view->classOrId  = $classOrId;
		$view->logoutLink = $displayLogoutLink;

		// return template
		return $view->loadTemplate();
	}

	/**
	 * Get group page templates
	 *
	 * @return    array
	 */
	public static function getPageTemplates($group)
	{
		// array to hold page templates
		$pageTemplates = array();

		// make sure we only get templates for super groups
		if (!$group->isSuperGroup())
		{
			return $pageTemplates;
		}

		// load com_groups params to get group folder path
		$params = Component::params('com_groups');
		$base = $params->get('uploadpath', '/site/groups');
		$base = DS . trim($base, DS) . DS . $group->get('gidNumber') . DS . 'template';

		// get all php files in template directory
		$files = Filesystem::files(PATH_APP . $base, '\\.php', false, true);

		// check to see if any of our files are page templates
		foreach ($files as $file)
		{
			// dont include the default template (default.php or index.php)
			if (strpos($file, 'default.php') !== false ||
				strpos($file, 'index.php') !== false)
			{
				continue;
			}

			// get file contents
			$contents = file_get_contents($file);

			// if template is defined
			if (preg_match('|Template Name:(.*)$|mi', $contents, $header))
			{
				$tmpl  = trim($header[1]);
				$parts = explode(DS, $file);
				$file  = array_pop($parts);
				$pageTemplates[$tmpl] = $file;
			}
		}

		// return page templates
		return $pageTemplates;
	}

	/**
	 * Get list of stylesheets for page
	 *
	 * @return     array
	 */
	public static function getPageCss($group)
	{
		// load stylesheets from specific group first
		$url = rtrim(str_replace('administrator', '', Request::base()), '/') . '/groups/' . $group->get('cn');
		$stylesheets = self::stylesheetsForUrl($url);

		// if we got nothing back lets get styles from groups intro page
		if (empty($stylesheets))
		{
			$url  = rtrim(str_replace('administrator', '', Request::base()), '/') . '/groups';
			$stylesheets = self::stylesheetsForUrl($url);
		}

		return $stylesheets;
	}

	/**
	 * Get Stylesheets for URL (with cURL)
	 * 
	 * @param  string    $url
	 * @return array
	 */
	private static function stylesheetsForUrl($url)
	{
		if ($stylesheets = \Cache::get('groups.' . $url))
		{
			return $stylesheets;
		}

		// get contents of main group page
		// we need to get all css files loaded on this page.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$html = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		// make sure it was a success
		if ($info['http_code'] != '200')
		{
			return array();
		}

		// load html through dom document object
		$domDocument = new \DOMDocument();
		// Since HTML 5 doctype has no DTD to check, the DOM will attempt to
		// use HTML4 TRansitional which will throw errors on valid HTML5
		// entities (e.g., header, footer, section). So, we temporarily
		// suppress errors.
		libxml_use_internal_errors(true);
		$domDocument->loadHTML($html);
		libxml_use_internal_errors(false);

		// parse reseults as xml and use xpath to get list of stylesheets
		$domDocumentXml = simplexml_import_dom($domDocument);
		$ss = $domDocumentXml->xpath('//link[@rel="stylesheet"]');

		// array to hold stylesheets for ckeditor
		$stylesheets = array();
		foreach ($ss as $s)
		{
			if ($s->attributes()->media == 'print')
			{
				continue;
			}
			$stylesheets[] = (string) $s->attributes()->href;
		}

		\Cache::put('groups.' . $url, $stylesheets, 15);

		//return stylesheets
		return $stylesheets;
	}

	/**
	 * Check User Authorization
	 *
	 * @return    string
	 */
	public static function authorize($group, $checkOnlyMembership = true)
	{
		//check to see if they are a site admin
		if (!$checkOnlyMembership && User::authorise('core.admin', 'com_groups'))
		{
			return 'admin';
		}

		//check to see if they are a group manager
		if (in_array(User::get('id'), $group->get('managers')))
		{
			return 'manager';
		}

		//check to see if they are a group member
		if (in_array(User::get('id'), $group->get('members')))
		{
			return 'member';
		}

		//return false if they are none of the above
		return false;
	}

	/**
	 * Attach Custom Error Handler/Page if we can
	 *
	 * @return     array
	 */
	public static function attachCustomErrorHandler($group)
	{
		// are we a super group?
		// and do we have an error template?
		if (!$group->isSuperGroup() || !Template::hasTemplate($group, 'error'))
		{
			return;
		}

		// attach custom error handler
		set_exception_handler(array('\Components\Groups\Helpers\View', 'handleCustomError'));
	}

	/**
	 * Custom Error Callback, Builds custom error page for super groups
	 *
	 * @return     array
	 */
	public static function handleCustomError(Exception $error)
	{
		$lerror = new \Hubzero\Error\Exception\LegacyException($error->getMessage(), $error->getCode(), null, null, $error->getTrace());
		$lerror->set('line', $error->getLine());
		$lerror->set('file', $error->getFile());
		//$lerror = new \Hubzero\Error\Exception\LegacyException($error->getMessage(), $error->getCode(), $error);

		// get error template
		// must wrap in output buffer to capture contents since returning content through output method returns to the
		// method that called handleSuperGroupError with call_user_func
		ob_start();
		$template = new Template();
		$template->set('group', \Hubzero\User\Group::getInstance(Request::getVar('cn', '')))
			     ->set('tab', Request::getVar('active','overview'))
			     ->set('error', $lerror)
			     ->parse()
			     ->render();

		// output content
		$template->output(true);
		$errorTemplate = ob_get_clean();

		// bootstrap document
		// add custom error template as component buffer
		$document = App::get('document');
		$document->addStylesheet('/core/plugins/system/debug/assets/css/debug.css');
		$document->addStylesheet('/core/components/com_groups/site/assets/css/groups.css');
		$document->setBuffer($errorTemplate, array('type'=>'component', 'name' => ''));
		$fullTemplate = $document->render(false, array(
			'template'  => 'system',
			'file'      => 'group.php',
			'directory' => PATH_CORE . DS . 'templates',
			'baseurl'   => rtrim(\Request::root(true), '/') . '/core'
		));

		// echo to screen
		$response = App::get('response');
		$response->headers->set('Cache-Control', 'no-cache', false);
		$response->headers->set('Pragma', 'no-cache');
		$response->headers->set('Content-Type', 'text/html');
		$response->headers->set('status', $lerror->getCode() . ' ' . str_replace("\n", ' ', $lerror->getMessage()));
		$response->setContent($fullTemplate);
		$response->send();

		App::close();
	}

	/**
	 * Display Super Group Login
	 *
	 * @return     array
	 */
	public static function superGroupLogin($group)
	{
		// if user is already logged in go to
		if (!User::isGuest())
		{
			App::redirect(
				Route::url('index.php?option=com_groups&cn=' . $group->get('cn')),
				Lang::txt('COM_GROUPS_VIEW_ALREADY_LOGGED_IN', User::get('name'), User::get('email')),
				'warning'
			);
		}

		// create view object
		$view = new \Hubzero\Component\View(array(
			'name'   => 'pages',
			'layout' => '_view_login'
		));

		// if super group add super group folder
		// to available paths
		if ($group->isSuperGroup())
		{
			$base = $group->getBasePath();
			$view->addTemplatePath(PATH_APP . $base . DS . 'template');
		}

		return $view->loadTemplate();
	}

	/**
	 * Display Super Group Components
	 *
	 * @return     array
	 */
	public static function superGroupComponents($group, $tab = '')
	{
		// var to hold component content
		$componentContent = null;

		// make sure this is a super group
		if (!$group->isSuperGroup())
		{
			return $componentContent;
		}

		// get group upload path
		$uploadPath = Component::params('com_groups')->get('uploadpath');

		// build path to group component
		$templateComponentFolder = PATH_APP . DS . trim($uploadPath, DS) . DS . $group->get('gidNumber') . DS . 'components' . DS . 'com_' . $tab;
		$templateComponentFile   = $templateComponentFolder . DS . $tab . '.php';

		// do we have a group component?
		if (!is_dir($templateComponentFolder) || !file_exists($templateComponentFile))
		{
			return $componentContent;
		}

		// define path to group comonent
		define('JPATH_GROUPCOMPONENT', $templateComponentFolder);

		// Call plugin to capture super group component route segments
		Event::trigger('onBeforeRenderSuperGroupComponent', array());

		// include and render component
		ob_start();
		include $templateComponentFile;
		$componentContent = ob_get_contents();
		ob_end_clean();

		// create view object
		$view = new \Hubzero\Component\View(array(
			'name'   => 'pages',
			'layout' => '_view_component'
		));

		// if super group add super group folder
		// to available paths
		if ($group->isSuperGroup())
		{
			$base = $group->getBasePath();
			$view->addTemplatePath(PATH_APP . $base . DS . 'template');
		}

		$view->content = $componentContent;
		return $view->loadTemplate();
	}

	/**
	 * Display Super Group Pages
	 *
	 * @return     array
	 */
	public static function superGroupPhpPages($group)
	{
		// var to hold content
		$phpPageContent = null;

		// make sure this is a super group
		if (!$group->isSuperGroup())
		{
			return $phpPageContent;
		}

		// get URI path
		$path = Request::path();
		$path = trim(str_replace('groups' . DS . $group->get('cn'), '', $path), DS);

		// make sure we have a path. if no path means were attempting to access the home page
		if ($path == '')
		{
			$path = 'overview';
		}

		// get group upload path
		$uploadPath = Component::params('com_groups')->get('uploadpath');

		// build path to php page in template
		$templatePhpPagePath = PATH_APP . DS . trim($uploadPath, DS) . DS . $group->get('gidNumber') . DS . 'pages' . DS . $path . '.php';

		// if the file is not a valid path
		if (!is_file($templatePhpPagePath))
		{
			return $phpPageContent;
		}

		// include & render php file
		ob_start();
		include $templatePhpPagePath;
		$phpPageContent = ob_get_contents();
		ob_end_clean();

		//create new group document helper
		$groupDocument = new Document();

		// set group doc needed props
		// parse and render content
		$groupDocument->set('group', $group)
			          ->set('page', null)
			          ->set('document', $phpPageContent)
			          ->parse()
			          ->render();

		// get doc content
		$phpPageContent = $groupDocument->output();

		// run as closure to ensure no $this scope
		$eval = function() use ($phpPageContent)
		{
			ob_start();
			eval("?>$phpPageContent<?php ");
			$document = ob_get_clean();
			return $document;
		};
		$phpPageContent = $eval();

		// create view object
		$view = new \Hubzero\Component\View(array(
			'name'   => 'pages',
			'layout' => '_view_php'
		));

		// if super group add super group folder
		// to available paths
		if ($group->isSuperGroup())
		{
			$base = $group->getBasePath();
			$view->addTemplatePath(PATH_APP . $base . DS . 'template');
		}

		$view->content = $phpPageContent;
		return $view->loadTemplate();
	}
}