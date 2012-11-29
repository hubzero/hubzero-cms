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

class NewsletterControllerTemplate extends Hubzero_Controller
{
	
	public function displayTask()
	{
		//database
		$database =& JFactory::getDBO();
		
		//get the templates
		$nt = new NewsletterTemplate( $database );
		$this->view->templates = $nt->getTemplate();
		
		// Set any errors
		if($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	public function addTask()
	{
		$this->view->setLayout('edit');
		$this->editTask();
	}
	
	public function editTask()
	{
		$id = JRequest::getInt("id", 0);
		
		//database
		$database =& JFactory::getDBO();
		
		//
		$this->view->template = array(
			'id' => '',
			'name' => '',
			'template' => ''
		);
		
		//
		if($id)
		{
			$nt = new NewsletterTemplate( $database );
			$template = $nt->getTemplate( $id );
			$this->view->template['id'] = $template->id;
			$this->view->template['name'] = $template->name;
			$this->view->template['template'] = $template->template;
		}
		
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}
	
	public function saveTask()
	{
		$template = JRequest::getVar("template", array(), 'post', 'ARRAY', JREQUEST_ALLOWHTML);
		                                          
		$database = JFactory::getDBO();
		$nt = new NewsletterTemplate( $database );
		
		//save the story 
		if($nt->save($template))
		{
			$this->_redirect = 'index.php?option=com_newsletter&controller=template';
			$this->_message = JText::_('Campaign Template Successfully Saved');
		}
	}
	
	public function cancel()
	{
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
	}
    

}
