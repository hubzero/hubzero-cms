<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Karma\Karma;

/**
 * Karma source for the news component
 *
 * Standing here follows other people's judgement of what a member wrote, not
 * the fact of their having written it. Nothing pays for posting, or for
 * commenting often, or for being early — those reward volume, and volume is
 * the one thing a discussion never runs short of.
 */
class plgKarmaStory extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * The scale these rules move
	 *
	 * @var  string
	 */
	const SCALE = 'global';

	/**
	 * Declare the rules this plugin emits
	 *
	 * These values are only what gets seeded on install. Once a rule exists
	 * the administrator owns it, and nothing here writes over what they set.
	 *
	 * @return  array
	 */
	public function onKarmaRules()
	{
		$this->loadLanguage();

		return array(
			array(
				'plugin'         => 'story',
				'alias'          => 'story.comment.upmoderated',
				'scale'          => self::SCALE,
				'title'          => Lang::txt('PLG_KARMA_STORY_RULE_UPMODERATED'),
				'description'    => Lang::txt('PLG_KARMA_STORY_RULE_UPMODERATED_DESC'),
				'delta'          => 1,
				'daily_cap'      => 8,
				'per_source_cap' => 2,
				'requires_karma' => 0
			),
			array(
				'plugin'         => 'story',
				'alias'          => 'story.comment.downmoderated',
				'scale'          => self::SCALE,
				'title'          => Lang::txt('PLG_KARMA_STORY_RULE_DOWNMODERATED'),
				'description'    => Lang::txt('PLG_KARMA_STORY_RULE_DOWNMODERATED_DESC'),
				'delta'          => -1,
				'daily_cap'      => 4,
				'per_source_cap' => 1,
				'requires_karma' => 0
			),
			array(
				'plugin'         => 'story',
				'alias'          => 'story.submission.accepted',
				'scale'          => self::SCALE,
				'title'          => Lang::txt('PLG_KARMA_STORY_RULE_ACCEPTED'),
				'description'    => Lang::txt('PLG_KARMA_STORY_RULE_ACCEPTED_DESC'),
				'delta'          => 3,
				'daily_cap'      => 9,
				'per_source_cap' => 1,
				'requires_karma' => 0
			)
		);
	}

	/**
	 * Somebody moderated a comment
	 *
	 * The direction of the moderation decides the rule; a moderation that
	 * moved nothing moves no karma either.
	 *
	 * @param   object   $comment  \Components\Story\Models\Comment, or its id
	 * @param   object   $moderation
	 * @return  void
	 */
	public function onStoryCommentModerated($comment, $moderation = null)
	{
		if (!$author = $this->authorOf($comment))
		{
			return;
		}

		$delta = is_object($moderation) ? (float) $moderation->get('delta', 0) : (float) $moderation;

		if (!$delta)
		{
			return;
		}

		$actor = is_object($moderation) ? (int) $moderation->get('created_by', 0) : 0;

		// Moderating your own comment is not somebody else's judgement of it.
		// The moderation library refuses this outright; the check is repeated
		// because a karma rule that trusts its caller is a karma rule that
		// eventually pays somebody for their own opinion of themselves.
		if ($actor && $actor === (int) $author)
		{
			return;
		}

		Karma::award($author, $delta > 0 ? 'story.comment.upmoderated' : 'story.comment.downmoderated', array(
			'scale'       => self::SCALE,
			'actor'       => $actor,
			'source_type' => 'com_story.comment',
			'source_id'   => $this->idOf($comment)
		));
	}

	/**
	 * A moderation was undone
	 *
	 * The per-source cap counts active awards, so revoking one frees the slot
	 * and the same comment can earn again if it is moderated again.
	 *
	 * @param   object   $comment
	 * @param   object   $moderation
	 * @return  void
	 */
	public function onStoryCommentModerationUndone($comment, $moderation = null)
	{
		if (!$author = $this->authorOf($comment))
		{
			return;
		}

		$delta = is_object($moderation) ? (float) $moderation->get('delta', 0) : (float) $moderation;

		if (!$delta)
		{
			return;
		}

		Karma::revoke(
			'com_story.comment',
			$this->idOf($comment),
			$delta > 0 ? 'story.comment.upmoderated' : 'story.comment.downmoderated',
			$author
		);
	}

	/**
	 * An editor put out a story from somebody's submission
	 *
	 * @param   object  $submission
	 * @return  void
	 */
	public function onStorySubmissionAccepted($submission)
	{
		if (!is_object($submission) || !$author = (int) $submission->get('created_by'))
		{
			return;
		}

		Karma::award($author, 'story.submission.accepted', array(
			'scale'       => self::SCALE,
			'actor'       => (int) $submission->get('decided_by', 0),
			'source_type' => 'com_story.submission',
			'source_id'   => (int) $submission->get('id')
		));
	}

	/**
	 * The id of a comment, given the comment or its id
	 *
	 * @param   mixed  $comment
	 * @return  integer
	 */
	protected function idOf($comment)
	{
		return is_object($comment) ? (int) $comment->get('id') : (int) $comment;
	}

	/**
	 * Who wrote a comment, or zero when that cannot be established
	 *
	 * An anonymous comment has an author on the row, because the hub still
	 * needs to know who said what. But anonymity is a promise about display
	 * and karma is visible standing, so it earns and costs nothing.
	 *
	 * @param   mixed  $comment
	 * @return  integer
	 */
	protected function authorOf($comment)
	{
		if (!is_object($comment))
		{
			$comment = \Components\Story\Models\Comment::oneOrNew((int) $comment);
		}

		if (!$comment->get('id') || $comment->get('anonymous'))
		{
			return 0;
		}

		return (int) $comment->get('created_by');
	}
}
