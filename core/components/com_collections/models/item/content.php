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

namespace Components\Collections\Models\Item;

use Components\Collections\Models\Item as GenericItem;
use Hubzero\Utility\String;
use Request;
use Route;
use Lang;

require_once(dirname(__DIR__) . DS . 'item.php');

/**
 * Collections model for an item
 */
class Content extends GenericItem
{
	/**
	 * Item type
	 *
	 * @var  string
	 */
	protected $_type = 'article';

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
			return Lang::txt('Article');
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
		if (Request::getCmd('option') != 'com_content')
		{
			return false;
		}

		if (!Request::getInt('id', 0))
		{
			return false;
		}

		return true;
	}

	/**
	 * Create an item entry for a resource
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

		$id = ($id ?: Request::getInt('id', 0));

		$this->_tbl->loadType($id, $this->_type);

		if ($this->exists())
		{
			return true;
		}

		include_once(PATH_CORE . DS . 'libraries' . DS . 'Joomla' . DS . 'database' . DS . 'table' . DS . 'content.php');

		$article = new \JTableContent($this->_db);
		$article->load($id);

		if (!$article->id)
		{
			$this->setError(Lang::txt('Article not found.'));
			return false;
		}

		$text = strip_tags($article->introtext);
		$text = str_replace(array("\n", "\r", "\t"), ' ', $text);
		$text = preg_replace('/\s+/', ' ', $text);

		$url = Request::getVar('REQUEST_URI', '', 'server');
		$url = ($url ?: Route::url('index.php?option=com_content&id=' . $article->alias));
		$url = str_replace('?tryto=collect', '', $url);
		$url = str_replace('no_html=1', '', $url);
		$url = trim($url, '&');

		$this->set('type', $this->_type)
		     ->set('object_id', $article->id)
		     ->set('created', $article->created)
		     ->set('created_by', $article->created_by)
		     ->set('title', $article->title)
		     ->set('description', String::truncate($text, 300, array('html' => true)))
		     ->set('url', $url);

		if (!$this->store())
		{
			return false;
		}

		return true;
	}
}
