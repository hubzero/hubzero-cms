<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem\Tests;

use PHPUnit\Framework\Attributes\CoversMethod;
use Hubzero\Filesystem\Adapter\Local;
use Hubzero\Filesystem\SafePath;
use Hubzero\Test\Basic;

/**
 * Tests for resolving paths inside a directory somebody else controls
 *
 * The scenarios are the ways a member can try to make the web server step out of
 * their own home directory: a link at the leaf, a link further up, a dangling
 * link, a hard link, '..' in a request parameter, and a link planted after the
 * path was resolved but before it was written to.
 */
#[CoversMethod(\Hubzero\Filesystem\SafePath::class, 'directory')]
#[CoversMethod(\Hubzero\Filesystem\SafePath::class, 'relative')]
#[CoversMethod(\Hubzero\Filesystem\SafePath::class, 'file')]
#[CoversMethod(\Hubzero\Filesystem\SafePath::class, 'write')]
#[CoversMethod(\Hubzero\Filesystem\Adapter\Local::class, 'deleteDirectory')]
#[CoversMethod(\Hubzero\Filesystem\Adapter\Local::class, 'delete')]
class SafePathTest extends Basic
{
	/**
	 * Contents of the victim's file, so tampering is visible
	 *
	 * @var  string
	 */
	const VICTIM = "victim content\n";

	/**
	 * Base directory standing in for the webdav mount
	 *
	 * @var  string
	 */
	private $base = null;

	/**
	 * The member's own directory below the base
	 *
	 * @var  string
	 */
	private $home = null;

	/**
	 * A second account below the base
	 *
	 * @var  string
	 */
	private $victim = null;

	/**
	 * Build a base directory holding two accounts
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		$this->base   = sys_get_temp_dir() . DS . 'safepath_' . bin2hex(random_bytes(6));
		$this->home   = $this->base . DS . 'member';
		$this->victim = $this->base . DS . 'victim';

		mkdir($this->home, 0755, true);
		mkdir($this->victim . DS . 'data', 0700, true);

		file_put_contents($this->victim . DS . 'data' . DS . 'secret', self::VICTIM);
	}

	/**
	 * Remove the temporary tree
	 *
	 * @return  void
	 */
	protected function tearDown(): void
	{
		$this->remove($this->base);
	}

	/**
	 * Delete a tree without descending into links
	 *
	 * @param   string  $path
	 * @return  void
	 */
	private function remove($path)
	{
		if (is_link($path) || !is_dir($path))
		{
			@unlink($path);
			return;
		}

		foreach (array_diff(scandir($path), array('.', '..')) as $item)
		{
			$this->remove($path . DS . $item);
		}

		@rmdir($path);
	}

	/**
	 * The victim's file is still what it was
	 *
	 * @return  void
	 */
	private function assertVictimIntact()
	{
		$this->assertEquals(self::VICTIM, file_get_contents($this->victim . DS . 'data' . DS . 'secret'));
	}

	/**
	 * An empty relative path resolves to the base itself.
	 *
	 * @return  void
	 */
	public function testResolvesTheBaseItself()
	{
		$this->assertEquals($this->base, SafePath::directory($this->base, ''));
	}

	/**
	 * A missing base is reported separately from an unsafe path.
	 *
	 * @return  void
	 */
	public function testRejectsMissingBase()
	{
		$reason = null;

		$this->assertFalse(SafePath::directory($this->base . DS . 'nope', 'member', false, 0700, $reason));
		$this->assertEquals(SafePath::REASON_BASE, $reason);
	}

	/**
	 * The base may itself be a link - it is configured, not member-owned.
	 *
	 * @return  void
	 */
	public function testAcceptsLinkedBase()
	{
		$link = sys_get_temp_dir() . DS . 'safepath_link_' . bin2hex(random_bytes(6));

		symlink($this->base, $link);

		$this->assertEquals($this->home, SafePath::directory($link, 'member'));

		@unlink($link);
	}

	/**
	 * Nested directories are created when asked, with the given mode.
	 *
	 * @return  void
	 */
	public function testCreatesNestedDirectories()
	{
		$expected = $this->home . DS . 'data' . DS . '.queued_drivers';

		$this->assertEquals($expected, SafePath::directory($this->home, 'data/.queued_drivers', true, 0700));
		$this->assertTrue(is_dir($expected));
		$this->assertEquals(0700, fileperms($expected) & 0777);
	}

