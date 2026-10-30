<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility\Tests;

use Hubzero\Test\Basic;
use Hubzero\Utility\Sanitize;

/**
 * Exposes the purifier's element whitelist without the application
 *
 * Sanitize::html() reads the request and the cache directory from the
 * application; the elements it adds are what these tests are about.
 */
class SanitizeWhitelist extends Sanitize
{
	/**
	 * Purify HTML with the elements Sanitize adds
	 *
	 * @param   string  $html
	 * @return  string
	 */
	public static function purify($html)
	{
		$config = \HTMLPurifier_Config::createDefault();
		$config->set('Cache.DefinitionImpl', null);

		$definition = $config->getHTMLDefinition(true);
		static::_addElementsToHtmlPurifierWhitelist($definition);
		static::_addAttributesToHtmlPurifierWhitelist($definition);

		return trim((new \HTMLPurifier($config))->purify($html));
	}
}

/**
 * Sanitize utility test
 */
class SanitizeTest extends Basic
{
	/**
	 * A video keeps its controls and its source
	 *
	 * @return  void
	 */
	public function testVideoKeepsControlsAndSource()
	{
		$html = SanitizeWhitelist::purify(
			'<video controls width="640"><source src="/site/media/a.mp4" type="video/mp4"></video>'
		);

		$this->assertStringContainsString('<video controls="controls" width="640">', $html);
		$this->assertStringContainsString('<source src="/site/media/a.mp4" type="video/mp4" />', $html);
	}

	/**
	 * A video written inside a paragraph stays there
	 *
	 * @return  void
	 */
	public function testVideoStaysInsideItsParagraph()
	{
		$html = SanitizeWhitelist::purify('<p>Intro <video controls src="/a.mp4"></video></p>');

		$this->assertSame('<p>Intro <video controls="controls" src="/a.mp4"></video></p>', $html);
	}

	/**
	 * Captions survive
	 *
	 * @return  void
	 */
	public function testTrackIsKept()
	{
		$html = SanitizeWhitelist::purify(
			'<video controls src="/a.mp4"><track kind="captions" src="/a.vtt" srclang="en" label="English" default></video>'
		);

		$this->assertStringContainsString(
			'<track kind="captions" src="/a.vtt" srclang="en" label="English" default="default" />',
			$html
		);
	}

	/**
	 * Media does not start by itself
	 *
	 * @return  void
	 */
	public function testAutoplayIsRemoved()
	{
		$html = SanitizeWhitelist::purify('<video autoplay controls src="/a.mp4"></video>');

		$this->assertStringNotContainsString('autoplay', $html);
		$this->assertStringContainsString('controls="controls"', $html);
	}

	/**
	 * Scripts get in through neither the addresses nor the handlers
	 *
	 * @return  void
	 */
	public function testScriptsAreStripped()
	{
		$html = SanitizeWhitelist::purify(
			'<video src="javascript:alert(1)" poster="javascript:alert(2)" onerror="alert(3)">'
			. '<source src="javascript:alert(4)"><track src="javascript:alert(5)"></video>'
		);

		$this->assertStringNotContainsString('javascript', $html);
		$this->assertStringNotContainsString('onerror', $html);
		$this->assertStringNotContainsString('alert', $html);
	}
}
