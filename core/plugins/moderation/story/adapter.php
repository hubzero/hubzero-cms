<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * A story comment, as something that can be moderated
 *
 * The whole of what Hubzero\Moderation needs to know about com_story. Seven
 * questions and an answer each; nothing here reaches back into the library.
 */
class StoryCommentModeratable implements \Hubzero\Moderation\Moderatable
{
	/**
	 * The comment
	 *
	 * @var  object
	 */
	protected $comment = null;

	/**
	 * The bounds a comment's score is held to
	 *
	 * Wider than a forum post's, because here the score does more than mark
	 * the useful: it sorts the page and decides what a reader sees at all.
	 * A range with room in it is what makes a threshold a useful dial rather
	 * than an on-off switch.
	 */
	const MIN_SCORE = -1;
	const MAX_SCORE = 5;

	/**
	 * Constructor
	 *
	 * @param   object  $comment  \Components\Story\Models\Comment
	 * @return  void
	 */
	public function __construct($comment)
	{
		$this->comment = $comment;
	}

	/**
	 * Wrap a comment by id, or return null where there is nothing to wrap
	 *
	 * A withdrawn comment is deliberately not wrappable. There is nothing
	 * left in it to have an opinion about, and moderating a tombstone would
	 * move its author's standing over text nobody can read.
	 *
	 * @param   integer  $id
	 * @return  mixed    object|null
	 */
	public static function forId($id)
	{
		$comment = \Components\Story\Models\Comment::oneOrNew((int) $id);

		if (!$comment->get('id') || $comment->isDeleted())
		{
			return null;
		}

		return new self($comment);
	}

	/**
	 * The underlying comment
	 *
	 * @return  object
	 */
	public function comment()
	{
		return $this->comment;
	}

	/**
	 * The item type
	 *
	 * @return  string
	 */
	public function itemType()
	{
		return 'com_story.comment';
	}

	/**
	 * The comment's id
	 *
	 * @return  integer
	 */
	public function itemId()
	{
		return (int) $this->comment->get('id');
	}

	/**
	 * Its score as it stands
	 *
	 * @return  float
	 */
	public function currentScore()
	{
		return (float) $this->comment->get('score', 0);
	}

	/**
	 * The bounds
	 *
	 * @return  array
	 */
	public function scoreBounds()
	{
		return array((float) self::MIN_SCORE, (float) self::MAX_SCORE);
	}

	/**
	 * Move the score
	 *
	 * `score_max` remembers the highest it ever reached, which is what a
	 * reviewer needs later to tell a comment that was lowered from one that
	 * was never raised.
	 *
	 * @param   float    $delta
	 * @param   integer  $reasonId
	 * @return  bool
	 */
	public function applyScore($delta, $reasonId)
	{
		$score = $this->currentScore() + (float) $delta;

		$this->comment->set('score', $score);
		$this->comment->set('reason_id', (int) $reasonId);

		if ($score > (float) $this->comment->get('score_max', $score))
		{
			$this->comment->set('score_max', $score);
		}

		return (bool) $this->comment->save();
	}

	/**
	 * Who wrote it
	 *
	 * An anonymous comment earns and loses nothing: anonymity is a promise
	 * about display, and karma is visible standing. The comment can still be
	 * moderated — the score is about the contribution — but no standing moves.
	 *
	 * @return  integer
	 */
	public function authorId()
	{
		if ($this->comment->get('anonymous'))
		{
			return 0;
		}

		return (int) $this->comment->get('created_by');
	}

	/**
	 * The discussion it sits in
	 *
	 * This is what the participant rule is scoped to: having said anything in
	 * this discussion disqualifies you from moderating any of it.
	 *
	 * @return  integer
	 */
	public function containerId()
	{
		return (int) $this->comment->get('discussion_id');
	}
}
