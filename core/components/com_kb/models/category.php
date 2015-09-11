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

namespace Components\Kb\Models;

use Components\Kb\Tables;
use Hubzero\Base\ItemList;
use Hubzero\Base\Model;
use User;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'category.php');
require_once(__DIR__ . DS . 'article.php');

/**
 * Knowledgebase model for a category
 */
class Category extends Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Kb\\Tables\\Category';

	/**
	 * Category
	 *
	 * @var object
	 */
	private $_parent = null;

	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_children = null;

	/**
	 * child category count
	 *
	 * @var integer
	 */
	private $_children_count = null;

	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_articles = null;

	/**
	 * Article count
	 *
	 * @var integer
	 */
	private $_articles_count = null;

	/**
	 * Base URL
	 *
	 * @var string
	 */
	private $_base = 'index.php?option=com_kb';

	/**
	 * Returns a reference to this model
	 *
	 * @param   mixed   $oid
	 * @return  object
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new self($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Get a list of articles
	 *
	 * @param      string   $rtrn     Data type to return [count, list]
	 * @param      array    $filters  Filters to apply to query
	 * @param      boolean  $clear    Clear cached data?
	 * @return     mixed    Returns an integer or iterator object depending upon format chosen
	 */
	public function articles($rtrn='list', $filters=array(), $clear=false)
	{
		$tbl = new Tables\Article($this->_db);

		if ($this->get('section'))
		{
			if (!isset($filters['section']))
			{
				$filters['section'] = $this->get('section');
			}
			if (!isset($filters['category']))
			{
				$filters['category'] = $this->get('id');
			}
		}
		else
		{
			if (!isset($filters['section']))
			{
				$filters['section'] = $this->get('id');
			}
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = self::APP_STATE_PUBLISHED;
		}

		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']))
		{
			$filters['sort_Dir'] = 'ASC';
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_articles_count) || !is_numeric($this->_articles_count) || $clear)
				{
					$this->_articles_count = $tbl->find('count', $filters);
				}
				return $this->_articles_count;
			break;

			case 'list':
			case 'results':
			default:
				if (!$this->_articles instanceof ItemList || $clear)
				{
					if ($results = $tbl->find('list', $filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Article($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_articles = new ItemList($results);
				}
				return $this->_articles;
			break;
		}
	}

	/**
	 * Get a list of responses
	 *
	 * @param      string   $rtrn     Data type to return [count, list]
	 * @param      array    $filters  Filters to apply to query
	 * @param      boolean  $clear    Clear cached data?
	 * @return     mixed    Returns an integer or iterator object depending upon format chosen
	 */
	public function children($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['section']))
		{
			$filters['section'] = $this->get('id');
		}
		if (!isset($filters['state']))
		{
			$filters['state']   = self::APP_STATE_PUBLISHED;
		}
		if (!isset($filters['access']))
		{
			$filters['access']  = User::getAuthorisedViewLevels();
		}
		if (!isset($filters['empty']))
		{
			$filters['empty']   = false;
		}

		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']))
		{
			$filters['sort_Dir'] = 'ASC';
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_children_count) || !is_numeric($this->_children_count) || $clear)
				{
					$this->_children_count = $this->_tbl->find('count', $filters);
				}
				return $this->_children_count;
			break;

			case 'list':
			case 'results':
			default:
				if (!$this->_children instanceof ItemList || $clear)
				{
					if ($results = $this->_tbl->find('list', $filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Category($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_children = new ItemList($results);
				}
				return $this->_children;
			break;
		}
	}

	/**
	 * Get parent section
	 *
	 * @return  object
	 */
	public function parent()
	{
		if (!($this->_parent instanceof Category))
		{
			$this->_parent = Category::getInstance($this->get('section', 0));
		}
		return $this->_parent;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type  The type of link to return
	 * @return  string
	 */
	public function link($type='')
	{
		$link  = $this->_base;
		if ($this->get('section'))
		{
			$link .= '&section=' . $this->parent()->get('alias');
			$link .= '&category=' . $this->get('alias');
		}
		else
		{
			$link .= '&section=' . $this->get('alias');
		}

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'component':
			case 'base':
				return $this->_base;
			break;

			case 'edit':
				$link .= '&task=edit';
			break;

			case 'delete':
				$link .= '&task=delete';
			break;

			case 'permalink':
			default:

			break;
		}

		return $link;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function delete()
	{
		// Can't delete what doesn't exist
		if (!$this->exists())
		{
			return true;
		}

		// Remove children
		foreach ($this->children('list') as $category)
		{
			$category->set('delete_action', $this->get('delete_action', 'deletefaqs'));
			if (!$category->delete())
			{
				$this->setError($category->getError());
				return false;
			}
		}

		// Remove articles
		foreach ($this->articles('list') as $article)
		{
			if ($this->get('delete_action', 'deletefaqs') == 'deletefaqs')
			{
				if (!$article->delete())
				{
					$this->setError($article->getError());
					return false;
				}
			}
			else
			{
				$key = ($article->get('category') == $this->get('id') ? 'category' : 'section');

				$article->set($key, 0);
				if (!$article->store(false))
				{
					$this->setError($article->getError());
					return false;
				}
			}
		}

		// Attempt to delete the record
		return parent::delete();
	}
}

