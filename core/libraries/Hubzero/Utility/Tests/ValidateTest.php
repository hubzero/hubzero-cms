<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Tests;

use Hubzero\Test\Basic;
use Hubzero\Utility\Validate;
use InvalidArgumentException;

/**
 * Validate utility test
 */
class ValidateTest extends Basic
{
	/**
	 * Tests if a value is a boolean integer or true/false
	 *
	 * @covers  \Hubzero\Utility\Validate::boolean
	 * @return  void
	 **/
	public function testBoolean()
	{
		$tests = array(
			0 => true,
			1 => true,
			'foo' => false,
			'1' => true,
			'0' => true,
			'true' => false,
			3543 => false
		);

		foreach ($tests as $test => $result)
		{
			$this->assertEquals(Validate::boolean($test), $result);
		}

		$this->assertTrue(Validate::boolean(true));
		$this->assertTrue(Validate::boolean(false));
	}

	/**
	 * Tests if a value is within a specified range.
	 *
	 * @covers  \Hubzero\Utility\Validate::between
	 * @return  void
	 **/
	public function testBetween()
	{
		$tests = array(
			array(
				'str' => 'Donec id elit non mi porta gravida at eget metus.',
				'min' => 3,
				'max' => 100,
				'val' => true
			),
			array(
				'str' => 'Vehicula Sit Dolor',
				'min' => 1,
				'max' => 7,
				'val' => false
			),
			array(
				'str' => '123456789',
				'min' => 0,
				'max' => 10,
				'val' => true
			),
			array(
				'str' => 'dolo',
				'min' => 5,
				'max' => 8,
				'val' => false
			),
		);

		foreach ($tests as $test)
		{
			$this->assertEquals(Validate::between($test['str'], $test['min'], $test['max']), $test['val']);
		}
	}

	/**
	 * Tests if a value is numeric.
	 *
	 * @covers  \Hubzero\Utility\Validate::numeric
	 * @return  void
	 **/
	public function testNumeric()
	{
		$tests = array(
			"42" => true,
			1337 => true,
			0x539 => true,
			02471 => true,
			0b10100111001 => true,
			1337e0 => true,
			"not numeric" => false,
			9.1 => true,
			null => false
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::numeric($value), $result);
		}

