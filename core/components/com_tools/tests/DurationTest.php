<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tests;

require_once dirname(__DIR__) . '/helpers/duration.php';

use Components\Tools\Helpers\Duration;
use Hubzero\Test\Basic;

/**
 * Tests for the helper the hard timeout is given and shown with
 */
class DurationTest extends Basic
{
	/**
	 * Each unit, and a fraction of one, comes out in seconds
	 *
	 * @return  void
	 */
	public function testToSeconds()
	{
		$this->assertSame(90, Duration::toSeconds('90', 'seconds'));
		$this->assertSame(5400, Duration::toSeconds('90', 'minutes'));
		$this->assertSame(5400, Duration::toSeconds('1.5', 'hours'));
		$this->assertSame(172800, Duration::toSeconds(' 2 ', 'days'));
	}

	/**
	 * Empty and zero mean no timeout at all
	 *
	 * @return  void
	 */
	public function testNothingIsNull()
	{
		$this->assertNull(Duration::toSeconds('', 'hours'));
		$this->assertNull(Duration::toSeconds('0', 'hours'));
		$this->assertNull(Duration::toSeconds(null, 'hours'));
	}

	/**
	 * Words, negative numbers and unknown units are refused
	 *
	 * @return  void
	 */
	public function testRefusesWhatIsNotALength()
	{
		foreach (array(array('two', 'hours'), array('-1', 'hours'), array('2', 'fortnights')) as $case)
		{
			try
			{
				Duration::toSeconds($case[0], $case[1]);
				$this->fail('Accepted ' . implode(' ', $case));
			}
			catch (\InvalidArgumentException $e)
			{
				$this->assertStringContainsString('Not a', $e->getMessage());
			}
		}
	}

	/**
	 * Seconds are shown in the largest unit that holds them whole
	 *
	 * @return  void
	 */
	public function testFromSeconds()
	{
		$this->assertSame(array(2, 'days'), Duration::fromSeconds(172800));
		$this->assertSame(array(2, 'hours'), Duration::fromSeconds(7200));
		$this->assertSame(array(90, 'minutes'), Duration::fromSeconds(5400));
		$this->assertSame(array(90, 'seconds'), Duration::fromSeconds(90));
		$this->assertSame(array('', 'hours'), Duration::fromSeconds(null));
	}
}
