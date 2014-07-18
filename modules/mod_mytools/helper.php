<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
 * Module class for displaying a user's recently used/favorite tools
 */
class modToolList extends \Hubzero\Module\Module
{
	/**
	 * Get a list of applications that the user might invoke.
	 *
	 * @param      array $lst Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	private function _getToollist($lst=NULL)
	{
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'tool.php');

		$toollist = array();

		// Create a Tool object
		$database = JFactory::getDBO();

		if (is_array($lst))
		{
			$tools = array();
			// Check if the list is empty or not
			if (!empty($lst))
			{
				ksort($lst);
				$items = array();
				// Get info for tools in the list
				foreach ($lst as $item)
				{
					if (strstr($item, '_r'))
					{
						$bits = explode('_r', $item);
						$rev = (is_array($bits) && count($bits > 1)) ? array_pop($bits) : '';
						$item = trim(implode('_r', $bits));
					}
					/*$thistool = ToolsModelVersion::getVersionInfo('', 'current', $item, '');

					if (is_array($thistool) && isset($thistool[0]))
					{
						$t = $thistool[0];
						$tools[] = $t;
					}*/
					$items[] = $item;
				}
				$tools = ToolsModelVersion::getVersionInfo('', 'current', $items, '');
			}
			else
			{
				return array();
			}
		}
		else
		{
			// Get all available tools
			$tools = ToolsModelTool::getMyTools();
		}

		$toolnames = array();

		// Turn it into an App array.
		foreach ($tools as $tool)
		{
			if (!in_array(strtolower($tool->toolname), $toolnames))
			{
				// include only one version
				$toollist[strtolower($tool->instance)] = new MwModApp(
					$tool->instance,
					$tool->title,
					$tool->description,
					$tool->mw,
					0, '', 0,
					1,
					$tool->revision,
					$tool->toolname
				);
			}
			$toolnames[] = strtolower($tool->toolname);
		}

		return $toollist;
	}

	/**
	 * Convert quote marks
	 *
	 * @param      string $txt Text to convert quotes in
	 * @return     string
	 */
	private function _prepText($txt)
	{
		$txt = stripslashes($txt);
		$txt = str_replace('"', '&quot;', $txt);
		return $txt;
	}

	/**
	 * Build the HTML for a list of tools
	 *
	 * @param      array  &$toollist List of tools to format
	 * @param      string $type      Type of list being formatted
	 * @return     string HTML
	 */
	public function buildList($toollist, $type='all')
	{
		if ($type == 'favs')
		{
			$favs = array();
		}
		elseif ($type == 'all')
		{
			$favs = $this->favs;
		}

		$database = JFactory::getDBO();

		$html  = "\t\t" . '<ul>' . "\n";
		if (count($toollist) <= 0)
		{
			$html .= "\t\t" . ' <li>' . JText::_('MOD_MYTOOLS_NONE_FOUND') . '</li>' . "\n";
		}
		else
		{
			foreach ($toollist as $tool)
			{
				// Make sure we have some info before attempting to display it
				if (!empty($tool->caption))
				{
					// Prep the text for XHTML output
					$tool->caption = $this->_prepText($tool->caption);
					$tool->desc = $this->_prepText($tool->desc);

					// Get the tool's name without any revision attachments
					// e.g. "qclab" instead of "qclab_r53"
					$toolname = $tool->toolname ? $tool->toolname : $tool->name;

					// from sep 28-07 version (svn revision) number is supplied at the end of the invoke command
					//$url = 'index.php?option=com_mw&task=invoke&sess='.$tool->name.'&version='.$tool->revision;

					//are we on the iPad
					$isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

					//get tool params
					$params = JComponentHelper::getParams('com_tools');
					$launchOnIpad = $params->get('launch_ipad', 0);

					//if we are on the ipad and we want to launch nanohub app
					if ($isiPad && $launchOnIpad)
					{
						$url = 'nanohub://tools/invoke/' . $tool->toolname . '/' . $tool->revision;
					}
					else
					{
						$url = 'index.php?option=com_tools&task=invoke&app='.$tool->toolname.'&version='.$tool->revision;
					}

					$cls = '';
					// Build the HTML
					$html .= "\t\t" . ' <li id="'.$tool->name.'"';
					// If we're in the 'all tools' pane ...
					if ($type == 'all')
					{
						// Highlight tools on the user's favorites list
						if (in_array($tool->name,$favs))
						{
							$cls = 'favd';
						}
					}
					if ($this->supportedtag)
					{
						if (in_array($tool->toolname, $this->supportedtagusage))
						{
							$cls .= ($cls) ? ' supported' : 'supported';
						}
					}
					$html .= ($cls) ? ' class="'.$cls.'"' : '';
					$html .= '>' . "\n";

					// Tool info link
					$html .= "\t\t\t" . ' <a href="/tools/'.$tool->toolname.'" class="tooltips" title="'.$tool->caption.' :: '.$tool->desc.'">'.$tool->caption.'</a>' . "\n";

					// Only add the "favorites" button to the all tools list
					if ($type == 'all')
					{
						$html .= "\t\t\t" . ' <a href="javascript:void(0);" class="fav" title="Add '.$tool->caption.' to your favorites">'.$tool->caption.'</a>' . "\n";
					}

					// Launch tool link
					if ($this->can_launch && $tool->middleware != 'download')
					{


						$html .= "\t\t\t" . ' <a href="'.$url.'" class="launchtool" title="Launch '.$tool->caption.'">Launch '.$tool->caption.'</a>' . "\n";
					}
					$html .= "\t\t" . ' </li>' . "\n";
				}
				// If we're in the 'favorites' pane ...
				// Add the tool's name to an array for the 'all tools'
				// pane to use in highlighting favorite tools
				if ($type == 'favs')
				{
					$favs[] = $tool->name;
				}
			}
		}
		$html .= "\t\t" . '</ul>' . "\n";

		if ($type == 'favs')
		{
			$this->favs = $favs;
		}
		return $html;
	}

	/**
	 * Display module content
	 *
	 * @return     void
	 */
	public function display()
	{
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'mw.utils.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'mw.class.php');
		include_once(JPATH_ROOT . DS . 'modules' . DS . $this->module->module . DS . 'app.php');

		$params = $this->params;

		$juser = JFactory::getUser();

		$mconfig = JComponentHelper::getParams('com_tools');

		// Ensure we have a connection to the middleware
		$this->can_launch = true;
		if (!$mconfig->get('mw_on')
		 || ($mconfig->get('mw_on') > 1 && !$juser->authorize('com_tools', 'manage')))
		{
			$this->can_launch = false;
		}

		// See if we have an incoming string of favorite tools
		// This should only happen on AJAX requests
		$this->fav     = JRequest::getVar('fav', '');
		$this->no_html = JRequest::getVar('no_html', 0);

		$rconfig = JComponentHelper::getParams('com_resources');
		$this->supportedtag = $rconfig->get('supportedtag');

		$database = JFactory::getDBO();
		if ($this->supportedtag)
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'tags.php');
			$this->rt = new ResourcesTags($database);
			$this->supportedtagusage = $this->rt->getTagUsage($this->supportedtag, 'alias');
		}

		if ($this->fav || $this->no_html)
		{
			// We have a string of tools! This means we're updating the
			// favorite tools pane of the module via AJAX
			$favs = explode(',', $this->fav);
			$favs = array_map('trim', $favs);

			$this->favtools = ($this->fav) ? $this->_getToollist($favs) : array();
		}
		else
		{
			$juser = JFactory::getUser();

			// Add the JavaScript that does the AJAX magic to the template
			$document = JFactory::getDocument();

			// Get a list of recent tools
			$rt = new RecentTool($database);
			$rows = $rt->getRecords($juser->get('id'));

			$recent = array();
			if (!empty($rows))
			{
				foreach ($rows as $row)
				{
					$recent[] = $row->tool;
				}
			}

			// Get the user's list of favorites
			$fav = $params->get('myhub_favs');
			if ($fav)
			{
				$favs = explode(',', $fav);
			}
			else
			{
				$favs = array();
			}
			$this->favs = $favs;

			// Get a list of applications that the user might invoke.
			$this->rectools = $this->_getToollist($recent);
			$this->favtools = $this->_getToollist($favs);
			$this->alltools = $this->_getToollist();
		}

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
