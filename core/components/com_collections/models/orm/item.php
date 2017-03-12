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

namespace Components\Collections\Models\Orm;

use Hubzero\Database\Relational;
use Hubzero\Item\Comment;
use Lang;
use Date;

require_once __DIR__ . DS . 'asset.php';
require_once __DIR__ . DS . 'vote.php';
require_once __DIR__ . DS . 'tags.php';

/**
 * Collection item model
 */
class Item extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'collections';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'created';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'type' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Fields to be parsed
	 *
	 * @var  array
	 */
	protected $parsed = array(
		'description'
	);

	/**
	 * Return a formatted timestamp for created date
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
	 * Creator profile
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Get a list of posts
	 *
	 * @return  object
	 */
	public function posts()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Post', 'item_id');
	}

	/**
	 * Get a list of assets
	 *
	 * @return  object
	 */
	public function assets()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Asset', 'item_id');
	}

	/**
	 * Get a list of votes
	 *
	 * @return  object
	 */
	public function votes()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Vote', 'item_id');
	}

	/**
	 * Get a list of comments
	 *
	 * @return  object
	 */
	public function comments()
	{
		//return $this->oneShiftsToMany(__NAMESPACE__ . '\\Comment', 'item_id', 'item_type')->whereEquals('parent', 0);
		return Comment::all()
			->whereEquals('item_type', 'collection')
			->whereEquals('item_id', $this->get('id'));
	}

	/**
	 * Get tags on an item
	 *
	 * @param   string   $as     How to return data
	 * @param   integer  $admin  Admin tags?
	 * @return  mixed    Returns an array of tags by default
	 */
	public function tags($as='array', $admin=0)
	{
		if ($this->isNew())
		{
			switch (strtolower($as))
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

		$tags = new Tags($this->get('id'));

		return $tags->render($as, array('admin' => $admin));
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
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Can't delete what doesn't exist
		if ($this->isNew())
		{
			return true;
		}

		// Remove posts
		foreach ($this->posts()->rows() as $post)
		{
			if (!$post->destroy())
			{
				$this->addError($post->getError());
				return false;
			}
		}

		// Remove votes
		foreach ($this->votes()->rows() as $vote)
		{
			if (!$vote->destroy())
			{
				$this->addError($vote->getError());
				return false;
			}
		}

		// Remove comments
		foreach ($this->comments()->rows() as $comment)
		{
			if (!$comment->destroy())
			{
				$this->addError($comment->getError());
				return false;
			}
		}

		// Remove assets
		foreach ($this->assets()->rows() as $asset)
		{
			if (!$asset->destroy())
			{
				$this->addError($asset->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}
}
