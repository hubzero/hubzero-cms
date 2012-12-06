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
	public function execute()
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
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @return     array
	 */
	public function displayTask()
	{
		$this->view->dateFormat = $this->dateFormat;
		$this->view->timeFormat = $this->timeFormat;
		$this->view->tz = $this->tz;

		$post_id = JRequest::getInt('id', 0);

		$this->view->post = new BulletinboardStick($this->database);
		$this->view->post->load($post_id);

		$this->view->row = new BulletinboardBulletin($this->database);
		$this->view->row->load($this->view->post->bulletin_id);
		$this->view->row->reposts = $this->view->row->getReposts();
		$this->view->row->voted   = $this->view->row->getVote();

		if (!$this->view->row->id) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
		}

		ximport('Hubzero_Item_Comment');
		$bc = new Hubzero_Item_Comment($this->database);
		$this->view->comments = $bc->getComments($this->view->row->id);

		//count($this->comments, COUNT_RECURSIVE)
		$this->view->comment_total = 0;
		if ($this->view->comments) 
		{
			foreach ($this->view->comments as $com)
			{
				$this->view->comment_total++;
				if ($com->replies) 
				{
					foreach ($com->replies as $rep)
					{
						$this->view->comment_total++;
						if ($rep->replies) 
						{
							$this->view->comment_total = $this->view->comment_total + count($rep->replies);
						}
					}
				}
			}
		}
		$this->view->board = new BulletinboardBoard($this->database);
		$this->view->board->load($this->view->post->board_id);

		$bt = new BulletinboardTags($this->database);
		$this->view->tags = $bt->get_tag_cloud(0, 0, $this->view->row->id);

		$this->view->config = $this->config;

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}
}
