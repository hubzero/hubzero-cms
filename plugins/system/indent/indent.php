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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Indent plugin for formatting HTML
 */
class  plgSystemIndent extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	protected
	 * @param	object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	* Converting the site URL to fit to the HTTP request
	*
	*/
	public function onAfterRender()
	{
		// Do not render if debugging is not enabled
		if (!JDEBUG) 
		{
			return;
		}

		// Only render for HTML output
		if (strtolower(JFactory::getDocument()->getType()) !== 'html') 
		{
			return;
		}

		$body = $this->_clean(JResponse::getBody());
		JResponse::setBody($body);
	}
	
	//Function to seperate multiple tags one line
	private function _fixNewlines($fixthistext)
	{
		$fixthistext_array = explode("\n", $fixthistext);
		foreach ($fixthistext_array as $unfixedtextkey => $unfixedtextvalue)
		{
			//Makes sure empty lines are ignores
			if (!preg_match("/^(\s)*$/", $unfixedtextvalue))
			{
				$fixedtextvalue = preg_replace("/>(\s|\t)*</U", ">\n<", $unfixedtextvalue);
				$fixedtext_array[$unfixedtextkey] = $fixedtextvalue;
			}
		}
		return implode("\n", $fixedtext_array);
	}

	private function _clean($uncleanhtml)
	{
		//Set wanted indentation
		$indent = $this->params->get('indent', "\t");

		//Uses previous function to seperate tags
		$fixed_uncleanhtml = $this->_fixNewlines($uncleanhtml);
		$fixed_uncleanhtml = preg_replace("/(\S)(<\/div>)/", "$1\n$2", $fixed_uncleanhtml);
		//$fixed_uncleanhtml = preg_replace("/(\S)(<\/div>)/", "$1$2<!-- $1\n$2 -->", $fixed_uncleanhtml);
		$uncleanhtml_array = explode("\n", $fixed_uncleanhtml);
		//Sets no indentation
		$indentlevel = 0;
		$chr = '';
		foreach ($uncleanhtml_array as $uncleanhtml_key => $currentuncleanhtml)
		{
			//Removes all indentation
			$currentuncleanhtml = preg_replace("/\t+/", '', $currentuncleanhtml);
			$currentuncleanhtml = preg_replace("/^\s+/", '', $currentuncleanhtml);
			$char = preg_replace("/^\s+/", '', $currentuncleanhtml);

			$replaceindent = '';

			//Sets the indentation from current indentlevel
			for ($o = 0; $o < $indentlevel; $o++)
			{
				$replaceindent .= $indent;
			}

			//If self-closing tag, simply apply indent
			if (preg_match("/<(.+)\/>/", $currentuncleanhtml))
			{
				$cleanhtml_array[$uncleanhtml_key] = $replaceindent . $currentuncleanhtml;
			}
			//If doctype declaration, simply apply indent
			else if (preg_match("/<!(.*)>/", $currentuncleanhtml))
			{
				$cleanhtml_array[$uncleanhtml_key] = $replaceindent . $currentuncleanhtml;
			}
			//If opening AND closing tag on same line, simply apply indent
			//else if (preg_match("/<[^\/](.*)>/", $currentuncleanhtml) && preg_match("/<\/(.*)>/", $currentuncleanhtml))
			//else if (preg_match("/<[^\/](.*)>(.*)<\/(.*)>/", $currentuncleanhtml) || preg_match("/<[^\/>]*>([\s]?)*<\/[^>]*>/", $currentuncleanhtml))
			else if (preg_match("/<[^\/](.*)>(.*)<\/(.*)>/", $currentuncleanhtml))
			{ 
				$cleanhtml_array[$uncleanhtml_key] = $replaceindent . $currentuncleanhtml;
			}
			//If closing HTML tag or closing JavaScript clams, decrease indentation and then apply the new level
			//else if (preg_match("/<\/(.*)>/", $currentuncleanhtml) || preg_match("/^(\s|\t)*\}{1}(\s|\t)*$/", $currentuncleanhtml))
			else if (preg_match("/<\/(.*)>/", $currentuncleanhtml))
			{
				/*$thischar = preg_replace("/<\/?(\w+)((\s+(\w|\w[\w-]*\w)(\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)\/?>/i", "$1", $currentuncleanhtml);*/
				$indentlevel--;
				/*if ($chr != $thischar)
				{
					$indentlevel--;
				}*/
				$replaceindent = '';
				for ($o = 0; $o < $indentlevel; $o++)
				{
					$replaceindent .= $indent;
				}

				// fix for textarea whitespace and in my opinion nicer looking script tags	
				if ($currentuncleanhtml == '</textarea>' || $currentuncleanhtml == '</script>' || $currentuncleanhtml == '</pre>')
				{
					$cleanhtml_array[$uncleanhtml_key] = $cleanhtml_array[($uncleanhtml_key - 1)] . $currentuncleanhtml;
					unset($cleanhtml_array[($uncleanhtml_key - 1)]);
				}
				else
				{
					$cleanhtml_array[$uncleanhtml_key] = $replaceindent . $currentuncleanhtml;
				}
			}
			//If opening HTML tag AND not a stand-alone tag, or opening JavaScript clams, increase indentation and then apply new level
			else if ((preg_match("/<[^\/](.*)>/", $currentuncleanhtml) && !preg_match("/<(link|meta|base|br|img|hr|param)(.*)>/", $currentuncleanhtml)) || preg_match("/^(\s|\t)*\{{1}(\s|\t)*$/", $currentuncleanhtml))
			//else if ((preg_match("/<[^\/](.*)>/", $currentuncleanhtml) && !preg_match("/<(link|meta|base|br|img|hr|param)(.*)>/", $currentuncleanhtml)))
			{
				/*$chr = preg_replace("/<\/?(\w+)((\s+(\w|\w[\w-]*\w)(\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)\/?>/i", "$1", $currentuncleanhtml);*/
				$cleanhtml_array[$uncleanhtml_key] = $replaceindent . $currentuncleanhtml;
				$indentlevel++;
				$replaceindent = '';
				for ($o = 0; $o < $indentlevel; $o++)
				{
					$replaceindent .= $indent;
				}
			}
			else
			//Else, only apply indentation
			{
				/*$chr = preg_replace("/<(.*)>/", "$1", $currentuncleanhtml);*/
				$cleanhtml_array[$uncleanhtml_key] = $replaceindent . $currentuncleanhtml;
			}
		}
		//Return single string seperated by newline
		return implode("\n", $cleanhtml_array);
	}
}