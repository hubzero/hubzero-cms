<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Tests;

use Hubzero\Test\Basic;
use Hubzero\Utility\Str;

/**
 * Str utility test
 */
class StrTest extends Basic
{
	/**
	 * Tests extracting key/value pairs out of a string with XML style attributes
	 *
	 * @covers  \Hubzero\Utility\Str::parseAttributes
	 * @return  void
	 **/
	public function testParseAttributes()
	{
		$strings = array(
			'a href="http://hubzero.org" title="HUBzero"' => array(
				'href'  => 'http://hubzero.org',
				'title' => 'HUBzero'
			),
			'<field description="Duis mollis, est non commodo luctus." default=0 height=55 width="35" type = "list">' => array(
				'description' => 'Duis mollis, est non commodo luctus.',
				//'default'     => '0',
				//'height'      => '55',
				'width'       => '35',
				'type'        => 'list'
			),
			'Sed posuere consectetur est at lobortis.' => array()
		);

		foreach ($strings as $string => $pairs)
		{
			$result = Str::parseAttributes($string);

			$this->assertTrue(is_array($result), 'Value returned was not an array');
			$this->assertEquals($result, $pairs);
		}
	}

	/**
	 * Tests converting a string to snake case
	 *
	 * @covers  \Hubzero\Utility\Str::snake
	 * @return  void
	 **/
	public function testSnake()
	{
		$start = 'this text is snake case';

		$result = Str::snake($start, '_');

		$this->assertEquals($result, 'this_text_is_snake_case');

		$result = Str::snake($start, '+');

		$this->assertEquals($result, 'this+text+is+snake+case');

		$result = Str::snake('thistextissnakecase');

		$this->assertEquals($result, 'thistextissnakecase');
	}

	/**
	 * Tests splitting a string in camel case format
	 *
	 * @covers  \Hubzero\Utility\Str::camel
	 * @return  void
	 **/
	public function testCamel()
	{
		$start = 'this text is camel case';
		$end   = 'ThisTextIsCamelCase';

		$result = Str::camel($start);

		$this->assertEquals($result, $end);

		$start = 'This-text_Is camelcase';
		$end   = 'ThisTextIsCamelcase';

		$result = Str::camel($start);

		$this->assertEquals($result, $end);
	}

	/**
	 * Tests splitting a string in camel case format
	 *
	 * @covers  \Hubzero\Utility\Str::splitCamel
	 * @return  void
	 **/
	public function testSplitCamel()
	{
		$start = 'ThisTextIsCamelCase';
		$end   = array('This', 'Text', 'Is', 'Camel', 'Case');

		$result = Str::splitCamel($start);

		$this->assertEquals($result, $end);

		$start = 'ThisTextisCamelCase';
		$end   = array('This', 'Textis', 'Camel', 'Case');

		$result = Str::splitCamel($start);

		$this->assertEquals($result, $end);
	}

	/**
	 * Tests if a given string contains a given substring.
	 *
	 * @covers  \Hubzero\Utility\Str::contains
	 * @return  void
	 **/
	public function testContains()
	{
		$string = 'Cras mattis consectetur purus sit amet fermentum.';

		$this->assertTrue(Str::contains($string, 'purus sit amet'), 'String does not contain given value.');

		$strings = array(
			'lorem ipsum',
			'mattis Cras',
			'mattis consectetur'
		);

		$this->assertTrue(Str::contains($string, $strings), 'String does not contain given value.');

		$this->assertFalse(Str::contains($string, 'felis euismod'), 'String does not contain given value.');

		$strings = array(
			'Donec id',
			'elit non mi',
			'porta gravida.'
		);

		$this->assertFalse(Str::contains($string, $strings), 'String does not contain given value.');
	}

	/**
	 * Tests if a given string starts with a given substring.
	 *
	 * @covers  \Hubzero\Utility\Str::startsWith
	 * @return  void
	 **/
	public function testStartsWith()
	{
		$string = 'Cras mattis consectetur purus sit amet fermentum.';

		$this->assertTrue(Str::startsWith($string, 'Cras mattis consectetur'), 'String does not start with given value.');

		$strings = array(
			'mattis consectetur',
			'Cras',
			'amet fermentum.'
		);

		$this->assertTrue(Str::startsWith($string, $strings), 'String does not start with given value.');

		$this->assertFalse(Str::startsWith($string, 'consectetur purus'), 'String does not start with given value.');

		$strings = array(
			'cras mattis',
			'consectetur purus',
			'amet fermentum.'
		);

		$this->assertFalse(Str::startsWith($string, $strings), 'String does not start with given value.');
	}

	/**
	 * Tests if a given string ends with a given substring.
	 *
	 * @covers  \Hubzero\Utility\Str::endsWith
	 * @return  void
	 **/
	public function testEndsWith()
	{
		$string = 'Cras mattis consectetur purus sit amet fermentum.';

		$this->assertTrue(Str::endsWith($string, 'sit amet fermentum.'), 'String does not end with given value.');

		$strings = array(
			'Cras mattis',
			'consectetur purus',
			'amet fermentum.'
		);

		$this->assertTrue(Str::endsWith($string, $strings), 'String does not end with given value.');

		$this->assertFalse(Str::endsWith($string, 'consectetur purus'), 'String does not end with given value.');

		$strings = array(
			'Cras mattis',
			'consectetur purus',
			'amet fermentum'
		);

		$this->assertFalse(Str::endsWith($string, $strings), 'String does not end with given value.');
	}

