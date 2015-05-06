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

namespace Components\Resources\Admin\Controllers;

use Components\Resources\Tables\Resource;
use Components\Resources\Helpers\Tags as TagCloud;
use Hubzero\Component\AdminController;
use stdClass;
use Request;
use Route;
use Lang;
use User;
use App;

/**
 * Manage resource entry tags
 */
class Tags extends AdminController
{
	/**
	 * Manage tags on a resource
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view->id = Request::getInt('id', 0);

		// Get resource title
		$this->view->row = new Resource($this->database);
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
		$rt = new TagCloud($this->view->id);

		$mytagarray    = array();
		$myrawtagarray = array();
		foreach ($rt->tags() as $tagMen)
		{
			$mytagarray[]    = $tagMen->get('tag');
			$myrawtagarray[] = $tagMen->get('raw_tag');
		}
		$this->view->mytagarray = $mytagarray;

		$this->view->objtags = new stdClass;
		$this->view->objtags->tagMen = implode(', ', $myrawtagarray);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Saves changes to the tag list on a resource
	 * Redirects back to main resource listing
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$id       = Request::getInt('id', 0);
		$entered  = Request::getVar('tags', '');
		$selected = Request::getVar('tgs', array(0));

		// Process tags
		$tagging = new TagCloud($id);
		$tagArray  = $tagging->parseTags($entered);
		$tagArray2 = $tagging->parseTags($entered, 1);

		$diffTags = array_diff($tagArray, $selected);
		foreach ($diffTags as $diffed)
		{
			array_push($selected, $tagArray2[$diffed]);
		}
		$tags = implode(',', $selected);
		$tagging->setTags($tags, User::get('id'), 1);

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=items', false),
			Lang::txt('COM_RESOURCES_TAGS_UPDATED', $id)
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=items', false)
		);
	}
}

