<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Moderation\Forum\Tests;

use Hubzero\Test\Basic;

require_once dirname(__DIR__) . DS . 'adapter.php';

/**
 * The forum post adapter
 *
 * The adapter is the whole of what Hubzero\Moderation knows about com_forum,
 * so these check it answers the six questions the way the library expects —
 * without needing a database, because the adapter itself holds no queries.
 */
class AdapterTest extends Basic
{
	/**
	 * A stand-in post
	 *
	 * @param   array  $data
	 * @return  object
	 */
	protected function post(array $data)
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
		};
	}

	/**
	 * Tests the item type, which everything else keys on
	 *
	 * @return  void
	 */
	public function testItemType()
	{
		$adapter = new \ForumPostModeratable($this->post(array('id' => 3)));

		$this->assertEquals('com_forum.post', $adapter->itemType());
		$this->assertEquals(3, $adapter->itemId());
	}

	/**
	 * Tests that a post with no score reads as zero rather than null
	 *
	 * A forum that predates moderation has no score on any row, and the
	 * library compares scores against bounds numerically.
	 *
	 * @return  void
	 */
	public function testAScorelessPostReadsAsZero()
	{
		$adapter = new \ForumPostModeratable($this->post(array('id' => 3)));

		$this->assertSame(0.0, $adapter->currentScore());
	}

	/**
	 * Tests the bounds a forum post is held to
	 *
	 * @return  void
	 */
	public function testBounds()
	{
		$adapter = new \ForumPostModeratable($this->post(array('id' => 3)));

		$this->assertEquals(array(-2.0, 4.0), $adapter->scoreBounds());
	}

	/**
	 * Tests that applying a score moves it and saves
	 *
	 * @return  void
	 */
	public function testApplyingAScoreMovesAndSaves()
	{
		$post    = $this->post(array('id' => 3, 'score' => 1.0));
		$adapter = new \ForumPostModeratable($post);

		$this->assertTrue($adapter->applyScore(-1, 5));
		$this->assertEquals(0.0, $adapter->currentScore());
		$this->assertTrue($post->saved);
	}

	/**
	 * Tests that the thread is the container
	 *
	 * The container is what "you posted here, so you cannot moderate here"
	 * is measured against, so a post must point at its thread and not at its
	 * immediate parent.
	 *
	 * @return  void
	 */
	public function testTheThreadIsTheContainer()
	{
		$adapter = new \ForumPostModeratable($this->post(array(
			'id' => 3, 'thread' => 88, 'parent' => 7
		)));

		$this->assertEquals(88, $adapter->containerId());
	}

	/**
	 * Tests that an author is reported for an ordinary post
	 *
	 * @return  void
	 */
	public function testAuthorOfAnOrdinaryPost()
	{
		$adapter = new \ForumPostModeratable($this->post(array(
			'id' => 3, 'created_by' => 42, 'anonymous' => 0
		)));

		$this->assertEquals(42, $adapter->authorId());
	}

	/**
	 * Tests that an anonymous post reports no author
	 *
	 * The post can still be moderated — the score is about the contribution —
	 * but nobody's standing moves, because anonymity is a promise about
	 * display and karma is visible standing.
	 *
	 * @return  void
	 */
	public function testAnonymousPostReportsNoAuthor()
	{
		$adapter = new \ForumPostModeratable($this->post(array(
			'id' => 3, 'created_by' => 42, 'anonymous' => 1
		)));

		$this->assertEquals(0, $adapter->authorId());
	}
}
