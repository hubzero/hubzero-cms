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
use Lang;
use Date;
use User;

require_once __DIR__ . DS . 'post.php';

/**
 * Forum model for a category
 */
class Category extends Relational
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
	public $orderBy = 'ordering';

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
		'title'      => 'notempty',
		'section_id' => 'positive|nonzero'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'alias',
		'modified',
		'modified_by',
		'scope'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by',
		'ordering',
		'asset_id'
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
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && $data['alias'] ? $data['alias'] : $data['title']);
		$alias = str_replace(' ', '-', $alias);
		return preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));
	}

	/**
	 * Generates automatic ordering field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticOrdering($data)
	{
		if (!isset($data['ordering']))
		{
			$last = self::all()
				->select('ordering')
				->whereEquals('scope', $data['scope'])
				->whereEquals('scope_id', (isset($data['scope_id']) ? $data['scope_id'] : 0))
				->order('ordering', 'desc')
				->row();

			$data['ordering'] = $last->ordering + 1;
		}

		return $data['ordering'];
	}

	/**
	 * Generates automatic scope field value
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
	 * Generates automatic created field value
	 *
	 * @return  string
	 */
	public function automaticModified()
	{
		return Date::of('now')->toSql();
	}

	/**
	 * Generates automatic created by field value
	 *
	 * @return  int
	 */
	public function automaticModifiedBy()
	{
		return User::get('id');
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
	 * Defines a belongs to one relationship between category and section
	 *
	 * @return  object
	 */
	public function section()
	{
		return $this->belongsToOne('Section', 'section_id')->row();
	}

	/**
	 * Get a list of posts
	 *
	 * @return  object
	 */
	public function posts()
	{
		return $this->oneToMany('Post', 'category_id');
	}

	/**
	 * Get a list of threads
	 *
	 * @return  object
	 */
	public function threads()
	{
		return $this->posts()->whereEquals('parent', 0);
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
	 * Is the record with the given alias unique?
	 *
	 * @return  bool
	 */
	public function isUnique()
	{
		$entries = self::all()
			->whereEquals('alias', $this->get('alias'))
			->whereEquals('section_id', $this->get('section_id'))
			->whereEquals('scope', $this->get('scope'))
			->whereEquals('scope_id', $this->get('scope_id'))
			->where('state', '!=', self::STATE_DELETED);

		if (!$this->isNew())
		{
			$entries->where('id', '!=', $this->get('id'));
		}

		$row = $entries->row();

		return ($row->get('id') <= 0);
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove posts
		foreach ($this->posts()->rows() as $post)
		{
			if (!$post->destroy())
			{
				$this->addError($post->getError());
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
				'com_forum.category.description',
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
		if (!$this->get('access'))
		{
			$this->set('access', (int) \Config::get('access'));
		}

		$result = parent::save();

		// Make sure state changes carry through to posts
		if ($result)
		{
			foreach ($this->posts()->rows() as $post)
			{
				// If it's marked as deleted, skip it
				if ($post->get('state') == self::STATE_DELETED)
				{
					continue;
				}

				$post->set('state', $this->get('state'));
				$post->save();
			}
		}

		return $result;
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
			if (!$this->get('section_alias'))
			{
				$this->set('section_alias', $this->section()->get('alias'));
			}
			$this->adapter->set('section', $this->get('section_alias'));
			$this->adapter->set('category', $this->get('alias'));
		}

		return $this->adapter;
	}

	/**
	 * Get the most recent post made in the thread
	 *
	 * @return  object
	 */
	public function lastActivity()
	{
		$last = $this->posts()
			->whereEquals('state', self::STATE_PUBLISHED)
			->whereIn('access', User::getAuthorisedViewLevels());

		return $last->order('created', 'desc')
			->limit(1)
			->row();
	}
}