	/**
	 * A missing directory is reported as missing, not as unsafe, so callers can
	 * tell "not there yet" from "someone is up to something".
	 *
	 * @return  void
	 */
	public function testReportsMissingDirectory()
	{
		$reason = null;

		$this->assertFalse(SafePath::directory($this->home, 'data', false, 0700, $reason));
		$this->assertEquals(SafePath::REASON_MISSING, $reason);
	}

	/**
	 * A link at the end of the path is refused, in either direction.
	 *
	 * @return  void
	 */
	public function testRejectsLinkedLeaf()
	{
		symlink($this->victim . DS . 'data', $this->home . DS . 'data');

		$reason = null;

		$this->assertFalse(SafePath::directory($this->home, 'data', true, 0700, $reason));
		$this->assertEquals(SafePath::REASON_UNSAFE, $reason);
		$this->assertVictimIntact();
	}

	/**
	 * A link part way along the path is refused too.
	 *
	 * @return  void
	 */
	public function testRejectsLinkedIntermediateComponent()
	{
		symlink($this->victim, $this->home . DS . 'shortcut');

		$reason = null;

		$this->assertFalse(SafePath::directory($this->home, 'shortcut/data', false, 0700, $reason));
		$this->assertEquals(SafePath::REASON_UNSAFE, $reason);
	}

	/**
	 * A dangling link is refused rather than created at its target.
	 *
	 * @return  void
	 */
	public function testRejectsDanglingLink()
	{
		$target = $this->victim . DS . 'new';

		symlink($target, $this->home . DS . 'data');

		$this->assertFalse(SafePath::directory($this->home, 'data', true));
		$this->assertFalse(file_exists($target));
	}

	/**
	 * Traversal is refused outright, however it is spelled.
	 *
	 * @return  void
	 */
	public function testRejectsTraversal()
	{
		$paths = array(
			'..',
			'../victim',
			'data/../../victim',
			'.',
			'./victim',
			'..' . DS . '..' . DS . 'etc',
			"data\0/victim"
		);

		foreach ($paths as $relative)
		{
			$reason = null;

			$this->assertFalse(SafePath::directory($this->base, $relative, false, 0700, $reason), $relative . ' was accepted');
			$this->assertEquals(SafePath::REASON_UNSAFE, $reason);
		}
	}

	/**
	 * On POSIX a backslash is an ordinary character in a name, not a separator,
	 * so 'victim\data' names one (absent) entry rather than reaching victim/data.
	 *
	 * @return  void
	 */
	public function testTreatsBackslashAsPartOfTheName()
	{
		if (DS == '\\')
		{
			$this->markTestSkipped('Backslash is a separator on this platform');
		}

		$reason = null;

		$this->assertFalse(SafePath::directory($this->base, 'victim\\data', false, 0700, $reason));
		$this->assertEquals(SafePath::REASON_MISSING, $reason);

		// ... and it is usable as a name of its own
		mkdir($this->home . DS . 'a\\b');

		$this->assertEquals($this->home . DS . 'a\\b', SafePath::directory($this->home, 'a\\b'));
	}

	/**
	 * Leading, trailing and doubled separators are harmless.
	 *
	 * @return  void
	 */
	public function testToleratesRedundantSeparators()
	{
		$this->assertEquals($this->home, SafePath::directory($this->base, DS . 'member' . DS));
		$this->assertEquals($this->home, SafePath::directory($this->base, 'member' . DS . DS));
	}

	/**
	 * relative() normalizes safe paths and refuses traversal.
	 *
	 * @return  void
	 */
	public function testNormalizesRelativePaths()
	{
		$this->assertEquals('a' . DS . 'b', SafePath::relative('/a//b/'));
		$this->assertEquals('', SafePath::relative(''));
		$this->assertEquals('.hidden', SafePath::relative('.hidden'));
		$this->assertFalse(SafePath::relative('a/../b'));
		$this->assertFalse(SafePath::relative('../b'));
	}

	/**
	 * A regular file is accepted; a link, a directory or a hard link is not.
	 *
	 * @return  void
	 */
	public function testResolvesFiles()
	{
		$dir = SafePath::directory($this->home, 'data', true);

		$this->assertEquals($dir . DS . 'keys', SafePath::file($dir, 'keys'));

		file_put_contents($dir . DS . 'keys', "regular\n");
		$this->assertEquals($dir . DS . 'keys', SafePath::file($dir, 'keys'));

		symlink($this->victim . DS . 'data' . DS . 'secret', $dir . DS . 'linked');
		$this->assertFalse(SafePath::file($dir, 'linked'));

		mkdir($dir . DS . 'adir');
		$this->assertFalse(SafePath::file($dir, 'adir'));

		$this->assertFalse(SafePath::file($dir, '../secret'));
		$this->assertFalse(SafePath::file($dir, 'sub/name'));
		$this->assertFalse(SafePath::file($dir, ''));
	}

