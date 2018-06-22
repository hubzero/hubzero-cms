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
use Lang;
use Date;
use stdClass;

require_once __DIR__ . DS . 'item.php';
require_once __DIR__ . DS . 'collection.php';

/**
 * Collection post model
 */
class Post extends Relational implements \Hubzero\Search\Searchable
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'collections';

	/**
	 * Default order by for model
	 *
	 * @var string
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
		'title'       => 'notempty',
		'object_type' => 'notempty',
		'object_id'   => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by',
		'ordering'
	);

	/**
	 * Fields to be parsed
	 *
	 * @var array
	 */
	protected $parsed = array(
		'description'
	);

	/**
	 * Generates automatic ordering field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  integer
	 */
	public function automaticOrdering($data)
	{
		if (!isset($data['ordering']))
		{
			$last = self::all()
				->select('ordering')
				->whereEquals('collection_id', $data['collection_id'])
				->order('ordering', 'desc')
				->row();

			$data['ordering'] = $last->ordering + 1;
		}

		return $data['ordering'];
	}

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
	 * Get parent collection
	 *
	 * @return  object
	 */
	public function collection()
	{
		return $this->belongsToOne('Collection');
	}

	/**
	 * Get represented item
	 *
	 * @return  object
	 */
	public function item()
	{
		return $this->oneToOne('Item', 'id', 'item_id');
	}

	/**
	 * Is this the orignal (first) posting?
	 *
	 * @return  bool
	 */
	public function isOriginal()
	{
		return ($this->get('original') == 1);
	}

	public function transformDescription()
	{
		$description = $this->get('description');
		$description = \Hubzero\Utility\Sanitize::stripAll($description);
		if (empty($description))
		{
			$description = $this->item->get('description');
			$description = \Hubzero\Utility\Sanitize::stripAll($description);
		}
		return $description;
	}

	/**
	 * Remove a post
	 *
	 * @return  boolean  True on success, false on error
	 */
	public function remove()
	{
		if ($this->isOriginal())
		{
			$this->addError(Lang::txt('Original posts must be deleted or moved.'));
			return false;
		}

		return $this->destroy();
	}

	/**
	 * Move a post
	 *
	 * @param   integer  $collection_id
	 * @return  boolean  True on success, false on error
	 */
	public function move($collection_id)
	{
		$collection_id = intval($collection_id);

		if (!$collection_id)
		{
			$this->addError(Lang::txt('Empty collection ID.'));
			return false;
		}

		$this->set('collection_id', $collection_id);

		return $this->save();
	}

	/**
	 * Namespace used for solr Search
	 *
	 * @return  string
	 */
	public static function searchNamespace()
	{
		$searchNamespace = 'collection';
		return $searchNamespace;
	}

	/**
	 * Generate solr search Id
	 *
	 * @return  string
	 */
	public function searchId()
	{
		$searchId = self::searchNamespace() . '-' . $this->id;
		return $searchId;
	}

	/**
	 * Generate search document for Solr
	 *
	 * @return  array
	 */
	public function searchResult()
	{
		if ($this->item->state != 1 || $this->collection->state != 1)
		{
			return false;
		}
		$post = new stdClass;
		$post->title = $this->item->title;
		$ownerType = $this->collection->object_type;
		$post->owner_type = $ownerType == 'member' ? 'user' : $ownerType;
		$post->access_level = $this->getAccessLevel();
		$post->author[] = $this->creator->name;
		$post->owner = $this->collection->object_id;
		$post->hubtype = self::searchNamespace();
		$post->id = $this->searchId();
		$post->description = $this->description;
		$post->url = rtrim(Request::root(), '/') . Route::urlForClient('site', $this->link());
		return $post;
	}

	/**
	 * Get access level of post based on parent collection and group permissions
	 *
	 * @return  string
	 */
	public function getAccessLevel()
	{
		$accessLevel = array();
		$accessLevel[] = $this->collection->access;
		if ($this->collection->object_type == 'group')
		{
			$group = \Hubzero\User\Group::getInstance($this->collection->object_id);
			if ($group)
			{
				$groupAccess = \Hubzero\User\Group\Helper::getPluginAccess($group, 'collections');
				if ($groupAccess == 'anyone')
				{
					$accessLevel[] = 1;
				}
				elseif ($groupAccess == 'registered')
				{
					$accessLevel[] = 2;
				}
				else
				{
					$accessLevel[] = 4;
				}
			}
		}
		$accessLevel = max($accessLevel);
		switch ($accessLevel)
		{
			case 0:
			case 1:
				return 'public';
				break;
			case 2:
				return 'registered';
				break;
			default:
				return 'private';
				break;
		}
	}

	/**
	 * Get total number of records that will be indexed by Solr.
	 *
	 * @return integer
	 */
	public static function searchTotal()
	{
		$total = self::all()->total();
		return $total;
	}

	/**
	 * Get records to be included in solr index
	 *
	 * @param   integer  $limit
	 * @param   integer  $offset
	 * @return  object   Hubzero\Database\Rows
	 */
	public static function searchResults($limit, $offset = 0)
	{
		return self::all()->start($offset)->limit($limit)->rows();
	}

	/**
	 * Get link for current collection post
	 * @return	string
	 */
	public function link()
	{
		return 'index.php?option=collections&controller=posts&post=' . $this->get('id');
	}
}
