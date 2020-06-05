<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Admin\Controllers;

use Components\Resources\Models\Entry;
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
		$row = Entry::oneOrFail($id);

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
		$entered  = Request::getString('tags', '');
		$selected = Request::getArray('tgs', array(0));

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

		$tagging->setTags($tags, User::get('id'), 0);

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
