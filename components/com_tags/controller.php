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
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

/**
 * Short description for 'TagsController'
 * 
 * Long description (if any) ...
 */
class TagsController extends Hubzero_Controller
{

	/**
	 * Short description for 'execute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->_task = strtolower(JRequest::getVar('task', ''));

		switch ($this->_task)
		{
			case 'autocomplete': $this->autocomplete(); break;
			case 'cancel': $this->cancel(); break;
			case 'save':   $this->save();   break;
			case 'edit':   $this->edit();   break;
			case 'create': $this->create(); break;
			case 'delete': $this->delete(); break;
			case 'view':   $this->view();   break;
			case 'browse': $this->browse(); break;
			case 'cloud':  $this->intro();  break;
			case 'intro':  $this->intro();  break;
			case 'feed':   $this->feed();   break;
			case 'feed.rss': $this->feed(); break;

			default: $this->intro(); break;
		}
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------


	/**
	 * Short description for 'intro'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function intro()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'intro') );
		$view->title = JText::_(strtoupper($this->_option));
		$view->showsizes = 0;
		$view->config = $this->config;

		// Set some variables
		$view->min_font_size = 1;
		$view->max_font_size = 1.8;

		// Check authorization
		$view->authorized = $this->_authorize();

		// Find all tags
		$t = new TagsTag( $this->database );

		$view->tags = $t->getTopTags( 100 );

		$view->newtags = $t->getRecentTags( 25 );

		// Load plugins
		JPluginHelper::importPlugin('tags');
		$dispatcher =& JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		$view->categories = array();
		$view->tagpile = array();

		if ($view->tags) {
			$retarr = array();
			foreach ($view->tags as $tag)
			{
				$retarr[$tag->raw_tag] = $tag->tcount;
			}
			ksort($retarr);

			$view->max_qty = max(array_values($retarr));  // Get the max qty of tagged objects in the set
			$view->min_qty = min(array_values($retarr));  // Get the min qty of tagged objects in the set
		} else {
			$view->max_qty = 0;
			$view->min_qty = 0;
		}

		// For ever additional tagged object from min to max, we add $step to the font size.
		$spread = $view->max_qty - $view->min_qty;
		if (0 == $spread) { // Divide by zero
			$spread = 1;
		}
		$view->step = ($view->max_font_size - $view->min_font_size)/($spread);

		// Set the page title
		$this->_buildTitle(null);

		// Set the pathway
		$this->_buildPathway(null);

		// Push some styles to the template
		$this->_getStyles();

		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'view'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function view()
	{
		$title = JText::_(strtoupper($this->_option));

		// Incoming
		$tagstring = trim(JRequest::getVar('tag', '', 'request', 'none', 2));

		$addtag = trim(JRequest::getVar('addtag', ''));

		//$search = trim(JRequest::getVar('tags', ''));

		// Ensure we were passed a tag
		if (!$tagstring && !$addtag && !$search) {
			JError::raiseError(404, JText::_('COM_TAGS_NO_TAG'));
			return;
		}

		if ($tagstring) {
			// Break the string into individual tags
			$tgs = explode(' ', $tagstring);
			$tgs = array_map('trim',$tgs);
		} else {
			$tgs = array();
		}
		/*if ($search) {
			// Break the string into individual tags
			$stgs = explode(',', $search);
			$stgs = array_map('trim',$stgs);
		} else {
			$stgs = array();
		}
		foreach ($stgs as $stg) 
		{
			if (!in_array($stg,$tgs)) {
				$tgs[] = $stg;
			}
		}*/

		// See if we're adding any tags to the search list
		if ($addtag && !in_array($addtag,$tgs)) {
			$tgs[] = $addtag;
		}

		// Sanitize the tag
		$t = new TagsHandler( $this->database );

		$tags = array();
		$added = array();
		foreach ($tgs as $tag)
		{
			$tag = $t->normalize_tag( $tag );

			if (in_array($tag,$added)) {
				continue;
			}

			$added[] = $tag;

			// Load the tag
			$tagobj = new TagsTag( $this->database );
			$tagobj->loadTag( $tag );

			// Ensure we loaded the tag's info from the database
			if ($tagobj->id) {
				$tags[] = $tagobj;
			}
		}

