<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgSystemDisablecache extends JPlugin
{
	private $_caching = 0;
	
	private $_path = '';
	
	public function plgSystemDisablecache(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin('system', 'disablecache');
		$this->_params = new JParameter($this->_plugin->params);
		//$this->_params = new JParameter($config);
	}
	
	public function onAfterRoute() 
	{
		if ($this->_checkRules() && JFactory::getApplication()->isSite()) {
			$this->_caching = JFactory::getConfig()->getValue('config.caching');
			JFactory::getConfig()->setValue('config.caching', 0);
		}
	}
	
	public function onAfterDispatch() 
	{
		if ($this->_checkRules() && JFactory::getApplication()->isSite()) {
			if ($this->_params->def('reenable_afterdispatch', 0)) {
				JFactory::getConfig()->setValue('config.caching', $this->_caching);
            }
        }
    }

	private function _checkRules() 
	{
		if (!$this->_path) {
			$juri =& JURI::getInstance();
			$this->_path = $this->_parseQueryString(str_replace($juri->base(), '', $juri->current()));
		}
		$defs = str_replace("\r", '', $this->_params->def('definitions',''));
		$defs = explode("\n", $defs);

		foreach ($defs As $def) 
		{
			$result = $this->_parseQueryString($def);
			if ($result == $this->_path) {
				return true;
			}
			/*if (is_array($result)) {
				$found = 0;
				$required = count($result);
				foreach ($result As $key => $value) 
				{
					if (JRequest::getVar($key) == $value || (JRequest::getVar($key, null) !== null && $value == '?' )) {
						$found++;
					}
				}
				if ($found == $required) {
					return true;
				}
			}*/
		}
		return false;
	}
	
	private function _parseQueryString($str) 
	{
		/*$op = array();
		$pairs = explode('&', $str);
		foreach ($pairs as $pair) 
		{
			list($k, $v) = array_map('urldecode', explode('=', $pair));
			$op[$k] = $v;
		}
		return $op;*/
		$str = trim($str);
		if (substr($str, 0, 1) == '/') {
			$str = substr($str, 1, strlen($str));
		}
		if (substr($str, -1) == '/') {
			$str = substr($str, 0, (strlen($str) - 1));
		}
	}
}