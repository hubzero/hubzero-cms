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

use Components\Groups\Models\Page;
use Components\Groups\Tables\PageHit;
use Component;
use Request;
use Config;
use Route;
use User;
use Lang;
use App;
use stdClass;

include_once __DIR__ . DS . 'permissions.php';

class Pages
{
	/**
	 * Build default home page object,
	 * Check to see if group have a home page override
	 *
	 * @param    object    $group    \Hubzero\User\Group Object
	 * @param    array     $pages    \Hubzero\Base\ItemList
	 * @return   object
	 */
	public static function addHomePage($group, $pages = null)
	{
		// check to see if we have a home page override
		if ($pages->fetch('home', 1) !== null)
		{
			$home = $pages->fetch('home', 1);
			$home->set('alias', 'overview');
			return $pages;
		}

		// create page object
		$home = new Page(0);
		$home->set('id', 0)
			 ->set('gidNumber', $group->get('gidNumber'))
			 ->set('title', 'Home')
			 ->set('alias', 'overview')
			 ->set('ordering', 0)
			 ->set('state', 1)
			 ->set('privacy', 'default')
			 ->set('home', 1)
			 ->set('parent', 0);

		// create page version object
		$homeVersion = new Page\Version(0);
		$homeVersion->set('pageid', 0)
					->set('version', 1)
					->set('approved', 1)
					->set('content', self::getDefaultHomePage($group));

		// add the version to home page object
		$home->versions()->add($homeVersion);

		// add default home page to view
		$pages->add($home);

		// return updated pages
		return $pages;
	}

	/**
	 * Get Default Home Page
	 *
	 * @param    Object    $group    \Hubzero\User\Group Object
	 * @return   String
	 */
	public static function getDefaultHomePage($group)
	{
		// create view object
		$view = new \Hubzero\Component\View(array(
			'name'   => 'pages',
			'layout' => '_view_default'
		));

		// pass vars to view
		$view->user  = User::getInstance();
		$view->group = $group;

		// get group desc
		$view->publicDesc  = $view->group->getDescription('parsed', 0, 'public');
		$view->privateDesc = $view->group->getDescription('parsed', 0, 'private');

		// make sure we have a public desc
		if ($view->publicDesc == '')
		{
			$view->publicDesc = $view->group->get('description');
		}

		// return template
		return $view->loadTemplate();
	}

	/**
	 * Is page Active based on current segment
	 *
	 * @param  [type]  $page [description]
	 * @return boolean       [description]
	 */
	public static function isPageActive($page)
	{
		$segments = self::getCurrentPathSegments();
		return $page->get('alias') == array_pop($segments);
	}

	/**
	 * Get Current Route Segments
	 *
	 * @return [type] [description]
	 */
	private static function getCurrentPathSegments()
	{
		// get group
		$cn = Request::getVar('cn','');
		$group = \Hubzero\User\Group::getInstance($cn);

		// use Route so in case the hub is /members/groups/... instead of top level /groups
		$base = Route::url('index.php?option=com_groups&cn=' . $group->get('cn'));

		if (preg_match("/^\/?groups\/(.*?)/i", Request::path()))
		{
			// remove /groups/{group_cname} from path
			$path = trim(str_replace($base, '', Request::path()), '/');

			if ($path == 'index.php')
			{
				return array();
			}

			$segments = explode('/', $path);
		}
		else
		{
			// Try to find a menu item
			$item = App::get('menu')->getActive();
			// Strip the menu route from the URI
			$up = ltrim(Request::path(), '/');
			$path = trim(substr($up, strlen($item->route)), '/');
			// Check if the first segment is the group name.
			// Due to the way some paths are built, a menu item can result
			// in a path like /menualias/cn/page instead of /menualias/page
			$segments = explode('/', $path);
			if (isset($segments[0]) && $segments[0] == $cn)
			{
				array_shift($segments);
			}
		}

		// get path segments & clean up
		$segments = array_filter($segments);

		// return path segments
		return $segments;
	}