		// Ensure we loaded the tag's info from the database
		if (empty($tags)) {
			JError::raiseError( 404, JText::_('COM_TAGS_NOT_FOUND') );
			return;
		}

		// Does the user have edit permissions?
		$authorized = $this->_authorize();

		// Set the page title
		$title .= ': ';
		$tagname = array();
		$tagstring = array();
		for ($i=0, $n=count( $tags ); $i < $n; $i++)
		{
			if ($i > 0) {
				$title .= '+ ';
			}
			$tagname[] = $tags[$i]->raw_tag;
			$tagstring[] = $tags[$i]->tag;
			$title .= $tags[$i]->raw_tag.' ';
		}

		// Load plugins
		JPluginHelper::importPlugin('tags');
		$dispatcher =& JDispatcher::getInstance();

		// Get configuration
		$config = JFactory::getConfig();
		
		// Incoming paging vars
		$limit = JRequest::getInt('limit', $config->getValue('config.list_limit'));
		$limitstart = JRequest::getInt( 'limitstart', 0 );
		$sort = JRequest::getVar( 'sort', 'date' );

		// Trigger the functions that return the areas we'll be using
		$areas = array();
		$searchareas = $dispatcher->trigger( 'onTagAreas' );
		foreach ($searchareas as $area)
		{
			$areas = array_merge( $areas, $area );
		}

		// Get the active category
		$area = JRequest::getVar( 'area', '' );
		if ($area) {
			$activeareas = array($area);
		} else {
			//$limit = 5;
			$activeareas = $areas;
		}

		// Get the search result totals
		$totals = $dispatcher->trigger( 'onTagView', array(
				$tags,
				0,
				0,
				$sort,
				$activeareas)
			);

		$limit = ($limit == 0) ? 'all' : $limit;

		// Get the search results
		/*$results = $dispatcher->trigger( 'onTagView', array(
				$tags,
				$limit,
				$limitstart,
				$sort,
				$activeareas)
		);*/

