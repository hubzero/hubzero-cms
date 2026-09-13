<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Moderation\Story\Tests;

use Hubzero\Test\Basic;

require_once dirname(__DIR__) . DS . 'adapter.php';

/**
 * The story comment adapter
 *
 * The adapter is the whole of what Hubzero\Moderation knows about com_story,
 * so these check it answers the seven questions the way the library expects —
 * without needing a database, because the adapter itself holds no queries
 * beyond the one lookup in forId().
 */
class AdapterTest extends Basic
{
	/**
	 * A stand-in comment
	 *
	 * @param   array  $data
	 * @return  object
	 */
	protected function comment(array $data)
	{
		return new class($data) {
			private $data;
			public $saved = false;
			public function __construct($data) { $this->data = $data; }
			public function get($key, $default = null)
			{
				return isset($this->data[$key]) ? $this->data[$key] : $default;
			}
			public function set($key, $value = null) { $this->data[$key] = $value; return $this; }
			public function save() { $this->saved = true; return true; }
			public function isDeleted() { return ($this->get('state') == -1); }
		};
	}

	/**
	 * The item type, which everything else keys on
	 *
	 * @return  void
	 */
	public function testItemType()
	{
		$item = new \StoryCommentModeratable($this->comment(array('id' => 3)));

		$this->assertEquals('com_story.comment', $item->itemType());
		$this->assertEquals(3, $item->itemId());
	}

	/**
	 * The bounds are wider than a forum post's, and deliberately so
	 *
	 * @return  void
	 */
	public function testScoreBounds()
	{
		$item = new \StoryCommentModeratable($this->comment(array('id' => 1)));

		$this->assertEquals(array(-1.0, 5.0), $item->scoreBounds());
	}

	/**
	 * The discussion is the container, which is what the participant rule
	 * is scoped to
	 *
	 * @return  void
	 */
	public function testContainerIsTheDiscussion()
	{
		$item = new \StoryCommentModeratable($this->comment(array('id' => 1, 'discussion_id' => 77)));

		$this->assertEquals(77, $item->containerId());
	}

	/**
	 * An anonymous comment has no author for standing to attach to
	 *
	 * It can still be moderated — the score is about the contribution — but
	 * nobody's karma moves.
	 *
	 * @return  void
	 */
	public function testAnonymousCommentHasNoAuthorForKarma()
	{
		$named = new \StoryCommentModeratable($this->comment(array('id' => 1, 'created_by' => 9)));
		$anon  = new \StoryCommentModeratable($this->comment(array('id' => 2, 'created_by' => 9, 'anonymous' => 1)));

		$this->assertEquals(9, $named->authorId());
		$this->assertEquals(0, $anon->authorId(), 'Anonymity is a promise about display, and karma is visible standing');
	}

	/**
	 * Moving the score records the reason that moved it
	 *
	 * @return  void
	 */
	public function testApplyScoreMovesAndRecords()
	{
		$comment = $this->comment(array('id' => 1, 'score' => 1, 'score_max' => 1));
		$item    = new \StoryCommentModeratable($comment);

		$this->assertTrue($item->applyScore(1, 4));

		$this->assertEquals(2, $comment->get('score'));
		$this->assertEquals(4, $comment->get('reason_id'));
		$this->assertTrue($comment->saved);
	}

	/**
	 * score_max remembers the highest it ever reached
	 *
	 * A reviewer needs that later to tell a comment that was raised and then
	 * lowered from one that was never raised at all.
	 *
	 * @return  void
	 */
	public function testScoreMaxRemembersTheHighWater()
	{
		$comment = $this->comment(array('id' => 1, 'score' => 1, 'score_max' => 1));
		$item    = new \StoryCommentModeratable($comment);

		$item->applyScore(2, 1);
		$this->assertEquals(3, $comment->get('score_max'));

		$item->applyScore(-2, 5);
		$this->assertEquals(1, $comment->get('score'));
		$this->assertEquals(3, $comment->get('score_max'), 'The high-water mark does not come back down');
	}

	/**
	 * The current score is read from the comment, not cached
	 *
	 * @return  void
	 */
	public function testCurrentScoreFollowsTheComment()
	{
		$comment = $this->comment(array('id' => 1, 'score' => 2));
		$item    = new \StoryCommentModeratable($comment);

		$this->assertEquals(2.0, $item->currentScore());

		$comment->set('score', 4);

		$this->assertEquals(4.0, $item->currentScore());
	}

	/**
	 * A comment with no score yet reads as zero rather than as an error
	 *
	 * @return  void
	 */
	public function testMissingScoreReadsAsZero()
	{
		$item = new \StoryCommentModeratable($this->comment(array('id' => 1)));

		$this->assertEquals(0.0, $item->currentScore());
	}
}
