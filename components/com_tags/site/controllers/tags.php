<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Tags\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Tags\Models\Cloud;
use Components\Tags\Models\Tag;
use Hubzero\Utility\String;
use Hubzero\Utility\Sanitize;
use Exception;
use Request;
use Pathway;
use Config;
use Event;
use Route;
use Lang;
use User;

/**
 * Controller class for tags
 */
class Tags extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->_authorize();

		$this->registerTask('feed.rss', 'feed');
		$this->registerTask('feedrss', 'feed');

		if (($tagstring = urldecode(trim(Request::getVar('tag', '', 'request', 'none', 2)))))
		{
			if (!Request::getVar('task', ''))
			{
				Request::setVar('task', 'view');
			}
		}

		parent::execute();
	}

	/**
	 * Display the main page for this component
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Set the page title
		$this->_buildTitle(null);

		// Set the pathway
		$this->_buildPathway(null);

		$this->view
			->set('cloud', new Cloud())
			->set('config', $this->config)
			->display();
	}

	/**
	 * View items tagged with this tag
	 *
	 * @return  void
	 */
	public function viewTask()
	{
		// Incoming
		$tagstring = urldecode(trim(Request::getVar('tag', '', 'request', 'none', 2)));

		$addtag = trim(Request::getVar('addtag', ''));

		// Ensure we were passed a tag
		if (!$tagstring && !$addtag)
		{
			if (Request::getWord('task', '', 'get'))
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option)
				);
				return;
			}
			throw new Exception(Lang::txt('COM_TAGS_NO_TAG'), 404);
		}

		if ($tagstring)
		{
			// Break the string into individual tags
			$tgs = explode(',', $tagstring);
			$tgs = array_map('trim', $tgs);
		}
		else
		{
			$tgs = array();
		}

		// See if we're adding any tags to the search list
		if ($addtag && !in_array($addtag, $tgs))
		{
			$tgs[] = $addtag;
		}

		// Sanitize the tag
		$tags  = array();
		$added = array();
		$rt    = array();
		foreach ($tgs as $tag)
		{
			// Load the tag
			$tagobj = Tag::getInstance($tag);

			if (in_array($tagobj->get('tag'), $added))
			{
				continue;
			}

			$added[] = $tagobj->get('tag');

			// Ensure we loaded the tag's info from the database
			if ($tagobj->exists())
			{
				$tags[] = $tagobj;
				$rt[]   = $tagobj->get('raw_tag');
			}
		}

		// Ensure we loaded the tag's info from the database
		if (empty($tags))
		{
			throw new Exception(Lang::txt('COM_TAGS_TAG_NOT_FOUND'), 404);
		}

		// Incoming paging vars
		$this->view->filters = array(
			'limit' => Request::getInt('limit', Config::get('list_limit')),
			'start' => Request::getInt('limitstart', 0),
			'sort'  => Request::getVar('sort', 'date')
		);

		// Get the active category
		$area = Request::getString('area', '');

		$this->view->categories = Event::trigger('tags.onTagView', array(
			$tags,
			$this->view->filters['limit'],
			$this->view->filters['start'],
			$this->view->filters['sort'],
			$area
		));

		$this->view->total   = 0;
		$this->view->results = null;

		if (!$area)
		{
			$query = '';
			if ($this->view->categories)
			{
				$s = array();
				foreach ($this->view->categories as $response)
				{
					$this->view->total += $response['total'];

					if (is_array($response['sql']))
					{
						continue;
					}
					if (trim($response['sql']) != '')
					{
						$s[] = $response['sql'];
					}
					if (isset($response['children']))
					{
						foreach ($response['children'] as $sresponse)
						{
							//$this->view->total += $sresponse['total'];

							if (is_array($sresponse['sql']))
							{
								continue;
							}
							if (trim($sresponse['sql']) != '')
							{
								$s[] = $sresponse['sql'];
							}
						}
					}
				}
				$query .= "(";
				$query .= implode(") UNION (", $s);
				$query .= ") ORDER BY ";
				switch ($this->view->filters['sort'])
				{
					case 'title': $query .= 'title ASC, publish_up';  break;
					case 'id':    $query .= "id DESC";                break;
					case 'date':
					default:      $query .= 'publish_up DESC, title'; break;
				}
				if ($this->view->filters['limit'] != 'all'
				 && $this->view->filters['limit'] > 0)
				{
					$query .= " LIMIT " . $this->view->filters['start'] . "," . $this->view->filters['limit'];
				}
			}
			$this->database->setQuery($query);
			$this->view->results = $this->database->loadObjectList();
		}
		else
		{
			if ($this->view->categories)
			{
				foreach ($this->view->categories as $response)
				{
					$this->view->total += $response['total'];
				}
				foreach ($this->view->categories as $response)
				{
					//$this->view->total += $response['total'];

					if (is_array($response['results']))
					{
						$this->view->results = $response['results'];
						break;
					}

					if (isset($response['children']))
					{
						foreach ($response['children'] as $sresponse)
						{
							//$this->view->total += $sresponse['total'];

							if (is_array($sresponse['results']))
							{
								$this->view->results = $sresponse['results'];
								break;
							}
						}
					}
				}
			}
		}

		$related = null;
		if (count($tags) == 1)
		{
			$this->view->tagstring = $tags[0]->get('tag');
		}
		else
		{
			$tagstring = array();
			foreach ($tags as $tag)
			{
				$tagstring[] = $tag->get('tag');
			}
			$this->view->tagstring = implode('+', $tagstring);
		}

		// Set the pathway
		$this->_buildPathway($tags);

		// Set the page title
		$this->_buildTitle($tags);

		// Output HTML
		if (Request::getVar('format', '') == 'xml')
		{
			$this->view->setLayout('view_xml');
		}

		$this->view->tags   = $tags;
		$this->view->active = $area;
		$this->view->search = implode(', ', $rt);

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Returns results (JSON format) for a search string
	 * Used for autocompletion scripts called via AJAX
	 *
	 * @return  string  JSON
	 */
	public function autocompleteTask()
	{
		$filters = array(
			'limit'  => 20,
			'start'  => 0,
			'admin'  => 0,
			'search' => trim(Request::getString('value', ''))
		);

		// Create a Tag object
		$cloud = new Cloud();

		// Fetch results
		$rows = $cloud->tags('list', $filters);

		// Output search results in JSON format
		$json = array();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$name = str_replace("\n", '', stripslashes(trim($row->get('raw_tag'))));
				$name = str_replace("\r", '', $name);

				$item = array(
					'id'   => $row->get('tag'),
					'name' => $name
				);

				// Push exact matches to the front
				if ($row->get('tag') == $filters['search'])
				{
					array_unshift($json, $item);
				}
				else
				{
					$json[] = $item;
				}
			}
		}

		echo json_encode($json);
	}

	/**
	 * Generate an RSS feed
	 *
	 * @return  string  RSS
	 */
	public function feedTask()
	{
		// Incoming
		$tagstring = trim(Request::getVar('tag', '', 'request', 'none', 2));

		// Ensure we were passed a tag
		if (!$tagstring)
		{
			throw new Exception(Lang::txt('COM_TAGS_NO_TAG'), 404);
		}

		// Break the string into individual tags
		$tgs = explode(',', $tagstring);

		// Sanitize the tag
		$tags  = array();
		$added = array();
		foreach ($tgs as $tag)
		{
			// Load the tag
			$tagobj = Tag::getInstance($tag);

			if (in_array($tagobj->get('tag'), $added))
			{
				continue;
			}

			$added[] = $tagobj->get('tag');

			// Ensure we loaded the tag's info from the database
			if ($tagobj->exists())
			{
				$tags[] = $tagobj;
			}
		}

		// Paging variables
		$limitstart = Request::getInt('limitstart', 0);
		$limit = Request::getInt('limit', Config::get('list_limit'));

		$areas = array();
		$searchareas = Event::trigger('tags.onTagAreas');
		foreach ($searchareas as $area)
		{
			$areas = array_merge($areas, $area);
		}

		// Get the active category
		$area = Request::getVar('area', '');
		$sort = Request::getVar('sort', '');

		if ($area)
		{
			$activeareas = array($area);
		}
		else
		{
			$activeareas = $areas;
		}

		// Get the search results
		if (count($activeareas) > 1)
		{
			$sqls = Event::trigger('tags.onTagView',
				array(
					$tags,
					$limit,
					$limitstart,
					$sort,
					$activeareas
				)
			);
			if ($sqls)
			{
				$s = array();
				foreach ($sqls as $sql)
				{
					if (!is_string($sql))
					{
						continue;
					}
					if (trim($sql) != '')
					{
						$s[] = $sql;
					}
				}
				$query  = "(";
				$query .= implode(") UNION (", $s);
				$query .= ") ORDER BY ";
				switch ($sort)
				{
					case 'title': $query .= 'title ASC, publish_up';  break;
					case 'id':    $query .= "id DESC";                break;
					case 'date':
					default:      $query .= 'publish_up DESC, title'; break;
				}
				$query .= ($limit != 'all' && $limit > 0) ? " LIMIT $limitstart, $limit" : "";
			}
			$this->database->setQuery($query);
			$results = array($this->database->loadObjectList());
		}
		else
		{
			$results = Event::trigger('tags.onTagView',
				array(
					$tags,
					$limit,
					$limitstart,
					$sort,
					$activeareas
				)
			);
		}

		// Run through the array of arrays returned from plugins and find the one that returned results
		$rows = array();
		if ($results)
		{
			foreach ($results as $result)
			{
				if (is_array($result) && !empty($result))
				{
					$rows = $result;
					break;
				}
			}
		}

		// Build some basic RSS document information
		$title = Lang::txt(strtoupper($this->_option)) . ': ';
		for ($i=0, $n=count($tags); $i < $n; $i++)
		{
			if ($i > 0)
			{
				$title .= '+ ';
			}
			$title .= $tags[$i]->get('raw_tag') . ' ';
		}
		$title = trim($title);
		$title .= ': ' . $area;

		include_once(PATH_CORE . DS . 'libraries' . DS . 'joomla' . DS . 'document' . DS . 'feed' . DS . 'feed.php');

		// Set the mime encoding for the document
		$jdoc = \JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		// Start a new feed object
		$doc = new \JDocumentFeed;
		$doc->link        = Route::url('index.php?option=' . $this->_option);
		$doc->title       = Config::get('sitename') . ' - ' . $title;
		$doc->description = Lang::txt('COM_TAGS_RSS_DESCRIPTION', Config::get('sitename'), $title);
		$doc->copyright   = Lang::txt('COM_TAGS_RSS_COPYRIGHT', gmdate("Y"), Config::get('sitename'));
		$doc->category    = Lang::txt('COM_TAGS_RSS_CATEGORY');

		// Start outputing results if any found
		if (count($rows) > 0)
		{
			include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');

			foreach ($rows as $row)
			{
				// Prepare the title
				$title = strip_tags($row->title);
				$title = html_entity_decode($title);

				// Strip html from feed item description text
				$description = html_entity_decode(String::truncate(Sanitize::stripAll(stripslashes($row->ftext)),300));
				$author = '';
				@$date = ($row->publish_up ? date('r', strtotime($row->publish_up)) : '');

				if (isset($row->data3) || isset($row->rcount))
				{
					$resourceEx = new \Components\Resources\Helpers\Helper($row->id, $this->database);
					$resourceEx->getCitationsCount();
					$resourceEx->getLastCitationDate();
					$resourceEx->getContributors();

					$author = strip_tags($resourceEx->contributors);
				}

				// Load individual item creator class
				$item = new \JFeedItem();
				$item->title       = $title;
				$item->link        = $row->href;
				$item->description = $description;
				$item->date        = $date;
				$item->category    = (isset($row->data1)) ? $row->data1 : '';
				$item->author      = $author;

				// Loads item info into rss array
				$doc->addItem($item);
			}
		}

		// Output the feed
		echo $doc->render();
	}

	/**
	 * Browse the list of tags
	 *
	 * @return  void
	 */
	public function browseTask()
	{
		// Instantiate a new view
		if (Request::getVar('format', '') == 'xml')
		{
			$this->view->setLayout('browse_xml');
		}

		// Incoming
		$this->view->filters = array(
			'admin' => 0,
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			))
		);

		// Fallback support for deprecated sorting option
		if ($sortby = Request::getVar('sortby'))
		{
			Request::setVar('sort', $sortby);
		}
		$this->view->filters['sort'] = urldecode(Request::getState(
			$this->_option . '.' . $this->_controller . '.sort',
			'sort',
			'raw_tag'
		));
		$this->view->filters['sort_Dir'] = strtolower(Request::getState(
			$this->_option . '.' . $this->_controller . '.sort_Dir',
			'sortdir',
			'asc'
		));
		if (!in_array($this->view->filters['sort'], array('raw_tag', 'total')))
		{
			$this->view->filters['sort'] = 'raw_tag';
		}
		if (!in_array($this->view->filters['sort_Dir'], array('asc', 'desc')))
		{
			$this->view->filters['sort_Dir'] = 'asc';
		}

		$this->view->total = 0;

		$t = new Cloud();

		$order = Request::getVar('order', '');
		if ($order == 'usage')
		{
			$limit = Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			);

			$this->view->rows = $t->tags('list', array(
				'limit'    => $limit,
				'admin'    => 0,
				'sort'     => 'total',
				'sort_Dir' => 'DESC',
				'by'       => 'user'
			));
		}
		else
		{
			// Record count
			$this->view->total = $t->tags('count', $this->view->filters);

			$this->view->filters['limit'] = Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			);

			// Get records
			$this->view->rows = $t->tags('list', $this->view->filters);
		}

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		$this->view->config = $this->config;

		// Output HTML
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Create a new tag
	 *
	 * @return  void
	 */
	public function createTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing a tag
	 *
	 * @param   object  $tag
	 * @return  void
	 */
	public function editTask($tag=NULL)
	{
		// Check that the user is authorized
		if (!$this->config->get('access-edit-tag'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}

		// Load a tag object if one doesn't already exist
		if (!is_object($tag))
		{
			// Incoming
			$tag = new Tag(intval(Request::getInt('id', 0, 'request')));
		}

		$this->view->tag = $tag;

		$this->view->filters = array(
			'limit'    => Request::getInt('limit', 0),
			'start'    => Request::getInt('limitstart', 0),
			'sort'     => Request::getVar('sort', ''),
			'sort_Dir' => Request::getVar('sortdir', ''),
			'search'   => urldecode(Request::getString('search', ''))
		);

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Pass error messages to the view
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Cancel a task and redirect to the main listing
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$return = Request::getVar('return', 'index.php?option=' . $this->_option . '&task=browse', 'get');

		App::redirect(
			Route::url($return)
		);
	}

	/**
	 * Save a tag
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Check that the user is authorized
		if (!$this->config->get('access-edit-tag'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}

		$tag = Request::getVar('fields', array(), 'post');

		// Bind incoming data
		$row = new Tag(intval($tag['id']));
		if (!$row->bind($tag))
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store(true))
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		$limit  = Request::getInt('limit', 0);
		$start  = Request::getInt('limitstart', 0);
		$sortby = Request::getInt('sortby', '');
		$search = urldecode(Request::getString('search', ''));

		// Redirect to main listing
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&task=browse&search=' . urlencode($search) . '&sortby=' . $sortby . '&limit=' . $limit . '&limitstart=' . $start)
		);
	}

	/**
	 * Delete one or more tags
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check that the user is authorized
		if (!$this->config->get('access-delete-tag'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}

		// Incoming
		$ids = Request::getVar('id', array());
		if (!is_array($ids))
		{
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=browse')
			);
			return;
		}

		foreach ($ids as $id)
		{
			$id = intval($id);

			// Remove references to the tag
			Event::trigger('tags.onTagDelete', array($id));

			// Remove the tag
			$tag = new Tag($id);
			$tag->delete();
		}

		$this->cleancacheTask(false);

		// Get the browse filters so we can go back to previous view
		$search = Request::getVar('search', '');
		$sortby = Request::getVar('sortby', '');
		$limit  = Request::getInt('limit', 25);
		$start  = Request::getInt('limitstart', 0);
		$count  = Request::getInt('count', 1);

		// Redirect back to browse mode
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&task=browse&search=' . $search . '&sortby=' . $sortby . '&limit=' . $limit . '&limitstart=' . $start . '#count' . $count)
		);
	}

	/**
	 * Clean cached tags data
	 *
	 * @param   boolean  $redirect  Redirect after?
	 * @return  void
	 */
	public function cleancacheTask($redirect=true)
	{
		$cache = \JCache::getInstance('', array(
			'defaultgroup' => '',
			'storage'      => Config::get('cache_handler', ''),
			'caching'      => true,
			'cachebase'    => Config::get('cache_path', PATH_APP . DS . 'cache')
		));
		$cache->clean('tags');

		if (!$redirect)
		{
			return true;
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&task=browse')
		);
	}

	/**
	 * Method to set the document path
	 *
	 * @param   array  $tags  Tags currently viewing
	 * @return  void
	 */
	protected function _buildPathway($tags=null)
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task && $this->_task != 'view' && $this->_task != 'display')
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
		if (is_array($tags) && count($tags) > 0)
		{
			$t = array();
			$l = array();
			foreach ($tags as $tag)
			{
				$t[] = stripslashes($tag->get('raw_tag'));
				$l[] = $tag->get('tag');
			}

			Pathway::append(
				implode(' + ', $t),
				'index.php?option=' . $this->_option . '&tag=' . implode('+', $l)
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @param   array  $tags  Tags currently viewing
	 * @return  void
	 */
	protected function _buildTitle($tags=null)
	{
		$this->view->title = Lang::txt(strtoupper($this->_option));
		if ($this->_task && $this->_task != 'view' && $this->_task != 'display')
		{
			$this->view->title .= ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}
		if (is_array($tags) && count($tags) > 0)
		{
			$t = array();
			foreach ($tags as $tag)
			{
				$t[] = stripslashes($tag->get('raw_tag'));
			}
			$this->view->title .= ': ' . implode(' + ', $t);
		}

		\JFactory::getDocument()->setTitle($this->view->title);
	}

	/**
	 * Method to check admin access permission
	 *
	 * @return  boolean  True on success
	 */
	protected function _authorize($assetType='tag', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);

		if (!User::isGuest())
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

