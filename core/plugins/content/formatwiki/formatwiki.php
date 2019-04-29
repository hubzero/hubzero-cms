<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die;

/**
 * Wiki formatted Content Plugin
 */
class plgContentFormatwiki extends \Hubzero\Plugin\Plugin
{
	/**
	 * Finder before save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param  string  $content The context of the content passed to the plugin
	 * @param  object  $article
	 * @param  boolean $isNew Unnecessary?
	 * @return multi
	 */
	public function onContentBeforeSave($context, &$article, $isNew)
	{
		if (!($article instanceof \Hubzero\Base\Obj) || $context == 'com_content.article')
		{
			return;
		}

		$key = $this->_key($context);

		$content = $article->get($key);

		// The content already has a format assigned to it so do nothing
		if (preg_match('/^<!-- \{FORMAT:(.*)\} -->/i', $content, $matches))
		{
			return;
		}

		if (!strstr($content, '</'))
		{

			if ($this->params->get('applyFormat'))
			{
				$content = '<!-- {FORMAT:WIKI} -->' . $content;

				$article->set($key, $content);
			}
		}
	}

	/**
	 * Convert content to HTML
	 *
	 * @param  string $context The context of the content being passed to the plugin.
	 * @param  object $article The article object.  Note $article->text is also available
	 * @param  object $params  The article params
	 * @param  int    $page    The 'page' number
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		if (!($article instanceof \Hubzero\Base\Obj) || $context == 'com_content.article')
		{
			return;
		}

		$key = $this->_key($context);

		$content = $article->get($key);

		// Is there a format already applied?
		if (preg_match('/^<!-- \{FORMAT:(.*)\} -->/i', $content, $matches))
		{
			// Is the format wiki?
			$format = strtolower(trim($matches[1]));
			if ($format != 'wiki')
			{
				// Not wiki. Do nothing.
				return;
			}
		}
		// No format applied
		else
		{
			// Force apply a format?
			if ($this->params->get('applyFormat') && $this->_isWiki($content))
			{
				// Are we converting the format?
				// Only apply the wiki format if not. Saves us an extra DB call.
				if (!$this->params->get('convertFormat') && $article instanceof \Hubzero\Base\Model)
				{
					$content = '<!-- {FORMAT:WIKI} -->' . $content;
					$article->set($key, $content);
					$article->store(false);
				}
			}
			else
			{
				return;
			}
		}

		$content = preg_replace('/^(<!-- \{FORMAT:WIKI\} -->)/i', '', $content);

		if (trim($content) && $this->_isWiki($content))
		{
			if (!isset($params['fullparse']))
			{
				$params['fullparse'] = true;
			}

			if ($this->params->get('convertFormat'))
			{
				$params['macros'] = false;
			}

			// Trigger the onFinderBeforeSave event.
			$results = Event::trigger('wiki.onWikiParseText', array($content, $params, $params['fullparse'], true));
			$content = implode('', $results);
		}

		if ($this->params->get('convertFormat') && $article instanceof \Hubzero\Base\Model)
		{
			$content = '<!-- {FORMAT:HTML} -->' . $content;
			$article->set($key, $content);
			$article->store(false);
		}

		$article->set($key, $content);
	}

	/**
	 * Try to determine if a string is wiki syntax
	 *
	 * @param   string $content Content to check
	 * @return  string
	 */
	private function _isWiki($content)
	{
		$content = trim($content);

		// First, remove <pre> tags
		//   This is in case the content is HTML but contains a block of
		//   sample wiki markup.
		$content = preg_replace('/<pre>(.*?)<\/pre>/i', '', $content);

		// If wiki <pre> syntax is found
		if ((strstr($content, '{{{') && strstr($content, '}}}')) || strstr($content, '#!html'))
		{
			return true;
		}

		// If wiki bold syntax is found (highly unlikely HTML content will contain this string)
		if (preg_match('/\'\'\'(.*?)\'\'\'/i', $content) || preg_match('/===(.*?)===/i', $content))
		{
			return true;
		}

		// If no HTML tags found ...
		if (!preg_match('/^(<([a-z]+)[^>]*>.+<\/([a-z]+)[^>]*>|<(\?|%|([a-z]+)[^>]*).*(\?|%|)>)/is', $content))
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if the context provided the content field name as
	 * it may vary between models.
	 *
	 * @param   string $context A dot-notation string
	 * @return  string
	 */
	private function _key($context)
	{
		$parts = explode('.', $context);
		$key = 'content';
		if (isset($parts[2]))
		{
			$key = $parts[2];
		}
		return $key;
	}
}
