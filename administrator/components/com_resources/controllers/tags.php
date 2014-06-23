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

/**
 * Manage resource entry tags
 */
class ResourcesControllerTags extends \Hubzero\Component\AdminController
{
	/**
	 * Manage tags on a resource
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->id = JRequest::getInt('id', 0);

		// Get resource title
		$this->view->row = new ResourcesResource($this->database);
		$this->view->row->load($this->view->id);

		// Get all tags
		$query  = "SELECT id, tag, raw_tag, admin FROM `#__tags` ORDER BY raw_tag ASC";
		$this->database->setQuery($query);
		$this->view->tags = $this->database->loadObjectList();
		if ($this->database->getErrorNum())
		{
			echo $this->database->stderr();
			return false;
		}

		// Get tags for this resource
		$rt = new ResourcesTags($this->database);
		$tagsMen = $rt->getTags($this->view->id, 0, 0, 1);

		$mytagarray    = array();
		$myrawtagarray = array();
		foreach ($tagsMen as $tagMen)
		{
			$mytagarray[]    = $tagMen->tag;
			$myrawtagarray[] = $tagMen->raw_tag;
		}
		$this->view->mytagarray = $mytagarray;

		$this->view->objtags = new stdClass;
		$this->view->objtags->tagMen = implode(', ', $myrawtagarray);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Saves changes to the tag list on a resource
	 * Redirects back to main resource listing
	 *
	 * @return     void
	 */
	public function saveTask()
	{
	    // Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id       = JRequest::getInt('id', 0);
		$entered  = JRequest::getVar('tags', '');
		$selected = JRequest::getVar('tgs', array(0));

		// Process tags
		$tagging = new ResourcesTags($this->database);
		$tagArray  = $tagging->_parse_tags($entered);
		$tagArray2 = $tagging->_parse_tags($entered, 1);

		$diffTags = array_diff($tagArray, $selected);
		foreach ($diffTags as $diffed)
		{
			array_push($selected, $tagArray2[$diffed]);
		}
		$tags = implode(',', $selected);
		$tagging->tag_object($this->juser->get('id'), $id, $tags, 0, 1);

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=items',
			JText::sprintf('COM_RESOURCES_TAGS_UPDATED', $id)
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect('index.php?option=' . $this->_option . '&controller=items');
	}
}

