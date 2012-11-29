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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access'); 

ximport('Hubzero_Controller');

class NewsletterControllerStory extends Hubzero_Controller
{
	public function addTask()
	{
		$this->view->setLayout('edit');
		$this->editTask();
	}
	
	//-----
	
	public function editTask()
	{   
		//
		$database = JFactory::getDBO();
		
		//
		$this->view->type = ucfirst(JRequest::getVar("type", "primary"));
		$this->view->campaign = JRequest::getInt("id", 0);
		$this->view->sid = JRequest::getInt("sid", 0);
		
		$this->view->story = array(
			'id' => null,
			'title' => null,
			'story' => null
		);
		
		if($this->view->sid)
		{
			if($this->view->type == "Primary")
			{
				$s = new NewsletterPrimaryStory( $database );
			}
			else
			{
				$s = new NewsletterSecondaryStory( $database );
			}
			
			$s->load($this->view->sid);;
			$this->view->story['id'] = $s->id;
			$this->view->story['title'] = $s->title;
			$this->view->story['story'] = $s->story;
		}
		
		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}
	
	//-----
	
	public function saveTask()
	{   
		//instantiate database
		$database = JFactory::getDBO();
		
		//get story
		$story = JRequest::getVar("story", array(), 'post', 'ARRAY', JREQUEST_ALLOWHTML);
		$type = JRequest::getVar("type", "primary");
		
		if($type == "primary")
		{
			$s = new NewsletterPrimaryStory( $database );
		}
		else
		{
			$s = new NewsletterSecondaryStory( $database );
		}
		
		//save the story 
		if($s->save($story))
		{
			$this->_redirect = 'index.php?option=com_newsletter&controller=campaign&task=edit&id='.$s->campaign;
			$this->_message = JText::_('Story Successfully Saved');
		}
	}
	
	//-----
	
	public function cancelTask()
	{
		$story = JRequest::getVar("story", array());
		$this->_redirect = 'index.php?option=com_newsletter&controller=campaign&task=edit&id='.$story['campaign'];
	}
}