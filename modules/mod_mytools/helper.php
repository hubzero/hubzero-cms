<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\MyTools;

use Hubzero\Module\Module;
use Component;
use Request;
use Route;
use Lang;
use User;

/**
 * Module class for displaying a user's recently used/favorite tools
 */
class Helper extends Module
{
	/**
	 * Get a list of applications that the user might invoke.
	 *
	 * @param   array  $lst  List of tools
	 * @return  array  List of tools
	 */
	private function _getToollist($lst=NULL)
	{
		require_once(Component::path('com_tools') . DS . 'models' . DS . 'tool.php');

		$toollist = array();

		// Create a Tool object
		$database = \JFactory::getDBO();

		if (is_array($lst))
		{
			$tools = array();

			// Check if the list is empty or not
			if (empty($lst))
			{
				return $tools;
			}

			ksort($lst);
			$items = array();
			// Get info for tools in the list
			foreach ($lst as $item)
			{
				if (strstr($item, '_r'))
				{
					$bits = explode('_r', $item);
					$rev  = (is_array($bits) && count($bits > 1)) ? array_pop($bits) : '';
					$item = trim(implode('_r', $bits));
				}

				$items[] = $item;
			}
			$tools = \Components\Tools\Models\Version::getVersionInfo('', 'current', $items, '');
		}
		else
		{
			// Get all available tools
			$tools = \Components\Tools\Models\Tool::getMyTools();
		}

		$toolnames = array();

		// Turn it into an App array.
		foreach ($tools as $tool)
		{
			if (!in_array(strtolower($tool->toolname), $toolnames))
			{
				// include only one version
				$toollist[strtolower($tool->instance)] = new App(
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
	 * @param   string  $txt  Text to convert quotes in
	 * @return  string
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
	 * @param   array   &$toollist  List of tools to format
	 * @param   string  $type       Type of list being formatted
	 * @return  string  HTML
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

		$database = \JFactory::getDBO();

		$html  = "\t\t" . '<ul>' . "\n";
		if (count($toollist) <= 0)
		{
			$html .= "\t\t" . ' <li>' . Lang::txt('MOD_MYTOOLS_NONE_FOUND') . '</li>' . "\n";
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
					$tool->desc    = $this->_prepText($tool->desc);

					// Get the tool's name without any revision attachments
					// e.g. "qclab" instead of "qclab_r53"
					$toolname = $tool->toolname ? $tool->toolname : $tool->name;

					// from sep 28-07 version (svn revision) number is supplied at the end of the invoke command
					//$url = 'index.php?option=com_mw&task=invoke&sess='.$tool->name.'&version='.$tool->revision;
					$url = Route::url('index.php?option=com_tools&controller=sessions&task=invoke&app=' . $tool->toolname . '&version=' . $tool->revision);

					$cls = '';
					// Build the HTML
					$html .= "\t\t" . ' <li id="' . $tool->name . '"';
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
					$html .= ($cls) ? ' class="' . $cls . '"' : '';
					$html .= '>' . "\n";

					// Tool info link
					$html .= "\t\t\t" . ' <a href="' . Route::url('index.php?option=com_tools&controller=pipeline&app=' . $tool->toolname) . '" class="tooltips" title="' . $tool->caption . ' :: ' . $tool->desc . '">' . $tool->caption . '</a>' . "\n";

					// Only add the "favorites" button to the all tools list
					if ($type == 'all')
					{
						$html .= "\t\t\t" . ' <a href="javascript:void(0);" class="fav" title="' . Lang::txt('MOD_MYTOOLS_ADD_TO_FAVORITES', $tool->caption) . '">' . $tool->caption . '</a>' . "\n";
					}

					// Launch tool link
					if ($this->can_launch && $tool->middleware != 'download')
					{


						$html .= "\t\t\t" . ' <a href="' . $url . '" class="launchtool" title="' . Lang::txt('MOD_MYTOOLS_LAUNCH_TOOL', $tool->caption) . '">' . Lang::txt('MOD_MYTOOLS_LAUNCH_TOOL', $tool->caption) . '</a>' . "\n";
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

		return $html;
	}

	/**
	 * Display module content
	 *
	 * @return     void
	 */
	public function display()
	{
		include_once(Component::path('com_tools') . DS . 'helpers' . DS . 'utils.php');
		include_once(Component::path('com_tools') . DS . 'tables' . DS . 'job.php');
		include_once(Component::path('com_tools') . DS . 'tables' . DS . 'view.php');
		include_once(Component::path('com_tools') . DS . 'tables' . DS . 'viewperm.php');
		include_once(Component::path('com_tools') . DS . 'tables' . DS . 'session.php');
		include_once(Component::path('com_tools') . DS . 'tables' . DS . 'host.php');
		include_once(Component::path('com_tools') . DS . 'tables' . DS . 'hosttype.php');
		include_once(Component::path('com_tools') . DS . 'tables' . DS . 'recent.php');
		include_once(PATH_CORE . DS . 'modules' . DS . $this->module->module . DS . 'app.php');

		$params = $this->params;

		$mconfig = Component::params('com_tools');

		// Ensure we have a connection to the middleware
		$this->can_launch = true;
		if (!$mconfig->get('mw_on')
		 || ($mconfig->get('mw_on') > 1 && !User::authorize('com_tools', 'manage')))
		{
			$this->can_launch = false;
		}

		// See if we have an incoming string of favorite tools
		// This should only happen on AJAX requests
		$this->fav     = Request::getVar('fav', '');
		$this->no_html = Request::getVar('no_html', 0);

		$rconfig = Component::params('com_resources');
		$this->supportedtag = $rconfig->get('supportedtag');

		$database = \JFactory::getDBO();
		if ($this->supportedtag)
		{
			include_once(Component::path('com_resources') . DS . 'helpers' . DS . 'tags.php');
			$this->rt = new \Components\Resources\Helpers\Tags(0);
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
			// Get a list of recent tools
			$rt = new \Components\Tools\Tables\Recent($database);
			$rows = $rt->getRecords(User::get('id'));

			$recent = array();
			if (!empty($rows))
			{
				foreach ($rows as $row)
				{
					$recent[] = $row->tool;
				}
			}

			// Get the user's list of favorites
			$fav = $params->get('favs');
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

		require $this->getLayoutPath();
	}
}
