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

namespace Components\Forum\Models;

use Hubzero\Database\Relational;
use Hubzero\Database\Value\Raw;
use Lang;
use Date;

require_once __DIR__ . DS . 'attachment.php';
require_once __DIR__ . DS . 'tags.php';

/**
 * Forum model for a post
 */
class Post extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'forum';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'lft';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'comment' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'title',
		'scope',
		'modified',
		'modified_by'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by',
		'lft',
		'rgt',
		'asset_id'
	);

	/**
	 * Fields to be parsed
	 *
	 * @var array
	 */
	protected $parsed = array(
		'comment'
	);

	/**
	 * ACL asset rules
	 *
	 * @var  array
	 */
	public $assetRules = null;

	/**
	 * Scope adapter
	 *
	 * @var  object
	 */
	protected $adapter = null;

	/**
	 * Generates automatic scope value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticTitle($data)
	{
		if (!isset($data['title']) || !$data['title'])
		{
			$data['title'] = substr(strip_tags($data['comment']), 0, 70);
			if (strlen($data['title']) >= 70)
			{
				$data['title'] .= '...';
			}
		}
		return $data['title'];
	}

	/**
	 * Generates automatic scope value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticScope($data)
	{
		if (!isset($data['scope']))
		{
			$data['scope'] = 'site';
		}
		return preg_replace("/[^a-zA-Z0-9]/", '', strtolower($data['scope']));
	}

	/**
	 * Generates automatic lft value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticLft($data)
	{
		if (!$data['parent'])
		{
			$data['lft'] = 0;
		}
		return $data['lft'];
	}

	/**
	 * Generates automatic lft value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticRgt($data)
	{
		if (!isset($data['rgt']))
		{
			if (!isset($data['lft']))
			{
				$data['lft'] = $this->automaticLft($data);
			}
			$data['rgt'] = $data['lft'] + 1;
		}
		return $data['rgt'];
	}

	/**
	 * Generates automatic created field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		return ($data['id'] ? Date::of('now')->toSql() : '0000-00-00 00:00:00');
	}

	/**
	 * Generates automatic created by field value
	 *
	 * @return  int
	 */
	public function automaticModifiedBy($data)
	{
		return ($data['id'] ? User::get('id') : 0);
	}

	/**
	 * Defines a belongs to one relationship between category and creator
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Return a formatted created timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		return $this->get('created');
	}

	/**
	 * Defines a belongs to one relationship between category and modifier
	 *
	 * @return  object
	 */
	public function modifier()
	{
		return $this->belongsToOne('Hubzero\User\User', 'modified_by');
	}

	/**
	 * Return a formatted modified timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function modified($as='')
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			return Date::of($this->get('modified'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($this->get('modified'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		return $this->get('modified');
	}

	/**
	 * Determine if record was modified
	 * 
	 * @return  boolean
	 */
	public function wasModified()
	{
		return ($this->get('modified') && $this->get('modified') != '0000-00-00 00:00:00');
	}

	/**
	 * Defines a belongs to one relationship between category and post
	 *
	 * @return  object
	 */
	public function category()
	{
		return $this->belongsToOne('Category', 'category_id')->row();
	}

	/**
	 * Get a list of attachments
	 *
	 * @return  object
	 */
	public function attachments()
	{
		return $this->oneToMany('Attachment', 'post_id');
	}

	/**
	 * Get a list of replies
	 *
	 * @return  object
	 */
	public function replies()
	{
		return self::all()
			->including(['creator', function ($creator){
				$creator->select('*');
			}])
			->whereEquals('parent', $this->get('id'));
	}

	/**
	 * Get a list of replies
	 *
	 * @return  object
	 */
	public function thread()
	{
		return self::all()
			->including(['creator', function ($creator){
				$creator->select('*');
			}])
			->whereEquals('thread', $this->get('thread'));
	}

	/**
	 * Get parent entry
	 *
	 * @return  object
	 */
	public function parent()
	{
		return self::one($this->get('parent'));
	}

	/**
	 * Get tags on an entry
	 *
	 * @param   string   $what   Data format to return (string, array, cloud)
	 * @param   integer  $admin  Get admin tags? 0=no, 1=yes
	 * @return  mixed
	 */
	public function tags($what='cloud', $admin=0)
	{
		if (!$this->get('id'))
		{
			switch (strtolower($what))
			{
				case 'array':
					return array();
				break;

				case 'string':
				case 'cloud':
				case 'html':
				default:
					return '';
				break;
			}
		}

		$cloud = new Tags($this->get('id'));

		return $cloud->render($what, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 *
	 * @param   string   $tags     Tags to apply
	 * @param   integer  $user_id  ID of tagger
	 * @param   integer  $admin    Tag as admin? 0=no, 1=yes
	 * @return  boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new Tags($this->get('id'));

		return $cloud->setTags($tags, $user_id, $admin);
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type    The type of link to return
	 * @param   mixed   $params  Optional string or associative array of params to append
	 * @return  string
	 */
	public function link($type='', $params=null)
	{
		return $this->adapter()->build($type, $params);
	}

	/**
	 * Get the adapter
	 *
	 * @return  object
	 */
	public function adapter()
	{
		if (!$this->adapter)
		{
			// Get the adapter
			$scope = strtolower($this->get('scope', 'site'));
			$cls = __NAMESPACE__ . '\\Adapters\\' . ucfirst($scope);

			if (!class_exists($cls))
			{
				$path = __DIR__ . DS . 'adapters' . DS . $scope . '.php';
				if (!is_file($path))
				{
					throw new \InvalidArgumentException(Lang::txt('Invalid scope of "%s"', $scope));
				}
				include_once($path);
			}

			$this->adapter = new $cls($this->get('scope_id'));

			// Set some needed info
			$this->adapter->set('thread', $this->get('thread'));
			$this->adapter->set('parent', $this->get('parent'));
			$this->adapter->set('post', $this->get('id'));

			if (!$this->get('category'))
			{
				$category = $this->category();
				$this->set('category', $category->get('alias'));
			}
			$this->adapter->set('category', $this->get('category'));

			if (!$this->get('section'))
			{
				$category = $this->category();
				$this->set('section', $category->section()->get('alias'));
			}
			$this->adapter->set('section', $this->get('section'));
		}

		return $this->adapter;
	}

	/**
	 * Is this thread closed?
	 *
	 * @return  boolean
	 */
	public function isClosed()
	{
		return ($this->get('closed') == 1);
	}

	/**
	 * Is this thread sticky?
	 *
	 * @return  boolean
	 */
	public function isSticky()
	{
		return ($this->get('sticky') == 1);
	}

	/**
	 * Has this post been reported?
	 *
	 * @return  boolean
	 */
	public function isReported()
	{
		return ($this->get('state') == 3);
	}

	/**
	 * Get the most recent post made in the thread
	 *
	 * @param   array  $filters
	 * @return  object
	 */
	public function lastActivity($filters=array())
	{
		$last = self::all()
			->whereEquals('state', self::STATE_PUBLISHED)
			->whereEquals('thread', $this->get('thread'))
			->whereIn('access', User::getAuthorisedViewLevels());

		return $last->order('created', 'desc')
			->limit(1)
			->row();
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove replies
		foreach ($this->replies()->rows() as $post)
		{
			if (!$post->destroy())
			{
				$this->addError($post->getError());
				return false;
			}
		}

		// Remove attachments
		foreach ($this->attachments()->rows() as $attachment)
		{
			if (!$attachment->destroy())
			{
				$this->addError($attachment->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Validates the set data attributes against the model rules
	 *
	 * @return  bool
	 **/
	public function validate()
	{
		$valid = parent::validate();

		if ($valid)
		{
			$results = \Event::trigger('content.onContentBeforeSave', array(
				'com_forum.post.comment',
				&$this,
				$this->isNew()
			));

			foreach ($results as $result)
			{
				if ($result === false)
				{
					$this->addError(Lang::txt('Content failed validation.'));
					$valid = false;
				}
			}
		}

		return $valid;
	}

	/**
	 * Save the record
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function save()
	{
		$section  = $this->get('section');
		$this->removeAttribute('section');
		$category = $this->get('category');
		$this->removeAttribute('category');

		if (!$this->get('access'))
		{
			$this->set('access', (int) \Config::get('access'));
		}

		if ($this->isNew() && !$this->get('parent'))
		{
			$this->set('lft', 0);
			$this->set('rgt', 1);
		}

		if ($this->isNew() && $this->get('parent'))
		{
			$parent = $this->parent();

			if (!$parent)
			{
				$this->addError(Lang::txt('Parent node does not exist.'));
				return false;
			}

			// Get the reposition data for shifting the tree and re-inserting the node.
			if (!($reposition = $this->getTreeRepositionData($parent, 2, 'last-child')))
			{
				// Error message set in getNode method.
				return false;
			}

			// Shift left values.
			$query = $this->getQuery()
				->update($this->getTableName())
				->set(['lft' => new Raw('lft + 2')])
				->where($reposition->left_where['col'], $reposition->left_where['op'], $reposition->left_where['val'])
				->whereEquals('scope', $parent->get('scope'))
				->whereEquals('scope_id', $parent->get('scope_id'))
				->whereEquals('thread', $parent->get('thread'));
			if (!$query->execute())
			{
				$this->addError($query->getError());
				return false;
			}

			// Shift right values.
			$query = $this->getQuery()
				->update($this->getTableName())
				->set(['rgt' => new Raw('rgt + 2')])
				->where($reposition->right_where['col'], $reposition->right_where['op'], $reposition->right_where['val'])
				->whereEquals('scope', $parent->get('scope'))
				->whereEquals('scope_id', $parent->get('scope_id'))
				->whereEquals('thread', $parent->get('thread'));
			if (!$query->execute())
			{
				$this->addError($query->getError());
				return false;
			}

			$this->set('lft', $reposition->new_lft);
			$this->set('rgt', $reposition->new_rgt);
		}

		$result = parent::save();

		if ($result)
		{
			// Set the thread ID
			if (!$this->get('parent'))
			{
				$this->set('thread', $this->get('id'));

				$result = parent::save();
			}

			// Make sure state and category changes carry through to replies
			foreach ($this->replies()->rows() as $reply)
			{
				// If it's marked as deleted, skip it
				if ($reply->get('state') != self::STATE_DELETED)
				{
					$reply->set('state', $this->get('state'));
				}
				$reply->set('category_id', $this->get('category_id'));
				$reply->save();
			}

			// Make sure state changes carry through to attachments
			foreach ($this->attachments()->rows() as $attachment)
			{
				$attachment->set('state', $this->get('state'));
				$attachment->save();
			}
		}

		if ($section)
		{
			$this->set('section', $section);
		}
		if ($category)
		{
			$this->set('category', $category);
		}

		return $result;
	}

	/**
	 * Method to get various data necessary to make room in the tree at a location
	 * for a node and its children.  The returned data object includes conditions
	 * for SQL WHERE clauses for updating left and right id values to make room for
	 * the node as well as the new left and right ids for the node.
	 *
	 * @param   object   $referenceNode  A node object with at least a 'lft' and 'rgt' with
	 *                                   which to make room in the tree around for a new node.
	 * @param   integer  $nodeWidth      The width of the node for which to make room in the tree.
	 * @param   string   $position       The position relative to the reference node where the room
	 *                                   should be made.
	 * @return  mixed    Boolean false on failure or data object on success.
	 */
	protected function getTreeRepositionData($referenceNode, $nodeWidth, $position = 'before')
	{
		// Make sure the reference an object with a left and right id.
		if (!is_object($referenceNode) && isset($referenceNode->lft) && isset($referenceNode->rgt))
		{
			return false;
		}

		// A valid node cannot have a width less than 2.
		if ($nodeWidth < 2)
		{
			return false;
		}

		// Initialise variables.
		$k = $this->pk;

		$data = new \stdClass;

		// Run the calculations and build the data object by reference position.
		switch ($position)
		{
			case 'first-child':
				$data->left_where  = array('col' => 'lft', 'op' => '>', 'val' => $referenceNode->lft);
				$data->right_where = array('col' => 'rgt', 'op' => '>=', 'val' => $referenceNode->lft);

				$data->new_lft = $referenceNode->lft + 1;
				$data->new_rgt = $referenceNode->lft + $nodeWidth;
			break;

			case 'last-child':
				$data->left_where  = array('col' => 'lft', 'op' => '>', 'val' => $referenceNode->rgt);
				$data->right_where = array('col' => 'rgt', 'op' => '>=', 'val' => $referenceNode->rgt);

				$data->new_lft = $referenceNode->rgt;
				$data->new_rgt = $referenceNode->rgt + $nodeWidth - 1;
			break;

			case 'before':
				$data->left_where  = array('col' => 'lft', 'op' => '>=', 'val' => $referenceNode->lft);
				$data->right_where = array('col' => 'rgt', 'op' => '>=', 'val' => $referenceNode->lft);

				$data->new_lft = $referenceNode->lft;
				$data->new_rgt = $referenceNode->lft + $nodeWidth - 1;
			break;

			default:
			case 'after':
				$data->left_where  = array('col' => 'lft', 'op' => '>', 'val' => $referenceNode->rgt);
				$data->right_where = array('col' => 'rgt', 'op' => '>', 'val' => $referenceNode->rgt);

				$data->new_lft = $referenceNode->rgt + 1;
				$data->new_rgt = $referenceNode->rgt + $nodeWidth;
			break;
		}

		return $data;
	}

	/**
	 * Get a list of participants from a thread
	 *
	 * @return  object
	 */
	public function participants()
	{
		$user = new \Hubzero\User\User;

		return self::all()
			->select($this->getTableName() . '.anonymous')
			->select($this->getTableName() . '.created_by')
			->select($user->getTableName() . '.name')
			->join($user->getTableName(), $user->getTableName() . '.id', $this->getTableName() . '.created_by', 'left')
			->whereEquals('thread', $this->get('thread'))
			->group('created_by');
	}

	/**
	 * Turn a list of rows into a tree
	 *
	 * @param   object  $rows
	 * @return  array
	 */
	public function toTree($rows)
	{
		$results = array();

		if ($rows->count() > 0)
		{
			$children = array(
				0 => array()
			);

			foreach ($rows as $row)
			{
				$pt   = $row->get('parent');
				$list = @$children[$pt] ? $children[$pt] : array();

				array_push($list, $row);

				$children[$pt] = $list;
			}

			$results = $this->treeRecurse($children[0], $children);
		}

		return $results;
	}

	/**
	 * Recursive function to build tree
	 *
	 * @param   array    $children  Container for parent/children mapping
	 * @param   array    $list      List of records
	 * @param   integer  $maxlevel  Maximum levels to descend
	 * @param   integer  $level     Indention level
	 * @return  void
	 */
	protected function treeRecurse($children, $list, $maxlevel=9999, $level=0)
	{
		if ($level <= $maxlevel)
		{
			foreach ($children as $v => $child)
			{
				$replies = array();

				if (isset($list[$child->get('id')]))
				{
					$replies = $this->treeRecurse($list[$child->get('id')], $list, $maxlevel, $level+1);
				}

				$children[$v]->set('replies', $replies);
			}
		}
		return $children;
	}
}
