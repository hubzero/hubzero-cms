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
defined('_HZEXEC_') or die();

/**
 * Content Plugin class for OpenGraph meta tags
 *
 * Inspired by work from Jan Pavelka (www.phoca.cz)
 */
class plgContentOpenGraph extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior.
	 * If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Instance tracker
	 *
	 * @var  integer
	 */
	public $pluginNr = 0;

	/**
	 * Event after content has been displayed
	 *
	 * @param  string  $context  The context of the content being passed to the plugin.
	 * @param  object  $article  The article object. Note $article->text is also available
	 * @param  object  $params   The article params
	 * @param  int     $page     The 'page' number
	 */
	public function onContentAfterDisplay($context, &$article, &$params, $page=0)
	{
		if (!App::isSite())
		{
			return;
		}

		$view = Request::getCmd('view');// article, category, featured
		if ($view == 'featured' && $this->params->get('displayf', 1) == 0)
		{
			return;
		}
		if ($view == 'category' && $this->params->get('displayc', 1) == 0)
		{
			return;
		}

		if ((int)$this->pluginNr > 0)
		{
			// Second instance in featured view or category view 
			return;
		}

		// We need help variables as we cannot change the $article variable - such then will influence global settings
		$suffix    = '';
		$thisDesc  = $article->metadesc;
		$thisTitle = $article->title;

		if ($view == 'featured' && $this->pluginNr == 0)
		{
			// Data from first article will be set
			$suffix = 'f';
			$this->pluginNr = 1;
		}
		else if ($view == 'category' && $this->pluginNr == 0)
		{
			// Data from first article will be set
			$suffix = 'c';
			if (isset($article->catid) && (int)$article->catid > 0)
			{
				$db = App::get('db');
				$db->setQuery('SELECT c.metadesc, c.title FROM `#__categories` AS c WHERE c.id = ' . (int) $article->catid);
				$cItem = $db->loadObjectList();

				if (isset($cItem[0]->metadesc) && $cItem[0]->metadesc != '')
				{
					$thisDesc = $cItem[0]->metadesc;
				}
				if (isset($cItem[0]->title) && $cItem[0]->title != '')
				{
					$thisTitle = $cItem[0]->title;
				}
			}
			$this->pluginNr = 1;
		}

		// Title
		if ($title = $this->params->get('title' . $suffix, $thisTitle))
		{
			Document::setMetadata('og:title', htmlspecialchars($title));
		}

		// Type
		Document::setMetadata('og:type', $this->params->get('type' . $suffix, 'article'));

		// Image
		if ($img = $this->params->get('image' . $suffix, ''))
		{
			Document::setMetadata('og:image', Request::base(false) . htmlspecialchars($img));
		}
		else
		{
			// Try to find image in article
			$img = 0;
			$fulltext = '';
			if (isset($article->fulltext) && $article->fulltext != '')
			{
				$fulltext = $article->fulltext;
			}
			$introtext = '';
			if (isset($article->introtext) && $article->introtext != '')
			{
				$fulltext = $article->introtext;
			}
			$content = $introtext . $fulltext;

			preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $content, $src);
			if (isset($src[1]) && $src[1] != '')
			{
				Document::setMetadata('og:image', Request::base(false) . htmlspecialchars($src[1]));
				$img = 1;
			}

			// Try to find image in images/phocaopengraph folder
			if ($img == 0)
			{
				if (isset($article->id) && (int)$article->id > 0)
				{
					$imgPath = '';
					$path = PATH_APP . DS . 'site' . DS . 'media' . DS . 'images' . DS . 'opengraph' . DS;
					if (Filesystem::exists($path . DS . (int)$article->id . '.jpg'))
					{
						$imgPath = Request::base(false) . 'images/opengraph/' . (int)$article->id . '.jpg';
					}
					else if (Filesystem::exists($path . DS . (int)$article->id.'.png'))
					{
						$imgPath = Request::base(false) . 'images/opengraph/' . (int)$article->id . '.png';
					}
					else if (Filesystem::exists($path . DS . (int)$article->id . '.gif'))
					{
						$imgPath = Request::base(false) . 'images/opengraph/' . (int)$article->id . '.gif';
					}

					if ($imgPath != '')
					{
						Document::setMetadata('og:image', $imgPath);
					}
				}
			}
		}

		// URL
		if ($url = $this->params->get('url' . $suffix, Request::current()))
		{
			Document::setMetadata('og:url', htmlspecialchars($url));
		}

		// Site Name
		if ($sitename = $this->params->get('site_name' . $suffix, Config::get('sitename')))
		{
			Document::setMetadata('og:site_name', htmlspecialchars($sitename));
		}

		// Description
		if ($desc = $this->params->get('description' . $suffix, $thisDesc))
		{
			Document::setMetadata('og:description', htmlspecialchars($desc));
		}
		else if ($desc = Config::get('MetaDesc'))
		{
			Document::setMetadata('og:description', htmlspecialchars($desc));
		}

		// FB App ID - COMMON
		if ($app_id = $this->params->get('app_id', ''))
		{
			Document::setMetadata('fb:app_id', htmlspecialchars($app_id));
		}

		// Other
		if ($other = $this->params->get('other', ''))
		{
			$other = explode (';', $other);
			if (!empty($other))
			{
				foreach ($other as $v)
				{
					if ($v != '')
					{
						$vother = explode('=', $v);
						if (!empty($vother))
						{
							if (isset($vother[0]) && isset($vother[1]))
							{
								Document::setMetadata(htmlspecialchars(strip_tags($vother[0])), htmlspecialchars($vother[1]));
							}
						}
					}
				}
			}
		}
	}
}