	/**
	 * Tests prefixing a string to a specificed length.
	 *
	 * @covers  \Hubzero\Utility\Str::pad
	 * @return  void
	 **/
	public function testPad()
	{
		$string = '5';

		$result = Str::pad($string, 4);

		$this->assertEquals(strlen($result), 4);
		$this->assertEquals(substr($result, 0, 3), '000');
		$this->assertEquals(substr($result, -1), $string);

		$string = '05';

		$result = Str::pad($string, 4);

		$this->assertEquals(strlen($result), 4);
		$this->assertEquals(substr($result, 0, 3), '000');
		$this->assertEquals(substr($result, -1), $string);

		$string = '12345';

		$result = Str::pad($string, 4);

		$this->assertEquals(strlen($result), 5);
		$this->assertEquals($result, $string);

		$string = -5;

		$result = Str::pad($string, 4);

		$this->assertEquals(strlen($result), 5);
		$this->assertEquals($result, 'n0005');
	}

	/**
	 * Tests prefixing a string to a specificed length.
	 *
	 * @covers  \Hubzero\Utility\Str::obfuscate
	 * @return  void
	 **/
	public function testObfuscate()
	{
		$string = 'test@example.com';

		$result = Str::obfuscate($string);

		$this->assertNotEquals($result, $string);

		preg_match('/&#/', $result, $matches);

		$this->assertTrue(count($matches) > 0);
	}

	/**
	 * Tests if a given string is truncated starting from the end.
	 *
	 * @covers  \Hubzero\Utility\Str::tail
	 * @return  void
	 **/
	public function testTail()
	{
		$string = 'Cras mattis consectetur purus sit amet fermentum.';
		$options = array();

		$result = Str::tail($string, 200, $options);

		$this->assertEquals($result, $string);

		$result = Str::tail($string, 25, $options);

		$this->assertEquals($result, '...us sit amet fermentum.');
		$this->assertEquals(strlen($result), 25);

		$options['ellipsis'] = '!!!';
		$result = Str::tail($string, 25, $options);

		$this->assertEquals($result, '!!!us sit amet fermentum.');

		$options['exact'] = false;
		$result = Str::tail($string, 25, $options);

		$this->assertEquals($result, '!!!sit amet fermentum.');
	}

	/**
	 * Tests if a given string is truncated starting from the end.
	 *
	 * @covers  \Hubzero\Utility\Str::insert
	 * @return  void
	 **/
	public function testInsert()
	{
		$result = Str::insert(':name is :age years old.', array('name' => 'Bob', 'age' => '65'));

		$this->assertEquals($result, 'Bob is 65 years old.');

		$result = Str::insert('*name is *age years old.', array('name' => 'Bob', 'age' => '65'), array('before' => '*'));

		$this->assertEquals($result, 'Bob is 65 years old.');

		$result = Str::insert('*name* is *age* years *old.', array('name' => 'Bob', 'age' => '65'), array('before' => '*', 'after' => '*'));

		$this->assertEquals($result, 'Bob is 65 years *old.');
	}

	/**
	 * Test cleanInsert
	 *
	 * @covers  \Hubzero\Utility\Str::cleanInsert
	 * @return  void
	 */
	public function testCleanInsert()
	{
		$result = Str::cleanInsert(':incomplete', [
			'clean'  => true,
			'before' => ':',
			'after'  => ''
		]);
		$this->assertEquals('', $result);

		$result = Str::cleanInsert(':incomplete', [
			'clean'  => [
				'method'      => 'text',
				'replacement' => 'complete'
			],
			'before' => ':',
			'after'  => ''
		]);
		$this->assertEquals('complete', $result);

		$result = Str::cleanInsert(':in.complete', [
			'clean'  => true,
			'before' => ':',
			'after'  => ''
		]);
		$this->assertEquals('', $result);

		$result = Str::cleanInsert(':in.complete and', [
			'clean'  => true,
			'before' => ':',
			'after'  => ''
		]);
		$this->assertEquals('', $result);

		$result = Str::cleanInsert(':in.complete or stuff', [
			'clean'  => true,
			'before' => ':',
			'after'  => ''
		]);
		$this->assertEquals('stuff', $result);

		$result = Str::cleanInsert('<p class=":missing" id=":missing">Text here</p>', [
			'clean'  => 'html',
			'before' => ':',
			'after'  => ''
		]);
		$this->assertEquals('<p>Text here</p>', $result);
	}

	/**
	 * Tests replacing &amp; with & for XHTML compliance
	 *
	 * @covers  \Hubzero\Utility\Str::ampReplace
	 * @return  void
	 **/
	public function testAmpReplace()
	{
		$result = Str::ampReplace('foo=bar&one=two');

		$this->assertEquals($result, 'foo=bar&amp;one=two');

		$result = Str::ampReplace('Cras mattis &#f0c2; consectetur & purus &amp; sit &&amp; amet &amp;amp; fermentum.');

		$this->assertEquals($result, 'Cras mattis &#f0c2; consectetur &amp; purus &amp; sit &&amp; amet &amp; fermentum.');
	}

