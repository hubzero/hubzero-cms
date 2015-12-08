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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Site\Controllers;

use Hubzero\Component\SiteController;
use Document;
use Pathway;
use Component;
use Request;
use Route;
use Lang;
use User;
use App;

/**
 * Tool classes
 */
include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'tool.php');
include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'version.php');
include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'group.php');
include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'author.php');

/**
 * Resource classes
 */
require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'assoc.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'contributor.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'tags.php');

/**
 * Controller class for contributing a tool
 */
class Resource extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->_authorize();

		// Load the com_resources component config
		$rconfig = Component::params('com_resources');
		$this->rconfig = $rconfig;

		parent::execute();
	}

	/**
	 * Display forms for editing/creating a reosurce
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Incoming
		$alias   = Request::getVar('app', '');
		$version = Request::getVar('editversion', 'dev');
		$step    = Request::getInt('step', 1);

		// Load the tool
		$obj = new \Components\Tools\Tables\Tool($this->database);
		$this->_toolid = $obj->getToolId($alias);

		if (!$this->_toolid)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=pipeline')
			);
			return;
		}

		// make sure user is authorized to go further
		if (!$this->_checkAccess($this->_toolid))
		{
			App::abort(403, Lang::txt('COM_TOOLS_ALERTNOTAUTH'));
			return;
		}

		$nextstep = $step + 1;

		// get tool version (dev or current) information
		$obj->getToolStatus($this->_toolid, $this->_option, $status, $version);

		// get resource information
		$row = new \Components\Resources\Tables\Resource($this->database);
		$row->loadAlias($alias);
		$row->alias = ($row->alias) ? $row->alias : $alias;
		if (!$status['fulltxt'])
		{
			$status['fulltxt'] = $row->fulltxt;
		}

		// process first step
		if ($nextstep == 3 && (isset($_POST['nbtag']) || isset($_POST['fulltxt'])))
		{
			if (!isset($_POST['fulltxt']) || !trim($_POST['fulltxt']))
			{
				$this->setError(Lang::txt('COM_TOOLS_REQUIRED_FIELD_CHECK', 'Abstract'));
				$step = 1;
				$nextstep--;
			}

			$hztv = \Components\Tools\Helpers\Version::getToolRevision($this->_toolid, $version);

			$objV = new \Components\Tools\Tables\Version($this->database);
			if (!$objV->bind($_POST))
			{
				$this->setError($objV->getError());
				return;
			}

			$body = $this->txtClean($_POST['fulltxt']);
			if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $body))
			{
				// Do nothing
				$status['fulltxt'] = trim(stripslashes($body));
			}
			else
			{
				// Wiki format will be used
				$status['fulltxt'] = Request::getVar('fulltxt', $status['fulltxt'], 'post');
			}

			// Get custom areas, add wrapper tags, and compile into fulltxt
			$type = new \Components\Resources\Tables\Type($this->database);
			$type->load($row->type);

			include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');
			$elements = new \Components\Resources\Models\Elements(array(), $type->customFields);
			$schema = $elements->getSchema();

			$fields = array();
			foreach ($schema->fields as $field)
			{
				$fields[$field->name] = $field;
			}

			$nbtag = Request::getVar('nbtag', array());
			$found = array();
			foreach ($nbtag as $tagname => $tagcontent)
			{
				$f = '';

				$status['fulltxt'] .= "\n" . '<nb:' . $tagname . '>';
				if (is_array($tagcontent))
				{
					$c = count($tagcontent);
					$num = 0;
					foreach ($tagcontent as $key => $val)
					{
						if (trim($val))
						{
							$num++;
						}
						$status['fulltxt'] .= '<' . $key . '>' . trim($val) . '</' . $key . '>';
					}
					if ($c == $num)
					{
						$f = 'found';
					}
				}
				else
				{
					$f = trim($tagcontent);
					if ($f)
					{
						$status['fulltxt'] .= trim($tagcontent);
					}
				}
				$status['fulltxt'] .= '</nb:' . $tagname . '>' . "\n";

				if (!$f && isset($fields[$tagname]) && $fields[$tagname]->required)
				{
					$this->setError(Lang::txt('COM_TOOLS_REQUIRED_FIELD_CHECK', $fields[$tagname]->label));
				}

				$found[] = $tagname;
			}

			foreach ($fields as $field)
			{
				if (!in_array($field->name, $found) && $field->required)
				{
					$found[] = $field->name;
					$this->setError(Lang::txt('COM_TOOLS_REQUIRED_FIELD_CHECK', $field->label));
				}
			}

			$hztv->fulltxt     = $objV->fulltxt     = $status['fulltxt'];
			$hztv->description = $objV->description = \Hubzero\Utility\String::truncate(Request::getVar('description', $status['description'], 'post'), 500);
			$hztv->title       = $objV->title       = \Hubzero\Utility\String::truncate(preg_replace('/\s+/', ' ', Request::getVar('title', $status['title'], 'post')), 500);

			if (!$hztv->update())
			{
				throw new Exception(Lang::txt('COM_TOOLS_ERROR_UPDATING_TOOL'), 500);
			}
			else
			{
				// get updated tool status
				$obj->getToolStatus($this->_toolid, $this->_option, $status, $version);
			}

			if ($version == 'dev')
			{
				// update resource page
				$this->updatePage($row->id, $status);
			}
		}

		// Group access
		//$accesses = array('Public', 'Registered', 'Special', 'Protected', 'Private');
		//$lists = array();
		//$lists['access'] = \Components\Tools\Helpers\Html::selectAccess($accesses, $row->access);
		//$groups = \Hubzero\User\Helper::getGroups(User::get('id'), 'members');

		// get authors
		$objA = new \Components\Tools\Tables\Author($this->database);
		$authors = ($version == 'current') ? $objA->getToolAuthors($version, $row->id, $status['toolname']) : array();

		// Tags
		$tags  = Request::getVar('tags', '', 'post');
		$tagfa = Request::getVar('tagfa', '', 'post');

		// Get any HUB focus areas
		// These are used where any resource is required to have one of these tags
		$tconfig = Component::params('com_tags');
		$fa1  = $tconfig->get('focus_area_01');
		$fa2  = $tconfig->get('focus_area_02');
		$fa3  = $tconfig->get('focus_area_03');
		$fa4  = $tconfig->get('focus_area_04');
		$fa5  = $tconfig->get('focus_area_05');
		$fa6  = $tconfig->get('focus_area_06');
		$fa7  = $tconfig->get('focus_area_07');
		$fa8  = $tconfig->get('focus_area_08');
		$fa9  = $tconfig->get('focus_area_09');
		$fa10 = $tconfig->get('focus_area_10');

		// Instantiate our tag object
		$tagcloud = new \Components\Resources\Helpers\Tags($row->id);

		// Normalize the focus areas
		$tagfa1  = $tagcloud->normalize($fa1);
		$tagfa2  = $tagcloud->normalize($fa2);
		$tagfa3  = $tagcloud->normalize($fa3);
		$tagfa4  = $tagcloud->normalize($fa4);
		$tagfa5  = $tagcloud->normalize($fa5);
		$tagfa6  = $tagcloud->normalize($fa6);
		$tagfa7  = $tagcloud->normalize($fa7);
		$tagfa8  = $tagcloud->normalize($fa8);
		$tagfa9  = $tagcloud->normalize($fa9);
		$tagfa10 = $tagcloud->normalize($fa10);

		// process new tags
		if ($tags or $tagfa)
		{
			$newtags = '';
			if ($tagfa)
			{
				$newtags = $tagfa . ', ';
			}
			if ($tags)
			{
				$newtags .= $tags;
			}
			$tagcloud->setTags($newtags, User::get('id'));
		}

		// Get all the tags on this resource
		$tags_men = $tagcloud->tags();
		$mytagarray = array();
		$fas = array($tagfa1, $tagfa2, $tagfa3, $tagfa4, $tagfa5, $tagfa6, $tagfa7, $tagfa8, $tagfa9, $tagfa10);
		$fats = array();
		if ($fa1)
		{
			$fats[$fa1] = $tagfa1;
		}
		if ($fa2)
		{
			$fats[$fa2] = $tagfa2;
		}
		if ($fa3)
		{
			$fats[$fa3] = $tagfa3;
		}
		if ($fa4)
		{
			$fats[$fa4] = $tagfa4;
		}
		if ($fa5)
		{
			$fats[$fa5] = $tagfa5;
		}
		if ($fa6)
		{
			$fats[$fa6] = $tagfa6;
		}
		if ($fa7)
		{
			$fats[$fa7] = $tagfa7;
		}
		if ($fa8)
		{
			$fats[$fa8] = $tagfa8;
		}
		if ($fa9)
		{
			$fats[$fa9] = $tagfa9;
		}
		if ($fa10)
		{
			$fats[$fa10] = $tagfa10;
		}

		// Loop through all the tags and pull out the focus areas - those will be displayed differently
		foreach ($tags_men as $tag_men)
		{
			if (in_array($tag_men->get('tag'), $fas))
			{
				$tagfa = $tag_men->get('tag');
			}
			else
			{
				$mytagarray[] = $tag_men->get('raw_tag');
			}
		}
		$tags = implode(', ', $mytagarray);

		// Set the document title
		$this->view->title = Lang::txt(strtoupper($this->_option)) . ': ' . Lang::txt('COM_TOOLS_EDIT_TOOL_PAGE') . ' (' . $status['toolname'] . ')';
		Document::setTitle($this->view->title);

		// Set the document pathway (breadcrumbs)
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}
		if (Pathway::count() <= 1)
		{
			Pathway::append(
				Lang::txt('COM_TOOLS_STATUS_FOR', $status['toolname']),
				'index.php?option=' . $this->_option . '&controller=pipeline&task=status&app=' . $alias
			);
			Pathway::append(
				Lang::txt('COM_TOOLS_EDIT_TOOL_PAGE'),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&app=' . $alias . '&task=start&step=1'
			);
		}

		$this->view->row     = $row;
		$this->view->step    = $step;
		$this->view->version = $version;
		$this->view->status  = $status;
		$this->view->tags    = $tags;
		$this->view->tagfa   = $tagfa;
		$this->view->fats    = $fats;
		$this->view->authors = $authors;

		// Pass error messages to the view
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output HTML
		$this->view->display();
	}

	/**
	 * Update the associated resource page for this tool
	 *
	 * @param      integer $rid       Resource ID
	 * @param      array   $status    Fields to update
	 * @param      integer $published Published state
	 * @param      integer $newtool   Updating for a new tool?
	 * @return     boolean True if no errors
	 */
	public function updatePage($rid, $status=array(), $published=0, $newtool=0)
	{
		if ($rid === NULL)
		{
			return false;
		}

		$resource = new \Components\Resources\Tables\Resource($this->database);
		$resource->load($rid);
		if (count($status) > 0)
		{
			$resource->fulltxt    = addslashes($status['fulltxt']);
			$resource->introtext   = $status['description'];
			$resource->title       = preg_replace('/\s+/', ' ', $status['title']);
			$resource->modified    = Date::toSql();
			$resource->modified_by = User::get('id');
		}
		if ($published)
		{
			$resource->published = $published;
		}
		if ($newtool && $published == 1)
		{
			$resource->publish_up = Date::toSql();
		}

		if (!$resource->store())
		{
			$this->setError($row->getError());
			return false;
		}
		else if ($newtool)
		{
			\Notify::success(Lang::txt('COM_TOOLS_NOTICE_RES_PUBLISHED'), 'tools');
		}
		else
		{
			\Notify::success(Lang::txt('COM_TOOLS_NOTICE_RES_UPDATED'), 'tools');
		}

		return true;
	}

	/**
	 * Generate a resource page from tool data
	 *
	 * @param      integer $toolid Tool ID
	 * @param      array   $tool   Tool info to generate resource from
	 * @return     mixed False if error, integer if success
	 */
	public function createPage($toolid, $tool)
	{
		$tool['title'] = preg_replace('/\s+/', ' ', $tool['title']);

		$params = array();
		$params[] = 'pageclass_sfx=';
		$params[] = 'show_title=1';
		$params[] = 'show_authors=1';
		$params[] = 'show_assocs=1';
		$params[] = 'show_type=1';
		$params[] = 'show_logicaltype=1';
		$params[] = 'show_rating=1';
		$params[] = 'show_date=1';
		$params[] = 'show_parents=1';
		$params[] = 'series_banner=';
		$params[] = 'show_banner=1';
		$params[] = 'show_footer=3';
		$params[] = 'show_stats=0';
		$params[] = 'st_appname=' . strtolower($tool['toolname']);
		$params[] = 'st_appcaption=' . $tool['title'] . $tool['version'];
		$params[] = 'st_method=com_narwhal';

		// Initiate extended database class
		$row = new \Components\Resources\Tables\Resource($this->database);
		$row->created_by = User::get('id');
		$row->created    = Date::toSql();
		$row->published  = 2;  // draft state
		$row->params     = implode("\n", $params);
		$row->attribs    = 'marknew=0';
		$row->standalone = 1;
		$row->type       = 7;
		$row->title      = $tool['title'];
		$row->introtext  = $tool['description'];
		$row->alias      = $tool['toolname'];
		$row->access     = 0;

		if (!$row->store())
		{
			$this->setError($row->getError());
			return false;
		}

		return $row->id;
	}

	/**
	 * Preview the resource
	 *
	 * @return     void
	 */
	public function previewTask()
	{
		// Incoming
		$alias   = Request::getVar('app', '');
		$version = Request::getVar('editversion', 'dev');
		$rid     = Request::getInt('rid', 0);

		// Load the tool
		$obj = new \Components\Tools\Tables\Tool($this->database);
		$this->_toolid = $obj->getToolId($alias);

		if (!$this->_toolid)
		{
			// not a tool resource page
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=pipeline')
			);
			return;
		}

		// Make sure user is authorized to go further
		if (!$this->_checkAccess($this->_toolid))
		{
			App::abort(403, Lang::txt('COM_TOOLS_ALERTNOTAUTH'));
			return;
		}

		// Get tool version (dev or current) information
		$obj->getToolStatus($this->_toolid, $this->_option, $status, $version);

		// Instantiate our tag object
		$tagcloud = new \Components\Resources\Helpers\Tags($rid);
		$tags  = Request::getVar('tags', '', 'post');
		$tagfa = Request::getVar('tagfa', '', 'post');
		// Process new tags
		$newtags = '';
		if ($tagfa)
		{
			$newtags = $tagfa . ', ';
		}
		if ($tags)
		{
			$newtags .= $tags;
		}
		$tagcloud->setTags($newtags, User::get('id'));

		// Get some needed libraries
		include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');

		// Load the resource object
		$resource = new \Components\Resources\Tables\Resource($this->database);
		$resource->loadAlias($alias);

		if (!User::isGuest())
		{
			$xgroups = \Hubzero\User\Helper::getGroups(User::get('id'), 'all');
			// Get the groups the user has access to
			$usersgroups = $this->_getUsersGroups($xgroups);
		}
		else
		{
			$usersgroups = array();
		}

		// Get updated version
		$objV = new \Components\Tools\Tables\Version($this->database);

		$thistool = $objV->getVersionInfo('', $version, $resource->alias, '');
		$thistool = $thistool ? $thistool[0] : '';

		// Replace resource info with requested version
		$objV->compileResource($thistool, '', $resource, 'dev', $this->rconfig);

		// get language library
		$lang = Lang::getRoot();
		if (!$lang->load(strtolower('com_resources'), JPATH_BASE))
		{
			$this->setError(Lang::txt('COM_TOOLS_ERROR_LOADING_LANGUAGE'));
		}

		// Set the document title
		$this->view->title = Lang::txt(strtoupper($this->_option)) . ': ' . Lang::txt('COM_TOOLS_PREVIEW_TOOL_PAGE') . ' (' . $resource->alias . ')';
		Document::setTitle($this->view->title);

		// Set the document pathway (breadcrumbs)
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}
		if (Pathway::count() <= 1)
		{
			Pathway::append(
				Lang::txt('COM_TOOLS_STATUS_FOR', $thistool->toolname),
				'index.php?option=' . $this->_option . '&controller=pipeline&task=status&app=' . $alias
			);
			Pathway::append(
				Lang::txt('COM_TOOLS_EDIT_TOOL_PAGE'),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&app=' . $alias . '&task=start&step=1'
			);
		}

		$this->view->toolid      = $this->_toolid;
		$this->view->step        = 5;
		$this->view->version     = $version;
		$this->view->resource    = $resource;
		$this->view->config      = $this->rconfig;
		$this->view->usersgroups = $usersgroups;
		$this->view->status      = $status;

		// Pass error messages to the view
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output HTML
		$this->view->display();
	}

	/**
	 * Strip some unwanted and potentially harmful items out of text
	 *
	 * @param      string &$text Text to clean
	 * @return     string
	 */
	public function txtClean(&$text)
	{
		$text = preg_replace('/{kl_php}(.*?){\/kl_php}/s', '', $text);
		$text = preg_replace('/{.+?}/', '', $text);
		$text = preg_replace("'<style[^>]*>.*?</style>'si", '', $text);
		$text = preg_replace("'<script[^>]*>.*?</script>'si", '', $text);
		$text = preg_replace('/<!--.+?-->/', '', $text);
		return $text;
	}

	/**
	 * Push a user's groups' alias to an array for easier searching
	 *
	 * @param      array $groups User's groups
	 * @return     array
	 */
	private function _getUsersGroups($groups)
	{
		$arr = array();
		if (!empty($groups))
		{
			foreach ($groups as $group)
			{
				if ($group->regconfirmed)
				{
					$arr[] = $group->cn;
				}
			}
		}
		return $arr;
	}

	/**
	 * Check if the current user has access to this tool
	 *
	 * @param      unknown $toolid       Tool ID
	 * @param      integer $allowAdmins  Allow admins access?
	 * @param      boolean $allowAuthors Allow authors access?
	 * @return     boolean True if they have access
	 */
	private function _checkAccess($toolid, $allowAdmins=1, $allowAuthors=false)
	{
		// Create a Tool object
		$obj = new \Components\Tools\Tables\Tool($this->database);

		// allow to view if admin
		if ($this->config->get('access-manage-component') && $allowAdmins)
		{
			return true;
		}

		// check if user in tool dev team
		if ($developers = $obj->getToolDevelopers($toolid))
		{
			foreach ($developers as $dv)
			{
				if ($dv->uidNumber == User::get('id'))
				{
					return true;
				}
			}
		}

		// allow access to tool authors
		if ($allowAuthors)
		{
			// Nothing here?
		}

		return false;
	}

	/**
	 * Authorization checks
	 *
	 * @param      string $assetType Asset type
	 * @param      string $assetId   Asset id to check against
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if (User::isGuest())
		{
			return;
		}

		// if no admin group is defined, allow superadmin to act as admin
		// otherwise superadmins can only act if they are also a member of the component admin group
		if (($admingroup = trim($this->config->get('admingroup', ''))))
		{
			// Check if they're a member of admin group
			$ugs = \Hubzero\User\Helper::getGroups(User::get('id'));
			if ($ugs && count($ugs) > 0)
			{
				$admingroup = strtolower($admingroup);
				foreach ($ugs as $ug)
				{
					if (strtolower($ug->cn) == $admingroup)
					{
						$this->config->set('access-manage-' . $assetType, true);
						$this->config->set('access-admin-' . $assetType, true);
						$this->config->set('access-create-' . $assetType, true);
						$this->config->set('access-delete-' . $assetType, true);
						$this->config->set('access-edit-' . $assetType, true);
					}
				}
			}
		}
		else
		{
			$asset  = $this->_option;
			if ($assetId)
			{
				$asset .= ($assetType != 'component') ? '.' . $assetType : '';
				$asset .= ($assetId) ? '.' . $assetId : '';
			}

			$at = '';
			if ($assetType != 'component')
			{
				$at .= '.' . $assetType;
			}

			// Admin
			$this->config->set('access-admin-' . $assetType, User::authorise('core.admin', $asset));
			$this->config->set('access-manage-' . $assetType, User::authorise('core.manage', $asset));
			// Permissions
			$this->config->set('access-create-' . $assetType, User::authorise('core.create' . $at, $asset));
			$this->config->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-' . $assetType, User::authorise('core.edit' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, User::authorise('core.edit.state' . $at, $asset));
			$this->config->set('access-edit-own-' . $assetType, User::authorise('core.edit.own' . $at, $asset));
		}
	}
}
