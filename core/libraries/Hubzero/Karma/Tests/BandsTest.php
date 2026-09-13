<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Karma\Tests;

use Hubzero\Test\Basic;
use Hubzero\Karma\Bands;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Threshold band tests
 */
class BandsTest extends Basic
{
	/**
	 * The adjective bands the global scale ships with
	 *
	 * @var  string
	 */
	const ADJECTIVES = '-15=Restricted|-5=Provisional|0=Standing|10=Established|30=Trusted|99999=Distinguished';

	/**
	 * A posting rate limit, as a gate would express it
	 *
	 * @var  string
	 */
	const PER_DAY = '-5=3|10=20|99999=40';

	/**
	 * Tests that a band string parses into an ordered map
	 *
	 * @return  void
	 */
	public function testParseOrdersAscending()
	{
		$bands = Bands::parse('30=Trusted|-15=Restricted|0=Standing');

		$this->assertEquals(array('-15', '0', '30'), array_keys($bands));
		$this->assertEquals('Restricted', $bands['-15']);
	}

	/**
	 * Tests that malformed segments are skipped rather than fatal
	 *
	 * These strings are administrator-editable; one bad segment should not
	 * take a page down.
	 *
	 * @return  void
	 */
	public function testParseSkipsMalformedSegments()
	{
		$bands = Bands::parse('0=Standing|nonsense|=empty|abc=NotNumeric|30=Trusted');

		$this->assertEquals(array('0', '30'), array_keys($bands));
	}

	/**
	 * Tests that an empty definition yields no bands
	 *
	 * @return  void
	 */
	public function testParseHandlesEmptyString()
	{
		$this->assertEquals(array(), Bands::parse(''));
	}

	/**
	 * Tests lookup at and between every boundary of the adjective bands
	 *
	 * The rule is that the first threshold at or above the subject wins, so
	 * a value sitting exactly on a boundary takes that boundary's band.
	 *
	 * @return  void
	 */
	#[DataProvider('adjectiveProvider')]
	public function testLookupPlacesValues($value, $expected)
	{
		$this->assertEquals($expected, Bands::lookup(self::ADJECTIVES, $value));
	}

	/**
	 * Values and the band each belongs to
	 *
	 * @return  array
	 */
	public static function adjectiveProvider()
	{
		return array(
			'far below the lowest band'   => array(-25, 'Restricted'),
			'on the lowest boundary'      => array(-15, 'Restricted'),
			'just above the lowest'       => array(-14, 'Provisional'),
			'on the next boundary'        => array(-5, 'Provisional'),
			'zero'                        => array(0, 'Standing'),
			'just above zero'             => array(1, 'Established'),
			'on the established boundary' => array(10, 'Established'),
			'just above established'      => array(11, 'Trusted'),
			'on the trusted boundary'     => array(30, 'Trusted'),
			'above every named band'      => array(50, 'Distinguished'),
		);
	}

	/**
	 * Tests the rate-limit bands, which return numbers rather than words
	 *
	 * @return  void
	 */
	public function testLookupReturnsNonWordValues()
	{
		$this->assertEquals('3', Bands::lookup(self::PER_DAY, -10));
		$this->assertEquals('3', Bands::lookup(self::PER_DAY, -5));
		$this->assertEquals('20', Bands::lookup(self::PER_DAY, 0));
		$this->assertEquals('20', Bands::lookup(self::PER_DAY, 10));
		$this->assertEquals('40', Bands::lookup(self::PER_DAY, 11));
	}

	/**
	 * Tests that a value above every band falls through to the default
	 *
	 * @return  void
	 */
	public function testLookupFallsThroughToDefault()
	{
		$this->assertEquals('unranked', Bands::lookup('0=Standing|10=Trusted', 999, 'unranked'));
		$this->assertNull(Bands::lookup('0=Standing', 1));
	}

	/**
	 * Tests that fractional karma places in the right band
	 *
	 * Balances are floats, so boundaries have to behave between integers.
	 *
	 * @return  void
	 */
	public function testLookupHandlesFractionalValues()
	{
		$this->assertEquals('Provisional', Bands::lookup(self::ADJECTIVES, -5.0));
		$this->assertEquals('Standing', Bands::lookup(self::ADJECTIVES, -4.5));
		$this->assertEquals('Established', Bands::lookup(self::ADJECTIVES, 9.9));
	}

	/**
	 * Tests that a parsed map renders back to a string that parses the same
	 *
	 * @return  void
	 */
	public function testBuildRoundTrips()
	{
		$rebuilt = Bands::build(Bands::parse(self::ADJECTIVES));

		$this->assertEquals(Bands::parse(self::ADJECTIVES), Bands::parse($rebuilt));
	}
}
