<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_kb' . DS . 'tables' . DS . 'category.php');
require_once(__DIR__ . '/article.php');

/**
 * Knowledgebase model for a category
 */
class KbModelCategory extends \Hubzero\Base\Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'KbTableCategory';

	/**
	 * KbModelCategory
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
	 * Returns a reference to a question model
	 *
	 * This method must be invoked as:
	 *     $offering = AnswersModelQuestion::getInstance($id);
	 *
	 * @param      integer $oid Question ID
	 * @return     object ForumModelCourse
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
			$instances[$oid] = new KbModelCategory($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Get a list of articles
	 *
	 * @param      string  $rtrn    Data type to return [count, list]
	 * @param      array   $filters Filters to apply to query
	 * @param      boolean $clear   Clear cached data?
	 * @return     mixed Returns an integer or iterator object depending upon format chosen
	 */
	public function articles($rtrn='list', $filters=array(), $clear=false)
	{
		$tbl = new KbTableArticle($this->_db);

		if ($this->get('section'))
		{
			if (!isset($filters['section']))
			{
				$filters['section'] = $this->get('section');
			}
			if (!isset($filters['category']))
			{
				$filters['category'] = $this->get('category');
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
			$filters['state']    = self::APP_STATE_PUBLISHED;
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
					$this->_articles_count = $tbl->count($filters);
				}
				return $this->_articles_count;
			break;

			case 'list':
			case 'results':
			default:
				if (!$this->_articles instanceof \Hubzero\Base\ItemList || $clear)
				{
					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new KbModelArticle($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_articles = new \Hubzero\Base\ItemList($results);
				}
				return $this->_articles;
			break;
		}
	}

	/**
	 * Get a list of responses
	 *
	 * @param      string  $rtrn    Data type to return [count, list]
	 * @param      array   $filters Filters to apply to query
	 * @param      boolean $clear   Clear cached data?
	 * @return     mixed Returns an integer or iterator object depending upon format chosen
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
			$filters['access']  = 0;
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
					$this->_children_count = $this->_tbl->count($filters);
				}
				return $this->_children_count;
			break;

			case 'list':
			case 'results':
			default:
				if (!$this->_children instanceof \Hubzero\Base\ItemList || $clear)
				{
					if ($results = $this->_tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new KbModelCategory($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_children = new \Hubzero\Base\ItemList($results);
				}
				return $this->_children;
			break;
		}
	}

	/**
	 * Get parent section
	 *
	 * @return     object KbModelCategory
	 */
	public function parent()
	{
		if (!($this->_parent instanceof KbModelCategory))
		{
			$this->_parent = KbModelCategory::getInstance($this->get('section', 0));
		}
		return $this->_parent;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type The type of link to return
	 * @return     boolean
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
	 * @return    boolean False if error, True on success
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
			if (!$category->delete())
			{
				$this->setError($category->getError());
				return false;
			}
		}

		// Remove articles
		foreach ($this->articles('list') as $article)
		{
			if (!$article->delete())
			{
				$this->setError($article->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::delete();
	}
}

