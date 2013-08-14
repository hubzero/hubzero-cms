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
defined('_JEXEC') or die('Restricted access'); 

class NewsletterControllerStory extends Hubzero_Controller
{
	/**
	 * Add Newsletter Story Task
	 *
	 * @return 	void
	 */
	public function addTask()
	{
		$this->editTask();
	}
	
	
	/**
	 * Edit Newsletter Story Task
	 *
	 * @return 	void
	 */
	public function editTask()
	{
		//set layout
		$this->view->setLayout('edit');
		
		//get request vars
		$this->view->type 	= strtolower(JRequest::getVar("type", "primary"));
		$this->view->id 	= JRequest::getInt("id", 0);
		$this->view->sid 	= JRequest::getInt("sid", 0);
		
		//load campaign
		$this->view->newsletter = new NewsletterNewsletter( $this->database );
		$this->view->newsletter->load( $this->view->id );
		
		//default object
		$this->view->story->id 				= null;
		$this->view->story->order			= null;
		$this->view->story->title 			= null;
		$this->view->story->story 			= null;
		$this->view->story->readmore_title 	= null;
		$this->view->story->readmore_link 	= null;
		
		//are we editing
		if ($this->view->sid)
		{
			if ($this->view->type == "primary")
			{
				$this->view->story = new NewsletterPrimaryStory( $this->database );
			}
			else
			{
				$this->view->story = new NewsletterSecondaryStory( $this->database );
			}
			
			$this->view->story->load( $this->view->sid );
		}
		
		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}
	
	
	/**
	 * Save Newsletter Story Task
	 *
	 * @return 	void
	 */
	public function saveTask()
	{
		//get story
		$story = JRequest::getVar("story", array(), 'post', 'ARRAY', JREQUEST_ALLOWHTML);
		$type = JRequest::getVar("type", "primary");
		
		//are we working with a primary or secondary story
		if ($type == "primary")
		{
			$newsletterStory = new NewsletterPrimaryStory( $this->database );
		}
		else
		{
			$newsletterStory = new NewsletterSecondaryStory( $this->database );
		}
		
		//check to make sure we have an order
		if (!isset($story['order']) || $story['order'] == '' || $story['order'] == 0)
		{
			$currentHighestOrder = $newsletterStory->_getCurrentHighestOrder( $story['nid'] );
			$newOrder = $currentHighestOrder + 1;
			
			$story['order'] = $newOrder;
		}
		
		//save the story 
		if (!$newsletterStory->save($story))
		{
			$this->setError( $newsletterStory->getError() );
			$this->editTask();
			return;
		}
		
		//inform and redirect
		$this->_message = JText::_('Newsletter Story Successfully Saved');
		$this->_redirect = 'index.php?option=com_newsletter&controller=newsletter&task=edit&id[]='.$newsletterStory->nid;
	}
	
	
	/**
	 * Reorder Newsletter Story Task
	 *
	 * @return 	void
	 */
	public function reorderTask()
	{
		//get request vars
		$id 		= JRequest::getInt('id', 0);
		$sid 		= JRequest::getInt('sid', 0);
		$type 		= JRequest::getWord('type', 'primary');
		$direction 	= JRequest::getWord('direction', 'down');
		
		//what kind of story do we want
		if (strtolower($type) == 'primary')
		{
			$story = new NewsletterPrimaryStory( $this->database );
		}
		else
		{
			$story = new NewsletterSecondaryStory( $this->database );
		}
		
		//load the story
		$story->load( $sid );
		
		//set vars
		$lowestOrder = 1;
		$highestOrder = $story->_getCurrentHighestOrder( $id );
		$currentOrder = $story->order;
		
		//move page up or down
		if ($direction == 'down')
		{
			$newOrder = $currentOrder + 1;
			if ($newOrder > $highestOrder)
			{
				$newOrder = $highestOrder;
			}
		}
		else
		{
			$newOrder = $currentOrder - 1;
			if($newOrder < $lowestOrder)
			{
				$newOrder = $lowestOrder;
			}
		}
		
		$database = JFactory::getDBO();
		
		//is there a nother story having the order we want?
		$sql = "SELECT * FROM {$story->getTableName()} WHERE `order`=" . $database->quote( $newOrder ) . " AND nid=" . $database->quote( $id );
		$database->setQuery( $sql );
		$moveTo = $database->loadResult();
		
		//if there isnt just update story
		if (!$moveTo)
		{
			$sql = "UPDATE {$story->getTableName()} SET `order`=" . $database->quote( $newOrder ) . " WHERE id=" . $database->quote( $sid );
			$database->setQuery( $sql );
			$database->query();
		}
		else
		{
			//swith orders
			$sql = "UPDATE {$story->getTableName()} SET `order`=" . $database->quote( $newOrder ) . " WHERE id=" . $database->quote( $sid );
			$database->setQuery( $sql );
			$database->query();
			
			$sql = "UPDATE {$story->getTableName()} SET `order`=" . $database->quote( $currentOrder ) . " WHERE id=" . $database->quote( $moveTo );
			$database->setQuery( $sql );
			$database->query();
		}
		
		//set success message
		$this->_message = JText::_('Newsletter Story Successfully Reordered');
		
		//redirect back to campaigns list
		$this->_redirect = 'index.php?option=com_newsletter&controller=newsletter&task=edit&id[]=' . $id . '#' . $type . '-stories';
	}
	
	
	/**
	 * Delete Newsletter Task
	 *
	 * @return 	void
	 */
	public function deleteTask()
	{
		//get the request vars
		$id 	= JRequest::getInt('id', 0);
		$sid 	= JRequest::getInt('sid', 0);
		$type 	= JRequest::getWord('type', 'primary');
		
		if (strtolower($type) == 'primary')
		{
			$story = new NewsletterPrimaryStory( $this->database );
		}
		else
		{
			$story = new NewsletterSecondaryStory( $this->database );
		}
		
		//load the story
		$story->load( $sid );
		
		//mark as deleted
		$story->deleted = 1;
		
		//save so story is marked deleted
		if (!$story->save( $story ))
		{
			$this->setError('Unable to delete newsletter story.');
			$this->editTask();
			return;
		}
		
		//set success message
		$this->_message = JText::_('Newsletter Story Successfully Deleted');
		
		//redirect back to campaigns list
		$this->_redirect = 'index.php?option=com_newsletter&controller=newsletter&task=edit&id[]=' . $id;
	}
	
	
	/**
	 * Display all campaigns task
	 *
	 * @return 	void
	 */
	public function cancelTask()
	{
		$story = JRequest::getVar("story", array());
		$this->_redirect = 'index.php?option=com_newsletter&controller=newsletter&task=edit&id[]='.$story['nid'];
	}
}