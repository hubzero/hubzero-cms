<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Karma\Forum\Tests;

use Hubzero\Test\Database;
use Hubzero\Database\Relational;
use Hubzero\Karma\Karma;
use Hubzero\Karma\Scale;
use Hubzero\Karma\Rule;
use Hubzero\Karma\Gate;
use Hubzero\Karma\Ledger;

require_once dirname(__DIR__) . DS . 'forum.php';

/**
 * Forum karma source tests
 *
 * The plugin is thin on purpose — it decides who earned what, and hands the
 * arithmetic to the library. These cover the deciding.
 */
class KarmaForumTest extends Database
{
	/**
	 * Who wrote the post
	 *
	 * @var  integer
	 */
	const AUTHOR = 42;

	/**
	 * Who liked it
	 *
	 * @var  integer
	 */
	const READER = 7;

	/**
	 * The plugin under test
	 *
	 * @var  object
	 */
	protected $plugin = null;

	/**
	 * Sets up the tests, called prior to each test
	 *
	 * @return  void
	 */
	public function setUp(): void
	{
		parent::setUp();

		$driver = $this->getMockDriver();

		Relational::setDefaultConnection($driver);
		Karma::setConnection($driver);

		Scale::forget();
		Rule::forget();
		Gate::forget();

		// The plugin base loads a language file on construction, which wants
		// a client from the container. A test needs neither, so it gets a
		// plugin whose language autoload is off.
		$subject = new \stdClass();
		$this->plugin = new TestablePlgKarmaForum($subject, array());
	}

	/**
	 * A stand-in for a forum post
	 *
	 * The plugin only ever asks a post for its id, its author and whether it
	 * was posted anonymously, so a test does not need com_forum loaded.
	 *
	 * @param   integer  $id
	 * @param   integer  $author
	 * @param   integer  $anonymous
	 * @return  object
	 */
	protected function post($id, $author, $anonymous = 0)
	{
		return new class($id, $author, $anonymous) {
			private $data;
			public function __construct($id, $author, $anonymous)
			{
				$this->data = array('id' => $id, 'created_by' => $author, 'anonymous' => $anonymous);
			}
			public function get($key, $default = null)
			{
				return isset($this->data[$key]) ? $this->data[$key] : $default;
			}
		};
	}

	/**
	 * Tests that the plugin declares the rule it emits
	 *
	 * The administration screen reads this to tell a configured rule from an
	 * orphaned one, so the alias here has to match what the migration seeds.
	 *
	 * @return  void
	 */
	public function testDeclaresItsRule()
	{
		$rules = $this->plugin->onKarmaRules();

		$this->assertCount(1, $rules);
		$this->assertEquals('forum.post.liked', $rules[0]['alias']);
		$this->assertEquals('forum', $rules[0]['plugin']);
		$this->assertEquals(1, $rules[0]['per_source_cap'], 'One reader, one post, one award');
	}

	/**
	 * Tests that a like moves the author's karma
	 *
	 * @return  void
	 */
	public function testLikeAwardsTheAuthor()
	{
		$this->plugin->onForumPostLiked($this->post(101, self::AUTHOR), self::READER);

		$this->assertEquals(1.0, Karma::of(self::AUTHOR));
		$this->assertEquals(0.0, Karma::of(self::READER), 'The reader earns nothing for liking');
	}

	/**
	 * Tests that liking your own post earns nothing
	 *
	 * @return  void
	 */
	public function testSelfLikeEarnsNothing()
	{
		$this->plugin->onForumPostLiked($this->post(101, self::AUTHOR), self::AUTHOR);

		$this->assertEquals(0.0, Karma::of(self::AUTHOR));
	}

	/**
	 * Tests that an anonymous post earns nothing
	 *
	 * Anonymity is a promise about display; karma is visible standing. A post
	 * cannot be both.
	 *
	 * @return  void
	 */
	public function testAnonymousPostEarnsNothing()
	{
		$this->plugin->onForumPostLiked($this->post(101, self::AUTHOR, 1), self::READER);

		$this->assertEquals(0.0, Karma::of(self::AUTHOR));
	}

	/**
	 * Tests that one post earns once however often it is liked
	 *
	 * @return  void
	 */
	public function testPerSourceCapHoldsForOnePost()
	{
		$this->plugin->onForumPostLiked($this->post(101, self::AUTHOR), self::READER);
		$this->plugin->onForumPostLiked($this->post(101, self::AUTHOR), 9);
		$this->plugin->onForumPostLiked($this->post(101, self::AUTHOR), 11);

		$this->assertEquals(1.0, Karma::of(self::AUTHOR));
	}

	/**
	 * Tests that different posts each earn
	 *
	 * @return  void
	 */
	public function testDifferentPostsEachEarn()
	{
		$this->plugin->onForumPostLiked($this->post(101, self::AUTHOR), self::READER);
		$this->plugin->onForumPostLiked($this->post(102, self::AUTHOR), self::READER);

		$this->assertEquals(2.0, Karma::of(self::AUTHOR));
	}

	/**
	 * Tests that taking a like back takes the karma back
	 *
	 * @return  void
	 */
	public function testUnlikeReversesTheAward()
	{
		$this->plugin->onForumPostLiked($this->post(101, self::AUTHOR), self::READER);
		$this->assertEquals(1.0, Karma::of(self::AUTHOR));

		$this->plugin->onForumPostUnliked($this->post(101, self::AUTHOR), self::READER);
		$this->assertEquals(0.0, Karma::of(self::AUTHOR));
	}

	/**
	 * Tests that a post can earn again after a like is withdrawn and given back
	 *
	 * The per-source cap counts active awards, so a reversal frees the slot.
	 *
	 * @return  void
	 */
	public function testAPostCanEarnAgainAfterAReversal()
	{
		$post = $this->post(101, self::AUTHOR);

		$this->plugin->onForumPostLiked($post, self::READER);
		$this->plugin->onForumPostUnliked($post, self::READER);
		$this->plugin->onForumPostLiked($post, self::READER);

		$this->assertEquals(1.0, Karma::of(self::AUTHOR));
	}

	/**
	 * Tests that the award is attributed to the post it came from
	 *
	 * Without this the ledger cannot explain itself and revoke has nothing
	 * to match on.
	 *
	 * @return  void
	 */
	public function testAwardRecordsItsSource()
	{
		$this->plugin->onForumPostLiked($this->post(101, self::AUTHOR), self::READER);

		$entry = Ledger::all()->whereEquals('subject_id', self::AUTHOR)->row();

		$this->assertEquals('com_forum.post', $entry->get('source_type'));
		$this->assertEquals(101, $entry->get('source_id'));
		$this->assertEquals(self::READER, $entry->get('actor_id'));
	}
}

/**
 * The plugin, minus the language autoload a test cannot satisfy
 *
 * Nothing else is overridden: the behaviour under test is the plugin's own.
 */
class TestablePlgKarmaForum extends \plgKarmaForum
{
	/**
	 * Do not load language files on construction
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = false;

	/**
	 * Language lookups return the key, which is all a test needs
	 *
	 * @param   string  $extension
	 * @param   string  $basePath
	 * @return  bool
	 */
	public function loadLanguage($extension = '', $basePath = '')
	{
		return true;
	}
}
