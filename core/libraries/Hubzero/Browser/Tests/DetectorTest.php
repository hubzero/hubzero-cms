<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Browser\Tests;

use Hubzero\Test\Basic;
use Hubzero\Browser\Detector;
use SimpleXmlElement;
use stdClass;

/**
 * Detector tests
 */
class DetectorTest extends Basic
{
	/**
	 * Tests the match() method.
	 *
	 * @covers  \Hubzero\Browser\Detector::__construct
	 * @covers  \Hubzero\Browser\Detector::match
	 * @covers  \Hubzero\Browser\Detector::agent
	 * @covers  \Hubzero\Browser\Detector::name
	 * @covers  \Hubzero\Browser\Detector::version
	 * @covers  \Hubzero\Browser\Detector::platform
	 * @covers  \Hubzero\Browser\Detector::major
	 * @covers  \Hubzero\Browser\Detector::minor
	 * @covers  \Hubzero\Browser\Detector::_setPlatform
	 * @return  void
	 **/
	public function testMatch()
	{
		$uas = self::map();

		foreach ($uas as $userAgentString)
		{
			$browser = new Detector($userAgentString->string);

			$this->assertEquals($userAgentString->string, $browser->agent());
			$this->assertEquals(strtolower($userAgentString->browser), $browser->name());
			$this->assertEquals($userAgentString->browserVersion, $browser->version());
			$this->assertEquals($userAgentString->os, $browser->platform());

			list($major, $minor) = explode('.', $userAgentString->browserVersion);

			$this->assertEquals(intval($major), $browser->major());
			$this->assertEquals(intval($major), $browser->version('major'));

			$this->assertEquals(intval($minor), $browser->minor());
			$this->assertEquals(intval($minor), $browser->version('minor'));
		}
	}

	/**
	 * Tests the isBrowser() method.
	 *
	 * @covers  \Hubzero\Browser\Detector::isBrowser
	 * @return  void
	 **/
	public function testIsBrowser()
	{
		$browser = new Detector('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.132 Safari/537.36 OPR/21.0.1432.67');

		$this->assertTrue($browser->isBrowser('Opera'));
		$this->assertFalse($browser->isBrowser('Chrome'));

		$browser = new Detector('Mozilla/5.0 (iPhone; CPU iPhone OS 8_1_2 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) CriOS/43.0.2357.51 Mobile/12B440 Safari/600.1.4');

		$this->assertTrue($browser->isBrowser('Chrome'));
		$this->assertFalse($browser->isBrowser('Safari'));
	}

	/**
	 * Tests the isMobile() method.
	 *
	 * @covers  \Hubzero\Browser\Detector::isMobile
	 * @return  void
	 **/
	public function testIsMobile()
	{
		$uas = self::map();

		foreach ($uas as $userAgentString)
		{
			$browser = new Detector($userAgentString->string);

			if ($userAgentString->device == 'iPhone' || $userAgentString->device == 'iPad' || $userAgentString->device == 'phone')
			{
				$this->assertTrue($browser->isMobile());
			}
			else
			{
				$this->assertFalse($browser->isMobile());
			}
		}
	}

	/**
	 * Tests the isRobot() method.
	 *
	 * @covers  \Hubzero\Browser\Detector::isRobot
	 * @return  void
	 **/
	public function testIsRobot()
	{
		$uas = array(
			'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0); 360Spider' => true,
			'Mozilla/5.0 (compatible; alexa site audit/1.0; http://www.alexa.com/help/webmasters; )' => true,
			'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10 _1) AppleWebKit/600.2.5 (KHTML, like Gecko) Version/8.0.2 Safari/600.2.5 (Applebot/0.1; +http://www.apple.com/go/applebot)' => true,
			'Mozilla/2.0 (compatible; Ask Jeeves/Teoma)' => true,
			'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Safari/537.36' => true,
			'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.89 Vivaldi/1.0.83.38 Safari/537.36' => false,
			'Mozilla/5.0 (AmigaOS; U; AmigaOS 1.3; en-US; rv:1.8.1.21) Gecko/20090303 SeaMonkey/1.1.15' => false,
			'Mozilla/5 (X11; Linux x86_64) AppleWebKit/537.4 (KHTML like Gecko) Arch Linux Firefox/23.0 Xfce' => false,
			'Mozilla/5.0 (X11; CrOS x86_64 4731.101.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.67 Safari/537.36' => false
		);

		foreach ($uas as $userAgentString => $isRobot)
		{
			$browser = new Detector($userAgentString);

			if ($isRobot)
			{
				$this->assertTrue($browser->isRobot());
			}
			else
			{
				$this->assertFalse($browser->isRobot());
			}
		}
	}

	/**
	 * Map test data
	 *
	 * @return  array
	 */
	private static function map()
	{
		$collection = array();

		$xml = new SimpleXmlElement(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'UserAgentStrings.xml'));

		if ($xml)
		{
			foreach ($xml->strings->string as $string)
			{
				$string = $string->field;

				$userAgentString = new stdClass();
				$userAgentString->browser        = (string)$string[0];
				$userAgentString->browserVersion = (string)$string[1];
				$userAgentString->os             = (string)$string[2];
				$userAgentString->osVersion      = (string)$string[3];
				$userAgentString->device         = (string)$string[4];
				$userAgentString->deviceVersion  = (string)$string[5];
				$userAgentString->string         = str_replace(array(PHP_EOL, '  '), ' ', (string)$string[6]);

				$collection[] = $userAgentString;
			}
		}

		return $collection;
	}
}
