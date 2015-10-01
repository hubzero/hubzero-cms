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

namespace Components\Kb\Models;

require_once(__DIR__ . DS . 'category.php');

/**
 * Knowledgebase archive model class
 */
class Archive
{
	/**
	 * Returns a reference to this model
	 *
	 * @param   string  $key
	 * @return  object
	 */
	static function &getInstance($key='site')
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self();
		}

		return $instances[$key];
	}

	/**
	 * Get a count or list of categories
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function categories($filters = array())
	{
		$counts = $this->articles()
			->select('category')
			->select('count(*)', 'articles');

		if (isset($filters['state']))
		{
			$counts->whereEquals('state', $filters['state']);
		}

		if (isset($filters['access']))
		{
			$counts->whereIn('access', $filters['access']);
		}

		$cts = $counts->group('category')
			->rows();

		$categories = Category::all();

		if (isset($filters['state']))
		{
			$categories->whereEquals('published', $filters['state']);
		}

		if (isset($filters['access']))
		{
			$categories->whereIn('access', $filters['access']);
		}

		$cats = $categories->whereEquals('parent_id', 1)
			->order('title', 'ASC')
			->rows();

		foreach ($cats as $category)
		{
			foreach ($cts as $c)
			{
				if ($c->get('category') == $category->get('id'))
				{
					$category->set('articles', $c->get('articles'));
				}
			}
		}

		return $cats;
	}

	/**
	 * Get a count or list of articles
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function articles()
	{
		return Article::all();
	}
}
