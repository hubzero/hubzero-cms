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
 * @author    Anthony Fuentes <fuentesa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Helpers;

require_once Component::path('com_tags') . '/models/tag.php';

use Components\Tags\Models\Tag;
use Hubzero\Utility\Arr;
use App;
use Date;

/**
 * Resources badges class
 */
class Badges
{

	const LABEL = 'badge';
	const TAGS_TABLE = '#__tags';
	const BADGE_TABLE = '#__tags_object';

	/**
	 * Instantiate a Badges instance
	 *
	 * @param   array   $args     Starting state
	 * @return  void
	 */
	public function __construct($args = [])
	{
		$this->scope = $args['scope'];
		$this->scopeId = $args['scopeId'];
		$this->_db = App::get('db');
	}

	/**
	 * Update a records badges
	 *
	 * This will get a records list of badges and
	 *
	 *   1) add any new badges not in the existing list
	 *   2) remove any badges in the existing list not found in the new list
	 *
	 * @param   string   $newBadgesString  String of comma-separated tags
	 * @param   integer  $taggerId         ID of user who associated tag w/ record
	 * @param   boolean  $admin            Mark tags as admin?
	 * @param   integer  $strength         Badge strength
	 * @return  void
	 */
	public function updateBadges($newBadgesString, $taggerId = 0, $admin = 0, $strength = 1)
	{
		if (!$taggerId)
		{
			$taggerId = User::get('id');
		}

		$rawTagNames = $this->_parseBadgesString($newBadgesString);

		$existingTagIds = $this->allBadgesAs('id');
		$updatedTagIds = $this->_translateRawNamesToIds($rawTagNames, $taggerId, $admin);

		$addedTagIds = array_diff($updatedTagIds, $existingTagIds);
		$removedTagIds = array_diff($existingTagIds, $updatedTagIds);

		$this->_addBadges($addedTagIds, $taggerId, $admin, $strength);
		$this->_removeBadges($removedTagIds);
	}

	/**
	 * Convert string of raw_tag names into formatted array
	 *
	 * @param   string   $rawTagNames   Comma seperated list of raw_tag names
	 * @return  void
	 */
	protected function _parseBadgesString($newBadgesString)
	{
		$rawTagNames = explode(',', $newBadgesString);
		$rawTagNames = array_map(function($rawTagName) {
			return trim($rawTagName);
		}, $rawTagNames);

		return $rawTagNames;
	}

	/**
	 * Retrieve IDs based on tag names
	 *
	 * @param   array    $rawTagNames   raw_tag attribute list for given tags
	 * @param   integer  $taggerId      ID of user who associated tag w/ record
	 * @param   boolean  $admin         Mark tags as admin?
	 * @return  void
	 */
	protected function _translateRawNamesToIds($rawTagNames, $taggerId, $admin)
	{
		$nameFormatter = Tag::blank();

		$ids = array_map(function ($rawTagName) use ($nameFormatter, $taggerId, $admin) {
			$tagName = $nameFormatter->normalize($rawTagName);
			$tag = Tag::oneByTag($tagName);

			if ($tag->isNew())
			{
				$this->_createTag($tag, $tagName, $rawTagName, $taggerId, $admin);
			}

			return $tag->get('id');
		}, $rawTagNames);

		return $ids;
	}

	/**
	 * Save a tag record
	 *
	 * @param   object   $tag         Unsaved tag ORM model
	 * @param   string   $tagName     Name of the tag
	 * @param   string   $rawTagName  Display name of tag
	 * @param   integer  $taggerId    ID of user who associated tag w/ record
	 * @param   boolean  $admin       Mark tags as admin?
	 * @return  boolean
	 */
	protected function _createTag($tag, $tagName, $rawTagName, $taggerId, $admin)
	{
		$tagData = [
			'tag' => $tagName,
			'raw_tag' => $rawTagName,
			'admin' => $admin,
			'created' => Date::toSql(),
			'created_by' => $taggerId
		];
		$tag->set($tagData);

		$saved = $tag->save();

		return $saved;
	}

	/**
	 * Associate tags with a record as badges
	 *
	 * @param   array    $tagIds      IDs of tags
	 * @param   integer  $taggerId    ID of user who associated tag w/ record
	 * @param   boolean  $admin       Mark tags as admin?
	 * @param   integer  $strength    Association strength
	 * @return  void
	 */
	protected function _addBadges($tagIds, $taggerId, $admin, $strength)
	{
		foreach ($tagIds as $tagId)
		{
			$this->_addBadge($tagId, $taggerId, $admin, $strength);
		}
	}

	/**
	 * Associate a tag with a record as a badge
	 *
	 * @param   integer  $tagId       ID of tag record
	 * @param   integer  $taggerId    ID of user who associated tag w/ record
	 * @param   boolean  $admin       Mark tags as admin?
	 * @param   integer  $strength    Badge strength
	 * @return  void
	 */
	protected function _addBadge($tagId, $taggerId, $admin, $strength)
	{
		$taggedOn = Date::toSql();
		$query = "INSERT INTO " . self::BADGE_TABLE;
		$query .= " (objectid,tagid,strength,taggerid,taggedon,tbl,label)";
		$query .= "values('$this->scopeId',$tagId,$strength,$taggerId,'$taggedOn','$this->scope',";
		$query .= "'" . self::LABEL . "');";

		$this->_db->setQuery($query);
		$this->_db->execute();
	}

	/**
	 * Disassociate badges (tags) from a record
	 *
	 * @param   array    $tagIds      IDs of tags
	 * @return  void
	 */
	protected function _removeBadges($tagIds)
	{
		foreach ($tagIds as $tagId)
		{
			$this->_removeBadge($tagId);
		}
	}

	/**
	 * Disassociate badge (tag) from a record
	 *
	 * @param   integer  $tagId       ID of tag record
	 * @return  void
	 */
	protected function _removeBadge($tagId)
	{
		$query = 'DELETE FROM ' . self::BADGE_TABLE;
		$query .= " WHERE tagid = $tagId AND tbl = '$this->scope' AND objectid = $this->scopeId;";

		$this->_db->setQuery($query);
		$this->_db->execute();
	}


	/**
	 * @param   string  $attribute   Name of attribute to map badges (tags) to
	 * @return  array
	 */
	public function allBadgesAs($attribute)
	{
		$badges = $this->all();
		$allBadgesAs = [];

		foreach ($badges as $badge)
		{
			$allBadgesAs[] = $badge->$attribute;
		}

		return $allBadgesAs;
	}

	/**
	 * Get all badges for given resource
	 *
	 * @return  array
	 */
	public function all()
	{
		$query = "SELECT tags.id, tags.raw_tag FROM `" . self::TAGS_TABLE . "` AS tags LEFT JOIN `" . self::BADGE_TABLE . "` AS links on links.tagid = tags.id WHERE links.tbl = '$this->scope' AND links.objectid = '$this->scopeId' AND links.label = '" . self::LABEL . "'";

		$this->_db->setQuery($query);
		$badges = $this->_db->loadObjectList();

		return $badges;
	}

	/**
	 * Render badges
	 *
	 * @param   string   $format   Format to render
	 * @return  string
	 */
	public function render($format)
	{
		switch ($format)
		{
			case 'string';
				$output = $this->_renderString();
				return $output;
		}
	}

	/**
	 * Return string of comma delimited badge names
	 *
	 * @return  string
	 */
	protected function _renderString()
	{
		$output = '';
		$badges = $this->all();

		foreach ($badges as $badge)
		{
			$output .= $badge->raw_tag . ', ';
		}
		$output = rtrim($output, ', ');

		return $output;
	}

}
