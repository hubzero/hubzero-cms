<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam\Tests;

use Hubzero\Test\Basic;
use Hubzero\Spam\StringProcessor\NoneStringProcessor;
use Hubzero\Spam\StringProcessor\NativeStringProcessor;

/**
 * Spam StringProcessor tests
 */
class StringProcessorTest extends Basic
{
	/**
	 * Tests for setting and getting a StringProcessor
	 *
	 * @covers  \Hubzero\Spam\StringProcessor\NoneStringProcessor::prepare
	 * @return  void
	 **/
	public function testNoneStringProcessor()
	{
		$processor = new NoneStringProcessor();

		$text   = 'Curabitur blandit tempus porttitor.';
		$result = $processor->prepare($text);

		$this->assertEquals($result, $text);
	}

	/**
	 * Tests for setting and getting a StringProcessor
	 *
	 * @covers  \Hubzero\Spam\StringProcessor\NativeStringProcessor::__construct
	 * @covers  \Hubzero\Spam\StringProcessor\NativeStringProcessor::prepare
	 * @return  void
	 **/
	public function testNativeStringProcessor()
	{
		// Test default preparation
		$text = " Curabitur foo @ blandit up......er tempus porttitor[dot]\nLorem ipsum dolor sit \tamet, consectetur & adipiscing elit.";

		$processor = new NativeStringProcessor();
		$result    = $processor->prepare($text);
		$expected  = "curabitur foo @ blandit up......er tempus porttitor[dot]lorem ipsum dolor sit amet, consectetur & adipiscing elit.";

		$this->assertEquals($result, $expected);

		// Test aggressive flag
		$processor = new NativeStringProcessor(array('aggressive' => true));
		$result    = $processor->prepare($text);
		$expected  = "curabiturfooatblanditup.ertempusporttitor.loremipsumdolorsitametconsecteturadipiscingelit.";

		$this->assertEquals($result, $expected);

		// Test ASCII conversion flag
		$text   = " Curabitur foo @ blandit ùp......er tempus porttitor[dot]\nLorem ipsum dölor sit \tamet, cönsectetur & adipiscing élit.";

		$processor = new NativeStringProcessor(array('ascii_conversion' => true));
		$result    = $processor->prepare($text);
		$expected  = "curabitur foo @ blandit up......er tempus porttitor[dot]lorem ipsum dolor sit amet, consectetur & adipiscing elit.";

		$this->assertEquals($result, $expected);
	}
}
