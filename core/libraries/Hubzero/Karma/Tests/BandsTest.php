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
	 * Slashdot's karma_adj, which every band case here is drawn from
	 *
	 * @var  string
	 */
	const ADJECTIVES = '-10=Terrible|-1=Bad|0=Neutral|12=Positive|25=Good|99999=Excellent';

	/**
	 * Slashdot's comments_perday_bykarma
	 *
	 * @var  string
	 */
	const PER_DAY = '-1=2|25=25|99999=50';

	/**
	 * Tests that a band string parses into an ordered map
	 *
	 * @return  void
	 */
	public function testParseOrdersAscending()
	{
		$bands = Bands::parse('25=Good|-10=Terrible|0=Neutral');

		$this->assertEquals(array('-10', '0', '25'), array_keys($bands));
		$this->assertEquals('Terrible', $bands['-10']);
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
		$bands = Bands::parse('0=Neutral|nonsense|=empty|abc=NotNumeric|25=Good');

		$this->assertEquals(array('0', '25'), array_keys($bands));
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
			'far below the lowest band' => array(-25, 'Terrible'),
			'on the lowest boundary'    => array(-10, 'Terrible'),
			'just above the lowest'     => array(-9, 'Bad'),
			'on the next boundary'      => array(-1, 'Bad'),
			'zero'                      => array(0, 'Neutral'),
			'just above zero'           => array(1, 'Positive'),
			'on the positive boundary'  => array(12, 'Positive'),
			'just above positive'       => array(13, 'Good'),
			'on the good boundary'      => array(25, 'Good'),
			'above every named band'    => array(50, 'Excellent'),
		);
	}

	/**
	 * Tests the rate-limit bands, which return numbers rather than words
	 *
	 * @return  void
	 */
	public function testLookupReturnsNonWordValues()
	{
		$this->assertEquals('2', Bands::lookup(self::PER_DAY, -5));
		$this->assertEquals('2', Bands::lookup(self::PER_DAY, -1));
		$this->assertEquals('25', Bands::lookup(self::PER_DAY, 0));
		$this->assertEquals('25', Bands::lookup(self::PER_DAY, 25));
		$this->assertEquals('50', Bands::lookup(self::PER_DAY, 26));
	}

	/**
	 * Tests that a value above every band falls through to the default
	 *
	 * @return  void
	 */
	public function testLookupFallsThroughToDefault()
	{
		$this->assertEquals('unranked', Bands::lookup('0=Neutral|10=Good', 999, 'unranked'));
		$this->assertNull(Bands::lookup('0=Neutral', 1));
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
		$this->assertEquals('Bad', Bands::lookup(self::ADJECTIVES, -1.0));
		$this->assertEquals('Neutral', Bands::lookup(self::ADJECTIVES, -0.5));
		$this->assertEquals('Positive', Bands::lookup(self::ADJECTIVES, 11.9));
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
