<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Kb\Models;

require_once __DIR__ . DS . 'category.php';

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
