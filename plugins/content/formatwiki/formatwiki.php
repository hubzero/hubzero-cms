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
 * Wiki formatted Content Plugin
 */
class plgContentFormatwiki extends JPlugin
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
		if (!($article instanceof \Hubzero\Base\Object) || $context == 'com_content.article')
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
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('wiki');

			if (!isset($params['fullparse']))
			{
				$params['fullparse'] = true;
			}

			if ($this->params->get('convertFormat'))
			{
				$params['macros'] = false;
			}

			// Trigger the onFinderBeforeSave event.
			$results = $dispatcher->trigger('onWikiParseText', array($content, $params, $params['fullparse'], true));
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
