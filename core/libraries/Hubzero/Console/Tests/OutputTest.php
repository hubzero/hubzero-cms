<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Tests;

use Hubzero\Test\Basic;
use Hubzero\Console\Output;
use \Mockery as m;

/**
 * Base relational model tests
 */
class OutputTest extends Basic
{
	/**
	 * Gets the buffered output for comparison
	 *
	 * @param   closure  $function  The action to perform
	 * @return  string
	 **/
	private function getBuffered($function)
	{
		ob_start();

		call_user_func($function);

		$contents = ob_get_contents();

		ob_end_clean();

		return addcslashes($contents, "\0..\37");
	}

	/**
	 * Tests to make sure we can output a single line
	 *
	 * @return  void
	 **/
	public function testOutputSingleLine()
	{
		$output = new Output;

		$string = $this->getBuffered(function () use ($output) {
			$output->addLine('Hello, friend');
		});

		$this->assertEquals('\033[0mHello, friend\033[0m\n', $string, 'Output did not have the expected string with trailing new line');
	}

	/**
	 * Tests to make sure we can output a string
	 *
	 * @return  void
	 **/
	public function testOutputString()
	{
		$output = new Output;

		$string = $this->getBuffered(function () use ($output) {
			$output->addString('Hello, friend');
		});

		$this->assertEquals('\033[0mHello, friend\033[0m', $string, 'Output did not have the expected string without trailing new line');
	}

	/**
	 * Tests to make sure string is not automatically rendered in non-interactive mode
	 *
	 * @return  void
	 **/
	public function testOutputNonInteractiveDoesNotAutmaticallyRender()
	{
		$output = new Output;
		$output->makeNonInteractive();

		$string = $this->getBuffered(function () use ($output) {
			$output->addLine('Hello, friend');
		});

		$this->assertEquals('', $string, 'Output did not have the expected empty string');
	}

	/**
	 * Tests to make sure we can output a paragraph, properly limited in line length
	 *
	 * @return  void
	 **/
	public function testOutputParagraph()
	{
		$output = new Output;

		$actual = $this->getBuffered(function () use ($output) {
			$paragraph  = 'PBR cred distillery, meggings farm-to-table craft beer pop-up before they sold out health goth.';
			$paragraph .= ' Crucifix drinking vinegar polaroid tote bag before they sold out, flexitarian plaid taxidermy.';
			$paragraph .= ' 90\'s cold-pressed pour-over pug asymmetrical small batch. Roof party freegan ennui single-ori';
			$paragraph .= 'gin coffee, Thundercats trust fund PBR&B flexitarian seitan kitsch bespoke taxidermy Pitchfork ';
			$paragraph .= 'fixie kogi. Church-key typewriter readymade, Portland 8-bit whatever sriracha tofu blog DIY Aus';
			$paragraph .= 'tin. Street art twee salvia, cray McSweeney\'s put a bird on it trust fund ethical bicycle righ';
			$paragraph .= 'ts pop-up narwhal umami cronut tilde PBR&B. Selfies banjo VHS cardigan farm-to-table.';
			$output->addParagraph($paragraph);
		});

		$expected  = '\033[0mPBR cred distillery, meggings farm-to-table craft beer pop-up before they\033[0m\n';
		$expected .= '\033[0msold out health goth. Crucifix drinking vinegar polaroid tote bag before\033[0m\n';
		$expected .= '\033[0mthey sold out, flexitarian plaid taxidermy. 90\'s cold-pressed pour-over\033[0m\n';
		$expected .= '\033[0mpug asymmetrical small batch. Roof party freegan ennui single-origin coffee,\033[0m\n';
		$expected .= '\033[0mThundercats trust fund PBR&B flexitarian seitan kitsch bespoke taxidermy\033[0m\n';
		$expected .= '\033[0mPitchfork fixie kogi. Church-key typewriter readymade, Portland 8-bit whatever\033[0m\n';
		$expected .= '\033[0msriracha tofu blog DIY Austin. Street art twee salvia, cray McSweeney\'s\033[0m\n';
		$expected .= '\033[0mput a bird on it trust fund ethical bicycle rights pop-up narwhal umami\033[0m\n';
		$expected .= '\033[0mcronut tilde PBR&B. Selfies banjo VHS cardigan farm-to-table.\033[0m\n';

		$this->assertEquals($expected, $actual, 'Output did not have the expected string in appropriate paragraph format');
	}

	/**
	 * Tests to make sure we can output a cool table
	 *
	 * @return  void
	 **/
	public function testOutputTable()
	{
		$output = new Output;

		$actual = $this->getBuffered(function () use ($output) {
			$table   = [];
			$table[] = ['John', 'Football'];
			$table[] = ['Stephen', 'Soccer'];
			$table[] = ['Ben', 'Baseball'];
			$output->addTable($table);
		});

		$expected  = '\033[0m/--------------------\\\033[0m\n';
		$expected .= '\033[0m| \033[0m\033[0mJohn\033[0m\033[0m    \033[0m\033[0m| \033[0m\033[0mFootball\033[0m\033[0m \033[0m\033[0m|\033[0m\n';
		$expected .= '\033[0m| \033[0m\033[0mStephen\033[0m\033[0m \033[0m\033[0m| \033[0m\033[0mSoccer\033[0m\033[0m   \033[0m\033[0m|\033[0m\n';
		$expected .= '\033[0m| \033[0m\033[0mBen\033[0m\033[0m     \033[0m\033[0m| \033[0m\033[0mBaseball\033[0m\033[0m \033[0m\033[0m|\033[0m\n';
		$expected .= '\033[0m\--------------------/\033[0m\n';

		$this->assertEquals($expected, $actual, 'Output did not have the expected table');
	}
}