	/**
	 * Tests truncating a block of text
	 *
	 * @covers  \Hubzero\Utility\Str::truncate
	 * @return  void
	 **/
	public function testTruncate()
	{
		$str = 'Cras mattis consectetur purus sit amet fermentum.';

		$result = Str::truncate($str, 30);

		$this->assertEquals($result, 'Cras mattis consectetur...');

		$result = Str::truncate($str, 30, array('ellipsis' => '!!!'));

		$this->assertEquals($result, 'Cras mattis consectetur!!!');

		$result = Str::truncate($str, 30, array('exact' => true));

		$this->assertEquals($result, 'Cras mattis consectetur pur...');
		$this->assertEquals(strlen($result), 30);

		$str = '<p>Cras <strong>mattis</strong> consectetur purus sit amet fermentum.</p>';

		$result = Str::truncate($str, 30, array('html' => true));

		$this->assertEquals($result, '<p>Cras <strong>mattis</strong> consectetur purus…</p>');

		$result = Str::truncate($str, 30, array('html' => true, 'exact' => true));

		$this->assertEquals($result, '<p>Cras <strong>mattis</strong> consectetur purus…</p>');
	}

	/**
	 * Tests extracting an excerpt from text
	 *
	 * @covers  \Hubzero\Utility\Str::excerpt
	 * @return  void
	 **/
	public function testExcerpt()
	{
		$str = 'Cras mattis consectetur purus sit amet fermentum.';

		$result = Str::excerpt($str, 'sit', 3);

		$this->assertEquals($result, '...us sit am...');

		$result = Str::excerpt($str, 'fermentum.', 3);

		$this->assertEquals($result, '...et fermentum.');

		$result = Str::excerpt($str, 'purus sit', 2, '!!!');

		$this->assertEquals($result, '!!!r purus sit a!!!');

		$str = 'Cras mattis consectetur purus sit amet fermentum.';

		$result = Str::excerpt($str, '', 10);

		$this->assertEquals($result, 'Cras mattis...');
	}

	/**
	 * Tests highlight() method.
	 *
	 * @covers  \Hubzero\Utility\Str::highlight
	 * @return  void
	 */
	public function testHighlight()
	{
		$text     = 'This is a test text';
		$phrases  = ['This', 'text'];
		$result   = Str::highlight($text, $phrases, ['format' => '<b>\1</b>']);
		$expected = '<b>This</b> is a test <b>text</b>';

		$this->assertEquals($expected, $result);

		$phrases  = ['is', 'text'];
		$result   = Str::highlight($text, $phrases, ['format' => '<b>\1</b>', 'regex' => "|\b%s\b|iu"]);
		$expected = 'This <b>is</b> a test <b>text</b>';

		$this->assertEquals($expected, $result);

		$text    = 'This is a test text';
		$phrases = null;
		$result  = Str::highlight($text, $phrases, ['format' => '<b>\1</b>']);

		$this->assertEquals($text, $result);

		$text    = 'This is a (test) text';
		$phrases = '(test';
		$result  = Str::highlight($text, $phrases, ['format' => '<b>\1</b>']);

		$this->assertEquals('This is a <b>(test</b>) text', $result);

		$text     = 'Ich saß in einem Café am Übergang';
		$expected = 'Ich <b>saß</b> in einem <b>Café</b> am <b>Übergang</b>';
		$phrases  = ['saß', 'café', 'übergang'];
		$result   = Str::highlight($text, $phrases, ['format' => '<b>\1</b>']);

		$this->assertEquals($expected, $result);

		// Test highlighting HTML
		$text1 = '<p>strongbow isn&rsquo;t real cider</p>';
		$text2 = '<p>strongbow <strong>isn&rsquo;t</strong> real cider</p>';
		$text3 = '<img src="what-a-strong-mouse.png" alt="What a strong mouse!" />';
		$text4 = 'What a strong mouse: <img src="what-a-strong-mouse.png" alt="What a strong mouse!" />';
		$options = [
			'format' => '<b>\1</b>',
			'html'   => true
		];
		$expected = '<p><b>strong</b>bow isn&rsquo;t real cider</p>';

		$this->assertEquals($expected, Str::highlight($text1, 'strong', $options));

		$expected = '<p><b>strong</b>bow <strong>isn&rsquo;t</strong> real cider</p>';

		$this->assertEquals($expected, Str::highlight($text2, 'strong', $options));
		$this->assertEquals($text3, Str::highlight($text3, 'strong', $options));
		$this->assertEquals($text3, Str::highlight($text3, ['strong', 'what'], $options));

		$expected = '<b>What</b> a <b>strong</b> mouse: <img src="what-a-strong-mouse.png" alt="What a strong mouse!" />';

		$this->assertEquals($expected, Str::highlight($text4, ['strong', 'what'], $options));
	}
}
