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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Controller class for tags
 */
class TagsControllerTags extends Hubzero_Controller
{
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->_authorize();

		$this->registerTask('feed.rss', 'feed');
		$this->registerTask('feedrss', 'feed');

		if (($tagstring = urldecode(trim(JRequest::getVar('tag', '', 'request', 'none', 2)))))
		{
			if (!JRequest::getVar('task', ''))
			{
				JRequest::setVar('task', 'view');
			}
		}

		parent::execute();
	}

	/**
	 * Display the main page for this component
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->cloud = new TagsModelCloud();

		$this->view->config = $this->config;

		// Set the page title
		$this->_buildTitle(null);

		// Set the pathway
		$this->_buildPathway(null);

		// Push some styles to the template
		$this->_getStyles('', 'introduction.css', true); // component, stylesheet name, look in media system dir
		$this->_getStyles();

		// Output HTML
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * View items tagged with this tag
	 * 
	 * @return     void
	 */
	public function viewTask()
	{
		// Incoming
		$tagstring = urldecode(trim(JRequest::getVar('tag', '', 'request', 'none', 2)));

		$addtag = trim(JRequest::getVar('addtag', ''));

		// Ensure we were passed a tag
		if (!$tagstring && !$addtag) 
		{
			JError::raiseError(404, JText::_('COM_TAGS_NO_TAG'));
			return;
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
		//$t = new TagsHandler($this->database);

		$tags  = array();
		$added = array();
		$rt    = array();
		foreach ($tgs as $tag)
		{
			// Load the tag
			$tagobj = TagsModelTag::getInstance($tag);

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
			JError::raiseError(404, JText::_('COM_TAGS_NOT_FOUND'));
			return;
		}

		// Load plugins
		JPluginHelper::importPlugin('tags');
		$dispatcher =& JDispatcher::getInstance();

		// Get configuration
		$config = JFactory::getConfig();

		// Incoming paging vars
		$this->view->filters = array();
		$this->view->filters['limit'] = JRequest::getInt('limit', $config->getValue('config.list_limit'));
		$this->view->filters['start'] = JRequest::getInt('limitstart', 0);
		$this->view->filters['sort']  = JRequest::getVar('sort', 'date');

		// Trigger the functions that return the areas we'll be using
		$areas = array();
		$searchareas = $dispatcher->trigger('onTagAreas');
		foreach ($searchareas as $area)
		{
			$areas = array_merge($areas, $area);
		}

		// Get the active category
		$area = JRequest::getVar('area', '');
		if ($area) 
		{
			$activeareas = array($area);
		} 
		else 
		{
			//$limit = 5;
			$activeareas = $areas;
		}

		// Get the search result totals
		$totals = $dispatcher->trigger('onTagView', array(
				$tags,
				0,
				0,
				$this->view->filters['sort'],
				$activeareas
			)
		);

		$this->view->filters['limit'] = ($this->view->filters['limit'] == 0) ? 'all' : $this->view->filters['limit'];

		// Get the search results
		$this->view->results = $dispatcher->trigger('onTagView', array(
				$tags,
				$this->view->filters['limit'],
				$this->view->filters['start'],
				$this->view->filters['sort'],
				$activeareas
			)
		);

		if (count($activeareas) > 1) 
		{
			$query = '';
			if ($this->view->results) 
			{
				$s = array();
				foreach ($this->view->results as $sql)
				{
					if (trim($sql) != '') 
					{
						$s[] = $sql;
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
			$this->view->results = array($this->database->loadObjectList());
		}

		// Get the total results found (sum of all categories)
		$i = 0;
		$this->view->total = 0;

		foreach ($areas as $c => $t)
		{
			$cats[$i]['category'] = $c;

			// Do sub-categories exist?
			if (is_array($t) && !empty($t)) 
			{
				// They do - do some processing
				$cats[$i]['title'] = ucfirst($c);
				$cats[$i]['total'] = 0;
				$cats[$i]['_sub']  = array();
				$z = 0;
				// Loop through each sub-category
				foreach ($t as $s => $st)
				{
					// Ensure a matching array of totals exist
					if (is_array($totals[$i]) && !empty($totals[$i]) && isset($totals[$i][$z])) 
					{
						// Add to the parent category's total
						$cats[$i]['total'] = $cats[$i]['total'] + $totals[$i][$z];
						// Get some info for each sub-category
						$cats[$i]['_sub'][$z]['category'] = $s;
						$cats[$i]['_sub'][$z]['title']    = $st;
						$cats[$i]['_sub'][$z]['total']    = $totals[$i][$z];
					}
					$z++;
				}
			} 
			else 
			{
				// No sub-categories - this should be easy
				$cats[$i]['title'] = $t;
				$cats[$i]['total'] = (!is_array($totals[$i])) ? $totals[$i] : 0;
			}

			// Add to the overall total
			$this->view->total = $this->view->total + intval($cats[$i]['total']);
			$i++;
		}
		$this->view->totals = $totals;
		$this->view->cats   = $cats;

		// Do we have an active area?
		if (count($activeareas) == 1 && isset($activeareas[0])) 
		{
			$active = $activeareas[0];
		} 
		else 
		{
			$active = '';
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

		// Push some styles to the template
		$this->_getStyles();

		// Output HTML
		if (JRequest::getVar('format', '') == 'xml') 
		{
			$this->view->setLayout('view_xml');
		}

		$this->view->tags   = $tags;
		$this->view->active = $active;
		$this->view->search = implode(', ', $rt);

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	/**
	 * Returns results (JSON format) for a search string
	 * Used for autocompletion scripts called via AJAX
	 * 
	 * @return     string JSON
	 */
	public function autocompleteTask()
	{
		$filters = array();
		$filters['limit']  = 20;
		$filters['start']  = 0;
		$filters['admin']  = 0;
		$filters['search'] = trim(JRequest::getString('value', ''));

		// Create a Tag object
		//$obj = new TagsTableTag($this->database);
		$cloud = new TagsModelCloud();

		// Fetch results
		//$rows = $obj->getAutocomplete($filters);
		$rows = $cloud->tags('list', $filters);

		// Output search results in JSON format
		$json = array();
		if (count($rows) > 0) 
		{
			foreach ($rows as $row)
			{
				$name = str_replace("\n", '', stripslashes(trim($row->get('raw_tag'))));
				$name = str_replace("\r", '', $name);

				$json[] = array(
					'id'   => $row->get('tag'),
					'name' => $name
				);
			}
		}

		echo json_encode($json);
	}

	/**
	 * Generate an RSS feed
	 * 
	 * @return     string RSS
	 */
	public function feedTask()
	{
		include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'document' . DS . 'feed' . DS . 'feed.php');

		$app = JFactory::getApplication();

		// Set the mime encoding for the document
		$jdoc =& JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		// Start a new feed object
		$doc = new JDocumentFeed;
		$params =& $app->getParams();
		$doc->link = JRoute::_('index.php?option=' . $this->_option);

		// Incoming
		$tagstring = trim(JRequest::getVar('tag', '', 'request', 'none', 2));

		// Ensure we were passed a tag
		if (!$tagstring) 
		{
			JError::raiseError(404, JText::_('COM_TAGS_NO_TAG'));
			return;
		}

		// Break the string into individual tags
		$tgs = explode(' ', $tagstring);

		// Sanitize the tag
		$t = new TagsHandler($this->database);

		$tags = array();
		foreach ($tgs as $tag)
		{
			$tag = $t->normalize_tag($tag);

			// Load the tag
			$tagobj = new TagsTableTag($this->database);
			$tagobj->loadTag($tag);

			// Ensure we loaded the tag's info from the database
			if ($tagobj->id) 
			{
				$tags[] = $tagobj;
			}
		}

		// Get configuration
		$config = JFactory::getConfig();

		// Paging variables
		$limitstart = JRequest::getInt('limitstart', 0);
		$limit = JRequest::getInt('limit', $config->getValue('config.list_limit'));

		// Load plugins
		JPluginHelper::importPlugin('tags');
		$dispatcher =& JDispatcher::getInstance();

		$areas = array();
		$searchareas = $dispatcher->trigger('onTagAreas');
		foreach ($searchareas as $area)
		{
			$areas = array_merge($areas, $area);
		}

		// Get the active category
		$area = JRequest::getVar('area', '');
		$sort = JRequest::getVar('sort', '');

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
			$sqls = $dispatcher->trigger('onTagView', 
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
			$results = $dispatcher->trigger('onTagView', 
				array(
					$tags,
					$limit,
					$limitstart,
					$sort,
					$activeareas
				)
			);
		}

		$jconfig =& JFactory::getConfig();

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
		$title = JText::_(strtoupper($this->_option)) . ': ';
		for ($i=0, $n=count($tags); $i < $n; $i++)
		{
			if ($i > 0) 
			{
				$title .= '+ ';
			}
			$title .= $tags[$i]->raw_tag . ' ';
		}
		$title = trim($title);
		$title .= ': ' . $area;

		$doc->title       = $jconfig->getValue('config.sitename') . ' - ' . $title;
		$doc->description = JText::sprintf('COM_TAGS_RSS_DESCRIPTION', $jconfig->getValue('config.sitename'), $title);
		$doc->copyright   = JText::sprintf('COM_TAGS_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		$doc->category    = JText::_('COM_TAGS_RSS_CATEGORY');

		// Start outputing results if any found
		if (count($rows) > 0) 
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');

			foreach ($rows as $row)
			{
				// Prepare the title
				$title = strip_tags($row->title);
				$title = html_entity_decode($title);

				// URL link to article
				//$link = JRoute::_($row->href);

				// Strip html from feed item description text
				$description = html_entity_decode(Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->ftext)),300,0));
				$author = '';
				@$date = ($row->publish_up ? date('r', strtotime($row->publish_up)) : '');

				if (isset($row->data3) || isset($row->rcount)) 
				{
					$resourceEx = new ResourcesHelper($row->id, $this->database);
					$resourceEx->getCitationsCount();
					$resourceEx->getLastCitationDate();
					$resourceEx->getContributors();

					$author = strip_tags($resourceEx->contributors);
				}

				// Load individual item creator class
				$item = new JFeedItem();
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
	 * @return     void
	 */
	public function browseTask()
	{
		// Instantiate a new view
		if (JRequest::getVar('format', '') == 'xml') 
		{
			$this->view->setLayout('browse_xml');
		}

		// Get configuration
		$jconfig = JFactory::getConfig();
		$app =& JFactory::getApplication();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['search']       = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search', 
			'search', 
			''
		));
		$this->view->filters['sortby'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortby',
			'sortby',
			''
		);

		if (!in_array($this->view->filters['sortby'], array('raw_tag', 'total')))
		{
			$this->view->filters['sortby'] = '';
		}

		$this->view->total = 0;

		$t = new TagsModelCloud();

		$order = JRequest::getVar('order', '');
		if ($order == 'usage') 
		{
			$limit = $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				$jconfig->getValue('config.list_limit'),
				'int'
			);

			//$this->view->rows = $t->getTopTags($limit);
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

			$this->view->filters['limit'] = $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				$jconfig->getValue('config.list_limit'),
				'int'
			);

			// Get records
			$this->view->rows = $t->tags('list', $this->view->filters);

			// Initiate paging
			jimport('joomla.html.pagination');
			$this->view->pageNav = new JPagination(
				$this->view->total, 
				$this->view->filters['start'], 
				$this->view->filters['limit']
			);
		}

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Push some styles to the document
		$this->_getStyles();

		// Push scripts to the document
		$this->_getScripts('assets/css/tags');

		$this->view->config = $this->config;

		// Output HTML
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Create a new tag
	 * 
	 * @return     void
	 */
	public function createTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing a task
	 * 
	 * @param      object $tag TagsTableTag
	 * @return     void
	 */
	public function editTask($tag=NULL)
	{
		$this->view->setLayout('edit');

		// Check that the user is authorized
		if (!$this->config->get('access-edit-tag')) 
		{
			JError::raiseWarning(403, JText::_('ALERTNOTAUTH'));
			return;
		}

		// Load a tag object if one doesn't already exist
		if (is_object($tag)) 
		{
			$this->view->tag = $tag;
		}
		else 
		{
			// Incoming
			$this->view->tag = new TagsModelTag(JRequest::getInt('id', 0, 'request'));
		}

		$this->view->filters = array();
		$this->view->filters['limit']  = JRequest::getInt('limit', 0);
		$this->view->filters['start']  = JRequest::getInt('limitstart', 0);
		$this->view->filters['sortby'] = JRequest::getInt('sortby', '');
		$this->view->filters['search'] = urldecode(JRequest::getString('search', ''));

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Push some styles to the template
		$this->_getStyles();

		// Push scripts to the document
		$this->_getScripts('assets/css/tags');

		// Pass error messages to the view
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	/**
	 * Cancel a task and redirect to the main listing
	 * 
	 * @return     void
	 */
	public function cancelTask()
	{
		$return = JRequest::getVar('return', 'index.php?option=' . $this->_option . '&task=browse', 'get');

		$this->setRedirect(
			JRoute::_($return)
		);
	}

	/**
	 * Save a tag
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check that the user is authorized
		if (!$this->config->get('access-edit-tag')) 
		{
			JError::raiseWarning(403, JText::_('ALERTNOTAUTH'));
			return;
		}

		$tag = JRequest::getVar('fields', array(), 'post');

		// Bind incoming data
		$row = new TagsModelTag($tag['id']);
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

		$limit  = JRequest::getInt('limit', 0);
		$start  = JRequest::getInt('limitstart', 0);
		$sortby = JRequest::getInt('sortby', '');
		$search = urldecode(JRequest::getString('search', ''));

		// Redirect to main listing
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&task=browse&search=' . urlencode($search) . '&sortby=' . $sortby . '&limit=' . $limit . '&limitstart=' . $start)
		);
	}

	/**
	 * Delete one or more tags
	 * 
	 * @return     void
	 */
	public function deleteTask()
	{
		// Check that the user is authorized
		if (!$this->config->get('access-delete-tag')) 
		{
			JError::raiseWarning(403, JText::_('ALERTNOTAUTH'));
			return;
		}

		// Incoming
		$ids = JRequest::getVar('id', array());
		if (!is_array($ids)) 
		{
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids)) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=browse')
			);
			return;
		}

		// Get Tags plugins
		JPluginHelper::importPlugin('tags');
		$dispatcher =& JDispatcher::getInstance();

		foreach ($ids as $id)
		{
			// Remove references to the tag
			$dispatcher->trigger('onTagDelete', array($id));

			// Remove the tag
			$tag = new TagsModelTag($id);
			$tag->delete();
		}

		//get the browse filters so we can go back to previous view
		$search = JRequest::getVar('search', '');
		$sortby = JRequest::getVar('sortby', '');
		$limit  = JRequest::getInt('limit', 25);
		$start  = JRequest::getInt('limitstart', 0);
		$count  = JRequest::getInt('count', 1);

		//redirect back to browse mode
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&task=browse&search=' . $search . '&sortby=' . $sortby . '&limit=' . $limit . '&limitstart=' . $start . '#count' . $count)
		);
	}

	/**
	 * Method to set the document path
	 * 
	 * @param      array $tags Tags currently viewing
	 * @return     void
	 */
	protected function _buildPathway($tags=null)
	{
		$pathway =& JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task && $this->_task != 'view' && $this->_task != 'display') 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
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

			$pathway->addItem(
				implode(' + ', $t),
				'index.php?option=' . $this->_option . '&tag=' . implode('+', $l)
			);
		}
	}

	/**
	 * Method to build and set the document title
	 * 
	 * @param      array $tags Tags currently viewing
	 * @return     void
	 */
	protected function _buildTitle($tags=null)
	{
		$this->view->title = JText::_(strtoupper($this->_option));
		if ($this->_task && $this->_task != 'view' && $this->_task != 'display') 
		{
			$this->view->title .= ': ' . JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task));
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
		$document =& JFactory::getDocument();
		$document->setTitle($this->view->title);
	}

	/**
	 * Method to check admin access permission
	 *
	 * @return	boolean	True on success
	 */
	protected function _authorize($assetType='tag', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);

		if (!$this->juser->get('guest')) 
		{
			if (version_compare(JVERSION, '1.6', 'ge'))
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
				$this->config->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $asset));
				$this->config->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $asset));
				// Permissions
				$this->config->set('access-create-' . $assetType, $this->juser->authorise('core.create' . $at, $asset));
				$this->config->set('access-delete-' . $assetType, $this->juser->authorise('core.delete' . $at, $asset));
				$this->config->set('access-edit-' . $assetType, $this->juser->authorise('core.edit' . $at, $asset));
				$this->config->set('access-edit-state-' . $assetType, $this->juser->authorise('core.edit.state' . $at, $asset));
				$this->config->set('access-edit-own-' . $assetType, $this->juser->authorise('core.edit.own' . $at, $asset));
			}
			else 
			{
				if ($this->juser->authorize($this->_option, 'manage'))
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
}