	/**
	 * Get True Group Tab
	 *
	 * Since we trick the overview tab to allow for displaying group pages,
	 * php pages, group login, or group components, this allows us to get the true active tab
	 * for use with displaying the correct content
	 *
	 * @param    $group    \Hubzero\User\Group Object
	 * @return   string
	 */
	public static function getActivePage($group, $pages = array())
	{
		// get path segments
		$segments = self::getCurrentPathSegments();

		// vars to hold page objs
		$page = null;
		$temp = $pages->filter(function($page)
		{
			if ($page->get('alias') == 'overview'
				&& $page->get('depth') == 0)
			{
				return $page;
			}
		});
		$prevPage = $homePage = $temp[0];

		// if we dont have segments that means were on the
		// overview page
		if (count($segments) == 0
			|| $segments[0] == 'overview')
		{
			return $homePage;
		}

		// loop through each segment to to get right page
		foreach ($segments as $k => $segment)
		{
			// make sure a page was found
			foreach ($pages as $p)
			{
				if ($p->get('alias') == $segment
					&& $p->get('depth') == ($k + 1))
				{
					$page = $p;
				}
			}

			// make sure we have page
			// make sure page is child of parent
			if (!$page || $page->get('parent') != $prevPage->get('id'))
			{
				return null;
			}

			// hold on to the page
			$prevPage = $page;
		}

		// return page object
		return $page;
	}

	/**
	 * Is Current User a Page Approver?
	 *
	 * @return void
	 */
	public static function isPageApprover($username = null)
	{
		$username  = (!is_null($username)) ? $username : User::get('username');
		return (in_array($username, self::getPageApprovers())) ? true : false;
	}

	/**
	 * Get page approvers
	 *
	 * @return void
	 */
	public static function getPageApprovers()
	{
		$approvers = Component::params('com_groups')->get('approvers', '');
		return array_map("trim", explode(',', $approvers));
	}

	/**
	 * Get page approvers Emails and names
	 * (used for emailing purposes)
	 *
	 * @return void
	 */
	public static function getPageApproversEmail()
	{
		$emails    = array();
		$approvers = self::getPageApprovers();

		foreach ($approvers as $approver)
		{
			$profile = User::getInstance($approver);
			if ($profile)
			{
				$emails[$profile->get('email')] = $profile->get('name');
			}
		}

		return $emails;
	}

	/**
	 * Send mail to page approvers
	 *
	 * @param     $type      type of object needing approval
	 * @param     $object    object needing approval
	 * @return    void
	 */
	public static function sendApproveNotification($type, $object)
	{
		// build title
		$title = Lang::txt('Page "%s" Requires Approval', $object->get('title'));
		if ($type == 'module')
		{
			$title = Lang::txt('Module "%s" Requires Approval', $object->get('title'));
		}

		// get approvers w/ emails
		$approvers = self::getPageApproversEmail();

		// subject details
		$subject = Config::get('sitename') . ' ' . Lang::txt('Groups') . ', ' . $title;

		// from details
		$from = array(
			'name'  => Config::get('sitename') . ' ' . Lang::txt('Groups'),
			'email' => Config::get('mailfrom')
		);

		// build html email
		$eview = new \Hubzero\Mail\View(array(
			'name'   => 'emails',
			'layout' => $type . '_plain'
		));
		$eview->option     = Request::getCmd('option', 'com_groups');;
		$eview->controller = Request::getCmd('controller', 'groups');
		$eview->group      = \Hubzero\User\Group::getInstance(Request::getCmd('cn', Request::getCmd('gid')));
		$eview->object     = $object;

		$plain = $eview->loadTemplate(false);
		$plain = str_replace("\n", "\r\n", $plain);

		$eview->setLayout($type);
		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// create new message
		$message = new \Hubzero\Mail\Message();

		// build message object and send
		$message->setSubject($subject)
				->addFrom($from['email'], $from['name'])
				->setTo($approvers)
				->addHeader('X-Mailer', 'PHP/' . phpversion())
				->addHeader('X-Component', 'com_groups')
				->addHeader('X-Component-Object', $type . '_approval')
				->addPart($plain, 'text/plain')
				->addPart($html, 'text/html')
				->send();
	}

	/**
	 * Send mail that page has been approved
	 *
	 * @param     $type      type of object just approved
	 * @param     $object    object approved
	 * @return    void
	 */
	public static function sendApprovedNotification($type, $object)
	{
		// build title
		$title = Lang::txt('Page "%s" Approved', $object->get('title'));
		if ($type == 'module')
		{
			$title = Lang::txt('Module "%s" Approved', $object->get('title'));
		}

		// get \Hubzero\User\Group object
		$group = \Hubzero\User\Group::getInstance(Request::getCmd('cn', Request::getCmd('gid')));

		// array to hold manager emails
		$managers = array();

		// get all manager email addresses
		foreach ($group->get('managers') as $m)
		{
			$profile = User::getInstance($m);
			if ($profile)
			{
				$managers[$profile->get('email')] = $profile->get('name');
			}
		}

		// subject details
		$subject = Config::get('sitename') . ' ' . Lang::txt('Groups') . ', ' . $title;

		// from details
		$from = array(
			'name'  => Config::get('sitename') . ' ' . Lang::txt('Groups'),
			'email' => Config::get('mailfrom')
		);

		// build html email
		$eview = new \Hubzero\Component\View(array(
			'base_path' => dirname(__DIR__) . DS . 'site',
			'name'      => 'emails',
			'layout'    => $type
		));

		$eview->option     = Request::getCmd('option', 'com_groups');;
		$eview->controller = Request::getCmd('controller', 'groups');
		$eview->group      = $group;
		$eview->object     = $object;
		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// create new message
		$message = new \Hubzero\Mail\Message();

		// build message object and send
		$message->setSubject($subject)
				->addFrom($from['email'], $from['name'])
				->setTo($managers)
				->addHeader('X-Mailer', 'PHP/' . phpversion())
				->addHeader('X-Component', 'com_groups')
				->addHeader('X-Component-Object', $type . '_approved')
				->addPart($html, 'text/html')
				->send();
	}

