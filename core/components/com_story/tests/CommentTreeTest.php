<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Files.SideEffects

namespace Components\Story\Tests;

use Hubzero\Test\Database;
use Hubzero\Database\Relational;
use Components\Story\Models\Comment;
use Components\Story\Models\Discussion;
use Components\Story\Models\Preference;
use Components\Story\Helpers\Thread;

require_once dirname(__DIR__) . DS . 'models' . DS . 'comment.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'discussion.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'preference.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'thread.php';

/**
 * The comment tree
 *
 * `parent` is the truth and `path`/`depth` are derived from it. These tests
 * exist to hold that claim to account: that the derivation is right on insert,
 * that a corrupted derived column can always be put back, and that removing a
 * comment in the middle of a conversation does not take the conversation with
 * it.
 */
class CommentTreeTest extends Database
{
	/**
	 * The discussion the tests build in
	 *
	 * @var  integer
	 */
	const DISCUSSION = 1;

	/**
	 * A second, to prove queries are actually scoped
	 *
	 * @var  integer
	 */
	const OTHER = 2;

	/**
	 * Sets up the tests, called prior to each test
	 *
	 * @return  void
	 */
	public function setUp(): void
	{
		parent::setUp();

		Relational::setDefaultConnection($this->getMockDriver());

		// destroy() records who withdrew a comment. The harness binds no user,
		// so it gets one here rather than the model pretending it does not care.
		// The container freezes a service once it has been read, so this is
		// registered once for the class rather than once per test.
		if (isset(self::$app['user']))
		{
			return;
		}

		self::$app['user'] = function ()
		{
			return new class {
				public function get($key, $default = null)
				{
					return ($key == 'id') ? 99 : $default;
				}
				public function isGuest()
				{
					return false;
				}
			};
		};
	}

	/**
	 * Add a comment and hand it back
	 *
	 * @param   integer  $parent
	 * @param   string   $body
	 * @param   integer  $discussion
	 * @return  object
	 */
	protected function comment($parent = 0, $body = 'Something said', $discussion = self::DISCUSSION)
	{
		$row = Comment::blank();
		$row->set(array(
			'discussion_id' => $discussion,
			'parent'        => $parent,
			'subject'       => 'A subject',
			'comment'       => $body,
			'created'       => '2026-09-13 12:00:00',
			'created_by'    => 7,
			'state'         => Comment::STATE_PUBLISHED
		));

		$this->assertTrue($row->save(), 'The comment should save: ' . implode(', ', $row->getErrors()));

		return $row;
	}

	/**
	 * The paths of a discussion, in stored order
	 *
	 * @param   integer  $discussion
	 * @return  array
	 */
	protected function paths($discussion = self::DISCUSSION)
	{
		$out = array();

		foreach (Comment::inDiscussion($discussion)->rows() as $row)
		{
			$out[(int) $row->get('id')] = array($row->get('path'), (int) $row->get('depth'));
		}

		return $out;
	}

	/**
	 * A root comment's path is its own id, padded
	 *
	 * @return  void
	 */
	public function testRootPathIsItsOwnId()
	{
		$row = $this->comment();

		$this->assertEquals(Comment::segment($row->get('id')), $row->get('path'));
		$this->assertEquals(0, (int) $row->get('depth'));
	}

	/**
	 * A reply hangs its id off its parent's path
	 *
	 * @return  void
	 */
	public function testReplyPathExtendsItsParent()
	{
		$root  = $this->comment();
		$reply = $this->comment($root->get('id'));

		$this->assertEquals($root->get('path') . '.' . Comment::segment($reply->get('id')), $reply->get('path'));
		$this->assertEquals(1, (int) $reply->get('depth'));

		$deeper = $this->comment($reply->get('id'));

		$this->assertEquals($reply->get('path') . '.' . Comment::segment($deeper->get('id')), $deeper->get('path'));
		$this->assertEquals(2, (int) $deeper->get('depth'));
	}

	/**
	 * Fixed-width segments are what make a string sort a tree sort
	 *
	 * Without the padding '10' would sort before '9' and the tenth reply would
	 * appear above the ninth.
	 *
	 * @return  void
	 */
	public function testPathSortsInTreeOrder()
	{
		$first  = $this->comment(0, 'First root');
		$reply  = $this->comment($first->get('id'), 'Reply to the first');
		$second = $this->comment(0, 'Second root');

		$ordered = array();

		foreach (Comment::inDiscussion(self::DISCUSSION)->rows() as $row)
		{
			$ordered[] = (int) $row->get('id');
		}

		$this->assertEquals(
			array((int) $first->get('id'), (int) $reply->get('id'), (int) $second->get('id')),
			$ordered,
			'A reply should sort under the comment it answers, not after the next root'
		);
	}

