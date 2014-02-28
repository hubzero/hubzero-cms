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
 * @author   Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

require_once(JPATH_ROOT.DS."components".DS."com_feedaggregator".DS."models".DS."feeds.php");
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');
//use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Client;

/**
 *  Feed Aggregator controller class
 */
class FeedaggregatorControllerFeeds extends Hubzero_Controller
{
	
	
public function displayTask()
{

$feeds = array(); //page on websites
		
$model = new FeedAggregatorModelFeeds;

$feeds = $model->loadAll();
$this->view->feeds = $feeds;

$this->view->title =  JText::_('Feed Aggregator');
$this->view->display();
	
}

public function editTask(/*$id =NULL*/)
{
	//isset ID kinda deal
	$id = JRequest::getVar('id');
	$model = new FeedAggregatorModelFeeds;
	$feed = $model->loadbyId($id);
	$this->view->feed = $feed;
	
	$this->view->user = $this->juser;
	
	//$this->view->setLayout('edit');
	$this->view->title = JText::_('Edit Feeds');
	$this->view->display();
}

public function newTask()
{
	$id = JRequest::getVar('id');
	$model = new FeedAggregatorModelFeeds;
	$feed = $model->loadbyId($id);
	$this->view->feed = $feed;
	
	$this->view->user = $this->juser;
	
	$this->view->setLayout('edit');
	$this->view->title = JText::_('Add Feed');
	$this->view->display();
}

public function saveTask()
{
	//do a JRequest instead of a bind()
	$db = JFactory::getDBO();
	$feed = new FeedAggregatorModelFeeds;
	$fields = JRequest::get('post');
	$feed->bind($fields);
	$feed->store();

	// Output messsage and redirect
	$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Feed Information Updated.')
			);
}


	
} // end class