<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User\Tests;

use Hubzero\Test\Basic;
use Hubzero\User\User;

/**
 * User model tests
 *
 * Covers the username and home directory checks made on save. They decide
 * from the new and stored values alone, so no database is needed.
 */
class UserTest extends Basic
{
	/**
	 * A new account must have a valid username and home directory
	 *
	 * @return  void
	 */
	public function testNewAccountWithBadValuesIsRefused()
	{
		$this->assertCount(1, User::identityErrors(
			array('username' => 'milanfar@ucsc.edu', 'homeDirectory' => '/home/nanohub/milanfar'),
			array()
		));
		$this->assertCount(1, User::identityErrors(
			array('username' => 'milanfar', 'homeDirectory' => '/home/nanohub/mil anfar'),
			array()
		));
		$this->assertCount(2, User::identityErrors(
			array('username' => 'a b', 'homeDirectory' => '/home/nanohub/a b'),
			array()
		));
	}

	/**
	 * A new account with valid values passes
	 *
	 * @return  void
	 */
	public function testNewAccountWithGoodValuesIsAccepted()
	{
		$this->assertSame(array(), User::identityErrors(
			array('username' => 'milanfar', 'homeDirectory' => '/home/nanohub/milanfar'),
			array()
		));
	}

	/**
	 * Third-party-auth accounts are created with a "-<auth_link_id>"
	 * placeholder username and no home directory until registration is done
	 *
	 * @return  void
	 */
	public function testPlaceholderAccountIsAccepted()
	{
		$this->assertSame(array(), User::identityErrors(
			array('username' => '-123', 'homeDirectory' => ''),
			array()
		));
		$this->assertSame(array(), User::identityErrors(
			array('username' => '-123', 'homeDirectory' => ''),
			array('username' => '-123', 'homeDirectory' => '')
		));
	}

	/**
	 * De-identified accounts have no home directory
	 *
	 * @return  void
	 */
	public function testDeidentifiedAccountIsAccepted()
	{
		$this->assertSame(array(), User::identityErrors(
			array('username' => 'anonUsername_123', 'homeDirectory' => ''),
			array('username' => 'anonUsername_123', 'homeDirectory' => '')
		));
	}

	/**
	 * Existing accounts with values from before the checks existed can still
	 * be saved, as long as those values are not changed
	 *
	 * @return  void
	 */
	public function testUnchangedLegacyValuesAreAccepted()
	{
		$legacy = array('username' => 'milanfar@ucsc.edu', 'homeDirectory' => '/home/nanohub/milanfar@ucsc.edu');

		$this->assertSame(array(), User::identityErrors($legacy, $legacy));
	}

	/**
	 * Changing an existing account to a bad value is refused
	 *
	 * @return  void
	 */
	public function testChangedBadValuesAreRefused()
	{
		$old = array('username' => 'milanfar', 'homeDirectory' => '/home/nanohub/milanfar');

		$this->assertCount(1, User::identityErrors(
			array('username' => 'milanfar@ucsc.edu', 'homeDirectory' => '/home/nanohub/milanfar'),
			$old
		));
		$this->assertCount(1, User::identityErrors(
			array('username' => 'milanfar', 'homeDirectory' => '/home/nanohub/../etc'),
			$old
		));
	}

	/**
	 * An existing account may not drop back to an empty home directory
	 * once it has a real username
	 *
	 * @return  void
	 */
	public function testChangedToEmptyHomeIsRefused()
	{
		$this->assertCount(1, User::identityErrors(
			array('username' => 'milanfar', 'homeDirectory' => ''),
			array('username' => 'milanfar', 'homeDirectory' => '/home/nanohub/milanfar')
		));
	}
}
