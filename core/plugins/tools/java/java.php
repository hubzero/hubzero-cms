<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin class for Java session rendering
 */
class plgToolsJava extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return some info about this plugin
	 * 
	 * @return  object
	 */
	public function onToolSessionIdentify()
	{
		$obj = new stdClass;
		$obj->name  = $this->_name;
		$obj->type  = $this->_type;
		$obj->title = Lang::txt('PLG_' . $this->_type . '_' . $this->_name . '_TITLE');

		return $obj;
	}

	/**
	 * Render a tool session
	 * 
	 * @param   object  $tool
	 * @param   object  $session
	 * @param   boolean $readOnly
	 * @return  string
	 */
	public function onToolSessionView($tool, $session, $readOnly=false)
	{
		$us = App::get('session');
		$us->set('tool_viewer', null);
		$declared = Request::getWord('viewer');
		$viewer = ($declared ? $declared : $us->get('tool_viewer'));

		if ((isset($session->rendered) && $session->rendered)
		 || ($viewer && $viewer != $this->_name))
		{
			return;
		}

		if (!$this->canRender())
		{
			return;
		}

		$session->rendered = $this->_name;

		if (!$declared)
		{
			//$us->set('tool_viewer', $this->_name);
		}

		$view = new \Hubzero\Plugin\View(array(
			'folder'  => $this->_type,
			'element' => $this->_name,
			'name'    => 'session',
			'layout'  => 'default'
		));

		return $view->set('option', Request::getCmd('option', 'com_tools'))
					->set('controller', Request::getWord('controller', 'sessions'))
					->set('output', $session)
					->set('app', $tool)
					->set('readOnly', $readOnly)
					->set('params', $this->params)
					->loadTemplate();
	}

	/**
	 * Check if the plugin can render for the provided browser
	 * 
	 * @return  boolean
	 */
	protected function canRender()
	{
		$browser = new \Hubzero\Browser\Detector();

		if ($allowed = trim($this->params->get('browsers')))
		{
			$browsers = array();

			$allowed = str_replace("\r", '', $allowed);
			$allowed = str_replace('\n', "\n", $allowed);
			$allowed = explode("\n", $allowed);
			foreach ($allowed as $allow)
			{
				$allow = trim($allow);

				if (preg_match('/(.+?),\s+([^\s]+)\s+(\d+)\.(\d+)/i', $allow, $matches))
				{
					$req = new stdClass;
					$req->name  = strtolower(trim($matches[2]));
					$req->major = intval($matches[3]);
					$req->minor = intval($matches[4]);
					$req->os    = strtolower(trim($matches[1]));

					$browsers[] = $req;
				}
			}

			$matched = false;

			foreach ($browsers as $minimum)
			{
				if ($minimum->os != '*' && $minimum->os != strtolower($browser->platform()))
				{
					continue;
				}

				if ($minimum->name != strtolower($browser->name()))
				{
					continue;
				}

				// If we get to here, we have a matching OS and browser

				if ($minimum->major > $browser->major())
				{
					return false;
				}

				if ($minimum->major == $browser->major() && $minimum->minor > $browser->minor())
				{
					return false;
				}

				$matched = true;
			}

			if (!$matched)
			{
				return false;
			}
		}

		if ($regexes = trim($this->params->get('regexes')))
		{
			$regexes = str_replace("\r", '', $regexes);
			$regexes = str_replace('\n', "\n", $regexes);
			$regexes = explode("\n", $regexes);
			foreach ($regexes as $disallow)
			{
				$disallow = trim($disallow);

				if (preg_match("/$disallow/i", $browser->agent(), $matches))
				{
					return false;
				}
			}
		}

		return true;
	}
}

