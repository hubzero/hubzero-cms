<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\Date;
use User;

/**
 * The conversation attached to a story
 *
 * A discussion is its own row rather than a column on the story because a
 * story is not the only thing that will eventually carry one, and because
 * closing a conversation should not mean touching the story it hangs from.
 */
class Discussion extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'story';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'last_activity';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created'
	);

	/**
	 * What the conversation is for
	 *
	 * An open discussion takes comments. A recycled one is kept for reading
	 * but its replies have been folded elsewhere. An archived one is closed
	 * for good.
	 */
	const TYPE_OPEN     = 'open';
	const TYPE_RECYCLE  = 'recycle';
	const TYPE_ARCHIVED = 'archived';

	/**
	 * Who may add to it
	 */
	const COMMENTS_DISABLED    = 'disabled';
	const COMMENTS_ENABLED     = 'enabled';
	const COMMENTS_LOGGED_IN   = 'logged_in';
	const COMMENTS_KARMA_GATED = 'karma_gated';

	/**
	 * The story this hangs from
	 *
	 * @return  object
	 */
	public function story()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Story', 'story_id');
	}

	/**
	 * Its comments, in tree order
	 *
	 * @return  object
	 */
	public function comments()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Comment', 'discussion_id');
	}

	/**
	 * Whether it is taking comments at all
	 *
	 * This answers the discussion's own state only. Whether a particular
	 * person may post is a further question, and belongs to the controller
	 * that knows about permissions and gates.
	 *
	 * @return  boolean
	 */
	public function isOpen()
	{
		if ($this->get('type') != self::TYPE_OPEN)
		{
			return false;
		}

		return ($this->get('comment_status') != self::COMMENTS_DISABLED);
	}

	/**
	 * Whether a reader has to be signed in to add to it
	 *
	 * @return  boolean
	 */
	public function requiresLogin()
	{
		return in_array($this->get('comment_status'), array(
			self::COMMENTS_LOGGED_IN,
			self::COMMENTS_KARMA_GATED
		));
	}

	/**
	 * Note that something was said, and when
	 *
	 * @return  boolean
	 */
	public function touch()
	{
		$this->set('last_activity', Date::of('now')->toSql());

		return (bool) $this->save();
	}

	/**
	 * Count the comments again from the rows themselves
	 *
	 * The stored count is a convenience for list pages. It is recomputed
	 * rather than incremented so that a delete, a rebuild or a direct edit
	 * cannot leave it drifting.
	 *
	 * @return  boolean
	 */
	public function recount()
	{
		$count = Comment::all()
			->whereEquals('discussion_id', $this->get('id'))
			->whereEquals('state', Comment::STATE_PUBLISHED)
			->count();

		$this->set('comment_count', (int) $count);

		return (bool) $this->save();
	}

	/**
	 * The discussion belonging to a story, made if it has none yet
	 *
	 * @param   object   $story
	 * @return  object
	 */
	public static function forStory($story)
	{
		if ($id = (int) $story->get('discussion_id'))
		{
			$row = self::oneOrNew($id);

			if ($row->get('id'))
			{
				return $row;
			}
		}

		$row = self::blank();
		$row->set(array(
			'story_id'       => (int) $story->get('id'),
			'title'          => $story->get('title'),
			'type'           => self::TYPE_OPEN,
			'comment_status' => self::COMMENTS_LOGGED_IN,
			'last_activity'  => Date::of('now')->toSql()
		));

		if (!$row->save())
		{
			return $row;
		}

		$story->set('discussion_id', $row->get('id'));
		$story->save();

		return $row;
	}
}
