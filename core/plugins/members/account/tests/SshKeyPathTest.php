<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Account\Tests;

use Hubzero\Test\Basic;
use ReflectionClass;

require_once dirname(__DIR__) . DS . 'account.php';

/**
 * A member whose username is all the plugin needs to build a path
 */
class TestMember
{
	/**
	 * Member username
	 *
	 * @var  string
	 */
	private $username = null;

	/**
	 * Constructor
	 *
	 * @param   string  $username
	 * @return  void
	 */
	public function __construct($username)
	{
		$this->username = $username;
	}

	/**
	 * Get a property
	 *
	 * @param   string  $property
	 * @return  mixed
	 */
	public function get($property)
	{
		return ($property == 'username' ? $this->username : null);
	}
}

/**
 * Plugin with the webdav mount pointed at a temporary directory and the path
 * helpers exposed, so they can be exercised without a hub
 */
class TestPlugin extends \plgMembersAccount
{
	/**
	 * Stand-in for /webdav/home
	 *
	 * @var  string
	 */
	public $base = null;

	/**
	 * {@inheritdoc}
	 */
	protected function webdavHome()
	{
		return $this->base;
	}

	/**
	 * {@inheritdoc}
	 */
	public function sshDirectory($create = true, &$error = null)
	{
		return parent::sshDirectory($create, $error);
	}

	/**
	 * {@inheritdoc}
	 */
	public function authorizedKeysPath($ssh, &$error = null)
	{
		return parent::authorizedKeysPath($ssh, $error);
	}

	/**
	 * {@inheritdoc}
	 */
	public function writeAuthorizedKeys($auth, $content)
	{
		return parent::writeAuthorizedKeys($auth, $content);
	}
}

/**
 * Tests for the SSH key path handling of the members account plugin
 *
 * The webdav mount lets the web server write inside a member's home directory,
 * but the member owns the contents of that directory: every one of these cases
 * is an attempt to make the plugin follow a link out of the member's own home
 * and into another account.
 */
class SshKeyPathTest extends Basic
{
	/**
	 * Unsafe paths are all reported with this key
	 *
	 * @var  string
	 */
	const UNSAFE = 'PLG_MEMBERS_ACCOUNT_KEY_UNSAFE_PATH';

	/**
	 * The victim's key, so we can tell whether it was tampered with
	 *
	 * @var  string
	 */
	const VICTIM_KEY = "ssh-rsa AAAAvictim victim@example.org\n";

	/**
	 * Stand-in for /webdav/home
	 *
	 * @var  string
	 */
	private $base = null;

	/**
	 * The plugin under test
	 *
	 * @var  object
	 */
	private $plugin = null;

	/**
	 * Build a webdav mount holding a member and a victim home directory
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		$this->base = sys_get_temp_dir() . DS . 'plg_members_account_' . bin2hex(random_bytes(6));

		mkdir($this->base . DS . 'member', 0755, true);
		mkdir($this->base . DS . 'victim' . DS . '.ssh', 0700, true);

		file_put_contents($this->base . DS . 'victim' . DS . '.ssh' . DS . 'authorized_keys', self::VICTIM_KEY);

		$this->plugin = $this->plugin('member');
	}

	/**
	 * Remove the temporary mount
	 *
	 * @return  void
	 */
	protected function tearDown(): void
	{
		$this->remove($this->base);
	}

	/**
	 * Get a plugin instance acting on behalf of the given member
	 *
	 * The constructor is skipped on purpose: it wants a dispatcher, plugin
	 * params and a language file, none of which the path helpers touch.
	 *
	 * @param   string  $username
	 * @return  object
	 */
	private function plugin($username)
	{
		$plugin = (new ReflectionClass(TestPlugin::class))->newInstanceWithoutConstructor();

		$plugin->base   = $this->base;
		$plugin->member = new TestMember($username);

		return $plugin;
	}

	/**
	 * Delete a directory tree without descending into symlinks
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
	 * Convenience accessor for the victim's key file
	 *
	 * @return  string
	 */
	private function victimKeys()
	{
		return $this->base . DS . 'victim' . DS . '.ssh' . DS . 'authorized_keys';
	}

