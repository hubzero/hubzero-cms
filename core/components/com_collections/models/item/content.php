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

		include_once(PATH_CORE . DS . 'libraries' . DS . 'joomla' . DS . 'database' . DS . 'table' . DS . 'content.php');

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
