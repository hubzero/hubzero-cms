<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die;

/**
 * HTML formatted Content Plugin
 */
class plgContentFormathtml extends \Hubzero\Plugin\Plugin
{
	/**
	 * Before save content method
	 *
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param   string  $context  The context of the content being passed to the plugin.
	 * @param   object  $article  The article object.  Note $article->text is also available
	 * @param   bool    $isNew
	 * @return  void
	 */
	public function onContentBeforeSave($context, &$article, $isNew)
	{
		if (!($article instanceof \Hubzero\Base\Obj) || $context == 'com_content.article')
		{
			return;
		}

		$content = '';

		$key = $this->_key($context);

		if ($article instanceof \Hubzero\Base\Obj
		 || $article instanceof \Hubzero\Database\Relational)
		{
			$content = $article->get($key);
		}
		else if (isset($article->$key))
		{
			$content = $article->$key;
		}

		$content = ltrim($content == null ? '' : $content);

		if (!$content)
		{
			return;
		}

		// Is there a format already applied?
		$hadHtmlMarker = false;
		if (preg_match('/^<!-- \{FORMAT:(.*?)\} -->/i', $content, $matches))
		{
			$format = strtolower(trim($matches[1]));

			// Wiki markup is owned (and escaped) by the formatwiki plugin
			if ($format == 'wiki' && \Plugin::isEnabled('content', 'formatwiki'))
			{
				return;
			}

			// An HTML marker is restored below. Any other marker is dropped and the
			// content handled as HTML, so a crafted marker cannot skip sanitisation.
			$hadHtmlMarker = ($format == 'html');
			$content = substr($content, strlen($matches[0]));
		}

		if ($this->params->get('sanitizeBefore', 1))
		{
			$content = \Hubzero\Utility\Sanitize::clean($content);
			$content = \Hubzero\Utility\Sanitize::html($content);
		}

		if ($this->params->get('applyFormat') || $hadHtmlMarker)
		{
			$content = '<!-- {FORMAT:HTML} -->' . $content;
		}

		if ($article instanceof \Hubzero\Base\Obj
		 || $article instanceof \Hubzero\Database\Relational)
		{
			$article->set($key, $content);
		}
		else
		{
			$article->$key = $content;
		}
	}

	/**
	 * Convert content to HTML
	 *
	 * @param  string  $context  The context of the content being passed to the plugin.
	 * @param  object  $article  The article object.  Note $article->text is also available
	 * @param  object  $params   The article params
	 * @param  int     $page     The 'page' number
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		//if (!($article instanceof \Hubzero\Base\Obj) || $context == 'com_content.article')
		if ($context == 'com_content.article')
		{
			return;
		}

		$content = '';

		$key = $this->_key($context);

		if ($article instanceof \Hubzero\Base\Obj
		 || $article instanceof \Hubzero\Database\Relational)
		{
			$content = $article->get($key);
		}
		else if (isset($article->$key))
		{
			$content = $article->$key;
		}

		$content = ltrim($content ?: "");

		if (!$content)
		{
			return;
		}

		// Neutralise dangerous HTML at render time too (content can reach the DB
		// without passing onContentBeforeSave). Only the purifier is used here: it is
		// idempotent, whereas Sanitize::clean() re-encodes entities on every pass.
		if ($this->params->get('sanitizeBefore', 1))
		{
			$formatMarker = '';
			if (preg_match('/^(<!-- \{FORMAT:[^}]*\} -->)/i', $content, $fm))
			{
				$formatMarker = $fm[1];
			}

			// {FORMAT:RENDERED} is the transient marker formatwiki puts on its
			// own parser output for this request. That is rendered HTML, not
			// stored markup, and purifying it costs a visible regression on
			// every wiki page: measured against the shipped configuration it
			// drops allowfullscreen from video embeds, deletes empty layout
			// <div>s outright via AutoFormat.RemoveEmpty, strips every data-*
			// attribute and rewrites rel="external", which the externalhref
			// plugin later keys on.
			//
			// Everything else still goes through, and unmarked content is the
			// case that matters: none of the blog entries on a stock hub carry
			// a marker at all, so exempting unmarked content here would reopen
			// the stored XSS that HZ-2026-0012 covers.
			//
			// The marker is only text, though, and text can be stored: a blog
			// body that begins with it would have skipped the purifier. So it
			// is honoured only when formatwiki registered this exact content
			// as its own output in this request; otherwise it is a user's
			// bytes, stripped, and the content is purified like any other.
			// Any other marker (HTML or a foreign format) is stored markup too.
			$isRendered = (stripos($formatMarker, '{FORMAT:RENDERED}') !== false);
			$trusted    = $isRendered
				&& class_exists('plgContentFormatwiki', false)
				&& \plgContentFormatwiki::wasRendered($content);

			if (!$trusted)
			{
				$content = substr($content, strlen($formatMarker));
				if ($isRendered)
				{
					$formatMarker = '';
				}
				$content = \Hubzero\Utility\Sanitize::html($content);
				$content = $formatMarker . $content;
			}
		}

		// Drop formatwiki's transient marker before the format test below, which
		// would otherwise read it as a foreign format and return -- losing the
		// asset-path rewriting and macro parsing that follow.
		$content = preg_replace('/^<!-- \{FORMAT:RENDERED\} -->/i', '', $content);

		// Is there a format already applied?
		if (preg_match('/^<!-- \{FORMAT:(.*)\} -->/i', $content, $matches))
		{
			// Is the format we want?
			$format = strtolower(trim($matches[1]));
			if ($format != 'html')
			{
				// Not HTML. Do nothing.
				return;
			}
		}

		$content = preg_replace('/^(<!-- \{FORMAT:HTML\} -->)/i', '', $content);

		if (trim($content))
		{
			// Fix asset paths
			$content = str_replace('src="/media/system/', 'src="/core/assets/', $content);
			$content = str_replace('src="/site', 'src="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $content);
			$content = str_replace("src='/site", "src='" . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $content);
			$content = str_replace('href="/media/system/', 'href="/core/assets/', $content);
			$content = str_replace('href="/site', 'href="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $content);
			$content = str_replace("href='/site", "href='" . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $content);

			include_once __DIR__ . '/parser.php';

			if ($this->params->get('unlink', 0))
			{
				$content = preg_replace_callback('/<a.*(?=href="([^"]*)")[^>]*>([^<]*)<\/a>/uiUs', array(&$this, 'delink'), $content);
			}

			$parser = new \Plugins\Content\Formathtml\Parser($params);

			if ($path = $this->params->get('macropath'))
			{
				$parser->addMacroPath($path);
			}

			$content = $parser->parse($content);
		}

		if ($article instanceof \Hubzero\Base\Obj
		 || $article instanceof \Hubzero\Database\Relational)
		{
			$article->set($key, $content);
		}
		else
		{
			$article->$key = $content;
		}
	}

	/**
	 * Replace links with text
	 *
	 * @param   array   $matches
	 * @return  string
	 */
	private function delink($matches)
	{
		if ($matches[1] == $matches[2])
		{
			return trim($matches[1]);
		}

		return trim($matches[2]) . ' (' . trim($matches[1]) . ')';
	}

	/**
	 * Check if the context provided the content field name as
	 * it may vary between models.
	 *
	 * @param   string  $context  A dot-notation string
	 * @return  string
	 */
	private function _key($context)
	{
		$key = 'content';

		if (!$context)
		{
			return $key;
		}

		$key = $context;

		if (strstr($context, '.'))
		{
			$parts = explode('.', $context);

			if (isset($parts[2]))
			{
				$key = $parts[2];
			}
		}

		return $key;
	}
}
