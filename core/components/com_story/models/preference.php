<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;

/**
 * How one reader wants a discussion shown to them
 *
 * Every reader has preferences whether or not they have a row: a guest, and a
 * signed-in reader who has never opened the screen, both get the defaults.
 * forUser() therefore always returns something usable, and only a reader who
 * saves the form ever occupies a row.
 */
class Preference extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'story';

	/**
	 * How the tree is laid out
	 *
	 * Threaded indents by depth and reads as a conversation. Nested is the
	 * same order without the indent, for narrow screens. Flat is oldest to
	 * newest regardless of who replied to whom. None hides the discussion.
	 */
	const MODE_THREADED = 'threaded';
	const MODE_NESTED   = 'nested';
	const MODE_FLAT     = 'flat';
	const MODE_NONE     = 'none';

	/**
	 * Which way round a flat list runs
	 */
	const SORT_OLDEST = 'oldest';
	const SORT_NEWEST = 'newest';

	/**
	 * The modes a reader may choose
	 *
	 * @return  array
	 */
	public static function modes()
	{
		return array(
			self::MODE_THREADED,
			self::MODE_NESTED,
			self::MODE_FLAT,
			self::MODE_NONE
		);
	}

	/**
	 * What a reader gets before they have said otherwise
	 *
	 * @return  array
	 */
	public static function defaults()
	{
		return array(
			'mode'                => self::MODE_THREADED,
			'sort'                => self::SORT_OLDEST,
			'threshold'           => 0,
			'highlight_threshold' => 4,
			'comment_limit'       => 100,
			'comment_spill'       => 50,
			'max_comment_size'    => 4096,
			'hide_scores'         => 0,
			'reparent'            => 1,
			'hide_signatures'     => 0,
			'default_score'       => 1,
			'willing_to_moderate' => 1,
			'bonus_long'          => 0,
			'bonus_short'         => 0,
			'length_long'         => 2000,
			'length_short'        => 200,
			'bonus_anonymous'     => 0,
			'bonus_new_user'      => 0,
			'new_user_percent'    => 10,
			'bonus_karma'         => 0,
			'reason_adjustments'  => ''
		);
	}

	/**
	 * Transform the per-reason adjustments into a Registry
	 *
	 * @return  object
	 */
	public function transformReasonAdjustments()
	{
		return new Registry($this->get('reason_adjustments'));
	}

	/**
	 * One reader's preferences, defaults included
	 *
	 * A guest, or anyone who has never saved the form, gets an unsaved row
	 * carrying the defaults. Callers can read it exactly as they would a
	 * stored one.
	 *
	 * @param   integer  $userId
	 * @return  object
	 */
	public static function forUser($userId)
	{
		$userId = (int) $userId;

		if ($userId)
		{
			$row = self::all()
				->whereEquals('user_id', $userId)
				->row();

			if ($row->get('id'))
			{
				return $row;
			}
		}

		$row = self::blank();
		$row->set(self::defaults());
		$row->set('user_id', $userId);

		return $row;
	}

	/**
	 * Keep the mode and sort to values the renderer understands
	 *
	 * @return  boolean
	 */
	public function save()
	{
		if (!in_array($this->get('mode'), self::modes()))
		{
			$this->set('mode', self::MODE_THREADED);
		}

		if (!in_array($this->get('sort'), array(self::SORT_OLDEST, self::SORT_NEWEST)))
		{
			$this->set('sort', self::SORT_OLDEST);
		}

		return (bool) parent::save();
	}
}
