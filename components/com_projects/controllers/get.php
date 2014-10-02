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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Projects Public links controller class
 */
class ProjectsControllerGet extends \Hubzero\Component\SiteController
{
	/**
	 * Pub view for project files, notes etc.
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		if (is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
			.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php'))
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php');
		}
		else
		{
			return false;
		}

		// Incoming
		$stamp = JRequest::getVar( 's', '' );

		// Clean up stamp value (only numbers and letters)
		$regex  = array('/[^a-zA-Z0-9]/');
		$stamp  = preg_replace($regex, '', $stamp);

		// Load item reference
		$objSt = new ProjectPubStamp( $this->database );
		if (!$stamp || !$objSt->loadItem($stamp))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
			return;
		}

		// Can only serve files or notes at the moment
		if (!in_array($objSt->type, array('files', 'notes', 'publications')))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
			return;
		}

		// Get plugin
		JPluginHelper::importPlugin( 'projects', $objSt->type );
		$dispatcher = JDispatcher::getInstance();

		// Serve requested item
		$content = $dispatcher->trigger('serve', array($objSt->projectid, $objSt->reference));

		// Return content
		if ($content[0])
		{
			return $content[0];
		}

		// Redirect if nothing fetched
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option)
		);

		return;
	}
}