	/**
	 * A reply to a comment in another discussion is refused
	 *
	 * @return  void
	 */
	public function testParentMustBeInTheSameDiscussion()
	{
		$elsewhere = $this->comment(0, 'In the other discussion', self::OTHER);

		$row = Comment::blank();
		$row->set(array(
			'discussion_id' => self::DISCUSSION,
			'parent'        => $elsewhere->get('id'),
			'comment'       => 'Trying to reach across',
			'created'       => '2026-09-13 12:00:00',
			'created_by'    => 7
		));

		$this->assertFalse($row->save(), 'A cross-discussion reply should be refused');
	}

	/**
	 * A subtree is selected by prefix, and siblings are not caught in it
	 *
	 * The trailing dot is what does this. Matching on the bare prefix would
	 * pull in any sibling whose id merely started with the same digits.
	 *
	 * @return  void
	 */
	public function testDescendantsExcludesSiblingsSharingAPrefix()
	{
		$root  = $this->comment(0, 'Root');
		$child = $this->comment($root->get('id'), 'Child');
		$other = $this->comment(0, 'Another root');

		$ids = array();

		foreach ($root->descendants()->rows() as $row)
		{
			$ids[] = (int) $row->get('id');
		}

		$this->assertEquals(array((int) $child->get('id')), $ids);
		$this->assertNotContains((int) $other->get('id'), $ids);
	}

	/**
	 * Removing a comment in the middle keeps what was said under it
	 *
	 * @return  void
	 */
	public function testDeletingAnInteriorCommentKeepsTheTree()
	{
		$root     = $this->comment(0, 'Root');
		$interior = $this->comment($root->get('id'), 'The one that goes');
		$leaf     = $this->comment($interior->get('id'), 'The one that stays');

		$before = $this->paths();

		$this->assertTrue($interior->destroy());

		$after = $this->paths();

		$this->assertCount(3, $after, 'The row should be kept as a tombstone, not removed');
		$this->assertEquals($before, $after, 'No path or depth should have moved');

		$kept = Comment::oneOrNew($interior->get('id'));

		$this->assertTrue($kept->isDeleted());
		$this->assertEquals('', $kept->get('comment'), 'The body should be gone');

		$still = Comment::oneOrNew($leaf->get('id'));

		$this->assertEquals(
			$interior->get('path') . '.' . Comment::segment($leaf->get('id')),
			$still->get('path'),
			'The reply should still hang where it did'
		);
	}

	/**
	 * A comment with nothing under it is removed outright
	 *
	 * @return  void
	 */
	public function testDeletingALeafRemovesIt()
	{
		$root = $this->comment(0, 'Root');
		$leaf = $this->comment($root->get('id'), 'Leaf');

		$this->assertTrue($leaf->destroy());

		$this->assertCount(1, $this->paths(), 'Only the root should remain');
	}

	/**
	 * A scrambled path column can always be put back
	 *
	 * This is the test the whole derived-ordering argument rests on. If it
	 * cannot pass, storing `path` is not defensible and the tree should be
	 * assembled in PHP on every read.
	 *
	 * @return  void
	 */
	public function testRebuildPathsRestoresACorruptedColumn()
	{
		$root   = $this->comment(0, 'Root');
		$a      = $this->comment($root->get('id'), 'First reply');
		$b      = $this->comment($root->get('id'), 'Second reply');
		$deep   = $this->comment($a->get('id'), 'A reply to a reply');
		$second = $this->comment(0, 'Another root');

		$expected = $this->paths();

		// Scramble both derived columns, leaving `parent` alone.
		foreach (Comment::inDiscussion(self::DISCUSSION)->rows() as $row)
		{
			$row->set('path', 'nonsense.' . $row->get('id'));
			$row->set('depth', 99);
			$row->save();
		}

		$this->assertNotEquals($expected, $this->paths(), 'The corruption should have taken');

		$corrected = Comment::rebuildPaths(self::DISCUSSION);

		$this->assertEquals(5, $corrected, 'Every row was wrong, so every row should have been rewritten');
		$this->assertEquals($expected, $this->paths(), 'The rebuild should restore the tree exactly');
	}

	/**
	 * Rebuilding a correct tree changes nothing
	 *
	 * @return  void
	 */
	public function testRebuildPathsIsIdempotent()
	{
		$root = $this->comment(0, 'Root');
		$this->comment($root->get('id'), 'Reply');

		$this->assertEquals(0, Comment::rebuildPaths(self::DISCUSSION), 'Nothing was wrong, so nothing should be written');
	}

