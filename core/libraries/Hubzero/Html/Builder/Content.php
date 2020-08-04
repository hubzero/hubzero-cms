<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Builder;

use Hubzero\Base\Obj;

/**
 * Utility class to fire onContentPrepare for non-article based content.
 */
class Content
{
	/**
	 * Fire onContentPrepare for content that isn't part of an article.
	 *
	 * @param   string  $text     The content to be transformed.
	 * @param   array   $params   The content params.
	 * @param   string  $context  The context of the content to be transformed.
	 * @return  string  The content after transformation.
	 */
	public static function prepare($text, $params = null, $context = 'text')
	{
		if ($params === null)
		{
			$params = new Obj;
		}

		$article = new \stdClass;
		$article->text = $text;

		\App::get('dispatcher')->trigger('content.onContentPrepare', array($context, &$article, &$params, 0));

		return $article->text;
	}
}
