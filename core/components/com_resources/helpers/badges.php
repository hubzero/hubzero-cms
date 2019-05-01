<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Helpers;

require_once Component::path('com_tags') . '/models/objct.php';
require_once Component::path('com_tags') . '/models/tag.php';

use Components\Tags\Models\Objct;
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
	 * @param   string   $rawTagNames   Comma separated list of raw_tag names
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
		$tagAssociation = Objct::blank();
		$tagAssociation->set([
			'objectid' => $this->scopeId,
			'tagid' => $tagId,
			'strength' => $strength,
			'taggerid' => $taggerId,
			'taggedon' => $taggedOn,
			'tbl' => $this->scope,
			'label' => self::LABEL
		]);

		$tagAssociation->save();
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
		$tagAssociationId = Objct::all()
			->select('id')
			->whereEquals('tagid', $tagId)
			->whereEquals('tbl', $this->scope)
			->whereEquals('objectid', $this->scopeId)
			->rows()->toArray()[0]['id'];
		$tagAssociation = Objct::one($tagAssociationId);

		$tagAssociation->destroy();
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
		$badgeIds = Objct::all()
			->select('tagid')
			->whereEquals('tbl', $this->scope)
			->whereEquals('objectid', $this->scopeId)
			->whereEquals('label', self::LABEL)
			->rows()->toArray();
		$badges = array_map(function($badgeId) {
			$badgeId = $badgeId['tagid'];
			return Tag::one($badgeId);
		}, $badgeIds);

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