	/**
	 * A hard link can reach a file outside the directory, so it is refused.
	 *
	 * @return  void
	 */
	public function testRejectsHardLinkedFile()
	{
		$dir = SafePath::directory($this->home, 'data', true);

		if (!@link($this->victim . DS . 'data' . DS . 'secret', $dir . DS . 'hard'))
		{
			$this->markTestSkipped('Hard links are not available here');
		}

		$this->assertFalse(SafePath::file($dir, 'hard'));
	}

	/**
	 * Writing leaves the right content and mode, and no temporary files.
	 *
	 * @return  void
	 */
	public function testWritesFile()
	{
		$dir  = SafePath::directory($this->home, 'data', true);
		$path = SafePath::file($dir, 'keys');

		$this->assertTrue(SafePath::write($path, "one\n"));
		$this->assertEquals("one\n", file_get_contents($path));
		$this->assertEquals(0600, fileperms($path) & 0777);

		// Overwriting, including with nothing at all
		$this->assertTrue(SafePath::write($path, ''));
		$this->assertEquals('', file_get_contents($path));

		$this->assertEquals(array(), glob($dir . DS . '.keys.*'));
	}

	/**
	 * A null mode leaves the file to the umask, as a plain write would.
	 *
	 * @return  void
	 */
	public function testWritesFileWithoutForcingMode()
	{
		$dir  = SafePath::directory($this->home, 'data', true);
		$path = SafePath::file($dir, 'driver.xml');

		$this->assertTrue(SafePath::write($path, "<run/>\n", null));
		$this->assertEquals(0666 & ~umask(), fileperms($path) & 0777);
	}

	/**
	 * A link planted after the path was resolved cannot divert the write:
	 * rename() replaces the link instead of following it.
	 *
	 * @return  void
	 */
	public function testWriteReplacesLinkPlantedAfterResolution()
	{
		$dir  = SafePath::directory($this->home, 'data', true);
		$path = SafePath::file($dir, 'keys');

		symlink($this->victim . DS . 'data' . DS . 'secret', $path);

		$this->assertTrue(SafePath::write($path, "mine\n"));
		$this->assertFalse(is_link($path));
		$this->assertEquals("mine\n", file_get_contents($path));
		$this->assertVictimIntact();
	}

	/**
	 * deleteDirectory() removes links instead of emptying what they point at.
	 *
	 * @return  void
	 */
	public function testDeleteDirectoryDoesNotFollowLinks()
	{
		$adapter = new Local();

		mkdir($this->home . DS . 'stuff');
		symlink($this->victim . DS . 'data', $this->home . DS . 'stuff' . DS . 'shortcut');

		$this->assertTrue($adapter->deleteDirectory($this->home . DS . 'stuff'));
		$this->assertFalse(is_dir($this->home . DS . 'stuff'));
		$this->assertTrue(is_dir($this->victim . DS . 'data'));
		$this->assertVictimIntact();
	}

	/**
	 * Pointing deleteDirectory() straight at a link removes only the link.
	 *
	 * @return  void
	 */
	public function testDeleteDirectoryOnALinkRemovesTheLink()
	{
		$adapter = new Local();
		$link    = $this->home . DS . 'shortcut';

		symlink($this->victim . DS . 'data', $link);

		$this->assertTrue($adapter->deleteDirectory($link));
		$this->assertFalse(is_link($link));
		$this->assertTrue(is_dir($this->victim . DS . 'data'));
		$this->assertVictimIntact();
	}

	/**
	 * delete() removes a link without chmod-ing whatever it points at.
	 *
	 * @return  void
	 */
	public function testDeleteDoesNotChmodLinkTargets()
	{
		$adapter = new Local();
		$secret  = $this->victim . DS . 'data' . DS . 'secret';
		$link    = $this->home . DS . 'shortcut';

		chmod($secret, 0600);
		symlink($secret, $link);

		$this->assertTrue($adapter->delete($link));
		$this->assertFalse(is_link($link));
		$this->assertEquals(0600, fileperms($secret) & 0777);
		$this->assertVictimIntact();
	}
}
