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

namespace Components\Tags\Models;

use Hubzero\Database\Relational;

require_once \Component::path('com_members') . DS . 'models' . DS . 'member.php';

/**
 * Tag substitute
 */
class Substitute extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'tags';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__tags_substitute';

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
		'raw_tag'  => 'notempty',
		'tag_id'   => 'positive|nonzero'
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
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $always = array(
		'tag'
	);

	/**
	 * Generates automatic tag field
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticTag($data)
	{
		$tag = (isset($data['raw_tag']) && $data['raw_tag'] ? $data['raw_tag'] : $data['tag']);
		return Tag::blank()->normalize($tag);
	}

	/**
	 * Creator profile
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->oneToOne('Components\Members\Models\Member', 'id', 'created_by');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		$timestamp = $this->get('created');

		$as = strtolower($as);

		if ($as == 'date')
		{
			$timestamp = Date::of($timestamp)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			$timestamp = Date::of($timestamp)->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		return $timestamp;
	}

	/**
	 * Save entry
	 *
	 * @return  object
	 */
	public function save()
	{
		$action = $this->isNew() ? 'substitute_created' : 'substitute_edited';

		$result = parent::save();

		if ($result)
		{
			$log = Log::blank();
			$log->set('tag_id', $this->get('id'));
			$log->set('action', $action);
			$log->set('comments', $this->toJson());
			$log->save();
		}

		return $result;
	}

	/**
	 * Get parent tag
	 *
	 * @return  object
	 */
	public function tag()
	{
		return $this->belongsToOne('Tag', 'tag_id');
	}

	/**
	 * Move all references to one tag to another tag
	 *
	 * @param   integer  $oldtagid  ID of tag to be moved
	 * @param   integer  $newtagid  ID of tag to move to
	 * @return  boolean  True if records changed
	 */
	public static function moveTo($oldtagid=null, $newtagid=null)
	{
		if (!$oldtagid || !$newtagid)
		{
			return false;
		}

		$items = self::all()
			->whereEquals('tag_id', $oldtagid)
			->rows();

		$entries = array();

		foreach ($items as $item)
		{
			$item->set('tag_id', $newtagid);
			$item->save();

			$entries[] = $item->toArray();
		}

		$data = new \stdClass;
		$data->old_id  = $oldtagid;
		$data->new_id  = $newtagid;
		$data->entries = $entries;

		$log = Log::blank();
		$log->set([
			'tag_id'   => $newtagid,
			'action'   => 'substitutes_moved',
			'comments' => json_encode($data)
		]);
		$log->save();

		return self::cleanUp($newtagid);
	}

	/**
	 * Clean up duplicate references
	 *
	 * @param   integer  $tag_id  ID of tag to clean up
	 * @return  boolean  True on success, false if errors
	 */
	public static function cleanUp($tag_id)
	{
		$subs = self::all()
			->whereEquals('tag_id', (int)$tag_id)
			->rows();

		$tags = array();

		foreach ($subs as $sub)
		{
			if (!isset($tags[$sub->get('tag')]))
			{
				// Item isn't in collection yet, so add it
				$tags[$sub->get('tag')] = $sub->get('id');
			}
			else
			{
				// Item tag *is* in collection.
				if ($tags[$sub->get('tag')] == $sub->get('id'))
				{
					// Really this shouldn't happen
					continue;
				}
				else
				{
					// Duplcate tag with a different ID!
					// We don't need duplicates.
					$sub->destroy();
				}
			}
		}

		return true;
	}

	/**
	 * Copy all substitutions for one tag to another
	 *
	 * @param   integer  $oldtagid  ID of tag to be copied
	 * @param   integer  $newtagid  ID of tag to copy to
	 * @return  boolean  True if records copied
	 */
	public static function copyTo($oldtagid=null, $newtagid=null)
	{
		if (!$oldtagid || !$newtagid)
		{
			return false;
		}

		$rows = self::all()
			->whereEquals('tag_id', $oldtagid)
			->rows();

		if ($rows)
		{
			$entries = array();

			foreach ($rows as $row)
			{
				$row->set('id', null)
					->set('tag_id', $newtagid)
					->save();

				$entries[] = $row->get('id');
			}

			$data = new \stdClass;
			$data->old_id  = $oldtagid;
			$data->new_id  = $newtagid;
			$data->entries = $entries;

			$log = Log::blank();
			$log->set([
				'tag_id'   => $newtagid,
				'action'   => 'substitutions_copied',
				'comments' => json_encode($data)
			]);
			$log->save();
		}

		return true;
	}
}
