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

// no direct access
defined('_HZEXEC_') or die;

/**
 * Plugin that loads module positions within content
 */
class plgContentLoadmodule extends \Hubzero\Plugin\Plugin
{
	/**
	 * List of modules by position
	 */
	protected static $modules = array();

	/**
	 * List of modules by name
	 */
	protected static $mods = array();

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
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer' || $article instanceof \Hubzero\Base\Object)
		{
			return true;
		}

		// simple performance check to determine whether bot should process further
		if (isset($article->text) && strpos($article->text, 'loadposition') === false && strpos($article->text, 'loadmodule') === false)
		{
			return true;
		}
		elseif (!isset($article->text) && isset($article->introtext))
		{
			// for terms of service
			$article->text = $article->introtext;
		}

		// expression to search for (positions)
		$regex = '/{loadposition\s+(.*?)}/i';
		$style = $this->params->def('style', 'none');
		$title = null;

		// Find all instances of plugin and put in $matches for loadposition
		// $matches[0] is full pattern match, $matches[1] is the position
		preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);

		// No matches, skip this
		if ($matches)
		{
			foreach ($matches as $match)
			{
				$matcheslist = explode(',', $match[1]);

				// We may not have a module style so fall back to the plugin default.
				if (!array_key_exists(1, $matcheslist))
				{
					$matcheslist[1] = $style;
				}

				$position = trim($matcheslist[0]);
				$style    = trim($matcheslist[1]);

				$output = $this->byPosition($position, $style);
				// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				$article->text = preg_replace("|$match[0]|", addcslashes($output, '\\$'), $article->text, 1);
			}
		}

		// expression to search for (names)
		$regexmod = '/{loadmodule\s+(.*?)}/i';
		$stylemod = $style;

		// Find all instances of plugin and put in $matchesmod for loadmodule
		preg_match_all($regexmod, $article->text, $matchesmod, PREG_SET_ORDER);

		// If no matches, skip this
		if ($matchesmod)
		{
			foreach ($matchesmod as $matchmod)
			{
				$matchesmodlist = explode(',', $matchmod[1]);
				//We may not have a specific module so set to null
				if (!array_key_exists(1, $matchesmodlist))
				{
					$matchesmodlist[1] = null;
				}
				// We may not have a module style so fall back to the plugin default.
				if (!array_key_exists(2, $matchesmodlist))
				{
					$matchesmodlist[2] = $stylemod;
				}

				$module = trim($matchesmodlist[0]);
				$name   = htmlspecialchars_decode(trim($matchesmodlist[1]));
				$style  = trim($matchesmodlist[2]);

				// $match[0] is full pattern match, $match[1] is the module,$match[2] is the title
				$output = $this->byName($module, $name, $style);

				// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				$article->text = preg_replace("|$matchmod[0]|", addcslashes($output, '\\$'), $article->text, 1);
			}
		}
	}

	/**
	 * This is always going to get the first instance of the module type unless
	 * there is a title.
	 *
	 * @param   string  $position  The module position
	 * @param   string  $style     Display style
	 * @return  string
	 */
	protected function byPosition($position, $style = 'none')
	{
		if (!isset(self::$modules[$position]))
		{
			self::$modules[$position] = '';
			$document   = Document::instance();
			$renderer   = $document->loadRenderer('module');
			$modules    = Module::byPosition($position);
			$params     = array('style' => $style);

			ob_start();
			foreach ($modules as $module)
			{
				echo $renderer->render($module, $params);
			}

			self::$modules[$position] = ob_get_clean();
		}

		return self::$modules[$position];
	}

	/**
	 * This is always going to get the first instance of the module type unless
	 * there is a title.
	 *
	 * @param   string  $module  The module name
	 * @param   string  $title   Module title
	 * @param   string  $style   Display style
	 * @return  string
	 */
	protected function byName($module, $title, $style = 'none')
	{
		$moduleinstance = $module . Hubzero\Utility\String::camel($title);

		if (!isset(self::$mods[$moduleinstance]))
		{
			self::$mods[$moduleinstance] = '';
			$document = Document::instance();
			$renderer = $document->loadRenderer('module');
			$params   = array('style' => $style);
			$mod      = Module::byName($module, $title);

			// If the module without the mod_ isn't found, try it with mod_.
			// This allows people to enter it either way in the content
			if (!isset($mod))
			{
				$name = 'mod_' . $module;
				$mod  = Module::byName($name, $title);
			}

			ob_start();
			echo $renderer->render($mod, $params);

			self::$mods[$moduleinstance] = ob_get_clean();
		}

		return self::$mods[$moduleinstance];
	}
}
