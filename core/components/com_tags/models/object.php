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
use Hubzero\User\Profile;
use stdClass;
use Date;
use User;

require_once(__DIR__ . DS . 'log.php');

/**
 * Tag object association
 */
class Object extends Relational
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
	protected $table = '#__tags_object';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'taggedon';

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
		'tbl'      => 'notempty',
		'tagid'    => 'positive|nonzero',
		'objectid' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'taggedon',
		'taggerid'
	);

	/**
	 * Generates automatic taggedon field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticTaggedon($data)
	{
		if (!isset($data['taggedon']) || !$data['taggedon'])
		{
			$data['taggedon'] = Date::toSql();
		}
		return $data['taggedon'];
	}

	/**
	 * Generates automatic taggerid field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  integer
	 */
	public function automaticTaggerid($data)
	{
		if (!isset($data['taggerid']) || !$data['taggerid'])
		{
			$data['taggerid'] = User::get('id');
		}
		return $data['taggerid'];
	}

	/**
	 * Get parent tag
	 *
	 * @return  object
	 */
	public function tag()
	{
		return $this->belongsToOne('Tag', 'tagid');
	}

	/**
	 * Creator profile
	 *
	 * @return  object
	 */
	public function creator()
	{
		if ($profile = Profile::getInstance($this->get('taggerid')))
		{
			return $profile;
		}
		return new Profile;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get('taggedon'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get('taggedon'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('taggedon');
			break;
		}
	}

	/**
	 * Retrieves one row loaded by a tag field
	 *
	 * @param   string   $scope     Object type (ex: resource, ticket)
	 * @param   integer  $scope_id  Object ID (e.g., resource ID, ticket ID)
	 * @param   integer  $tag_id    Tag ID
	 * @param   integer  $tagger    User ID of person adding tag
	 * @return  mixed
	 **/
	public static function oneByScoped($scope, $scope_id, $tag_id, $tagger=0)
	{
		$instance = self::blank()
			->whereEquals('tbl', $scope)
			->whereEquals('objectid', $scope_id)
			->whereEquals('tagid', $tag_id);

		if ($tagger)
		{
			$instance->whereEquals('taggerid', $tagger);
		}

		return $instance->row();
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
			->whereEquals('tagid', $oldtagid)
			->rows();

		$entries = array();

		foreach ($items as $item)
		{
			$item->set('tagid', $newtagid);
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
			'action'   => 'objects_moved',
			'comments' => json_encode($data)
		]);
		$log->save();

		return true;
	}

	/**
	 * Copy all tags on an object to another object
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
			->whereEquals('tagid', $oldtagid)
			->rows();

		if ($rows)
		{
			$entries = array();

			foreach ($rows as $row)
			{
				$row->set('id', null)
					->set('tagid', $newtagid)
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
				'action'   => 'objects_copied',
				'comments' => json_encode($data)
			]);
			$log->save();
		}

		return true;
	}
}

