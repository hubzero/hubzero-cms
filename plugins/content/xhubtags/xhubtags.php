<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Content Plugin class for {xhub} tags
 */
class plgContentXhubtags extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Plugin that loads module positions within content
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int		The 'page' number
	 */
	public function onPrepareContent(&$article, &$params, $page = 0)
	{
		$context = '';
		return $this->onContentPrepare($context, $article, $params, $page);
	}

	/**
	 * Plugin that loads module positions within content
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int		The 'page' number
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		// simple performance check to determine whether bot should process further
		if (strpos($article->text, '{xhub') === false) 
		{
			return true;
		}

		// expression to search for
		$regex = "/\{xhub:\s*[^\}]*\}/i";

		// weblinks is somehow calling this with null params
		/*if (!is_object($params)) 
		{
			return false;
		}*/

		// check whether plugin has been unpublished
		/*if (!$params->get('enabled', 1)) 
		{
			$row->text = preg_replace($regex, '', $row->text);

			return true;
		}*/

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
	 * @param  string $options Tag options (e.g. 'component="support"')
	 * @return string
	 */
	private function _modules($options)
	{
		$regex = "/position\s*=\s*(\"|&quot;)([^\"]+)(\"|&quot;)/i";

		if (!preg_match($regex, $options, $position))
		{
			return '';
		}

		$regex = "/style\s*=\s*(\"|&quot;)([^\"]+)(\"|&quot;)/i";

		if (!preg_match($regex, $options, $style))
		{
			$style[2] = $this->params->def('style', 'none');
		}

		ximport('Hubzero_Module_Helper');

		return Hubzero_Module_Helper::renderModules($position[2], $style[2]);
	}

	/**
	 * {xhub:templatedir}
	 * 
	 * @return string Template path
	 */
	private function _templateDir()
	{
		$app = JFactory::getApplication();

		return '/templates/' . $app->getTemplate();
	}

	/**
	 * {xhub:include type="script" component="component" filename="filename"}
	 * {xhub:include type="stylesheet" component="component" filename="filename"}
	 * 
	 * @param  string $options Tag options (e.g. 'component="support"')
	 * @return string
	 */
	private function _include($options)
	{
		$app = JFactory::getApplication();

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

		$template = $app->getTemplate();

		if ($file[2][0] == '/')
		{
			$filename = $file[2];
		}
		else if (preg_match($regex, $options, $component)) 
		{
			$filename = 'templates/' . $template . '/html/' . $component[2] . '/' . $file[2];
			if (!file_exists(JPATH_SITE . DS . $filename))
			{
				$filename  = 'components/' . $component[2] . '/' . $file[2];
			}
			$filename = DS . $filename;
		}
		else
		{
			$filename = "/templates/$template/";
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

		$document = JFactory::getDocument();

		if (!file_exists(JPATH_SITE . $filename))
		{
			return '';
		}

		if ($type[2] == 'script')
		{
			$document->addScript(JURI::base(true) . '/' . ltrim($filename, '/') . '?v=' . filemtime(JPATH_SITE . $filename));
		}
		else if ($type[2] == 'stylesheet')
		{
			$document->addStyleSheet(JURI::base(true) . '/' . ltrim($filename, '/') . '?v=' . filemtime(JPATH_SITE . $filename), 'text/css', 'screen');
		}

		return '';
	}

	/**
	 * {xhub:image component="component" filename="filename"}
	 * 
	 * @param  string $options Tag options (e.g. 'component="support"')
	 * @return string
	 */
	private function _image($options)
	{
		$app = JFactory::getApplication();

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

		ximport('Hubzero_Document');

		if (empty($component) && empty($module))
		{
			return substr(Hubzero_Document::getHubImage($file[2]),1);
		}
		else if (!empty($component))
		{
			return substr(Hubzero_Document::getComponentImage($component[2], $file[2]), 1);
		}
		else if (!empty($module))
		{
			return substr(Hubzero_Dcoument::getModuleImage($module[2],$file[2]),1);
		}

		return '';
	}

	/**
	 * {xhub:getcfg variable}
	 * 
	 * @param  string $options Variable name
	 * @return string
	 */
	private function _getCfg($options)
	{
		$jconfig = JFactory::getConfig();

		$options = trim($options, " \n\t\r}");

		$sitename = $jconfig->getValue('config.sitename');
		$live_site = rtrim(JURI::base(),'/');

		if ($options == 'hubShortName') 
		{
			return $sitename;
		}
		else if ($options == 'hubShortURL') 
		{
			return $live_site;
		}

		return '';
	}
}
