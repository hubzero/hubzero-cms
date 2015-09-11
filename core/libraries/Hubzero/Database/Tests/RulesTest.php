<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     Class available since release 2.0.0
 */

namespace Hubzero\Database\Tests;

use Hubzero\Test\Basic;
use Hubzero\Database\Rules;

/**
 * Database rules validation tests
 */
class RulesTest extends Basic
{
	/**
	 * Test to make sure an empty string fails validation
	 *
	 * @return  void
	 **/
	public function testNotEmpty()
	{
		$pass = Rules::validate(['name' => 'Me'], ['name' => 'notempty']);
		$fail = Rules::validate(['name' => ''], ['name' => 'notempty']);

		$this->assertTrue($pass, 'Rule (notempty) should have validated with a name of "Me"');
		$this->assertCount(1, $fail, 'Rule (notempty) should have returned one error for empty name');
	}

	/**
	 * Test to make sure a negative number fails validation
	 *
	 * @return  void
	 **/
	public function testPositive()
	{
		$pass = Rules::validate(['age' => 25], ['age' => 'positive']);
		$fail = Rules::validate(['age' => -1], ['age' => 'positive']);

		$this->assertTrue($pass, 'Rule (positive) should have validated with an age of 25');
		$this->assertCount(1, $fail, 'Rule (positive) should have returned one error for an age of -1');
	}

	/**
	 * Test to make sure a zero fails validation
	 *
	 * @return  void
	 **/
	public function testNonzero()
	{
		$passPos = Rules::validate(['score' =>  1], ['score' => 'nonzero']);
		$passNeg = Rules::validate(['score' => -1], ['score' => 'nonzero']);
		$fail    = Rules::validate(['score' =>  0], ['score' => 'nonzero']);

		$this->assertTrue($passPos, 'Rule (nonzero) should have validated with a score of 1');
		$this->assertTrue($passNeg, 'Rule (nonzero) should have validated with a score of -1');
		$this->assertCount(1, $fail, 'Rule (nonzero) should have returned one error for a score of 0');
	}

	/**
	 * Test to make sure a number fails validation
	 *
	 * @return  void
	 **/
	public function testAlpha()
	{
		$pass = Rules::validate(['name' => "John Awesome"], ['name' => 'alpha']);
		$fail = Rules::validate(['name' =>  "MI6"], ['name' => 'alpha']);

		$this->assertTrue($pass, 'Rule (alpha) should have validated with a name of "John Awesome"');
		$this->assertCount(1, $fail, 'Rule (alpha) should have returned one error for a name of "MI6"');
	}

	/**
	 * Test to make sure a bad phone fails validation
	 *
	 * @return  void
	 **/
	public function testPhone()
	{
		$pass1 = Rules::validate(['phone' => "765-494-4000"], ['phone' => 'phone']);
		$pass2 = Rules::validate(['phone' => "(765) 494-4000"], ['phone' => 'phone']);
		$pass3 = Rules::validate(['phone' => "7654944000"], ['phone' => 'phone']);
		$fail1 = Rules::validate(['phone' =>  "12345"], ['phone' => 'phone']);
		$fail2 = Rules::validate(['phone' =>  "123-456-7890"], ['phone' => 'phone']);

		$this->assertTrue($pass1, 'Rule (phone) should have validated with a phone of "765-494-4000"');
		$this->assertTrue($pass2, 'Rule (phone) should have validated with a phone of "(765) 494-4000"');
		$this->assertTrue($pass3, 'Rule (phone) should have validated with a phone of "7654944000"');
		$this->assertCount(1, $fail1, 'Rule (phone) should have returned one error for a phone of "12345"');
		$this->assertCount(1, $fail2, 'Rule (phone) should have returned one error for a phone of "123-456-7890"');
	}

	/**
	 * Test to make sure an improper email fails validation
	 *
	 * @return  void
	 **/
	public function testEmail()
	{
		$pass = Rules::validate(['email' => "you@gmail.com"], ['email' => 'email']);
		$fail = Rules::validate(['email' =>  "me.com"], ['email' => 'email']);

		$this->assertTrue($pass, 'Rule (email) should have validated with a email of "you@gmail.com"');
		$this->assertCount(1, $fail, 'Rule (email) should have returned one error for a email of "me.com"');
	}

	/**
	 * Test to make sure an empty string fails validation
	 *
	 * @return  void
	 **/
	public function testCompoundRules()
	{
		$pass = Rules::validate(['name' => "mr cool"], ['name' => 'notempty|alpha']);
		$fail = Rules::validate(['name' => ""],        ['name' => 'notempty|alpha']);

		$this->assertTrue($pass, 'Rule (notempty|alpha) should have validated with a name of "mr cool"');
		$this->assertCount(2, $fail, 'Rules (notempty|alpha) should have returned two errors for a name of ""');
	}

	/**
	 * Test to make sure partial failure still results in failure
	 *
	 * @return  void
	 **/
	public function testPartialFailure()
	{
		$fail = Rules::validate(['name' => "Mr. Awesome"], ['name' => 'notempty|alpha']);

		$this->assertCount(1, $fail, 'Rules (notempty|alpha) should have returned one error for a name of "Mr. Awesome"');
	}

	/**
	 * Test to make sure custom validation rules result in failure
	 *
	 * @return  void
	 **/
	public function testCustomRules()
	{
		$endAfterStart = function($data)
		{
			return $data['end'] > $data['start'] ? false : 'The end must be after the beginning';
		};

		$fail = Rules::validate(['start' => '2015-07-01 00:00:00', 'end' => '2015-06-01 00:00:00'], ['end' => $endAfterStart]);

		$beSquare = function($data)
		{
			return $data['height'] == $data['width'] ? false : 'It\'s not a square!';
		};

		$pass = Rules::validate(['height' => 5, 'width' => 5], ['width' => $beSquare]);

		$this->assertCount(1, $fail, 'Rules (custom) should have returned one error for an end date before the beginning date');
		$this->assertTrue($pass, 'Rule (custom) should have validated with an equal height and width');
	}
}