	/**
	 * A member without a .ssh directory gets one, with restrictive permissions.
	 *
	 * @covers  plgMembersAccount::sshDirectory
	 * @return  void
	 */
	public function testCreatesMissingSshDirectory()
	{
		$expected = $this->base . DS . 'member' . DS . '.ssh';

		$ssh = $this->plugin->sshDirectory();

		$this->assertEquals($expected, $ssh);
		$this->assertTrue(is_dir($expected));
		$this->assertFalse(is_link($expected));
		$this->assertEquals(0700, fileperms($expected) & 0777);
	}

	/**
	 * A .ssh symlinked into another account is refused, and that account's
	 * authorized_keys file is left alone.
	 *
	 * @covers  plgMembersAccount::sshDirectory
	 * @return  void
	 */
	public function testRejectsSshDirectorySymlinkedIntoAnotherAccount()
	{
		symlink($this->base . DS . 'victim' . DS . '.ssh', $this->base . DS . 'member' . DS . '.ssh');

		$error = null;

		$this->assertFalse($this->plugin->sshDirectory(true, $error));
		$this->assertEquals(self::UNSAFE, $error);
		$this->assertEquals(self::VICTIM_KEY, file_get_contents($this->victimKeys()));
	}

	/**
	 * The same, by relative path: nothing may resolve outside the member's home.
	 *
	 * @covers  plgMembersAccount::sshDirectory
	 * @return  void
	 */
	public function testRejectsSshDirectorySymlinkedByRelativePath()
	{
		symlink('..' . DS . 'victim' . DS . '.ssh', $this->base . DS . 'member' . DS . '.ssh');

		$error = null;

		$this->assertFalse($this->plugin->sshDirectory(true, $error));
		$this->assertEquals(self::UNSAFE, $error);
	}

	/**
	 * A dangling .ssh symlink is reported as an unsafe path, and nothing is
	 * created at its target. file_exists() cannot see such a link, so without
	 * the is_link() test this would fall through to mkdir() and be reported as
	 * a plain "create folder failed".
	 *
	 * @covers  plgMembersAccount::sshDirectory
	 * @return  void
	 */
	public function testDanglingSshSymlinkIsRejected()
	{
		$target = $this->base . DS . 'victim' . DS . '.ssh-new';

		symlink($target, $this->base . DS . 'member' . DS . '.ssh');

		$error = null;

		$this->assertFalse($this->plugin->sshDirectory(true, $error));
		$this->assertEquals(self::UNSAFE, $error);
		$this->assertFalse(file_exists($target));
	}

	/**
	 * A home directory that is itself a symlink is refused.
	 *
	 * @covers  plgMembersAccount::sshDirectory
	 * @return  void
	 */
	public function testRejectsSymlinkedHomeDirectory()
	{
		symlink($this->base . DS . 'victim', $this->base . DS . 'other');

		$error = null;

		$this->assertFalse($this->plugin('other')->sshDirectory(true, $error));
		$this->assertEquals(self::UNSAFE, $error);
	}

	/**
	 * A username that isn't a plain name is refused before a path is built.
	 *
	 * @covers  plgMembersAccount::sshDirectory
	 * @return  void
	 */
	public function testRejectsUnsafeUsernames()
	{
		foreach (array('..', '.', '../victim', 'member' . DS . '..' . DS . 'victim', '', '.hidden') as $username)
		{
			$error = null;

			$this->assertFalse($this->plugin($username)->sshDirectory(true, $error), $username . ' was accepted');
			$this->assertEquals(self::UNSAFE, $error);
		}
	}

	/**
	 * A symlinked authorized_keys file is refused.
	 *
	 * @covers  plgMembersAccount::authorizedKeysPath
	 * @return  void
	 */
	public function testRejectsSymlinkedAuthorizedKeys()
	{
		$ssh = $this->plugin->sshDirectory();

		symlink($this->victimKeys(), $ssh . DS . 'authorized_keys');

		$error = null;

		$this->assertFalse($this->plugin->authorizedKeysPath($ssh, $error));
		$this->assertEquals(self::UNSAFE, $error);
	}