		// Get the search results
		if (count($activeareas) > 1) {
			$sqls = $dispatcher->trigger( 'onTagView', array(
					$tags,
					$limit,
					$limitstart,
					$sort,
					$activeareas)
				);
			if ($sqls) {
				$s = array();
				foreach ($sqls as $sql)
				{
					if (trim($sql) != '') {
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
			$this->database->setQuery( $query );
			$results = array($this->database->loadObjectList());
		} else {
			$results = $dispatcher->trigger( 'onTagView', array(
					$tags,
					$limit,
					$limitstart,
					$sort,
					$activeareas)
				);
		}

		// Get the total results found (sum of all categories)
		$i = 0;
		$total = 0;

		foreach ($areas as $c=>$t)
		{
			$cats[$i]['category'] = $c;

			// Do sub-categories exist?
			if (is_array($t) && !empty($t)) {
				// They do - do some processing
				$cats[$i]['title'] = ucfirst($c);
				$cats[$i]['total'] = 0;
				$cats[$i]['_sub'] = array();
				$z = 0;
				// Loop through each sub-category
				foreach ($t as $s=>$st)
				{
					// Ensure a matching array of totals exist
					if (is_array($totals[$i]) && !empty($totals[$i]) && isset($totals[$i][$z])) {
						// Add to the parent category's total
						$cats[$i]['total'] = $cats[$i]['total'] + $totals[$i][$z];
						// Get some info for each sub-category
						$cats[$i]['_sub'][$z]['category'] = $s;
						$cats[$i]['_sub'][$z]['title'] = $st;
						$cats[$i]['_sub'][$z]['total'] = $totals[$i][$z];
					}
					$z++;
				}
			} else {
				// No sub-categories - this should be easy
				$cats[$i]['title'] = $t;
				$cats[$i]['total'] = (!is_array($totals[$i])) ? $totals[$i] : 0;
			}

			// Add to the overall total
			$total = $total + intval($cats[$i]['total']);
			$i++;
		}

		// Do we have an active area?
		if (count($activeareas) == 1 && isset($activeareas[0])) {
			$active = $activeareas[0];
		} else {
			$active = '';
		}

		$related = null;
		if (count($tags) == 1) {
			$tagstring = $tags[0]->tag;
		} else {
			$tagstring = array();
			foreach ($tags as $tag)
			{
				$tagstring[] = $tag->tag;
			}
			$tagstring = implode('+',$tagstring);
		}

		// Set the pathway
		$this->_buildPathway($tags);

		// Set the page title
		$this->_buildTitle($tags);

		// Push some styles to the template
		$this->_getStyles();

		// Output HTML
		$format = JRequest::getVar('format', '');
		if ($format == 'xml') {
			$view = new JView( array('name'=>'tag', 'layout'=>'xml') );
		} else {
			$view = new JView( array('name'=>'tag') );
		}
		$view->title = $title;
		$view->authorized = $authorized;
		$view->tags = $tags;
		$view->option = $this->_option;
		$view->totals = $totals;
		$view->total = $total;
		$view->results = $results;
		$view->cats = $cats;
		$view->active = $active;
		$view->limitstart = $limitstart;
		$view->limit = $limit;
		$view->sort = $sort;
		$view->total = $total;
		$view->tagstring = $tagstring;
		$view->search = implode(', ',$tgs);
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'autocomplete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function autocomplete()
	{
		$filters = array();
		$filters['limit']  = 20;
		$filters['start']  = 0;
		$filters['search'] = trim(JRequest::getString('value', ''));

		// Create a Tag object
		$obj = new TagsTag($this->database);

		// Fetch results
		$rows = $obj->getAutocomplete($filters);

		// Output search results in JSON format
		$json = array();
		if (count($rows) > 0) {
			foreach ($rows as $row)
			{
				//$json[] = '"'.$row->raw_tag.'"';
				$json[] = '["'.htmlentities(stripslashes($row->raw_tag), ENT_COMPAT, 'UTF-8').'","'.$row->tag.'"]';
			}
		}

		echo '['.implode(',',$json).']';
	}

	/**
	 * Short description for 'feed'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function feed()
	{
		include_once( JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'feed'.DS.'feed.php');

		global $mainframe;

		// Set the mime encoding for the document
		$jdoc =& JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		// Start a new feed object
		$doc = new JDocumentFeed;
		$params =& $mainframe->getParams();
		$doc->link = JRoute::_('index.php?option='.$this->_option);

		// Incoming
		$tagstring = trim(JRequest::getVar('tag', '', 'request', 'none', 2));

		// Ensure we were passed a tag
		if (!$tagstring) {
			JError::raiseError( 404, JText::_('COM_TAGS_NO_TAG') );
			return;
		}

		// Break the string into individual tags
		$tgs = explode(' ', $tagstring);

		// Sanitize the tag
		$t = new TagsHandler( $this->database );

		$tags = array();
		foreach ($tgs as $tag)
		{
			$tag = $t->normalize_tag( $tag );

			// Load the tag
			$tagobj = new TagsTag( $this->database );
			$tagobj->loadTag( $tag );

			// Ensure we loaded the tag's info from the database
			if ($tagobj->id) {
				$tags[] = $tagobj;
			}
		}

		// Get configuration
		$config = JFactory::getConfig();
		
		// Paging variables
		$limitstart = JRequest::getInt('limitstart', 0);
		$limit = JRequest::getInt('limit', $config->getValue('config.list_limit'));

		// Load plugins
		JPluginHelper::importPlugin( 'tags' );
		$dispatcher =& JDispatcher::getInstance();

		$areas = array();
		$searchareas = $dispatcher->trigger( 'onTagAreas' );
		foreach ($searchareas as $area)
		{
			$areas = array_merge( $areas, $area );
		}

		// Get the active category
		$area = JRequest::getVar( 'area', '' );
		$sort = JRequest::getVar( 'sort', '' );

		if ($area) {
			$activeareas = array($area);
		} else {
			$activeareas = $areas;
		}
		/*if (!$area) {
			$t = array();
			foreach ($tags as $tag) 
			{
				$t[] = $tag->tag;
			}
			$url = JRoute::_('index.php?option='.$this->_option.'&tag='.implode('+',$t));
			$this->_redirect = str_replace('%20','+',$url);
			return;
		}

		// Get the active category
		$activeareas = array($area);
	
		// Get the search results
		$results = $dispatcher->trigger( 'onTagView', array(
				$tags,
				$limit,
				$limitstart,
				$sort,
				$activeareas)
		);*/
		// Get the search results
		if (count($activeareas) > 1) {
			$sqls = $dispatcher->trigger( 'onTagView', array(
					$tags,
					$limit,
					$limitstart,
					$sort,
					$activeareas)
				);
			if ($sqls) {
				$s = array();
				foreach ($sqls as $sql)
				{
					if (trim($sql) != '') {
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
			$this->database->setQuery( $query );
			$results = array($this->database->loadObjectList());
		} else {
			$results = $dispatcher->trigger( 'onTagView', array(
					$tags,
					$limit,
					$limitstart,
					$sort,
					$activeareas)
				);
		}

		$jconfig =& JFactory::getConfig();

		// Run through the array of arrays returned from plugins and find the one that returned results
		$rows = array();
		if ($results) {
			foreach ($results as $result)
			{
				if (is_array($result) && !empty($result)) {
					$rows = $result;
					break;
				}
			}
		}

		// Build some basic RSS document information
		$title = JText::_(strtoupper($this->_option)) .': ';
		for ($i=0, $n=count( $tags ); $i < $n; $i++)
		{
			if ($i > 0) {
				$title .= '+ ';
			}
			$title .= $tags[$i]->raw_tag.' ';
		}
		$title = trim($title);
		$title .= ': '.$area;

		$doc->title = $jconfig->getValue('config.sitename').' - '.$title;
		$doc->description = JText::sprintf('COM_TAGS_RSS_DESCRIPTION',$jconfig->getValue('config.sitename'), $title);
		$doc->copyright = JText::sprintf('COM_TAGS_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		$doc->category = JText::_('COM_TAGS_RSS_CATEGORY');

		// Start outputing results if any found
		if (count($rows) > 0) {
			include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'helper.php' );

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
				@$date = ( $row->publish_up ? date( 'r', strtotime($row->publish_up) ) : '' );

				if (isset($row->data3) || isset($row->rcount)) {
					$resourceEx = new ResourceExtended($row->id, $this->database);
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
				$doc->addItem( $item );
			}
		}

		// Output the feed
		echo $doc->render();
	}

	//----------------------------------------------------------
	//  Administrative Views
	//----------------------------------------------------------


	/**
	 * Short description for 'browse'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function browse()
	{
		// Instantiate a new view
		$format = JRequest::getVar('format', '');
		if ($format == 'xml') {
			$view = new JView(array('name'=>'browse', 'layout'=>'xml'));
		} else {
			$view = new JView(array('name'=>'browse'));
		}
		$view->option = $this->_option;
		$view->title = JText::_(strtoupper($this->_option)) .': '. JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));

		// Check that the user is authorized
		$view->authorized = $this->_authorize();

		// Get configuration
		$config = JFactory::getConfig();

		// Incoming
		$view->filters = array();
		$view->filters['start']  = JRequest::getInt('limitstart', 0);
		$view->filters['search'] = urldecode(JRequest::getString('search'));
		$view->filters['sortby'] = JRequest::getVar('sortby', '');
		$view->total = 0;

		$t = new TagsTag($this->database);

		$order = JRequest::getVar('order', '');
		if ($order == 'usage') {
			$limit = JRequest::getInt('limit', $config->getValue('config.list_limit'));

			$view->rows = $t->getTopTags($limit);
		} else {
			// Record count
			$view->total = $t->getCount($view->filters);

			$view->filters['limit'] = JRequest::getInt('limit', $config->getValue('config.list_limit'));

			// Get records
			$view->rows = $t->getRecords($view->filters);

			// Initiate paging
			jimport('joomla.html.pagination');
			$view->pageNav = new JPagination($view->total, $view->filters['start'], $view->filters['limit']);
		}

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Push some styles to the template
		$this->_getStyles();

		// Output HTML
		if ($this->getError()) {
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Short description for 'create'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function create()
	{
		$this->edit();
	}

	/**
	 * Short description for 'edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $tag Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function edit($tag=NULL)
	{
		// Check that the user is authorized
		if (!$this->_authorize()) {
			JError::raiseWarning( 404, JText::_('ALERTNOTAUTH') );
			return;
		}

		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );

		// Load a tag object if one doesn't already exist
		if (!$tag) {
			$tag = new TagsTag( $this->database );
			$tag->load( $id );
		}

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Push some styles to the template
		$this->_getStyles();

		// Output HTML
		$view = new JView( array('name'=>'edit') );
		$view->option = $this->_option;
		$view->title = JText::_(strtoupper($this->_option)) .': '. JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
		$view->tag = $tag;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'cancel'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function cancel()
	{
		$return = JRequest::getVar('return', 'index.php?option='.$this->_option.'&task=browse', 'get');
		$this->_redirect = JRoute::_( $return );
	}

	/**
	 * Short description for 'save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function save()
	{
		$row = new TagsTag( $this->database );
		if (!$row->bind( $_POST )) {
			$this->setError( $row->getError() );
			$this->edit($row);
			return;
		}

		$row->raw_tag = trim($row->raw_tag);

		$t = new TagsHandler($this->database);
		$row->tag = $t->normalize_tag($row->raw_tag);

		// Check content
		if (!$row->check()) {
			$this->setError( $row->getError() );
			$this->edit($row);
			return;
		}

		// Make sure the tag doesn't already exist
		if (!$row->id) {
			if ($row->checkExistence()) {
				$this->setError( JText::_( 'COM_TAGS_TAG_EXIST' ) );
				$this->edit($row);
				return;
			}
		}

		// Store new content
		if (!$row->store()) {
			$this->setError( $row->getError() );
			$this->edit($row);
			return;
		}

		$this->_redirect = JRoute::_( 'index.php?option='.$this->_option.'&task=browse' );
	}

	/**
	 * Short description for 'delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function delete()
	{
		// Check that the user is authorized
		if (!$this->_authorize()) {
			JError::raiseWarning( 404, JText::_('ALERTNOTAUTH') );
			return;
		}

		// Incoming
		$ids = JRequest::getVar('id', array());
		if (!is_array( $ids )) {
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids)) {
			$this->_redirect = JRoute::_( 'index.php?option='.$this->_option.'&task=browse' );
			return;
		}

		// Get Tags plugins
		JPluginHelper::importPlugin('tags');
		$dispatcher =& JDispatcher::getInstance();

		foreach ($ids as $id)
		{
			// Remove references to the tag
			$dispatcher->trigger( 'onTagDelete', array($id) );

			// Remove the tag
			$tag = new TagsTag( $this->database );
			$tag->delete( $id );
		}

		$this->_redirect = JRoute::_( 'index.php?option='.$this->_option.'&task=browse' );
	}

	//----------------------------------------------------------
	// Private functions
	//----------------------------------------------------------


	/**
	 * Short description for '_buildPathway'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $tags Parameter description (if any) ...
	 * @return     void
	 */
	protected function _buildPathway($tags=null)
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();

		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option='.$this->_option
			);
		}
		if ($this->_task && $this->_task != 'view') {
			$pathway->addItem(
				JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
				'index.php?option='.$this->_option.'&task='.$this->_task
			);
		}
		if (is_array($tags) && count($tags) > 0) {
			$t = array();
			$l = array();
			foreach ($tags as $tag)
			{
				$t[] = stripslashes($tag->raw_tag);
				$l[] = $tag->tag;
			}
			$title = implode(' + ',$t);
			$lnk = implode('+',$l);
			$pathway->addItem(
				$title,
				'index.php?option='.$this->_option.'&tag='.$lnk
			);
		}
	}

	/**
	 * Short description for '_buildTitle'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $tags Parameter description (if any) ...
	 * @return     void
	 */
	protected function _buildTitle($tags=null)
	{
		$title = JText::_(strtoupper($this->_option));
		if ($this->_task && $this->_task != 'view') {
			$title .= ': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
		}
		if (is_array($tags) && count($tags) > 0) {
			$title .= ': ';
			$t = array();
			foreach ($tags as $tag)
			{
				$t[] = stripslashes($tag->raw_tag);
			}
			$title .= implode(' + ',$t);
		}
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
	}
}