	/**
	 * Get code flags
	 *
	 * @return void
	 */
	public static function getCodeFlags()
	{
		return array(
			'php' => array(
				'minor'    => array(),
				'elevated' => array(
					'include',
					'require',
					'call_user_func',
					'curl',
					'chgrp',
					'chmod',
					'file_put_contents',
					'file_get_contents',
					'lchgrp',
					'lchown',
					'link',
					'mkdir',
					'move_uploaded_file',
					'rename',
					'rmdir',
					'symlink',
					'tempnam',
					'touch',
					'unlink'
				),
				'severe'   => array(
					'die',
					'exit',
					'exec',
					'dl',
					'show_source',
					'apache_',
					'closelog',
					'debugger_',
					'define_syslog_variables',
					'escapeshellarg',
					'escapeshellcmd',
					'openlog',
					'passthru',
					'pclose',
					'pcntl_exec',
					'popen',
					'proc_',
					'shell_exec',
					'syslog',
					'system',
					'url_exec',
					'assert',
					'posix_',
					'phpinfo',
					'eval',
					'define_syslog_variables',
					'fp',
					'fput',
					'ftp_',
					'ini_',
					'inject_code',
					'mysql_',
					'php_uname',
					'phpAds_',
					'system',
					'xmlrpc_entity_decode',
				),
			),
			'mysql' => array(
				'minor'    => array(),
				'elevated' => array(),
				'severe'   => array(
					'drop',
					'rename',
					'truncate',
					'delete'
				)
			)
		);
	}

	/**
	 * Get page checkout details
	 *
	 * @param    $pageid    Id of page to get info
	 * @return   object
	 */
	public static function getCheckout($pageid)
	{
		$db = \App::get('db');

		// get person who has page checkedout
		$sql = "SELECT * FROM `#__xgroups_pages_checkout` WHERE `userid`<>" . User::get('id') . " AND `pageid`=" . $db->quote((int) $pageid) . " ORDER BY `when` LIMIT 1";
		$db->setQuery($sql);
		return $db->loadObject();
	}

	/**
	 * Checkout Page
	 *
	 * @param    $pageid    Id of page to get info
	 * @return   object
	 */
	public static function checkout($pageid)
	{
		$db = \App::get('db');

		// check in other pages
		self::checkinForUser();

		// mark page as checked out
		$sql = "INSERT INTO `#__xgroups_pages_checkout` (`pageid`,`userid`,`when`)
			    VALUES(" . $db->quote((int) $pageid) . "," . $db->quote((int) User::get('id')) . ", " . $db->quote(Date::toSql()) . ");";
		$db->setQuery($sql);
		$db->query();
	}

	/**
	 * Checkin Page
	 *
	 * @param    $pageid    Id of page to get info
	 * @return   object
	 */
	public static function checkin($pageid)
	{
		$db = \App::get('db');

		// check in page
		$sql = "DELETE FROM `#__xgroups_pages_checkout` WHERE `pageid`=" . $db->quote((int) $pageid);
		$db->setQuery($sql);
		$db->query();
	}

	/**
	 * Checkin all pages for user
	 *
	 * @return   object
	 */
	public static function checkinForUser()
	{
		$db = \App::get('db');

		// check in all pages for this user
		$sql = "DELETE FROM `#__xgroups_pages_checkout` WHERE `userid`=" . $db->quote((int) User::get('id'));
		$db->setQuery($sql);
		$db->query();
	}


