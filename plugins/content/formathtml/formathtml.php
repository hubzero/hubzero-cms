<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die;

/**
 * HTML formatted Content Plugin
 */
class plgContentFormathtml extends JPlugin
{
	/**
	 * Finder before save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param   string The context of the content passed to the plugin
	 */
	public function onContentBeforeSave($context, &$article, $isNew)
	{
		if (!($article instanceof \Hubzero\Base\Object) || $context == 'com_content.article')
		{
			return;
		}

		$key = $this->_key($context);

		$content = ltrim($article->get($key));

		if (!$content) return;

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

		$article->set($key, $content);
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
		if (!($article instanceof \Hubzero\Base\Object) || $context == 'com_content.article')
		{
			return;
		}

		$key = $this->_key($context);

		$content = ltrim($article->get($key));

		if (!$content) return;

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
			include_once(__DIR__ . '/parser.php');

			$parser = new \Plugins\Content\Formathtml\Parser($params);

			$content = $parser->parse($content);
		}

		$article->set($key, $content);
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
