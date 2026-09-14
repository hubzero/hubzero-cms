<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Moderation\Activity;
use Components\Story\Models\Comment;
use Components\Story\Models\Discussion;

require_once __DIR__ . DS . 'adapter.php';

/**
 * Story comments, as something the moderation library can work on
 *
 * The library knows how credits are earned and spent. It does not know what a
 * discussion is, who has taken part in one, or where to find a comment. That
 * is all this plugin answers.
 */
class plgModerationStory extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * The type this plugin speaks for
	 *
	 * @var  string
	 */
	const TYPE = 'com_story.comment';

	/**
	 * Register the item type and how its economy is tuned
	 *
	 * `permission` is the governance model in one line: whoever holds
	 * story.moderate is eligible for credits, and granting it to nobody
	 * empties the eligible pool. Turning crowd moderation off is a permission
	 * change, not a setting, and needs no special mode or null grantor.
	 *
	 * @return  array
	 */
	public function onModerationItemTypes()
	{
		return array(
			self::TYPE => array(
				'permission'            => 'story.moderate',
				'credits_per_grant'     => (int) $this->params->get('credits_per_grant', 5),
				'credit_lifetime_hours' => (int) $this->params->get('credit_lifetime_hours', 96),
				'grant_interval_hours'  => (int) $this->params->get('grant_interval_hours', 72),
				'grant_fraction'        => (float) $this->params->get('grant_fraction', 0.15),
				'eligible_hitcount'     => (int) $this->params->get('eligible_hitcount', 3),
				'min_account_age_days'  => (int) $this->params->get('min_account_age_days', 30),
				'karma_scale'           => 'global',
				'min_karma'             => (float) $this->params->get('min_karma', 0),

				// Review ships off and stays off until a hub has the volume
				// for it. See Reviewer::hasVolumeFor() for what that means and
				// why switching it on early is worse than leaving it alone.
				'review_enabled'        => (int) $this->params->get('review_enabled', 0),
				'review_consensus'      => (int) $this->params->get('review_consensus', 9),
				'review_min_karma'      => (float) $this->params->get('review_min_karma', 0),
				'review_interval_hours' => (int) $this->params->get('review_interval_hours', 24),
				'review_consequences'   => (string) $this->params->get('review_consequences', '')
			)
		);
	}

	/**
	 * Has this member said anything in this discussion?
	 *
	 * The library asks because only com_story knows what taking part means
	 * here. Somebody who has commented in a discussion may not moderate any
	 * of it — the single most effective restraint the model has, because it
	 * stops moderation being a way to win an argument you are already in.
	 *
	 * A withdrawn comment still counts. You were in the conversation; taking
	 * your words back afterwards does not put you outside it.
	 *
	 * @param   string   $itemType
	 * @param   integer  $containerId  The discussion
	 * @param   integer  $userId
	 * @return  bool
	 */
	public function onModerationHasContributed($itemType, $containerId, $userId)
	{
		if ($itemType != self::TYPE || !$containerId || !$userId)
		{
			return false;
		}

		return (bool) Comment::all()
			->whereEquals('discussion_id', (int) $containerId)
			->whereEquals('created_by', (int) $userId)
			->total();
	}

	/**
	 * Hand back an adapter for a comment id
	 *
	 * Used when moderations have to be undone and the library needs the item
	 * back to move its score.
	 *
	 * @param   string   $itemType
	 * @param   integer  $itemId
	 * @return  mixed    object|null
	 */
	public function onModerationResolveItem($itemType, $itemId)
	{
		if ($itemType != self::TYPE)
		{
			return null;
		}

		return \StoryCommentModeratable::forId($itemId);
	}

	/**
	 * Note that somebody read a discussion
	 *
	 * Moderation is offered to people who read. Only signed-in members are
	 * recorded, and signed-in members are never page-cached, so this never
	 * sits on a cached path.
	 *
	 * @param   object  $discussion
	 * @return  void
	 */
	public function onStoryDiscussionViewed($discussion = null)
	{
		if (User::isGuest())
		{
			return;
		}

		Activity::record(User::get('id'), self::TYPE);
	}

	/**
	 * Somebody commented in a discussion they had moderated
	 *
	 * Taking part after moderating is the same conflict as moderating after
	 * taking part, and it is resolved the same way round: the moderations go,
	 * their scores are put back and the karma they moved is revoked.
	 *
	 * Deliberately not a refusal. Somebody who moderates and then finds they
	 * have something to say should be able to say it — they simply cannot
	 * keep both.
	 *
	 * @param   object  $comment
	 * @return  void
	 */
	public function onStoryCommentSaved($comment = null)
	{
		if (!$comment || !$comment->get('discussion_id') || User::isGuest())
		{
			return;
		}

		$moderator = new \Hubzero\Moderation\Moderator(User::get('id'));

		$moderator->undoIn(self::TYPE, (int) $comment->get('discussion_id'), function ($id)
		{
			return \StoryCommentModeratable::forId($id);
		});
	}
}
