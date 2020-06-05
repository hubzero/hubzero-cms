<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin for converting support comments from MarkDown to HTML
 */
class plgSupportMarkdown extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Holds the parser for re-use
	 *
	 * @var  object
	 */
	public static $parser = null;

	/**
	 * Turns MarkDown to HTML
	 *
	 * @param   string  $context
	 * @param   object  $comment
	 * @param   string  $text
	 * @return  void
	 */
	public function onCommentPrepare($context, &$comment)
	{
		if ($context != 'com_support.comment')
		{
			return;
		}

		$text = $comment->get('comment');

		if (!$text)
		{
			return;
		}

		if (!self::$parser)
		{
			include_once __DIR__ . '/markdown/block/CodeTrait.php';
			include_once __DIR__ . '/markdown/block/FencedCodeTrait.php';
			include_once __DIR__ . '/markdown/block/HeadlineTrait.php';
			include_once __DIR__ . '/markdown/block/HtmlTrait.php';
			include_once __DIR__ . '/markdown/block/ListTrait.php';
			include_once __DIR__ . '/markdown/block/QuoteTrait.php';
			include_once __DIR__ . '/markdown/block/RuleTrait.php';
			include_once __DIR__ . '/markdown/block/TableTrait.php';
			include_once __DIR__ . '/markdown/inline/CodeTrait.php';
			include_once __DIR__ . '/markdown/inline/EmphStrongTrait.php';
			include_once __DIR__ . '/markdown/inline/LinkTrait.php';
			include_once __DIR__ . '/markdown/inline/StrikeoutTrait.php';
			include_once __DIR__ . '/markdown/inline/UrlLinkTrait.php';
			include_once __DIR__ . '/markdown/Parser.php';
			include_once __DIR__ . '/markdown/Markdown.php';
			include_once __DIR__ . '/markdown/MarkdownExtra.php';
			include_once __DIR__ . '/markdown/GithubMarkdown.php';

			$cls = '\\cebe\\markdown\\' . $this->params->get('type', 'Markdown');

			self::$parser = new $cls();
			self::$parser->html5 = true;
			self::$parser->keepListStartNumber = true;
			//self::$parser->enableNewlines = true;
		}

		$text = preg_replace("/<br\s?\/>/i", '', $text);
		$text = rtrim($text);

		$result = self::$parser->parse($text);

		if ($result)
		{
			$text = $result;
		}

		$comment->set('comment', $text);

		// We only pass back so that the triggerer knows we did something
		return $text;
	}
}
