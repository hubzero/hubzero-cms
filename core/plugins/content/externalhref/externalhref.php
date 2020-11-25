<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;

/**
 * External HREF processor
 */
class plgContentExternalhref extends \Hubzero\Plugin\Plugin
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
		return $this->prepareLinks($context, $article);
	}

	/**
	 * Convert content to HTML
	 *
	 * @param   string  $context  The context of the content being passed to the plugin.
	 * @param   object  $article  The article object.  Note $article->text is also available
	 * @param   object  $params   The article params
	 * @param   int     $page     The 'page' number
	 * @return  void
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		return $this->prepareLinks($context, $article);
	}

	/**
	 * Prepare external links
	 *
	 * @param   string  $context  The context of the content being passed to the plugin.
	 * @param   object  $article  The article object.  Note $article->text is also available
	 * @return  void
	 */
	public function prepareLinks($context, &$article)
	{
		if ($context == 'com_content.article')
		{
			return;
		}

		$content = '';

		$key = $this->_key($context);

		if ($article instanceof Hubzero\Base\Obj
		 || $article instanceof Hubzero\Database\Relational)
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

		$mode   = $this->params->get('mode');
		$target = $this->params->get('target');
		$classes = array();

		if ($cls = $this->params->get('classes'))
		{
			$classes = explode(',', preg_replace('/\s*/', '', $cls));
			$classes = array_map('strtolower', $classes);
		}

		//$content = preg_replace_callback('/<a\s[^>]*href\s*=\s*(?:[\"\']??)(http[^\\1 >]*?)\\1[^>]*>(.*)<\/a>/uiUs', array(&$this, 'nofollow'), $content);

		$tags = array(
			'a'    => '/<a\s+([^>]*)>/i',
			'area' => '/<area\s+([^>]*)>/i'
		);
		foreach ($tags as $tag => $pattern)
		{
			$links = array();
			preg_match_all($pattern, $content, $links, PREG_SET_ORDER);

			foreach ($links as $link)
			{
				// Get attributes
				$pattern = "/(\w+)(\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?/i";
				$attribs = array();
				preg_match_all($pattern, $link[1], $attribs, PREG_SET_ORDER);

				$list = array();
				foreach ($attribs as $attrib)
				{
					if (!isset($attrib[2]))
					{
						// something wrong, may be js in email cloaking plugin
						continue;
					}

					$att = strtolower(trim($attrib[1]));
					$list[$att] = preg_replace("/=\s*[\"']?([^'\"]*)[\"']?/", "$1", $attrib[2]);
					$list[$att] = trim($list[$att]);
				}

				// Skip if non http link or anchor
				if (!isset($list['href']))
				{
					continue;
				}

				if (stripos($list['href'], 'http') !== 0)
				{
					continue;
				}

				$href = preg_replace("/https?:\/\//i", '', $list['href']);

				// Skip if internal link
				if (stripos($href, $_SERVER['SERVER_NAME']) === 0)
				{
					continue;
				}

				// Get classes
				if (!empty($list['class']))
				{
					$linkClasses = preg_split('/\s+/', $list['class']);
					$linkClasses = array_map('strtolower', $linkClasses);
				}
				else
				{
					$linkClasses = array();
				}

				if (array_intersect($linkClasses, $classes))
				{
					// Link classes are present in the ignored classes list
					continue;
				}

				if ($mode == 0 && !isset($list['rel']))
				{
					$list['rel'] = 'nofollow';
				}
				else if ($mode == 1)
				{
					$list['rel'] = 'nofollow';
				}
				else if ($mode == 2)
				{
					unset($list['rel']);
				}

				if ($target == 0 && !isset($list['target']))
				{
					$list['target'] = '_blank';
					$list['rel'] = (isset($list['rel']) ? $list['rel'] . ' ' : '') . 'noopener noreferrer';
				}
				else if ($target == 1)
				{
					$list['target'] = '_blank';
					$list['rel'] = (isset($list['rel']) ? $list['rel'] . ' ' : '') . 'noopener noreferrer';
				}
				else if ($target == 2)
				{
					$list['target'] = '_parent';
				}
				else if ($list['target'] == '_blank')
				{
					$list['rel'] = (isset($list['rel']) ? $list['rel'] . ' ' : '') . 'noopener noreferrer';
				}

				$ahref = "<$tag ";
				foreach ($list as $k => $v)
				{
					$ahref .= "{$k}=\"{$v}\" ";
				}
				$ahref .= '>';

				$content = str_replace($link[0], $ahref, $content);
			}
		}

		if ($article instanceof Hubzero\Base\Obj
		 || $article instanceof Hubzero\Database\Relational)
		{
			$article->set($key, $content);
		}
		else
		{
			$article->$key = $content;
		}
	}

	/**
	 * Create a link tag with specified attributes for external links
	 *
	 * @param   array   $matches
	 * @return  string
	 */
	private function nofollow($matches)
	{
		static $rel;

		if (!$rel)
		{
			$rel = array('external');
			if ($this->params->get('nofollow', 1))
			{
				$rel[] = 'nofollow';
			}
			if ($this->params->get('noreferrer', 0))
			{
				$rel[] = 'noreferrer';
			}
			if ($other = $this->params->get('other'))
			{
				$other = explode(' ', $other);
				$other = array_map('trim', $other);
				$rel = array_merge($rel, $other);
			}
			$rel = array_filter($rel);
			$rel = array_unique($rel);
			$rel = implode(' ', $rel);
		}

		return '<a href="' . $matches[1] . '" rel="' . $rel . '">' . $matches[2] . '</a>';
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