	/**
	 * A hard link can reach a file outside the account, so it is refused too.
	 *
	 * @covers  plgMembersAccount::authorizedKeysPath
	 * @return  void
	 */
	public function testRejectsHardLinkedAuthorizedKeys()
	{
		$ssh = $this->plugin->sshDirectory();

		if (!@link($this->victimKeys(), $ssh . DS . 'authorized_keys'))
		{
			$this->markTestSkipped('Hard links are not available here');
		}

		$error = null;

		$this->assertFalse($this->plugin->authorizedKeysPath($ssh, $error));
		$this->assertEquals(self::UNSAFE, $error);
	}

	/**
	 * A directory where authorized_keys belongs is refused.
	 *
	 * @covers  plgMembersAccount::authorizedKeysPath
	 * @return  void
	 */
	public function testRejectsAuthorizedKeysDirectory()
	{
		$ssh = $this->plugin->sshDirectory();

		mkdir($ssh . DS . 'authorized_keys');

		$error = null;

		$this->assertFalse($this->plugin->authorizedKeysPath($ssh, $error));
		$this->assertEquals(self::UNSAFE, $error);
	}

	/**
	 * A regular authorized_keys file is accepted.
	 *
	 * @covers  plgMembersAccount::authorizedKeysPath
	 * @return  void
	 */
	public function testAcceptsRegularAuthorizedKeys()
	{
		$ssh = $this->plugin->sshDirectory();

		file_put_contents($ssh . DS . 'authorized_keys', "ssh-rsa AAAAmember member@example.org\n");

		$this->assertEquals($ssh . DS . 'authorized_keys', $this->plugin->authorizedKeysPath($ssh));
	}

	/**
	 * Writing the key leaves the right content, restrictive permissions and no
	 * temporary files behind.
	 *
	 * @covers  plgMembersAccount::writeAuthorizedKeys
	 * @return  void
	 */
	public function testWritesKeyWithRestrictivePermissions()
	{
		$ssh     = $this->plugin->sshDirectory();
		$auth    = $this->plugin->authorizedKeysPath($ssh);
		$content = "ssh-rsa AAAAmember member@example.org\n";

		$this->assertTrue($this->plugin->writeAuthorizedKeys($auth, $content));
		$this->assertEquals($content, file_get_contents($auth));
		$this->assertEquals(0600, fileperms($auth) & 0777);
		$this->assertEquals(array(), glob($ssh . DS . '.authorized_keys.*'));
	}

	/**
	 * Emptying the key file works: it used to be a special case.
	 *
	 * @covers  plgMembersAccount::writeAuthorizedKeys
	 * @return  void
	 */
	public function testWritesEmptyKey()
	{
		$ssh  = $this->plugin->sshDirectory();
		$auth = $this->plugin->authorizedKeysPath($ssh);

		file_put_contents($auth, "ssh-rsa AAAAmember member@example.org\n");

		$this->assertTrue($this->plugin->writeAuthorizedKeys($auth, ''));
		$this->assertEquals('', file_get_contents($auth));
	}

	/**
	 * A symlink planted after the path was validated still can't divert the
	 * write: rename() replaces the link instead of following it.
	 *
	 * @covers  plgMembersAccount::writeAuthorizedKeys
	 * @return  void
	 */
	public function testWriteReplacesSymlinkPlantedAfterValidation()
	{
		$ssh     = $this->plugin->sshDirectory();
		$auth    = $this->plugin->authorizedKeysPath($ssh);
		$content = "ssh-rsa AAAAattacker attacker@example.org\n";

		// The member wins the race and points authorized_keys at the victim
		symlink($this->victimKeys(), $auth);

		$this->assertTrue($this->plugin->writeAuthorizedKeys($auth, $content));

		// The write landed in the member's own directory, not the victim's
		$this->assertFalse(is_link($auth));
		$this->assertEquals($content, file_get_contents($auth));
		$this->assertEquals(self::VICTIM_KEY, file_get_contents($this->victimKeys()));
	}
}
