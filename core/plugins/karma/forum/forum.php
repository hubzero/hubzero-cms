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
 * Karma source for the forum
 *
 * Karma is earned by being well received, not by posting. A rule that paid
 * for contributions would pay for noise, and the people who post most are
 * not reliably the people worth trusting.
 */
class plgKarmaForum extends \Hubzero\Plugin\Plugin
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
	 * The administration screen reads this to show which rules have a source
	 * and which sources have no rule. The values here are only what gets
	 * seeded on install: once a rule exists, the administrator owns it and
	 * nothing in this plugin overrides what they set.
	 *
	 * @return  array
	 */
	public function onKarmaRules()
	{
		$this->loadLanguage();

		return array(
			array(
				'plugin'         => 'forum',
				'alias'          => 'forum.post.liked',
				'scale'          => self::SCALE,
				'title'          => Lang::txt('PLG_KARMA_FORUM_RULE_LIKED'),
				'description'    => Lang::txt('PLG_KARMA_FORUM_RULE_LIKED_DESC'),
				'delta'          => 1,
				'daily_cap'      => 10,
				'per_source_cap' => 1,
				'requires_karma' => 0
			)
		);
	}

	/**
	 * Somebody found a forum post worth marking
	 *
	 * @param   object   $post     \Components\Forum\Models\Post, or its id
	 * @param   integer  $actorId  Who liked it
	 * @return  void
	 */
	public function onForumPostLiked($post, $actorId = 0)
	{
		if (!$author = $this->authorOf($post))
		{
			return;
		}

		// Liking your own post is not a contribution anybody else valued.
		if ((int) $author === (int) $actorId)
		{
			return;
		}

		Karma::award($author, 'forum.post.liked', array(
			'scale'       => self::SCALE,
			'actor'       => (int) $actorId,
			'source_type' => 'com_forum.post',
			'source_id'   => $this->idOf($post)
		));
	}

	/**
	 * A like was taken back
	 *
	 * The per-source cap means the karma can be earned again if the same
	 * person likes the post a second time, which is the behaviour we want:
	 * the cap counts active awards, and this reverses one.
	 *
	 * @param   object   $post     \Components\Forum\Models\Post, or its id
	 * @param   integer  $actorId  Who took it back
	 * @return  void
	 */
	public function onForumPostUnliked($post, $actorId = 0)
	{
		if (!$author = $this->authorOf($post))
		{
			return;
		}

		Karma::revoke('com_forum.post', $this->idOf($post), 'forum.post.liked', $author);
	}

	/**
	 * The id of a post, given the post or its id
	 *
	 * @param   mixed  $post
	 * @return  integer
	 */
	protected function idOf($post)
	{
		return is_object($post) ? (int) $post->get('id') : (int) $post;
	}

	/**
	 * Who wrote a post, or zero when that cannot be established
	 *
	 * An anonymous post has an author on the row, but anonymity is a promise
	 * about display and karma is visible standing, so it earns nothing.
	 *
	 * @param   mixed  $post
	 * @return  integer
	 */
	protected function authorOf($post)
	{
		if (!is_object($post))
		{
			$post = \Components\Forum\Models\Post::oneOrNew((int) $post);
		}

		if (!$post->get('id') || $post->get('anonymous'))
		{
			return 0;
		}

		return (int) $post->get('created_by');
	}
}
