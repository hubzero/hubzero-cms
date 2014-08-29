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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'following.php');

/**
 * Collections model class for following something/one
 */
class CollectionsModelFollowing extends \Hubzero\Base\Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'CollectionsTableFollowing';

	/**
	 * CollectionsModelFollowingAbstract
	 *
	 * @var object
	 */
	private $_following = null;

	/**
	 * CollectionsModelFollowingAbstract
	 *
	 * @var object
	 */
	private $_follower = null;

	/**
	 * Constructor
	 *
	 * @param   mixed   $oid            Following ID, array, or object
	 * @param   string  $following_type Type being followed [collection, member, group]
	 * @param   integer $follower_id    Follower ID [member, group]
	 * @param   string  $follower_type  [member, group]
	 * @return  void
	 */
	public function __construct($oid=null, $following_type=null, $follower_id=0, $follower_type='member')
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CollectionsTableFollowing($this->_db);

		if (is_numeric($oid))
		{
			if ($oid)
			{
				$this->_tbl->load($oid, $following_type, $follower_id, $follower_type);
			}
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}
	}

	/**
	 * Returns a reference to a CollectionsModelFollowing object
	 *
	 * @param   mixed   $oid            Following ID, array, or object
	 * @param   string  $following_type Type being followed [collection, member, group]
	 * @param   integer $follower_id    Follower ID [member, group]
	 * @param   string  $follower_type  [member, group]
	 * @return  object  CollectionsModelFollowing
	 */
	static function &getInstance($oid=null, $following_type=null, $follower_id=0, $follower_type='member')
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$key = $oid . '_' . $following_type . '_' . $follower_id . '_' . $follower_type;

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $following_type, $follower_id, $follower_type);
		}

		return $instances[$key];
	}

	/**
	 * Return the adapter for this entry's follower,
	 * instantiating it if it doesn't already exist
	 *
	 * @return  object
	 */
	public function follower()
	{
		if (!$this->_follower)
		{
			$this->_follower = $this->_adapter('follower');
		}
		return $this->_follower;
	}

	/**
	 * Return the adapter for this entry's following,
	 * instantiating it if it doesn't already exist
	 *
	 * @return  object
	 */
	public function following()
	{
		if (!$this->_following)
		{
			$this->_following = $this->_adapter('following');
		}
		return $this->_following;
	}

	/**
	 * Get an adapter
	 *
	 * @param   string $what Key name [following, follower]
	 * @return  object
	 */
	private function _adapter($key='following')
	{
		$scope = strtolower($this->get($key . '_type'));
		$cls = 'CollectionsModelFollowing' . ucfirst($scope);

		if (!class_exists($cls))
		{
			$path = dirname(__FILE__) . '/following/' . $scope . '.php';
			if (!is_file($path))
			{
				throw new \InvalidArgumentException(JText::sprintf('Invalid scope of "%s"', $scope));
			}
			include_once($path);
		}

		return new $cls($this->get($key . '_id'));
	}

	/**
	 * Get a count for the specified key
	 *
	 * @param   string $what Key name [following, followers, collectios, posts]
	 * @return  integer
	 */
	public function count($what='following')
	{
		$what = strtolower(trim($what));

		$value = $this->get($what);

		switch ($what)
		{
			case 'following':
				if ($value === null)
				{
					$value = $this->_tbl->count(array(
						'follower_type' => $this->get('following_type'),
						'follower_id'   => $this->get('following_id')
					));
					$this->set($what, $value);
				}
			break;

			case 'followers':
				if ($value === null)
				{
					$value = $this->_tbl->count(array(
						'following_type' => $this->get('following_type'),
						'following_id'   => $this->get('following_id')
					));
					$this->set($what, $value);
				}
			break;

			case 'collections':
				if ($value === null && $thi->get('following_type') != 'collection')
				{
					$model = CollectionsModelCollections::getInstance($this->get('following_type'), $this->get('following_id'));
					$value = $model->collections(array('count'));
					$this->set($what, $value);
				}
			break;

			case 'posts':
				if ($value === null)
				{
					if ($thi->get('following_type') != 'collection')
					{
						$model = CollectionsModelCollections::getInstance($this->get('following_type'), $this->get('following_id'));
						$value = $model->posts(array('count'));
						$this->set($what, $value);
					}
					else
					{
						$model = CollectionsModelCollection::getInstance($this->get('following_id'));
						$value = $model->posts(array('count'));
						$this->set($what, $value);
					}
				}
			break;
		}

		if ($value === null)
		{
			$value = 0;
		}

		return $value;
	}

	/**
	 * Stop following an object
	 *
	 * @param   integer $id ID of record to unfollow
	 * @return  boolean
	 */
	public function unfollow($id=null)
	{
		if (!$id)
		{
			$id = $this->get('id');
		}

		if (!$this->_tbl->delete($id))
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		return true;
	}
}
