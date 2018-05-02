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

namespace Modules\Popular;

use Hubzero\Module\Module;
use Components\Content\Models\Article;
use Components\Categories\Models\Category;
use Component;
use Exception;
use Route;
use Lang;
use User;

/**
 * Module class for displaying popular articles
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		if (!\App::isAdmin())
		{
			return;
		}

		require_once Component::path('com_content') . '/models/article.php';

		// [!] Legacy compatibility
		$params = $this->params;

		// Get module data.
		$list = $this->getList($params);

		// Render the module
		require $this->getLayoutPath($params->get('layout', 'default'));
	}

	/**
	 * Get a list of the most popular articles
	 *
	 * @param   object  $params  The module parameters.
	 * @return  array
	 */
	public static function getList($params)
	{
		// Get an instance of the generic articles model
		$query = Article::all();

		// Set Category Filter
		$categoryId = $params->get('catid');
		if (is_numeric($categoryId))
		{
			$query->whereEquals('catid', $categoryId);
		}

		// Set User Filter.
		$userId = User::get('id');
		switch ($params->get('user_id'))
		{
			case 'by_me':
				$query->whereEquals('created_by', $userId);
			break;

			case 'not_me':
				$query->where('created_by', '!=', $userId);
			break;
		}

		// Set the Start and Limit
		$query->start(0)
			->limit($params->get('count', 5))
			->order('hits', 'desc');

		$items = $query->rows();

		// Set the links
		foreach ($items as &$item)
		{
			if (User::authorise('core.edit', 'com_content.article.' . $item->id))
			{
				$item->link = Route::url('index.php?option=com_content&task=article.edit&id=' . $item->id);
			}
			else
			{
				$item->link = '';
			}
		}

		return $items;
	}

	/**
	 * Get the alternate title for the module
	 *
	 * @param   object  $params  The module parameters.
	 * @return  string  The alternate title for the module.
	 */
	public static function getTitle($params)
	{
		$who   = $params->get('user_id');
		$catid = (int) $params->get('catid');

		if ($catid)
		{
			require_once Component::path('com_categories') . '/models/category.php';

			$category = Category::one($catid);
			if ($category)
			{
				$title = $category->title;
			}
			else
			{
				$title = Lang::txt('MOD_POPULAR_UNEXISTING');
			}
		}
		else
		{
			$title = '';
		}

		return Lang::txts('MOD_POPULAR_TITLE' . ($catid ? '_CATEGORY' : '') . ($who!='0' ? "_$who" : ''), (int)$params->get('count'), $title);
	}
}
