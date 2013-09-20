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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'section.php');

/**
 * Courses model class for a forum
 */
class ForumModel extends ForumModelAbstract
{
	/**
	 * ForumModelCategory
	 * 
	 * @var object
	 */
	private $_cache = array();

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_stats = array();

	/**
	 * Constructor
	 * 
	 * @param      integer $id Course ID or alias
	 * @return     void
	 */
	public function __construct($scope='site', $scope_id=0)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new stdClass;

		$this->set('scope', $scope);
		$this->set('scope_id', $scope_id);
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
	static function &getInstance($scope='site', $scope_id=0)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		$key = $scope . '_' . $scope_id;

		if (!isset($instances[$key])) 
		{
			$instances[$key] = new ForumModel($scope, $scope_id);
		}

		return $instances[$key];
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @access	public
	 * @param	string $property The name of the property
	 * @param	mixed  $default The default value
	 * @return	mixed The value of the property
	 * @see		getProperties()
	 * @since	1.5
 	 */
	public function get($property, $default=null)
	{
		if (isset($this->_tbl->$property)) 
		{
			return $this->_tbl->$property;
		}
		return $default;
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @access	public
	 * @param	string $property The name of the property
	 * @param	mixed  $value The value of the property to set
	 * @return	mixed Previous value of the property
	 * @see		setProperties()
	 * @since	1.5
	 */
	public function set($property, $value = null)
	{
		$previous = isset($this->_tbl->$property) ? $this->_tbl->$property : null;
		$this->_tbl->$property = $value;
		return $previous;
	}

	/**
	 * Check if the forum exists
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function setup()
	{
		// Create a default section
		$section = new ForumModelSection(0, $this->get('scope'), $this->get('scope_id'));
		$section->bind(array(
			'title'    => JText::_('Default Section'),
			'scope'    => $this->get('scope'),
			'scope_id' => $this->get('scope_id'),
			'state'    => 1
		));
		if (!$section->store(true))
		{
			$this->setError($section->getError());
			return false;
		}

		// Create a default category
		$category = new ForumModelCategory(0);
		$category->bind(array(
			'title'       => JText::_('Discussions'),
			'description' => JText::_('Default category for all discussions in this forum.'),
			'section_id'  => $section->get('id'),
			'scope'       => $this->get('scope'),
			'scope_id'    => $this->get('scope_id'),
			'state'       => 1
		));
		if (!$category->store(true))
		{
			$this->setError($category->getError());
			return false;
		}

		/*$model = new ForumCategory($this->database);
		// Check if there are uncategorized posts
		// This should mean legacy data
		if (($posts = $model->getPostCount(0, 0)) || !$this->view->sections || !count($this->view->sections))
		{
			// Create a default section
			$dSection = new ForumSection($this->database);
			$dSection->title = JText::_('Default Section');
			$dSection->scope = 'site';
			$dSection->scope_id = 0;
			$dSection->state = 1;
			if ($dSection->check())
			{
				$dSection->store();
			}

			// Create a default category
			$dCategory = new ForumCategory($this->database);
			$dCategory->title = JText::_('Discussions');
			$dCategory->description = JText::_('Default category for all discussions in this forum.');
			$dCategory->section_id = $dSection->id;
			$dCategory->scope = 'site';
			$dCategory->state = 1;
			$dCategory->scope_id = 0;
			if ($dCategory->check())
			{
				$dCategory->store();
			}

			if ($posts)
			{
				// Update all the uncategorized posts to the new default
				$tModel = new ForumPost($this->database);
				$tModel->updateCategory(0, $dCategory->id, 0);
			}

			$this->view->sections = $sModel->getRecords(array(
				'state' => 1, 
				'scope' => $this->view->filters['scope'],
				'scope_id' => $this->view->filters['scope_id']
			));
		}*/

		$this->_cache['sections'] = new ForumModelIterator(array($section));

		return true;
	}

	/**
	 * Set and get a specific offering
	 * 
	 * @return     void
	 */
	public function section($id=null)
	{
		if (!isset($this->_cache['section']) 
		 || ($id !== null && (int) $this->_cache['section']->get('id') != $id && (string) $this->_cache['section']->get('alias') != $id))
		{
			$this->_cache['section'] = null;
			if (isset($this->_cache['sections']) && is_a($this->_cache['sections'], 'ForumModelIterator'))
			{
				foreach ($this->_cache['sections'] as $key => $section)
				{
					if ((int) $section->get('id') == $id || (string) $section->get('alias') == $id)
					{
						$this->_cache['section'] = $section;
						break;
					}
				}
			}
			
			if (!$this->_cache['section'])
			{
				$this->_cache['section'] = ForumModelSection::getInstance($id, $this->get('scope'), $this->get('scope_id'));
			}
		}
		return $this->_cache['section'];
	}

