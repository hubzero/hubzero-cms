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
 * @author    Sam Wilson <samwilson@purdue.edu
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Time component reports controller
 */
class TimeControllerReports extends TimeControllerBase
{
	/**
	 * Default view function
	 *
	 * @return void
	 */
	public function displayTask()
	{
		$this->_buildPathway();
		$this->view->title = $this->_buildTitle();

		\JPluginHelper::importPlugin('time');
		$this->view->reports = JPluginHelper::getPlugin('time');

		if ($this->view->report_type = JRequest::getCmd('report_type', false))
		{
			$className = 'plgTime' . ucfirst($this->view->report_type);

			if (class_exists($className))
			{
				if (($method = JRequest::getCmd('method', false)) && in_array($method, $className::$accepts))
				{
					if (method_exists($className, $method))
					{
						$this->view->content = $className::$method();
					}
				}
				elseif (method_exists($className, 'render'))
				{
					$this->view->content = $className::render();
				}
			}
		}

		// Set a few things for the vew
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Display
		$this->view->display();
	}
}