	/**
	 * Checkin in all abondoned checkouts
	 *
	 * @return   object
	 */
	public static function checkinAbandoned()
	{
		$db = \App::get('db');

		// check in all pages for this user
		$sql = "DELETE FROM `#__xgroups_pages_checkout` WHERE `when` < NOW() - INTERVAL 12 HOUR";
		$db->setQuery($sql);
		$db->query();
	}

	/**
	 * Display Group Page
	 *
	 * @param    Object    $group    \Hubzero\User\Group Object
	 * @param    Object    $page     \Components\Groups\Models\Page Object
	 * @return   String
	 */
	public static function displayPage($group, $page, $markHit = true)
	{
		// create view object
		$view = new \Hubzero\Component\View(array(
			'name'   => 'pages',
			'layout' => '_view'
		));

		// if super group add super group folder
		// to available paths
		if ($group->isSuperGroup())
		{
			$base = $group->getBasePath();
			$view->addTemplatePath(PATH_APP . $base . DS . 'template' . DS . 'pages');
		}

		// get needed vars
		$database    = \App::get('db');
		$authorized  = \Components\Groups\Helpers\View::authorize($group);
		$version     = ($page) ? $page->approvedVersion() : null;

		// stops from displaying pages that dont exist
		if ($page === null)
		{
			App::abort(404, Lang::txt('Group Page Not Found'));
			return;
		}

		// stops from displaying unpublished pages
		// make sure we have approved version to display
		if ($page->get('state') == $page::APP_STATE_UNPUBLISHED || $version === null)
		{
			// determine which layout to use
			$layout = ($version === null) ? '_view_notapproved' : '_view_unpublished';

			// show unpublished or no version layout
			if ($authorized == 'manager' || Permissions::userHasPermissionForGroupAction($group, 'group.pages'))
			{
				$view->setLayout($layout);
				$view->group   = $group;
				$view->page    = $page;
				$view->version = $version;
				return $view->loadTemplate();
			}

			// show 404
			App::abort(404, Lang::txt('Group Page Not Found'));
			return;
		}

		// build page hit object
		// mark page hit
		if ($markHit)
		{
			$groupsTablePageHit = new PageHit($database);
			$pageHit            = new stdClass;
			$pageHit->gidNumber = $group->get('gidNumber');
			$pageHit->pageid    = $page->get('id');
			$pageHit->userid    = User::get('id');
			$pageHit->date      = date('Y-m-d H:i:s');
			$pageHit->ip        = $_SERVER['REMOTE_ADDR'];
			$groupsTablePageHit->save($pageHit);
		}

		// parse old wiki content
		//$content = self::parseWiki($group, $version->get('content'), $fullparse = true);
		$content = $version->get('content', '<p class="warning">' . Lang::txt('COM_GROUPS_PAGES_PAGE_NO_CONTENT') . '</p>');

		// parse php tags and modules
		$content = self::parse($group, $page, $content);

		// set content
		$version->set('content', trim($content));

		// set vars to view
		$view->user       = User::getInstance();
		$view->group      = $group;
		$view->page       = $page;
		$view->version    = $version;
		$view->authorized = $authorized;
		$view->config     = Component::params('com_groups');

		// return rendered template
		return $view->loadTemplate();
	}

	/**
	 * Parse Wiki content
	 *
	 * @param    Object    $group        \Hubzero\User\Group Object
	 * @param    String    $content      Content to parse
	 * @param    BOOL      $fullparse    Fully parse wiki content
	 * @return   String
	 */
	public static function parseWiki($group, $content, $fullparse = true)
	{
		// do we have wiki content that needs parsing?
		if (self::_isWiki($content))
		{
			// create path
			$path = Component::params('com_groups')->get('uploadpath');

			include_once(Component::path('com_wiki') . DS . 'helpers' . DS . 'parser.php');

			// build wiki config
			$wikiConfig = array(
				'option'   => 'com_groups',
				'scope'    => '',
				'pagename' => $group->get('cn'),
				'pageid'   => 0,
				'filepath' => $path . DS . $group->get('gidNumber') . DS . 'uploads',
				'domain'   => $group->get('cn')
			);

			// create wiki parser
			$wikiParser = \Components\Wiki\Helpers\Parser::getInstance();

			// parse content
			$content = $wikiParser->parse("\n" . $content, $wikiConfig, $fullparse);
		}

		//return content
		return $content;
	}