	/**
	 * A comment whose parent has gone is treated as a root, not dropped
	 *
	 * @return  void
	 */
	public function testRebuildPathsRehomesAnOrphan()
	{
		$root   = $this->comment(0, 'Root');
		$orphan = $this->comment($root->get('id'), 'About to lose its parent');

		// Point it at a parent that was never there. A direct DELETE against
		// the table, or an import that lost a row, leaves exactly this.
		$orphan->set('parent', 999999);
		$orphan->save();

		$corrected = Comment::rebuildPaths(self::DISCUSSION);

		$after = Comment::oneOrNew($orphan->get('id'));

		$this->assertEquals(1, $corrected, 'Only the orphan needed moving');
		$this->assertEquals(
			Comment::segment($orphan->get('id')),
			$after->get('path'),
			'An orphan should become a root rather than vanish from the discussion'
		);
		$this->assertEquals(0, (int) $after->get('depth'));

		// And it is still in the discussion, which is the point.
		$this->assertCount(2, $this->paths());
	}

	/**
	 * The discussion's own count comes from the rows, not from a tally
	 *
	 * @return  void
	 */
	public function testRecountCountsPublishedComments()
	{
		$root = $this->comment(0, 'Root');
		$this->comment($root->get('id'), 'Reply');

		$discussion = Discussion::oneOrNew(self::DISCUSSION);
		$discussion->recount();

		$this->assertEquals(2, (int) $discussion->get('comment_count'));
	}

	/**
	 * Every mode returns the discussion in the order it promises
	 *
	 * @return  void
	 */
	public function testThreadBuildsInAllFourModes()
	{
		$first  = $this->comment(0, 'First root');
		$reply  = $this->comment($first->get('id'), 'Reply to the first');
		$second = $this->comment(0, 'Second root');

		$tree = array((int) $first->get('id'), (int) $reply->get('id'), (int) $second->get('id'));

		// Threaded: tree order, and the reply is indented.
		$built = Thread::build(self::DISCUSSION, $this->preference(Preference::MODE_THREADED));
		$this->assertEquals($tree, $this->idsOf($built));
		$this->assertEquals(array(0, 1, 0), $this->indentsOf($built));

		// Nested: same order, no indent.
		$built = Thread::build(self::DISCUSSION, $this->preference(Preference::MODE_NESTED));
		$this->assertEquals($tree, $this->idsOf($built));
		$this->assertEquals(array(0, 0, 0), $this->indentsOf($built));

		// Flat: chronological, which here is the order they were written.
		$built = Thread::build(self::DISCUSSION, $this->preference(Preference::MODE_FLAT));
		$this->assertEquals($tree, $this->idsOf($built));
		$this->assertEquals(array(0, 0, 0), $this->indentsOf($built));

		// None: nothing at all.
		$built = Thread::build(self::DISCUSSION, $this->preference(Preference::MODE_NONE));
		$this->assertEquals(array(), $built);
	}

	/**
	 * Flat mode is the only one that will run newest first
	 *
	 * @return  void
	 */
	public function testFlatModeHonoursTheSortOrder()
	{
		$first  = $this->comment(0, 'First root');
		$reply  = $this->comment($first->get('id'), 'Reply to the first');
		$second = $this->comment(0, 'Second root');

		$preference = $this->preference(Preference::MODE_FLAT);
		$preference->set('sort', Preference::SORT_NEWEST);

		$this->assertEquals(
			array((int) $second->get('id'), (int) $reply->get('id'), (int) $first->get('id')),
			$this->idsOf(Thread::build(self::DISCUSSION, $preference))
		);
	}

	/**
	 * The indent stops growing before it walks off the page
	 *
	 * @return  void
	 */
	public function testIndentIsCapped()
	{
		$this->assertEquals(3, Thread::indent(3));
		$this->assertEquals(Thread::MAX_INDENT, Thread::indent(Thread::MAX_INDENT + 40));
	}

	/**
	 * A preference object set to one mode
	 *
	 * @param   string  $mode
	 * @return  object
	 */
	protected function preference($mode)
	{
		$preference = Preference::blank();
		$preference->set(Preference::defaults());
		$preference->set('mode', $mode);

		return $preference;
	}

	/**
	 * The comment ids of a built thread
	 *
	 * @param   array  $built
	 * @return  array
	 */
	protected function idsOf($built)
	{
		$out = array();

		foreach ($built as $entry)
		{
			$out[] = (int) $entry->comment->get('id');
		}

		return $out;
	}

	/**
	 * The indents of a built thread
	 *
	 * @param   array  $built
	 * @return  array
	 */
	protected function indentsOf($built)
	{
		$out = array();

		foreach ($built as $entry)
		{
			$out[] = (int) $entry->indent;
		}

		return $out;
	}
}
