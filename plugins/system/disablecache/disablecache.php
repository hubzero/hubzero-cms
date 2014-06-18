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

jimport('joomla.plugin.plugin');

/**
 * System plugin for disabling the cache for select pages
 */
class plgSystemDisablecache extends JPlugin
{
	/**
	 * Caching turned on/off
	 *
	 * @var integer
	 */
	private $_caching = 0;

	/**
	 * Current URI
	 *
	 * @var string
	 */
	private $_path = '';

	/**
	 * Constructor
	 *
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Check if caching is disabled for this page and set the site config accordingly
	 *
	 * @return 	void
	 */
	public function onAfterRoute()
	{
		if ($this->_checkRules() && JFactory::getApplication()->isSite())
		{
			$this->_caching = JFactory::getConfig()->getValue('config.caching');
			JFactory::getConfig()->setValue('config.caching', 0);
		}
	}

	/**
	 * Check if caching should be re-enabled for this page if it was disabled and
	 * set the site config accordingly
	 *
	 * @return 	void
	 */
	public function onAfterDispatch()
	{
		if ($this->_checkRules() && JFactory::getApplication()->isSite())
		{
			if ($this->params->def('reenable_afterdispatch', 0))
			{
				JFactory::getConfig()->setValue('config.caching', $this->_caching);
			}
		}
 	}

	/**
	 * Check if the current URL is one of the set rules
	 *
	 * @return 	boolean	True if the current page is a rule
	 */
	private function _checkRules()
	{
		if (!$this->_path)
		{
			$juri = JURI::getInstance();
			$this->_path = $this->_parseQueryString(str_replace($juri->base(), '', $juri->current()));
		}

		$defs = str_replace("\r", '', $this->params->def('definitions',''));
		$defs = explode("\n", $defs);

		foreach ($defs As $def)
		{
			$result = $this->_parseQueryString($def);
			if ($result == $this->_path)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Trim leading and trailing slashes off a URI
	 *
	 * @param	string	$str
	 * @return 	string
	 */
	private function _parseQueryString($str)
	{
		$str = trim($str);
		$str = trim($str, DS);

		return $str;
	}
}