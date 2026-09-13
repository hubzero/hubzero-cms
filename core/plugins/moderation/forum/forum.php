<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Forum\Models\Post;
use Hubzero\Moderation\Activity;

require_once __DIR__ . DS . 'adapter.php';

/**
 * Makes forum posts moderatable
 *
 * Everything com_forum has to say to Hubzero\Moderation, in one place. The
 * component itself gains a score column and an affordance; the rules about
 * who may moderate what live here.
 */
class plgModerationForum extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * The item type this plugin speaks for
	 *
	 * @var  string
	 */
	const TYPE = 'com_forum.post';

	/**
	 * Register the item type and how its economy is tuned
	 *
	 * Read by the cron plugin. A hub that disables this plugin has no forum
	 * economy at all — no credits granted, no cron work done.
	 *
	 * @return  array
	 */
	public function onModerationItemTypes()
	{
		return array(
			self::TYPE => array(
				'permission'            => 'forum.moderate',
				'credits_per_grant'     => (int) $this->params->get('credits_per_grant', 5),
				'credit_lifetime_hours' => (int) $this->params->get('credit_lifetime_hours', 96),
				'grant_interval_hours'  => (int) $this->params->get('grant_interval_hours', 72),
				'grant_fraction'        => (float) $this->params->get('grant_fraction', 0.15),
				'eligible_hitcount'     => (int) $this->params->get('eligible_hitcount', 3),
				'min_account_age_days'  => (int) $this->params->get('min_account_age_days', 30),
				'karma_scale'           => 'global',
				'min_karma'             => (float) $this->params->get('min_karma', 0)
			)
		);
	}

	/**
	 * Has this member posted in this thread?
	 *
	 * The library asks because only com_forum knows what taking part means.
	 * Somebody who has posted in a thread may not moderate in it, which is
	 * what stops moderation being a way to win an argument.
	 *
	 * @param   string   $itemType
	 * @param   integer  $containerId  The thread
	 * @param   integer  $userId
	 * @return  bool
	 */
	public function onModerationHasContributed($itemType, $containerId, $userId)
	{
		if ($itemType != self::TYPE || !$containerId || !$userId)
		{
			return false;
		}

		return (bool) Post::all()
			->whereEquals('thread', (int) $containerId)
			->whereEquals('created_by', (int) $userId)
			->where('state', '!=', Post::STATE_DELETED)
			->total();
	}

	/**
	 * Hand back an adapter for a post id
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

		return \ForumPostModeratable::forId($itemId);
	}

	/**
	 * Note that somebody read a thread
	 *
	 * Moderation is offered to people who read. Only signed-in members are
	 * recorded, and signed-in members are never page-cached, so this never
	 * sits on a cached path.
	 *
	 * @param   object  $thread
	 * @return  void
	 */
	public function onForumThreadViewed($thread = null)
	{
		if (User::isGuest())
		{
			return;
		}

		Activity::record(User::get('id'), self::TYPE);
	}

	/**
	 * Somebody posted in a thread they had moderated
	 *
	 * Taking part after moderating is the same conflict as moderating after
	 * taking part, and it is resolved the same way round: the moderations go.
	 *
	 * @param   object  $post
	 * @return  void
	 */
	public function onForumPostSaved($post = null)
	{
		if (!$post || !$post->get('thread') || User::isGuest())
		{
			return;
		}

		$moderator = new \Hubzero\Moderation\Moderator(User::get('id'));

		$moderator->undoIn(self::TYPE, (int) $post->get('thread'), function ($id)
		{
			return \ForumPostModeratable::forId($id);
		});
	}
}
