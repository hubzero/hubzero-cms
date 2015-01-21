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

namespace Modules\Popular;

use Hubzero\Module\Module;
use JModelLegacy;
use JFactory;
use JException;
use JRoute;
use JText;
use JCategories;

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
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_content/models', 'ContentModel');

		jimport('joomla.application.categories');

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
	 * @param   JObject  The module parameters.
	 * @return  array
	 */
	public static function getList($params)
	{
		// Initialise variables
		$user = JFactory::getuser();

		// Get an instance of the generic articles model
		$model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		// Set List SELECT
		$model->setState('list.select', 'a.id, a.title, a.checked_out, a.checked_out_time, a.created, a.hits');

		// Set Ordering filter
		$model->setState('list.ordering', 'a.hits');
		$model->setState('list.direction', 'DESC');

		// Set Category Filter
		$categoryId = $params->get('catid');
		if (is_numeric($categoryId))
		{
			$model->setState('filter.category_id', $categoryId);
		}

		// Set User Filter.
		$userId = $user->get('id');
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
			throw new JException($error, 500);
			return false;
		}

		// Set the links
		foreach ($items as &$item)
		{
			if ($user->authorise('core.edit', 'com_content.article.' . $item->id))
			{
				$item->link = JRoute::_('index.php?option=com_content&task=article.edit&id=' . $item->id);
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
	 * @param   JObject The module parameters.
	 * @return  string  The alternate title for the module.
	 */
	public static function getTitle($params)
	{
		$who   = $params->get('user_id');
		$catid = (int) $params->get('catid');

		if ($catid)
		{
			$category = JCategories::getInstance('Content')->get($catid);
			if ($category)
			{
				$title = $category->title;
			}
			else
			{
				$title = JText::_('MOD_POPULAR_UNEXISTING');
			}
		}
		else
		{
			$title = '';
		}

		return JText::plural('MOD_POPULAR_TITLE' . ($catid ? '_CATEGORY' : '') . ($who!='0' ? "_$who" : ''), (int)$params->get('count'), $title);
	}
}
