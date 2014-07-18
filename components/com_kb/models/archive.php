<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(__DIR__ . '/category.php');

/**
 * Knowledgebase archive model class
 */
class KbModelArchive extends \Hubzero\Base\Object
{
	/**
	 * KbModelCategory
	 *
	 * @var object
	 */
	private $_category = null;

	/**
	 * \Hubzero\Base\Model
	 *
	 * @var object
	 */
	private $_categories = null;

	/**
	 * Category count
	 *
	 * @var integer
	 */
	private $_categories_count = null;

	/**
	 * \Hubzero\Base\Model
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
	 * JDatabase
	 *
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * JRegistry
	 *
	 * @var object
	 */
	private $_config;

	/**
	 * Constructor
	 *
	 * @param      integer $id Course ID or alias
	 * @return     void
	 */
	public function __construct()
	{
		$this->_db = JFactory::getDBO();

		$this->_config = JComponentHelper::getParams('com_kb');
	}

	/**
	 * Returns a reference to a forum model
	 *
	 * This method must be invoked as:
	 *     $offering = ForumModelCourse::getInstance($alias);
	 *
	 * @param      mixed $oid Course ID (int) or alias (string)
	 * @return     object ForumModelCourse
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
			$instances[$key] = new KbModelArchive();
		}

		return $instances[$key];
	}

	/**
	 * Set and get a specific offering
	 *
	 * @return     void
	 */
	public function category($id=null)
	{
		if (!isset($this->_category)
		 || ($id !== null && (int) $this->_category->get('id') != $id && (string) $this->_category->get('alias') != $id))
		{
			$this->_category = null;

			if ($this->_categories instanceof \Hubzero\Base\ItemList)
			{
				foreach ($this->_categories as $key => $entry)
				{
					if ((int) $category->get('id') == $id || (string) $category->get('alias') == $id)
					{
						$this->_category = $category;
						break;
					}
				}
			}

			if (!$this->_category)
			{
				$this->_category = KbModelCategory::getInstance($id);
			}
		}
		return $this->_category;
	}

	/**
	 * Get a list of categories
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function categories($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['state']))
		{
			$filters['state']   = \Hubzero\Base\Model::APP_STATE_PUBLISHED;
		}
		if (!isset($filters['access']))
		{
			$filters['access']  = 0;
		}
		if (!isset($filters['section']))
		{
			$filters['section'] = 0;
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
			$filters['sort_Dir']  = 'ASC';
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_categories_count) || !is_numeric($this->_categories_count) || $clear)
				{
					$tbl = new KbTableCategory($this->_db);
					$this->_categories_count = (int) $tbl->count($filters);
				}
				return $this->_categories_count;
			break;

			case 'list':
			case 'results':
			default:
				if (!$this->_categories instanceof \Hubzero\Base\ItemList || $clear)
				{
					$tbl = new KbTableCategory($this->_db);
					if ($results = $tbl->find($filters))
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
					$this->_categories = new \Hubzero\Base\ItemList($results);
					return $this->_categories;
				}
			break;
		}
	}

	/**
	 * Get a list of articles
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function articles($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['state']))
		{
			$filters['state']  = \Hubzero\Base\Model::APP_STATE_PUBLISHED;
		}
		if (!isset($filters['access']))
		{
			$filters['access'] = 0;
		}
		if (!isset($filters['start']))
		{
			$filters['start']  = 0;
		}

		switch (strtolower($rtrn))
		{
			case 'popular':
				$filters['sort']     = 'popularity';
				$filters['sort_Dir'] = 'DESC';

				return $this->articles('list', $filters, true);
			break;

			case 'recent':
				$filters['sort']     = 'recent';
				$filters['sort_Dir'] = 'DESC';

				return $this->articles('list', $filters, true);
			break;

			case 'count':
				if (!isset($this->_articles_count) || !is_numeric($this->_articles_count) || $clear)
				{
					$tbl = new KbTableArticle($this->_db);
					$this->_articles_count = (int) $tbl->count($filters);
				}
				return $this->_articles_count;
			break;

			case 'list':
			case 'results':
			default:
				if (!$this->_articles instanceof \Hubzero\Base\ItemList || $clear)
				{
					if (isset($filters['sort']))
					{
						$filters['order'] = $filters['sort'] . " " . $filters['sort_Dir'];
					}
					$tbl = new KbTableArticle($this->_db);
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
					return $this->_articles;
				}
			break;
		}
	}
}