	/**
	 * Get a list of categories for a forum
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function sections($rtrn='', $filters=array())
	{
		if (!isset($filters['scope']))
		{
			$filters['scope'] = (string) $this->get('scope');
		}
		if (!isset($filters['scope_id']))
		{
			$filters['scope_id'] = (int) $this->get('scope_id');
		}

		$tbl = new ForumSection($this->_db);

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['sections_count'])) // || $this->_cache['filters'] != serialize($filters))
				{
					//$this->_cache['filters'] = serialize($filters);
					$this->_cache['sections_count'] = (int) $tbl->getCount($filters);
				}
				return $this->_cache['sections_count'];
			break;

			case 'first':
				$filters['limit'] = 1;
				$filters['start'] = 0;
				$filters['sort'] = 'created';
				$filters['sort_Dir'] = 'ASC';
				$results = $tbl->getRecords($filters);
				$res = isset($results[0]) ? $results[0] : null;
				return new ForumModelSection($res);
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_cache['sections']) || !is_a($this->_cache['sections'], 'ForumModelIterator'))
				{
					if ($results = $tbl->getRecords($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new ForumModelSection($result);
						}
					}
					else
					{
						$results = array();
					}
					//$this->_entries = new BlogModelIterator($results);
					//$this->_cache['filters']  = serialize($filters);
					$this->_cache['sections'] = new ForumModelIterator($results);
				}
				return $this->_cache['sections'];
			break;
		}
		//return null;
	}

	/**
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action='view', $assetId=null)
	{
		$assetType = 'section';

		$this->config()->set('access-view-' . $assetType, true);

		if (!$juser->get('guest')) 
		{
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$asset  = 'com_forum';
				if ($assetId)
				{
					$asset .= ($assetType != 'component') ? '.' . $assetType : '';
					$asset .= ($assetId) ? '.' . $assetId : '';
				}

				$at = '';
				if ($assetType != 'component')
				{
					$at .= '.' . $assetType;
				}

				// Admin
				$this->config()->set('access-admin-' . $assetType, $juser->authorise('core.admin', $asset));
				$this->config()->set('access-manage-' . $assetType, $juser->authorise('core.manage', $asset));
				// Permissions
				$this->config()->set('access-create-' . $assetType, $juser->authorise('core.create' . $at, $asset));
				$this->config()->set('access-delete-' . $assetType, $juser->authorise('core.delete' . $at, $asset));
				$this->config()->set('access-edit-' . $assetType, $juser->authorise('core.edit' . $at, $asset));
				$this->config()->set('access-edit-state-' . $assetType, $juser->authorise('core.edit.state' . $at, $asset));
				$this->config()->set('access-edit-own-' . $assetType, $juser->authorise('core.edit.own' . $at, $asset));
			}
			else 
			{
				if ($assetType == 'post' || $assetType == 'thread')
				{
					$this->config()->set('access-create-' . $assetType, true);
					$this->config()->set('access-edit-' . $assetType, true);
					$this->config()->set('access-delete-' . $assetType, true);
				}
				if ($juser->authorize($this->_option, 'manage'))
				{
					$this->config()->set('access-manage-' . $assetType, true);
					$this->config()->set('access-admin-' . $assetType, true);
					$this->config()->set('access-create-' . $assetType, true);
					$this->config()->set('access-delete-' . $assetType, true);
					$this->config()->set('access-edit-' . $assetType, true);
				}
			}
		}
	}

	/**
	 * Return a count for the type of data specified
	 * 
	 * @param      string $what What to count
	 * @return     integer
	 */
	public function count($what='threads')
	{
		$what = strtolower(trim($what));

		if (!isset($this->_stats[$what]))
		{
			$this->_stats[$what] = 0;

			switch ($what)
			{
				case 'sections':
					$this->_stats[$what] = $this->sections()->total();
				break;

				case 'categories':
					foreach ($this->sections() as $section)
					{
						$this->_stats[$what] += $section->categories()->total();
					}
				break;

				case 'threads':
					foreach ($this->sections() as $section)
					{
						$this->_stats[$what] += $section->count('threads');
					}
				break;

				case 'posts':
					foreach ($this->sections() as $section)
					{
						$this->_stats[$what] += $section->count('posts');
					}
				break;

				default:
					$this->setError(JText::_('Property value not accepted'));
					return 0;
				break;
			}
		}

		return $this->_stats[$what];
	}

	/**
	 * Get the most recent post mad ein the forum
	 * 
	 * @return     ForumModelPost
	 */
	public function lastActivity()
	{
		if (!isset($this->_cache['last']) || !is_a($this->_cache['last'], 'ForumModelPost'))
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'post.php');

			$post = new ForumPost($this->_db);
			if (!($last = $post->getLastActivity($this->get('scope_id'), $this->get('scope'))))
			{
				$last = 0;
			}
			$this->_cache['last'] = new ForumModelPost($last);
		}
		return $this->_cache['last'];
	}
}

