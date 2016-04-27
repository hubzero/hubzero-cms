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

namespace Modules\Latest;

use Hubzero\Module\Module;
use Exception;
use Route;
use Lang;
use User;

/**
 * Module class for displaying the latest articles
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

		\JModelLegacy::addIncludePath(PATH_CORE . '/components/com_content/admin/models', 'ContentModel');

		jimport('joomla.application.categories');

		// [!] Legacy compatibility
		$params = $this->params;

		// Get module data.
		$list = $this->getList($params);

		// Render the module
		require $this->getLayoutPath($params->get('layout', 'default'));
	}

	/**
	 * Get a list of articles.
	 *
	 * @param   object  $params  The module parameters.
	 * @return  mixed   An array of articles, or false on error.
	 */
	public static function getList($params)
	{
		// Get an instance of the generic articles model
		$model = \JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		// Set List SELECT
		$model->setState('list.select', 'a.id, a.title, a.checked_out, a.checked_out_time, a.access, a.created, a.created_by, a.created_by_alias, a.featured, a.state');

		// Set Ordering filter
		switch ($params->get('ordering'))
		{
			case 'm_dsc':
				$model->setState('list.ordering', 'modified DESC, created');
				$model->setState('list.direction', 'DESC');
			break;

			case 'c_dsc':
			default:
				$model->setState('list.ordering', 'created');
				$model->setState('list.direction', 'DESC');
			break;
		}

		// Set Category Filter
		$categoryId = $params->get('catid');
		if (is_numeric($categoryId))
		{
			$model->setState('filter.category_id', $categoryId);
		}

		// Set User Filter.
		$userId = User::get('id');
		switch ($params->get('user_id'))
		{
			case 'by_me':
				$model->setState('filter.author_id', $userId);
			break;

			case 'not_me':
				$model->setState('filter.author_id', $userId);
				$model->setState('filter.author_id.include', false);
			break;
		}

		// Set the Start and Limit
		$model->setState('list.start', 0);
		$model->setState('list.limit', $params->get('count', 5));

		$items = $model->getItems();

		if ($error = $model->getError())
		{
			throw new Exception($error, 500);
			return false;
		}

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
		$catid = (int)$params->get('catid');
		$type  = $params->get('ordering') == 'c_dsc' ? '_CREATED' : '_MODIFIED';

		if ($catid)
		{
			$category = \JCategories::getInstance('Content')->get($catid);
			if ($category)
			{
				$title = $category->title;
			}
			else
			{
				$title = Lang::txt('MOD_LATEST_UNEXISTING');
			}
		}
		else
		{
			$title = '';
		}

		return Lang::txts('MOD_LATEST_TITLE' . $type . ($catid ? '_CATEGORY' : '') . ($who != '0' ? "_$who" : ''), (int)$params->get('count'), $title);
	}
}
