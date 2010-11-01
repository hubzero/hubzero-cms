<?php
/**
 * @version		$Id: JURITest.php 14408 2010-01-26 15:00:08Z louis $
 * @package		Joomla.UnitTest
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU General Public License
 */

class JURITest extends PHPUnit_Framework_TestCase
{
	public $instance;
	
	/**
	 * Clear the cache and load sample data
	 */
	public function setUp()
	{
		jimport( 'joomla.environment.uri' );
		$this->instance = new JURI();
	}

	public function provider() {
		return array(
			array(
				'http://www.joomla.org/',
				array(
					'shouldParse' => true,
					'scheme' => 'http',
					'host' => 'www.joomla.org',
					'user' => null,
					'pass' => null,
					'port' => null,
					'path' => '/',
					'query' => null,
					'fragment' => null
				)
			)
		);
	
	}

	/**
	 * @dataProvider provider
	 */
	public function testParse($url, $parsedURL)
	{
		$result = $this->instance->parse($url);
		// assert that we succeed if we should be able to parse the url, or that we fail if we shouldn't
		$this->assertEquals($result, $parsedURL['shouldParse']);
		$this->assertEquals($this->instance->getScheme(), $parsedURL['scheme']);
		$this->assertEquals($this->instance->getHost(), $parsedURL['host']);
		$this->assertEquals($this->instance->getUser(), $parsedURL['user']);
		$this->assertEquals($this->instance->getPass(), $parsedURL['pass']);
		$this->assertEquals($this->instance->getPort(), $parsedURL['port']);
		$this->assertEquals($this->instance->getPath(), $parsedURL['path']);
		$this->assertEquals($this->instance->getQuery(), $parsedURL['query']);
		$this->assertEquals($this->instance->getFragment(), $parsedURL['fragment']);
	}
	
}