	/**
	 * Function to determine if content contains wiki syntax
	 *
	 * @param  [type]  $content [description]
	 * @return boolean          [description]
	 */
	private static function _isWiki($content)
	{
		// trim content
		$content = trim($content);

		// First, remove <pre> tags
		//   This is in case the content is HTML but contains a block of
		//   sample wiki markup.
		$content = preg_replace('/<pre>(.*?)<\/pre>/i', '', $content);

		// If wiki <pre> syntax is found
		if ((strstr($content, '{{{') && strstr($content, '}}}')) || strstr($content, '#!html'))
		{
			return true;
		}

		// If wiki bold syntax is found (highly unlikely HTML content will contain this string)
		if (preg_match('/\'\'\'(.*?)\'\'\'/i', $content) || preg_match('/===(.*?)===/i', $content))
		{
			return true;
		}

		// If no HTML tags found ...
		if (!preg_match('/^(<([a-z]+)[^>]*>.+<\/([a-z]+)[^>]*>|<(\?|%|([a-z]+)[^>]*).*(\?|%|)>)/is', $content))
		{
			return true;
		}
		return false;
	}

	/**
	 *  Parse Page Includes & php
	 *
	 * @return 		void
	 */
	private static function parse($group, $page, $document)
	{
		// create new group document helper
		$groupDocument = new Document();

		// strip out scripts & php tags if not super group
		$params = new \Hubzero\Config\Registry($group->params);

		if (!$group->isSuperGroup() && !$params->get('page_trusted', 0))
		{
			$document = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $document);
			$document = preg_replace('/<\?[\s\S]*?\?>/', '', $document);
		}

		//get config
		$config = Component::params('com_groups');

		// are we allowed to display group modules
		if (!$group->isSuperGroup() && !$config->get('page_modules', 0))
		{
			$groupDocument->set('allowed_tags', array());
		}

		// set group doc needed props
		// parse and render content
		$groupDocument->set('group', $group)
			          ->set('page', $page)
			          ->set('document', $document)
			          ->parse()
			          ->render();

		// get doc content
		$document = $groupDocument->output();

		// if there is PHP were safe to assume its not Wiki content
		$formatHandler = '';
		if (strpos($document, '<?php') !== false)
		{
			$formatHandler = '<!-- {FORMAT:HTML} -->';
		}

		// only parse php if Super Group
		if ($group->isSuperGroup())
		{
			// run as closure to ensure no $this scope
			$eval = function() use ($document)
			{
				ob_start();
				unset($this);
				eval("?>$document<?php ");
				$document = ob_get_clean();
				return $document;
			};
			$document = $eval();
		}

		// return content
		// add HTML format handler. Basically if php is in content lets make sure wiki handler doesnt touch it.
		// addresses cases where html an html tag not the first string in the content block
		return $formatHandler . $document;
	}


	/**
	 * Generate Group Page Preview
	 *
	 * @param    $page   Group page object
	 * @return   void
	 */
	public static function generatePreview($page, $version = 0, $contentOnly = false)
	{
		// get groups
		$gidNumber = $page->get('gidNumber');
		$group     = \Hubzero\User\Group::getInstance($gidNumber);

		//get config
		$config = Component::params('com_groups');

		// load page version
		$content = $page->version($version)->content('parsed');

		// create new group document helper
		$groupDocument = new Document();

		// strip out scripts & php tags if not super group
		if (!$group->isSuperGroup())
		{
			$content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
			$content = preg_replace('/<\?[\s\S]*?\?>/', '', $content);
		}

		// are we allowed to display group modules
		if (!$group->isSuperGroup() && !$config->get('page_modules', 0))
		{
			$groupDocument->set('allowed_tags', array());
		}

		// set group doc needed props
		// parse and render content
		$groupDocument->set('group', $group)
			          ->set('page', $page)
			          ->set('document', $content)
			          ->parse()
			          ->render();

		// get doc content
		$content = $groupDocument->output();

		// only parse php if Super Group
		if ($group->isSuperGroup())
		{
			// run as closure to ensure no $this scope
			$eval = function() use ($content)
			{
				ob_start();
				unset($this);
				eval("?>$content<?php ");
				$content = ob_get_clean();
				return $content;
			};
			$content = $eval();
		}

		// do we want to retun only content?
		if ($contentOnly)
		{
			return $content;
		}

		// get group css
		$pageCss = View::getPageCss($group);

		$css = '';
		foreach ($pageCss as $p)
		{
			$p = rtrim(Request::root(), '/') . '/' . ltrim($p, '/');
			$css .= '<link rel="stylesheet" href="' . $p . '" />';
		}

		// output html
		$html = '<!DOCTYPE html>
				<html>
					<head>
						<title>' . $group->get('description') . '</title>
						' . $css . '
						<style>#system-debug { display: none !important; }</style>
					</head>
					<body class="group-page-preview">
						' . $content . '
					</body>
				</html>';

		// return html
		return $html;
	}
}
