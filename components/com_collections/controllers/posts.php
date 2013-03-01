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

/**
 * Controller class for bulletin boards
 */
class CollectionsControllerPosts extends Hubzero_Controller
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	/*public function execute()
	{
		$this->_authorize('collection');
		$this->_authorize('item');

		$this->dateFormat = '%d %b %Y';
		$this->timeFormat = '%I:%M %p';
		$this->tz = 0;
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$this->dateFormat = 'd M Y';
			$this->timeFormat = 'H:i p';
			$this->tz = true;
		}

		parent::execute();
	}*/

	/**
	 * View a post
	 * 
	 * @return     void
	 */
	public function postTask()
	{
		echo 'not done';
	}

	/**
	 * View a post
	 * 
	 * @return     void
	 */
	public function commentTask()
	{
		echo 'not done';
	}

	/**
	 * Vote for an item
	 * 
	 * @return     void
	 */
	public function voteTask()
	{
		if ($this->juser->get('guest')) 
		{
			return $this->loginTask();
		}

		// Incoming
		$id = JRequest::getInt('post', 0);

		// Get the post model
		$post = CollectionsModelPost::getInstance($id);

		// Record the vote
		if (!$post->item()->vote())
		{
			$this->setError($post->item()->getError());
		}

		// Display updated item stats if called via AJAX
		$no_html = JRequest::getInt('no_html', 0);
		if ($no_html)
		{
			echo JText::sprintf('%s likes', $post->item()->get('positive'));
			exit;
		}

		// Get the collection model
		//$model = new CollectionsModel('member', $this->juser->get('id'));

		//$collection = $model->collection($post->get('collection_id'));

		// Display the main listing
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&controller=collections&task=posts')
		);
	}

	/**
	 * Repost an entry
	 * 
	 * @return     string
	 */
	public function collectTask()
	{
		if ($this->juser->get('guest')) 
		{
			return $this->loginTask();
		}

		$model = new CollectionsModel('member', $this->juser->get('id'));

		$no_html = JRequest::getInt('no_html', 0);

		// No collection ID selected so present repost form
		$repost = JRequest::getInt('repost', 0);
		if (!$repost)
		{
			// Incoming
			$post_id       = JRequest::getInt('post', 0);
			$collection_id = JRequest::getVar('board', 0);

			if (!$post_id && $collection_id)
			{
				$collection = $model->collection($collection_id);

				$item_id       = $collection->item()->get('id');
				$collection_id = $collection->item()->get('object_id');
			}
			else
			{
				$post = CollectionsModelPost::getInstance($post_id);

				$item_id = $post->get('item_id');
			}

			$this->view->myboards      = $model->mine();
			$this->view->groupboards   = $model->mine('groups');

			//$this->view->name          = $this->_name;
			$this->view->option        = $this->_option;
			$this->view->juser         = $this->juser;
			$this->view->no_html       = $no_html;
			$this->view->post_id       = $post_id;
			$this->view->collection_id = $collection_id;
			$this->view->item_id       = $item_id;

			$this->view->display();
			return;
		}

		$collection_id = JRequest::getInt('collection_id', 0);
		$item_id       = JRequest::getInt('item_id', 0);

		// Try loading the current collection/post to see
		// if this has already been posted to the collection (i.e., no duplicates)
		$post = new CollectionsTablePost($this->database);
		$post->loadByBoard($collection_id, $item_id);
		if (!$post->get('id'))
		{
			// No record found -- we're OK to add one
			$post->item_id       = $item_id;
			$post->collection_id = $collection_id;
			$post->description   = JRequest::getVar('description', '');
			if ($post->check()) 
			{
				$this->setError($post->getError());
			}
			// Store new content
			if (!$post->store()) 
			{
				$this->setError($post->getError());
			}
		}
		if ($this->getError())
		{
			return $this->getError();
		}

		// Display updated item stats if called via AJAX
		if ($no_html)
		{
			echo JText::sprintf('%s reposts', $post->getCount(array('item_id' => $post->get('item_id'), 'original' => 0)));
			exit;
		}

		// Display the main listing
		//return $this->recentTask();
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&controller=collections&task=posts')
		);
	}
}
