<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Admin\Controllers;

use Components\Resources\Tables\Resource;
use Components\Resources\Helpers\Tags as TagCloud;
use Hubzero\Component\AdminController;
use stdClass;
use Request;
use Notify;
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
		$id = Request::getInt('id', 0);

		// Get resource title
		$row = new Resource($this->database);
		$row->load($id);

		if (!$row->get('id'))
		{
			Notify::error(Lang::txt('COM_RESOURCES_NOTFOUND'));

			return $this->cancelTask();
		}

		// Get tags for this resource
		$rt = new TagCloud($id);

		$mytagarray    = array();
		$myrawtagarray = array();
		foreach ($rt->tags() as $tagMen)
		{
			$mytagarray[]    = $tagMen->get('tag');
			$myrawtagarray[] = $tagMen->get('raw_tag');
		}

		// Get all tags
		$tags = $rt->tags('list', array(
			'scope'    => null,
			'scope_id' => 0
		), true);

		$objtags = new stdClass;
		$objtags->tagMen = implode(', ', $myrawtagarray);

		// Output the HTML
		$this->view
			->set('id', $id)
			->set('row', $row)
			->set('tags', $tags)
			->set('mytagarray', $mytagarray)
			->set('objtags', $objtags)
			->setErrors($this->getErrors())
			->display();
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
		Request::checkToken();

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

		Notify::success(Lang::txt('COM_RESOURCES_TAGS_UPDATED', $id));

		// Redirect
		$this->cancelTask();
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
