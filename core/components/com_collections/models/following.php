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

namespace Components\Collections\Models;

use Hubzero\Base\Model;
use Lang;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'following.php');

/**
 * Collections model class for following something/one
 */
class Following extends Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Collections\\Tables\\Following';

	/**
	 * Following
	 *
	 * @var object
	 */
	private $_following = null;

	/**
	 * Follower
	 *
	 * @var object
	 */
	private $_follower = null;

	/**
	 * Constructor
	 *
	 * @param   mixed    $oid             Following ID, array, or object
	 * @param   string   $following_type  Type being followed [collection, member, group]
	 * @param   integer  $follower_id     Follower ID [member, group]
	 * @param   string   $follower_type   [member, group]
	 * @return  void
	 */
	public function __construct($oid=null, $following_type=null, $follower_id=0, $follower_type='member')
	{
		$this->_db = \App::get('db');

		$tbl = $this->_tbl_name;
		$this->_tbl = new $tbl($this->_db);

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
	 * Returns a reference to this object
	 *
	 * @param   mixed    $oid             Following ID, array, or object
	 * @param   string   $following_type  Type being followed [collection, member, group]
	 * @param   integer  $follower_id     Follower ID [member, group]
	 * @param   string   $follower_type   [member, group]
	 * @return  object
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
	 * @param   string  $what  Key name [following, follower]
	 * @return  object
	 */
	private function _adapter($key='following')
	{
		$scope = strtolower($this->get($key . '_type'));
		$cls = __NAMESPACE__ . '\\Following\\' . ucfirst($scope);

		if (!class_exists($cls))
		{
			$path = __DIR__ . '/following/' . $scope . '.php';
			if (!is_file($path))
			{
				throw new \InvalidArgumentException(Lang::txt('Invalid scope of "%s"', $scope));
			}
			include_once($path);
		}

		return new $cls($this->get($key . '_id'));
	}

	/**
	 * Get a count for the specified key
	 *
	 * @param   string   $what  Key name [following, followers, collectios, posts]
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
				if ($value === null && $this->get('following_type') != 'collection')
				{
					$model = Collections::getInstance($this->get('following_type'), $this->get('following_id'));
					$value = $model->collections(array('count'));
					$this->set($what, $value);
				}
			break;

			case 'posts':
				if ($value === null)
				{
					if ($this->get('following_type') != 'collection')
					{
						$model = Archive::getInstance($this->get('following_type'), $this->get('following_id'));
						$value = $model->posts(array('count'));
						$this->set($what, $value);
					}
					else
					{
						$model = Collection::getInstance($this->get('following_id'));
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
	 * @param   integer  $id  ID of record to unfollow
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
