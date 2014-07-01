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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Wiki plugin class for loading the default parser
 */
class plgWikiParserdefault extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Holds the parser for re-use
	 *
	 * @var object
	 */
	public $parser;

	/**
	 * Get the wiki parser, creating a new one if not already existing or $getnew is set
	 *
	 * @param      array   $config Options for initializing a parser
	 * @param      boolean $getnew Init a new parser?
	 * @return     object
	 */
	public function onGetWikiParser($config, $getnew=false)
	{
		if (!is_object($this->parser) || $getnew)
		{
			$path = dirname(__FILE__);
			if (is_file($path . DS . 'parser.php'))
			{
				include_once($path . DS . 'parser.php');
			}
			else
			{
				return null;
			}

			$config['option']   = (isset($config['option']))   ? $config['option']   : 'com_wiki';
			$config['scope']    = (isset($config['scope']))    ? $config['scope']    : '';
			$config['pagename'] = (isset($config['pagename'])) ? $config['pagename'] : '';
			$config['pageid']   = (isset($config['pageid']))   ? $config['pageid']   : 0;
			$config['filepath'] = (isset($config['filepath'])) ? $config['filepath'] : '';
			$config['domain']   = (isset($config['domain']))   ? $config['domain']   : null;
			$config['loglinks'] = (isset($config['loglinks'])) ? $config['loglinks'] : null;

			$this->parser = new WikiParser($config);
		}
		return $this->parser;
	}

	/**
	 * Turns wiki markup to HTML
	 *
	 * @param      string  $text      Text to convert
	 * @param      array   $config    Options for initializing a parser
	 * @param      boolean $fullparse Do a full parse or ignore some things like macros?
	 * @param      boolean $getnew    Init a new parser?
	 * @return     string
	 */
	public function onWikiParseText($text, $config, $fullparse=true, $getnew=false)
	{
		$parser = $this->onGetWikiParser($config, $getnew);
		$config['camelcase'] = (isset($config['camelcase']) ? $config['camelcase'] : 1);

		return is_object($parser) ? $parser->parse("\n" . $text, $fullparse, 0, $config['camelcase']) : $text;
	}
}
