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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Item;

use Hubzero\User\Group;
use Components\Collections\Models\Item as GenericItem;
use Components\Wiki\Models\Book;
use Components\Wiki\Models\Page;
use Request;
use Route;
use Lang;

require_once(dirname(__DIR__) . DS . 'item.php');

/**
 * Collections model for an item
 */
class Wiki extends GenericItem
{
	/**
	 * Item type
	 *
	 * @var  string
	 */
	protected $_type = 'wiki';

	/**
	 * Get the item type
	 *
	 * @param   string  $as  Return type as?
	 * @return  string
	 */
	public function type($as=null)
	{
		if ($as == 'title')
		{
			return Lang::txt('Wiki page');
		}
		return parent::type($as);
	}

	/**
	 * Chck if we're on a URL where an item can be collected
	 *
	 * @return  boolean
	 */
	public function canCollect()
	{
		if (Request::getCmd('option') != 'com_wiki')
		{
			return false;
		}

		if (!Request::getVar('pagename', ''))
		{
			return false;
		}

		return true;
	}

	/**
	 * Create an item entry for a wiki page
	 *
	 * @param   integer  $id  Optional ID to use
	 * @return  boolean
	 */
	public function make($id=null)
	{
		if ($this->exists())
		{
			return true;
		}

		include_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'models' . DS . 'book.php');
		$page = null;

		if (!$id)
		{
			$scope = 'site';
			$scope_id = 0;

			if ($group = Request::getVar('cn', ''))
			{
				$group = Group::getInstance($group);

				$scope = 'group';
				$scope_id = $group->get('gidNumber');
			}

			$book = new Book($scope, $scope_id);
			$page = $book->page();

			$id = $page->get('id');
		}

		$this->_tbl->loadType($id, $this->_type);

		if ($this->exists())
		{
			return true;
		}

		if (!$page)
		{
			$page = Page::oneOrFail($id);
		}

		if (!$page->exists())
		{
			$this->setError(Lang::txt('Wiki page not found.'));
			return false;
		}

		$this->set('type', $this->_type)
		     ->set('object_id', $page->get('id'))
		     ->set('created', $page->get('created'))
		     ->set('created_by', $page->get('created_by'))
		     ->set('title', $page->title)
		     ->set('description', strip_tags($page->version->content($page)))
		     ->set('url', Route::url($page->link()));

		if (!$this->store())
		{
			return false;
		}

		return true;
	}
}
