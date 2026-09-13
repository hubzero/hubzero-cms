<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * A forum post, as something that can be moderated
 *
 * The whole of what Hubzero\Moderation needs to know about com_forum. Six
 * questions and an answer each; nothing here reaches back into the library.
 */
class ForumPostModeratable implements \Hubzero\Moderation\Moderatable
{
	/**
	 * The post
	 *
	 * @var  object
	 */
	protected $post = null;

	/**
	 * The bounds a forum post's score is held to
	 *
	 * Narrower than a news discussion would want. A forum thread is a
	 * conversation rather than a ranked list, so the score is there to lift
	 * the useful and sink the unhelpful, not to sort.
	 */
	const MIN_SCORE = -2;
	const MAX_SCORE = 4;

	/**
	 * Constructor
	 *
	 * @param   object  $post  \Components\Forum\Models\Post
	 * @return  void
	 */
	public function __construct($post)
	{
		$this->post = $post;
	}

	/**
	 * Wrap a post by id, or return null where there is nothing to wrap
	 *
	 * @param   integer  $id
	 * @return  mixed    object|null
	 */
	public static function forId($id)
	{
		$post = \Components\Forum\Models\Post::oneOrNew((int) $id);

		return $post->get('id') ? new self($post) : null;
	}

	/**
	 * The underlying post
	 *
	 * @return  object
	 */
	public function post()
	{
		return $this->post;
	}

	/**
	 * The item type
	 *
	 * @return  string
	 */
	public function itemType()
	{
		return 'com_forum.post';
	}

	/**
	 * The post's id
	 *
	 * @return  integer
	 */
	public function itemId()
	{
		return (int) $this->post->get('id');
	}

	/**
	 * Its score as it stands
	 *
	 * @return  float
	 */
	public function currentScore()
	{
		return (float) $this->post->get('score', 0);
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
	 * @param   float    $delta
	 * @param   integer  $reasonId
	 * @return  bool
	 */
	public function applyScore($delta, $reasonId)
	{
		$this->post->set('score', $this->currentScore() + (float) $delta);

		return (bool) $this->post->save();
	}

	/**
	 * Who wrote it
	 *
	 * An anonymous post earns and loses nothing: anonymity is a promise about
	 * display, and karma is visible standing. The post can still be
	 * moderated — the score is about the contribution — but no standing moves.
	 *
	 * @return  integer
	 */
	public function authorId()
	{
		if ($this->post->get('anonymous'))
		{
			return 0;
		}

		return (int) $this->post->get('created_by');
	}

	/**
	 * The thread it sits in
	 *
	 * @return  integer
	 */
	public function containerId()
	{
		return (int) $this->post->get('thread');
	}
}
