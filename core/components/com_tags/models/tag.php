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
use Lang;
use Date;
use User;

require_once(__DIR__ . DS . 'object.php');
require_once(__DIR__ . DS . 'substitute.php');
require_once(__DIR__ . DS . 'log.php');

/**
 * Tag model
 */
class Tag extends Relational
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
	protected $table = '#__tags';

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
		'raw_tag' => 'notempty'
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
		'tag',
		'modified',
		'modified_by'
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
	 * Generates automatic modified field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		return Date::toSql();
	}

	/**
	 * Generates automatic modified by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  integer
	 */
	public function automaticModifiedBy($data)
	{
		return User::get('id');
	}

	/**
	 * Generates automatic tag field
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticTag($data)
	{
		$tag = (isset($data['raw_tag']) && $data['raw_tag'] ? $data['raw_tag'] : $data['tag']);
		return $this->normalize($tag);
	}

	/**
	 * Normalize tag input
	 *
	 * @param   string  $tag
	 * @return  string
	 */
	public function normalize($tag)
	{
		$transliterationTable = array(
			'á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ă' => 'a', 'Ă' => 'A', 'â' => 'a', 'Â' => 'A', 'å' => 'a', 'Å' => 'A', 'ã' => 'a', 'Ã' => 'A', 'ą' => 'a', 'Ą' => 'A', 'ā' => 'a', 'Ā' => 'A', 'ä' => 'ae', 'Ä' => 'AE', 'æ' => 'ae', 'Æ' => 'AE',
			'ḃ' => 'b', 'Ḃ' => 'B',
			'ć' => 'c', 'Ć' => 'C', 'ĉ' => 'c', 'Ĉ' => 'C', 'č' => 'c', 'Č' => 'C', 'ċ' => 'c', 'Ċ' => 'C', 'ç' => 'c', 'Ç' => 'C',
			'ď' => 'd', 'Ď' => 'D', 'ḋ' => 'd', 'Ḋ' => 'D', 'đ' => 'd', 'Đ' => 'D', 'ð' => 'dh', 'Ð' => 'Dh',
			'é' => 'e', 'É' => 'E', 'è' => 'e', 'È' => 'E', 'ĕ' => 'e', 'Ĕ' => 'E', 'ê' => 'e', 'Ê' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ė' => 'e', 'Ė' => 'E', 'ę' => 'e', 'Ę' => 'E', 'ē' => 'e', 'Ē' => 'E',
			'ḟ' => 'f', 'Ḟ' => 'F', 'ƒ' => 'f', 'Ƒ' => 'F',
			'ğ' => 'g', 'Ğ' => 'G', 'ĝ' => 'g', 'Ĝ' => 'G', 'ġ' => 'g', 'Ġ' => 'G', 'ģ' => 'g', 'Ģ' => 'G',
			'ĥ' => 'h', 'Ĥ' => 'H', 'ħ' => 'h', 'Ħ' => 'H',
			'í' => 'i', 'Í' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I', 'į' => 'i', 'Į' => 'I', 'ī' => 'i', 'Ī' => 'I',
			'ĵ' => 'j', 'Ĵ' => 'J',
			'ķ' => 'k', 'Ķ' => 'K',
			'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L', 'ļ' => 'l', 'Ļ' => 'L', 'ł' => 'l', 'Ł' => 'L',
			'ṁ' => 'm', 'Ṁ' => 'M',
			'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ņ' => 'n', 'Ņ' => 'N',
			'ó' => 'o', 'Ó' => 'O', 'ò' => 'o', 'Ò' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ø' => 'oe', 'Ø' => 'OE', 'ō' => 'o', 'Ō' => 'O', 'ơ' => 'o', 'Ơ' => 'O', 'ö' => 'oe', 'Ö' => 'OE',
			'ṗ' => 'p', 'Ṗ' => 'P',
			'ŕ' => 'r', 'Ŕ' => 'R', 'ř' => 'r', 'Ř' => 'R', 'ŗ' => 'r', 'Ŗ' => 'R',
			'ś' => 's', 'Ś' => 'S', 'ŝ' => 's', 'Ŝ' => 'S', 'š' => 's', 'Š' => 'S', 'ṡ' => 's', 'Ṡ' => 'S', 'ş' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S', 'ß' => 'SS',
			'ť' => 't', 'Ť' => 'T', 'ṫ' => 't', 'Ṫ' => 'T', 'ţ' => 't', 'Ţ' => 'T', 'ț' => 't', 'Ț' => 'T', 'ŧ' => 't', 'Ŧ' => 'T',
			'ú' => 'u', 'Ú' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ŭ' => 'u', 'Ŭ' => 'U', 'û' => 'u', 'Û' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ű' => 'u', 'Ű' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ų' => 'u', 'Ų' => 'U', 'ū' => 'u', 'Ū' => 'U', 'ư' => 'u', 'Ư' => 'U', 'ü' => 'ue', 'Ü' => 'UE',
			'ẃ' => 'w', 'Ẃ' => 'W', 'ẁ' => 'w', 'Ẁ' => 'W', 'ŵ' => 'w', 'Ŵ' => 'W', 'ẅ' => 'w', 'Ẅ' => 'W',
			'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ŷ' => 'y', 'Ŷ' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y',
			'ź' => 'z', 'Ź' => 'Z', 'ž' => 'z', 'Ž' => 'Z', 'ż' => 'z', 'Ż' => 'Z',
			'þ' => 'th', 'Þ' => 'Th', 'µ' => 'u',
			'а' => 'a', 'А' => 'a', 'б' => 'b',
			'Б' => 'b', 'в' => 'v', 'В' => 'v',
			'г' => 'g', 'Г' => 'g', 'д' => 'd',
			'Д' => 'd', 'е' => 'e', 'Е' => 'e',
			'ё' => 'e', 'Ё' => 'e', 'ж' => 'zh',
			'Ж' => 'zh', 'з' => 'z', 'З' => 'z',
			'и' => 'i', 'И' => 'i', 'й' => 'j',
			'Й' => 'j', 'к' => 'k', 'К' => 'k',
			'л' => 'l', 'Л' => 'l', 'м' => 'm',
			'М' => 'm', 'н' => 'n', 'Н' => 'n',
			'о' => 'o', 'О' => 'o', 'п' => 'p',
			'П' => 'p', 'р' => 'r', 'Р' => 'r',
			'с' => 's', 'С' => 's', 'т' => 't',
			'Т' => 't', 'у' => 'u', 'У' => 'u',
			'ф' => 'f', 'Ф' => 'f', 'х' => 'h',
			'Х' => 'h', 'ц' => 'c', 'Ц' => 'c',
			'ч' => 'ch', 'Ч' => 'ch', 'ш' => 'sh',
			'Ш' => 'sh', 'щ' => 'sch', 'Щ' => 'sch',
			'ъ' => '', 'Ъ' => '', 'ы' => 'y',
			'Ы' => 'y', 'ь' => '', 'Ь' => '',
			'э' => 'e', 'Э' => 'e', 'ю' => 'ju',
			'Ю' => 'ju', 'я' => 'ja', 'Я' => 'ja'
		);

		$tag = str_replace(array_keys($transliterationTable), array_values($transliterationTable), $tag);
		return strtolower(preg_replace("/[^a-zA-Z0-9]/", '', $tag));
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		return $this->_datetime($as, 'created');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function modified($as='')
	{
		if (!$this->get('modified') || $this->get('modified') == '0000-00-00 00:00:00')
		{
			$this->set('modified', $this->get('created'));
		}
		return $this->_datetime($as, 'modified');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	private function _datetime($as='', $key='created')
	{
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get($key))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get($key))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get($key);
			break;
		}
	}

	/**
	 * Determine if record was modified
	 *
	 * @return  boolean  True if modified, false if not
	 */
	public function wasModified()
	{
		if ($this->get('modified') && $this->get('modified') != '0000-00-00 00:00:00')
		{
			return true;
		}
		return false;
	}

	/**
	 * Creator profile
	 *
	 * @return  object
	 */
	public function creator()
	{
		if ($profile = Profile::getInstance($this->get('created_by')))
		{
			return $profile;
		}
		return new Profile;
	}

	/**
	 * Get a list of substitutes
	 *
	 * @return  object
	 */
	public function substitutes()
	{
		return $this->oneToMany('Substitute', 'tag_id');
	}

	/**
	 * Get a comma-separated list of substitutes
	 *
	 * @return  string
	 */
	public function transformSubstitutes()
	{
		$subs = array();

		foreach ($this->substitutes()->rows() as $sub)
		{
			$subs[] = $sub->get('raw_tag');
		}

		return implode(', ', $subs);
	}

	/**
	 * Get a list of objects
	 *
	 * @return  object
	 */
	public function objects()
	{
		return $this->oneToMany('Object', 'tagid');
	}

	/**
	 * Get a list of logs
	 *
	 * @return  object
	 */
	public function logs()
	{
		return $this->oneToMany('\Components\Tags\Models\Log', 'tag_id');
	}

	/**
	 * Delete entry and associated data
	 *
	 * @return  object
	 */
	public function destroy()
	{
		$tag_id = $this->get('id');

		$comment = $this->toJson();

		foreach ($this->substitutes()->rows() as $row)
		{
			$row->destroy();
		}

		foreach ($this->objects()->rows() as $row)
		{
			$row->destroy();
		}

		$result = parent::destroy();

		if ($result)
		{
			$log = Log::blank();
			$log->set('tag_id', $tag_id);
			$log->set('action', 'tag_deleted');
			$log->set('comments', $comment);
			$log->save();
		}

		return $result;
	}

	/**
	 * Save entry
	 *
	 * @return  object
	 */
	public function save()
	{
		$action = $this->isNew() ? 'tag_created' : 'tag_edited';

		$result = parent::save();

		if ($result)
		{
			$log = Log::blank();
			$log->set('tag_id', $this->get('id'));
			$log->set('action', $action);
			$log->set('comments', $this->toJson());
			$log->save();
		}

		$this->purgeCache();

		return $result;
	}

	/**
	 * Retrieves one row loaded by a tag field
	 *
	 * @param   string  $tag  The tag to load by
	 * @return  mixed
	 **/
	public static function oneByTag($tag)
	{
		$instance = self::blank();

		$model = $instance->whereEquals('tag', $instance->normalize($tag))->row();

		if (!$model->get('id'))
		{
			$sub = Substitute::blank()
				->whereEquals('tag', $instance->normalize($tag))->row();
			if ($tag_id = $sub->get('tag_id'))
			{
				$model = self::oneOrNew($tag_id);
			}
		}

		return $model;
	}

	/**
	 * Remove this tag from an object
	 *
	 * If $taggerid is provided, it will only remove the tags added to an object by
	 * that specific user
	 *
	 * @param   string   $scope     Object type (ex: resource, ticket)
	 * @param   integer  $scope_id  Object ID (e.g., resource ID, ticket ID)
	 * @param   integer  $tagger    User ID of person to filter tag by
	 * @return  boolean
	 */
	public function removeFrom($scope, $scope_id, $tagger=0)
	{
		// Check if the relationship exists
		$to = Object::oneByScoped($scope, $scope_id, $this->get('id'), $tagger);

		if (!$to->get('id'))
		{
			return true;
		}

		// Attempt to delete the record
		if (!$to->destroy())
		{
			$this->setError($to->getError());
			return false;
		}

		$this->set('objects', $this->objects()->total());
		$this->save();

		return true;
	}

	/**
	 * Add this tag to an object
	 *
	 * @param   string   $scope     Object type (ex: resource, ticket)
	 * @param   integer  $scope_id  Object ID (e.g., resource ID, ticket ID)
	 * @param   integer  $tagger    User ID of person adding tag
	 * @param   integer  $strength  Tag strength
	 * @param   string   $label     Label to apply
	 * @return  boolean
	 */
	public function addTo($scope, $scope_id, $tagger=0, $strength=1, $label='')
	{
		// Check if the relationship already exists
		$to = Object::oneByScoped($scope, $scope_id, $this->get('id'), $tagger);

		if ($to->get('id'))
		{
			return true;
		}

		// Set some data
		$to->set('tbl', (string) $scope);
		$to->set('objectid', (int) $scope_id);
		$to->set('tagid', (int) $this->get('id'));
		$to->set('strength', (int) $strength);

		if ($label)
		{
			$to->set('label', (string) $label);
		}
		if ($tagger)
		{
			$to->set('taggerid', $tagger);
		}

		// Attempt to store the new record
		if (!$to->save())
		{
			$this->setError($to->getError());
			return false;
		}

		$this->set('objects', $this->objects()->total());
		$this->save();

		return true;
	}

	/**
	 * Move all data from this tag to another, including the tag itself
	 *
	 * @param   integer  $tag_id  ID of tag to merge with
	 * @return  boolean
	 */
	public function mergeWith($tag_id)
	{
		if (!$tag_id)
		{
			$this->setError(Lang::txt('Missing tag ID.'));
			return false;
		}

		// Get all the associations to this tag
		// Loop through the associations and link them to a different tag
		if (!Object::moveTo($this->get('id'), $tag_id))
		{
			$this->setError($to->getError());
			return false;
		}

		// Get all the substitutions to this tag
		// Loop through the records and link them to a different tag
		if (!Substitute::moveTo($this->get('id'), $tag_id))
		{
			$this->setError($ts->getError());
			return false;
		}

		// Make the current tag a substitute for the new tag
		$sub = Substitute::blank();
		$sub->set('raw_tag', $this->get('raw_tag'));
		$sub->set('tag_id', $tag_id);
		if (!$sub->save())
		{
			$this->setError($sub->getError());
			return false;
		}

		// Update new tag's counts
		$tag = self::one($tag_id);
		$tag->set('objects', $tag->objects()->total())
			->set('substitutes', $tag->substitutes()->total())
			->save();

		// Destroy the old tag
		if (!$this->destroy())
		{
			return false;
		}

		return true;
	}

	/**
	 * Copy associations from this tag to another
	 *
	 * @param   integer  $tag_id  ID of tag to copy associations to
	 * @return  boolean
	 */
	public function copyTo($tag_id)
	{
		if (!$tag_id)
		{
			$this->setError(Lang::txt('Missing tag ID.'));
			return false;
		}

		// Get all the associations to this tag
		// Loop through the associations and link them to a different tag
		if (!Object::copyTo($this->get('id'), $tag_id))
		{
			$this->setError($to->getError());
			return false;
		}

		// Update new tag's counts
		$tag = self::one($tag_id);
		$tag->set('objects', $tag->objects()->total())
			->save();

		return true;
	}

	/**
	 * Save tag substitutions
	 *
	 * @param   string   $tag_string
	 * @return  boolean
	 */
	public function saveSubstitutions($tag_string='')
	{
		// Get the old list of substitutions
		$subs = array();
		foreach ($this->substitutes()->rows() as $sub)
		{
			$subs[$sub->get('tag')] = $sub;
		}

		// Add the specified tags as substitutes if not
		// already a substitute
		$raw_tags = trim($tag_string);
		$raw_tags = preg_split("/(,|;)/", $raw_tags);

		$tags = array();
		foreach ($raw_tags as $raw_tag)
		{
			$nrm = $this->normalize($raw_tag);

			$tags[] = $nrm;

			if (isset($subs[$nrm]))
			{
				continue; // Substitution already exists
			}

			$sub = Substitute::blank();
			$sub->set('raw_tag', trim($raw_tag));
			$sub->set('tag', trim($nrm));
			$sub->set('tag_id', $this->get('id'));
			if (!$sub->save())
			{
				$this->setError($sub->getError());
			}
		}

		// Run through the old list of substitutions, finding any
		// not in the new list and delete them
		foreach ($subs as $key => $sub)
		{
			if (!in_array($key, $tags))
			{
				if (!$sub->destroy())
				{
					$this->setError($sub->getError());
					return false;
				}
			}
		}

		// Get all possibly existing tags that are now aliases
		$ids = self::all()
			->whereIn('tag', $tags)
			->rows();

		// Move associations on tag and delete tag
		foreach ($ids as $tag)
		{
			if ($tag->get('id') != $this->get('id'))
			{
				// Get all the associations to this tag
				// Loop through the associations and link them to a different tag
				Object::moveTo($tag->get('id'), $this->get('id'));

				// Get all the substitutions to this tag
				// Loop through the records and link them to a different tag
				Substitute::moveTo($tag->get('id'), $this->get('id'));

				// Delete the tag
				$tag->destroy();
			}
		}

		$this->set('objects', $this->objects()->total());
		$this->set('substitutes', $this->substitutes()->total());
		$this->save();

		return true;
	}
}

