<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class GroupsHelperPages
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
		$home = new GroupsModelPage(0);
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
		$homeVersion = new GroupsModelPageVersion(0);
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
	public static function getDefaultHomePage( $group )
	{
		// create view object
		$view = new \Hubzero\Component\View(array(
			'name'   => 'pages',
			'layout' => '_view_default'
		));

		// pass vars to view
		$view->juser = JFactory::getUser();
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
		// get current path
		$uri  = JURI::getInstance();

		// get group
		$cn = JRequest::getVar('cn','');
		$group = Hubzero\User\Group::getInstance($cn);

		// use JRoute so in case the hub is /members/groups/... instead of top level /groups
		$base = JRoute::_('index.php?option=com_groups&cn=' .$group->get('cn'));

		// remove /groups/{group_cname} from path
		$path = str_replace($base, '', $uri->getPath());

		// get path segments & clean up
		$segments = explode(DS, trim($path, DS));
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
	public static function isPageApprover( $username = null)
	{
		$username  = (!is_null($username)) ? $username : JFactory::getUser()->get('username');
		return (in_array($username, self::getPageApprovers())) ? true : false;
	}

	/**
	 * Get page approvers
	 *
	 * @return void
	 */
	public static function getPageApprovers()
	{
		$approvers = JComponentHelper::getParams('com_groups')->get('approvers', '');
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
			$profile = \Hubzero\User\Profile::getInstance( $approver );
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
	public static function sendApproveNotification( $type, $object )
	{
		// build title
		$title = JText::sprintf('Page "%s" Requires Approval', $object->get('title'));
		if ($type == 'module')
		{
			$title = JText::sprintf('Module "%s" Requires Approval', $object->get('title'));
		}

		// get approvers w/ emails
		$approvers = self::getPageApproversEmail();

		// get site config
		$jconfig = JFactory::getConfig();

		// subject details
		$subject = $jconfig->getValue('config.sitename') . ' ' . JText::_('Groups') . ', ' . $title;

		// from details
		$from = array(
			'name'  => $jconfig->getValue('config.sitename') . ' ' . JText::_('Groups'),
			'email' => $jconfig->getValue('config.mailfrom')
		);

		// build html email
		$eview = new \Hubzero\Component\View(array(
			'name'   => 'emails',
			'layout' => $type
		));
		$eview->option     = JRequest::getCmd('option', 'com_groups');;
		$eview->controller = JRequest::getCmd('controller', 'groups');
		$eview->group      = \Hubzero\User\Group::getInstance(JRequest::getCmd('cn', JRequest::getCmd('gid')));
		$eview->object     = $object;
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
	public static function sendApprovedNotification( $type, $object )
	{
		// build title
		$title = JText::sprintf('Page "%s" Approved', $object->get('title'));
		if ($type == 'module')
		{
			$title = JText::sprintf('Module "%s" Approved', $object->get('title'));
		}

		// get \Hubzero\User\Group object
		$group = \Hubzero\User\Group::getInstance(JRequest::getCmd('cn', JRequest::getCmd('gid')));

		// array to hold manager emails
		$managers = array();

		// get all manager email addresses
		foreach ($group->get('managers') as $m)
		{
			$profile = \Hubzero\User\Profile::getInstance( $m );
			if ($profile)
			{
				$managers[$profile->get('email')] = $profile->get('name');
			}
		}

		// get site config
		$jconfig = JFactory::getConfig();

		// subject details
		$subject = $jconfig->getValue('config.sitename') . ' ' . JText::_('Groups') . ', ' . $title;

		// from details
		$from = array(
			'name'  => $jconfig->getValue('config.sitename') . ' ' . JText::_('Groups'),
			'email' => $jconfig->getValue('config.mailfrom')
		);

		// build html email
		$eview = new \Hubzero\Component\View(array(
			'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_groups',
			'name'   => 'emails',
			'layout' => $type
		));

		$eview->option     = JRequest::getCmd('option', 'com_groups');;
		$eview->controller = JRequest::getCmd('controller', 'groups');
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
		// get joomla objects
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		// get person who has page checkedout
		$sql = "SELECT * FROM `#__xgroups_pages_checkout`
			    WHERE `userid`<>" . $user->get('id') . " AND `pageid`=" . $db->quote($pageid) . " ORDER BY `when` LIMIT 1";
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
		// get needed joomla objects
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		// check in other pages
		self::checkinForUser();

		// mark page as checked out
		$sql = "INSERT INTO `#__xgroups_pages_checkout` (`pageid`,`userid`,`when`)
			    VALUES(".$db->quote($pageid).",".$db->quote($user->get('id')).", '".JFactory::getDate()->toSql()."');";
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
		// get joomla objects
		$db = JFactory::getDBO();

		// check in page
		$sql = "DELETE FROM `#__xgroups_pages_checkout` WHERE `pageid`=" . $db->quote($pageid);
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
		// get joomla objects
		$user = JFactory::getUser();
		$db   = JFactory::getDBO();

		// check in all pages for this user
		$sql = "DELETE FROM `#__xgroups_pages_checkout` WHERE `userid`=" . $db->quote($user->get('id'));
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
		// get joomla objects
		$db   = JFactory::getDBO();

		// check in all pages for this user
		$sql = "DELETE FROM `#__xgroups_pages_checkout` WHERE `when` < NOW() - INTERVAL 12 HOUR";
		$db->setQuery($sql);
		$db->query();
	}

	/**
	 * Display Group Page
	 *
	 * @param    Object    $group    \Hubzero\User\Group Object
	 * @param    Object    $page     GroupsModelPage Object
	 * @return   String
	 */
	public static function displayPage( $group, $page, $markHit = true )
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
			$view->addTemplatePath(JPATH_ROOT . $base . DS . 'template' . DS . 'pages');
		}

		// get needed vars
		$database    = JFactory::getDBO();
		$juser       = JFactory::getUser();
		$authorized  = GroupsHelperView::authorize($group);
		$version     = ($page) ? $page->approvedVersion() : null;

		// stops from displaying pages that dont exist
		if ($page === null)
		{
			JError::raiseError(404, 'Group Page Not Found');
			return;
		}

		// stops from displaying unpublished pages
		// make sure we have approved version to display
		if ($page->get('state') == $page::APP_STATE_UNPUBLISHED || $version === null)
		{
			// determine which layout to use
			$layout = ($version === null) ? '_view_notapproved' : '_view_unpublished';

			// show unpublished or no version layout
			if ($authorized == 'manager'
				|| \Hubzero\User\Profile::userHasPermissionForGroupAction($group, 'group.pages'))
			{
				$view->setLayout($layout);
				$view->group   = $group;
				$view->page    = $page;
				$view->version = $version;
				return $view->loadTemplate();
			}

			// show 404
			JError::raiseError(404, 'Group Page Not Found');
			return;
		}

		// build page hit object
		// mark page hit
		if ($markHit)
		{
			$groupsTablePageHit = new GroupsTablePageHit( $database );
			$pageHit            = new stdClass;
			$pageHit->gidNumber = $group->get('gidNumber');
			$pageHit->pageid    = $page->get('id');
			$pageHit->userid    = $juser->get('id');
			$pageHit->date      = date('Y-m-d H:i:s');
			$pageHit->ip        = $_SERVER['REMOTE_ADDR'];
			$groupsTablePageHit->save( $pageHit );
		}

		// parse old wiki content
		//$content = self::parseWiki($group, $version->get('content'), $fullparse = true);
		$content = $version->get('content');

		// parse php tags and modules
		$content = self::parse($group, $page, $content);

		// set content
		$version->set('content', trim($content));

		// set vars to view
		$view->juser      = $juser;
		$view->group      = $group;
		$view->page       = $page;
		$view->version    = $version;
		$view->authorized = $authorized;
		$view->config     = JComponentHelper::getParams('com_groups');

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
	public static function parseWiki( $group, $content, $fullparse = true )
	{
		// do we have wiki content that needs parsing?
		if (self::_isWiki($content))
		{
			// create path
			$path = JComponentHelper::getparams( 'com_groups' )->get('uploadpath');

			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'parser.php');

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
			$wikiParser = WikiHelperParser::getInstance();

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
	private static function parse( $group, $page, $document )
	{
		// create new group document helper
		$groupDocument = new GroupsHelperDocument();

		// strip out scripts & php tags if not super group
		if (!$group->isSuperGroup())
		{
			$document = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $document);
			$document = preg_replace('/<\?[\s\S]*?\?>/', '', $document);
		}

		//get config
		$config = JComponentHelper::getParams('com_groups');

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
	public static function generatePreview( $page, $version = 0, $contentOnly = false )
	{
		// get groups
		$gidNumber = $page->get('gidNumber');
		$group     = \Hubzero\User\Group::getInstance($gidNumber);

		//get config
		$config = JComponentHelper::getParams('com_groups');

		// load page version
		$content = $page->version($version)->content('parsed');

		// create new group document helper
		$groupDocument = new GroupsHelperDocument();

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
		$pageCss = GroupsHelperView::GetPageCss($group);

		$css = '';
		foreach ($pageCss as $p)
		{
			$p = rtrim(JURI::root(), DS) . DS . ltrim($p, DS);
			$css .= '<link rel="stylesheet" href="'.$p.'" />';
		}

		// output html
		$html = '<!DOCTYPE html>
				<html>
					<head>
						<title>'.$group->get('description').'</title>
						'.$css.'
					</head>
					<body class="group-page-preview">
						'. $content .'
					</body>
				</html>';

		// return html
		return $html;
	}
}