		$this->assertFalse(Validate::numeric(array()));
	}

	/**
	 * Tests if value is an integer
	 *
	 * @covers  \Hubzero\Utility\Validate::integer
	 * @return  void
	 **/
	public function testInteger()
	{
		$tests = array(
			"42" => true,
			'+51' => true,
			-16 => true,
			1337 => true,
			0x539 => false,
			02471 => true,
			1337e0 => true,
			"not numeric" => false,
			9.1 => true,
			null => false
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::integer($value), $result);
		}

		$this->assertFalse(Validate::integer(array()));
	}

	/**
	 * Tests if value is a positive integer
	 *
	 * @covers  \Hubzero\Utility\Validate::positiveInteger
	 * @return  void
	 **/
	public function testPositiveInteger()
	{
		$tests = array(
			0 => false,
			"42" => true,
			'+51' => true,
			-16 => false,
			1337 => true,
			0x539 => true,
			02471 => true,
			1337e0 => true,
			"not numeric" => false,
			9.1 => true,
			null => false
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::positiveInteger($value), $result);
		}

		$this->assertFalse(Validate::positiveInteger(array()));
	}

	/**
	 * Tests if value is a non positive integer
	 *
	 * @covers  \Hubzero\Utility\Validate::nonPositiveInteger
	 * @return  void
	 **/
	public function testNonPositiveInteger()
	{
		$tests = array(
			0 => true,
			"42" => false,
			'+51' => false,
			-16 => true,
			'-1337' => true,
			"not numeric" => false,
			9.1 => false,
			null => false
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::nonPositiveInteger($value), $result);
		}

		$this->assertFalse(Validate::nonPositiveInteger(array()));
	}

	/**
	 * Tests if value is a non-negative integer
	 *
	 * @covers  \Hubzero\Utility\Validate::nonNegativeInteger
	 * @return  void
	 **/
	public function testNonNegativeInteger()
	{
		$tests = array(
			0 => true,
			"42" => true,
			'+51' => true,
			-16 => false,
			1337 => true,
			"not numeric" => false,
			9.1 => true,
			null => false
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::nonNegativeInteger($value), $result);
		}

		$this->assertFalse(Validate::nonNegativeInteger(array()));
	}

	/**
	 * Tests if value is a negative integer
	 *
	 * @covers  \Hubzero\Utility\Validate::negativeInteger
	 * @return  void
	 **/
	public function testNegativeInteger()
	{
		$tests = array(
			0 => false,
			"42" => false,
			'+51' => false,
			-16 => true,
			1337 => false,
			"not numeric" => false,
			9.1 => false,
			null => false
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::negativeInteger($value), $result);
		}

		$this->assertFalse(Validate::negativeInteger(array()));
	}

	/**
	 * Tests if value is an orcid
	 *
	 * @covers  \Hubzero\Utility\Validate::orcid
	 * @return  void
	 **/
	public function testOrcid()
	{
		$tests = array(
			'0000-0000-0000-0000' => true,
			'123-45635-7891-0112' => false,
			'123A-45B6-7CD1-E190' => false,
			'1234567891011112' => false,
			'1234-4567-8910-1112' => true,
			'1234-4567-8910' => false,
			'1234-4567' => false,
			'1234' => false,
			'A123-4567-8910-1112' => false
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::orcid($value), $result);
		}
	}

	/**
	 * Tests if value is not Empty
	 *
	 * @covers  \Hubzero\Utility\Validate::notEmpty
	 * @return  void
	 **/
	public function testNotEmpty()
	{
		$value = '';
		$this->assertEquals(Validate::notEmpty($value), false);

		$value = '  ';
		$this->assertEquals(Validate::notEmpty($value), false);

		$value = "\n";
		$this->assertEquals(Validate::notEmpty($value), false);

		$value = "\t";
		$this->assertEquals(Validate::notEmpty($value), false);

		$value = "\n0";
		$this->assertEquals(Validate::notEmpty($value), true);

		$value = '0';
		$this->assertEquals(Validate::notEmpty($value), true);

		$value = 'fsdd';
		$this->assertEquals(Validate::notEmpty($value), true);

		$value = array('check' => 'fsdd');
		$this->assertEquals(Validate::notEmpty($value), true);
	}

	/**
	 * Tests if value is a valid group alias
	 *
	 * @covers  \Hubzero\Utility\Validate::group
	 * @return  void
	 **/
	public function testGroup()
	{
		$tests = array(
			'testname' => true,
			'91test' => true,
			'91_test' => true,
			'_91test' => false,
			'12345' => false,
			'Test Name' => false,
			'TESTNAME' => false,
			'test-name' => false,
			'bin' => false
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::group($value), $result);
		}

		$tests = array(
			'test-name' => true
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::group($value, true), $result);
		}
	}

	/**
	 * Tests if value is a valid username
	 *
	 * @covers  \Hubzero\Utility\Validate::username
	 * @return  void
	 **/
	public function testUsername()
	{
		$tests = array(
			'testname' => true,
			'91test' => true,
			'91_test' => true,
			'_91test' => false,
			'12345' => false,
			126575 => false,
			0 => false,
			'Test Name' => false,
			'test.name ' => false,
			'TESTNAME' => false,
			'test-name' => false,
			'bin' => false
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::username($value), $result);
		}
	}

	/**
	 * Tests if value is reserved
	 *
	 * @covers  \Hubzero\Utility\Validate::reserved
	 * @return  void
	 **/
	public function testReserved()
	{
		$usernames = array(
			'adm',
			'alfred',
			'apache',
			'backup',
			'bin',
			'canna',
			'condor',
			'condor-util',
			'daemon',
			'debian-exim',
			'exim',
			' ftp',
			'   games',
			'ganglia',
			'gnats',
			'gopher',
			'gridman',
			'halt',
			'httpd',
			'ibrix',
			' invigosh ',
			'irc',
			'LDAP',
			'list',
			'lp',
			'mail  ',
			'   mailnull',
			'man',
			'mysql',
			'nagios',
			'netdump',
			'news',
			'nfsnobody',
			"\nnoaccess",
			"nobody\t",
			'nscd',
			'ntp',
			'operator',
			'openldap',
			'pcap',
			'postgres',
			'proxy',
			'pvm',
			"root\t",
			'rpc',
			'rpcuser',
			'rpm',
			'sag',
			'shutdown',
			'smmsp',
			'sshd',
			'statd',
			'sync',
			'sys',
			'submit',
			'uucp',
			'vncproxy',
			'vncproxyd',
			'vcsa',
			'wheel',
			'www',
			'www-data',
			'xfs',
		);
		$groups = array(
			'abrt',
			'adm',
			'apache',
			'apps',
			'audio',
			'avahi',
			'avahi-autoipd',
			'backup',
			'bin',
			'boinc',
			'cdrom',
			'cgred',
			'cl-builder',
			'clamav',
			'condor',
			'crontab',
			'ctapiusers',
			'daemon',
			"\ndbus",
			'debian-exim',
			'desktop_admin_r',
			'desktop_user_r   ',
			'dialout',
			'dip',
			'disk',
			'fax',
			'floppy',
			' ftp',
			'fuse',
			'   games',
			'gdm',
			'gnats',
			'gopher',
			'gridman',
			'haldaemon',
			' hsqldb ',
			'irc',
			'itisunix',
			'jackuser',
			'kmem',
			'kvm',
			'LDAP',
			'libuuid',
			'list',
			'lock',
			'lp',
			'mail  ',
			'man',
			'mem',
			'messagebus',
			'mysql',
			'netdev',
			'news',
			'nfsnobody',
			"nobody\t",
			'nogroup',
			'nscd',
			'nslcd',
			'ntp',
			'openldap',
			'operator',
			'oprofile',
			'plugdev',
			'postdrop',
			'postfix',
			'powerdev',
			'proxy',
			'pulse',
			'pulse-access',
			'qemu',
			'qpidd',
			'radvd',
			'rdma',
			"root\t",
			'rpc',
			'rpcuser',
			'rtkit',
			'sasl',
			'saslauth',
			'shadow',
			'slocate',
			'src',
			'ssh',
			'sshd',
			'ssl-cert',
			'STAFF',
			'stapdev',
			'stapusr',
			'stap-server',
			'stapsys',
			'stunnel4',
			'sudo',
			'sys',
			'tape',
			'tcpdump',
			'tomcat',
			'tty',
			'tunnelers',
			'usbmuxd',
			'users',
			'utmp',
			'utempter',
			'uucp',
			'video',
			'vcsa',
			'voice',
			'wbpriv',
			'webalizer',
			'wheel',
			'www-data',
			'zookeeper',
		);

		foreach ($usernames as $val)
		{
			$this->assertTrue(Validate::reserved('username', $val));
		}

		foreach ($groups as $val)
		{
			$this->assertTrue(Validate::reserved('group', $val));
		}

		$diff = array_diff($groups, $usernames);

		foreach ($diff as $val)
		{
			$this->assertFalse(Validate::reserved('username', $val));
		}

		$diff = array_diff($usernames, $groups);

		foreach ($diff as $val)
		{
			$this->assertFalse(Validate::reserved('group', $val));
		}

		$this->setExpectedException(InvalidArgumentException::class);
		$result = Validate::reserved('foo', 'bar');
	}

	/**
	 * Tests if value is a string contains only integer or letters
	 *
	 * @covers  \Hubzero\Utility\Validate::alphaNumeric
	 * @return  void
	 **/
	public function testAlphaNumeric()
	{
		$tests = array(
			'testname' => true,
			'91test' => true,
			'91_test' => false,
			'AfOO981' => true,
			'12345' => true,
			'Test Name' => false,
			'TESTNAME' => true,
			'test!' => false,
			'test-name' => false
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::alphaNumeric($value), $result);
		}

		$value = '0';
		$this->assertTrue(Validate::alphaNumeric($value));

		$value = '';
		$this->assertFalse(Validate::alphaNumeric($value));

		foreach ($tests as $value => $result)
		{
			$value = array('check' => $value);
			$this->assertEquals(Validate::alphaNumeric($value), $result);
		}
	}

	/**
	 * Tests if value is a number is in specified range.
	 *
	 * @covers  \Hubzero\Utility\Validate::range
	 * @return  void
	 **/
	public function testRange()
	{
		$value = 5;
		$this->assertTrue(Validate::range($value));

		$this->assertTrue(Validate::range($value, 1));

		$this->assertTrue(Validate::range($value, 1, 10));

		$this->assertTrue(Validate::range($value, 9));

		$this->assertTrue(Validate::range($value, null, 4));

		$this->assertFalse(Validate::range($value, 1, 4));

		$value = -5;
		$this->assertTrue(Validate::range($value, -10, -1));

		$value = 'five';
		$this->assertFalse(Validate::range($value));

		$value = log(0);
		$this->assertFalse(Validate::range($value), 0, 100000);
	}

	/**
	 * Tests if value is blank
	 *
	 * @covers  \Hubzero\Utility\Validate::blank
	 * @return  void
	 **/
	public function testBlank()
	{
		$value = "\n ";
		$this->assertTrue(Validate::blank($value));

		$value = '  ';
		$this->assertTrue(Validate::blank($value));

		$value = '';
		$this->assertTrue(Validate::blank($value));

		$value = ' 2 ';
		$this->assertFalse(Validate::blank($value));

		$value = null;
		$this->assertTrue(Validate::blank($value));

		$value = '0';
		$this->assertFalse(Validate::blank($value));

		$value = 0;
		$this->assertFalse(Validate::blank($value));

		$value = array('check' => '0');
		$this->assertFalse(Validate::blank($value));
	}

	/**
	 * Tests if value is a valid URL
	 *
	 * @covers  \Hubzero\Utility\Validate::url
	 * @return  void
	 **/
	public function testUrl()
	{
		$value = 'https://example.com';
		$this->assertTrue(Validate::url($value));

		$value = 'http://www.example.com';
		$this->assertTrue(Validate::url($value));

		$value = 'www.example.com';
		$this->assertTrue(Validate::url($value));

		$value = 'http://example.com.uk';
		$this->assertTrue(Validate::url($value));

		$value = 'http://example.com.uk/foo/bar';
		$this->assertTrue(Validate::url($value));

		$value = 'http://example.com#foo';
		$this->assertTrue(Validate::url($value));

		$value = 'http://example.com/foo.php';
		$this->assertTrue(Validate::url($value));

		$value = 'http://example.com?foo=bar&lorem=ipsum#!dolor';
		$this->assertTrue(Validate::url($value));

		$value = 'http://example.com:80/foo/bar?foo=bar#content';
		$this->assertTrue(Validate::url($value));

		$value = 'ftp://example.com';
		$this->assertTrue(Validate::url($value));

		//$value = 'https://login:pass@secure.example.com:443/test/query.php?kingkong=toto#doc3';
		//$this->assertTrue(Validate::url($value));
		$value = 'https:/example.com';
		$this->assertFalse(Validate::url($value));

		$value = 'http://user@:80';
		$this->assertFalse(Validate::url($value));

		$value = 'skl://example.com';
		$this->assertFalse(Validate::url($value));
	}

	/**
	 * Tests is valid ip check
	 *
	 * @return  void
	 * @covers  \Hubzero\Utility\Validate::ip
	 **/
	public function testIp()
	{
		$this->assertTrue(Validate::ip('192.168.0.1'));

		$this->assertFalse(Validate::ip('256.256.256.256'));

		$this->assertTrue(Validate::ip('684D:1111:222:3333:4444:5555:6:77', 'ipv6'));

		$this->assertFalse(Validate::ip('192.168.0.1', 'ipv6'));

		$this->assertFalse(Validate::ip('684D:1111:222:3333:4444:5555:6:77', 'ipv4'));
	}

	/**
	 * Tests is valid password check
	 *
	 * @return  void
	 * @covers  \Hubzero\Utility\Validate::password
	 **/
	public function testPassword()
	{
		$this->assertTrue(Validate::password('password'));

		$this->assertTrue(Validate::password('PASS'));

		$this->assertTrue(Validate::password('256.256.256.256'));

		$this->assertTrue(Validate::password('{lorem-ipsum}'));

		$this->assertFalse(Validate::password('\lorem-ipsum'));

		$this->assertFalse(Validate::password('lorem ipsum dolor'));
	}
}
