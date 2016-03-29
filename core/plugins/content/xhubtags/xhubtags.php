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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Content Plugin class for {xhub} tags
 */
class plgContentXhubtags extends \Hubzero\Plugin\Plugin
{
	/**
	 * Plugin that loads module positions within content
	 *
	 * @param   string   $context  The context of the content being passed to the plugin.
	 * @param   object   $article  The article object.  Note $article->text is also available
	 * @param   object   $params   The article params
	 * @param   integer  $page     The 'page' number
	 * @return  void
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		if (($article instanceof \Hubzero\Base\Object) || !in_array($context, ['com_content.article', 'text']))
		{
			return;
		}

		// Fix asset paths
		$article->text = str_replace('src="/media/system/', 'src="/core/assets/', $article->text);
		$article->text = str_replace('src="/site', 'src="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $article->text);
		$article->text = str_replace("src='/site", "src='" . substr(PATH_APP, strlen(PATH_ROOT)) . "/site", $article->text);

		// simple performance check to determine whether bot should process further
		if (strpos($article->text, '{xhub') === false)
		{
			return true;
		}

		// expression to search for
		$regex = "/\{xhub:\s*[^\}]*\}/i";

		// find all instances of plugin and put in $matches
		preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);

		if ($matches)
		{
			foreach ($matches as $match)
			{
				$regex = "/\{xhub:\s*([^\s]+)\s*(.*)/i";
				if (preg_match($regex, $match[0], $tag))
				{
					switch (strtolower(trim($tag[1])))
					{
						case 'include':
							$text = $this->_include($tag[2]);
						break;

						case 'image':
							$text = $this->_image($tag[2]);
						break;

						case 'module':
							$text = $this->_modules($tag[2]);
						break;

						case 'templatedir':
							$text = $this->_templateDir($tag[2]);
						break;

						case 'getcfg':
							$text = $this->_getCfg($tag[2]);
						break;

						default:
							$text = '';
						break;
					}

					$article->text = str_replace($match[0], $text, $article->text);
				}
			}
		}
	}

	/**
	 * {xhub:module position="position" style="style"}
	 * Renders a module from an {xhub} tag
	 *
	 * @param   string  $options  Tag options (e.g. 'component="support"')
	 * @return  string
	 */
	private function _modules($options)
	{
		$regex = "/position\s*=\s*(\"|&quot;)([^\"]+)(\"|&quot;)/i";

		if (!preg_match($regex, $options, $position))
		{
			return '';
		}

		$attribs = array('style' => $this->params->get('style', 'none'));

		$regex = "/style\s*=\s*(\"|&quot;)([^\"]+)(\"|&quot;)/i";

		if (preg_match($regex, $options, $style))
		{
			$attribs['style'] = $style[2];

			if ($attribs['style'] == -1 || $attribs['style'] == '-1')
			{
				$attribs['style'] = 'none';
			}
			if ($attribs['style'] == -2 || $attribs['style'] == '-2')
			{
				$attribs['style'] = 'xhtml';
			}
		}

		$regex = "/params\s*=\s*(\"|&quot;)([^\"]+)(\"|&quot;)/i";

		if (preg_match($regex, $options, $params))
		{
			$attribs['params'] = $params[2];
		}

		return \Module::position($position[2], $attribs);
	}

	/**
	 * {xhub:templatedir}
	 *
	 * @return  string  Template path
	 */
	private function _templateDir()
	{
		return substr(App::get('template')->path, strlen(PATH_ROOT));
	}

	/**
	 * {xhub:include type="script" component="component" filename="filename"}
	 * {xhub:include type="stylesheet" component="component" filename="filename"}
	 *
	 * @param   string  $options  Tag options (e.g. 'component="support"')
	 * @return  string
	 */
	private function _include($options)
	{
		$regex = "/type\s*=\s*(\"|&quot;)(script|stylesheet)(\"|&quot;)/i";

		if (!preg_match($regex, $options, $type))
		{
			return '';
		}

		$regex = "/filename\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";

		if (!preg_match($regex, $options, $file))
		{
			return '';
		}

		$regex = "/component\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";

		$template = App::get('template')->template;

		if (substr($file[2], 0, strlen('http')) == 'http'
		 || substr($file[2], 0, strlen('://')) == '://'
		 || substr($file[2], 0, strlen('//')) == '//')
		{
			if ($type[2] == 'script')
			{
				Document::addScript($file[2]);
			}
			else if ($type[2] == 'stylesheet')
			{
				Document::addStyleSheet($file[2], 'text/css', 'screen');
			}

			return '';
		}

		if ($file[2][0] == '/')
		{
			$filename = $file[2];
		}
		else if (preg_match($regex, $options, $component))
		{
			$filename = $this->_templateDir(). '/html/' . $component[2] . '/' . $file[2]; //'templates/' . $template
			if (!file_exists(PATH_ROOT . $filename))
			{
				$filename = substr(Component::path($component[2]), strlen(PATH_ROOT)) . '/' . $file[2];
			}
		}
		else
		{
			$filename = $this->_templateDir() . '/'; //"/templates/$template/";
			if ($type[2] == 'script')
			{
				$filename .= 'js/';
			}
			else
			{
				$filename .= 'css/';
			}
			$filename .= $file[2];
		}

		if (!file_exists(PATH_ROOT . $filename))
		{
			return '';
		}

		if ($type[2] == 'script')
		{
			Document::addScript(Request::base(true) . '/' . ltrim($filename, '/') . '?v=' . filemtime(PATH_ROOT . $filename));
		}
		else if ($type[2] == 'stylesheet')
		{
			Document::addStyleSheet(Request::base(true) . '/' . ltrim($filename, '/') . '?v=' . filemtime(PATH_ROOT . $filename), 'text/css', 'screen');
		}

		return '';
	}

	/**
	 * {xhub:image component="component" filename="filename"}
	 *
	 * @param   string  $options  Tag options (e.g. 'component="support"')
	 * @return  string
	 */
	private function _image($options)
	{
		$regex = "/filename\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";

		if (!preg_match($regex, $options, $file))
		{
			return '';
		}

		$regex = "/component\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";

		if (!preg_match($regex, $options, $component))
		{
			$regex = "/module\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";

			preg_match($regex, $options, $module);
		}

		if (empty($component) && empty($module))
		{
			return ''; //substr(\Hubzero\Document\Assets::getHubImage($file[2]),1);
		}
		else if (!empty($component))
		{
			return substr(\Hubzero\Document\Assets::getComponentImage($component[2], $file[2]), 1);
		}
		else if (!empty($module))
		{
			return substr(\Hubzero\Document\Assets::getModuleImage($module[2],$file[2]),1);
		}

		return '';
	}

	/**
	 * {xhub:getcfg variable}
	 *
	 * @param   string  $options  Variable name
	 * @return  string
	 */
	private function _getCfg($options)
	{
		$options = trim($options, " \n\t\r}");

		$sitename = Config::get('sitename');
		$live_site = rtrim(Request::base(),'/');

		if ($options == 'hubShortName')
		{
			return $sitename;
		}
		else if ($options == 'hubShortURL')
		{
			return $live_site;
		}
		else if ($options == 'hubHostname')
		{
			return Request::getHost();
		}

		return '';
	}
}
