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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		if (!($article instanceof \Hubzero\Base\Object) || $context == 'com_content.article')
		{
			return;
		}

		$content = '';

		$key = $this->_key($context);

		if ($article instanceof \Hubzero\Base\Object
		 || $article instanceof \Hubzero\Database\Relational)
		{
			$content = $article->get($key);
		}
		else if (isset($article->$key))
		{
			$content = $article->$key;
		}

		$content = ltrim($content);

		if (!$content)
		{
			return;
		}

		// Is there a format already applied?
		if (preg_match('/^<!-- \{FORMAT:(.*)\} -->/i', $content, $matches))
		{
			$format = strtolower(trim($matches[1]));
			if ($format != 'html')
			{
				return;
			}
		}
		// No format applied
		elseif (strstr($content, '</'))
		{
			// Force apply a format?
			if (!$this->params->get('applyFormat'))
			{
				return;
			}
		}

		if ($this->params->get('sanitizeBefore', 1))
		{
			$content = \Hubzero\Utility\Sanitize::clean($content);
			$content = \Hubzero\Utility\Sanitize::html($content);
		}

		if ($this->params->get('applyFormat'))
		{
			$content = preg_replace('/^(<!-- \{FORMAT:HTML\} -->)/i', '', $content);
			$content = '<!-- {FORMAT:HTML} -->' . $content;
		}

		if ($article instanceof \Hubzero\Base\Object
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
		//if (!($article instanceof \Hubzero\Base\Object) || $context == 'com_content.article')
		if ($context == 'com_content.article')
		{
			return;
		}

		$content = '';

		$key = $this->_key($context);

		if ($article instanceof \Hubzero\Base\Object
		 || $article instanceof \Hubzero\Database\Relational)
		{
			$content = $article->get($key);
		}
		else if (isset($article->$key))
		{
			$content = $article->$key;
		}

		$content = ltrim($content);

		if (!$content)
		{
			return;
		}

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

			include_once(__DIR__ . '/parser.php');

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

		if ($article instanceof \Hubzero\Base\Object
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
	 * @param   string $context A dot-notation string
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
