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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for displaying a user's publication curation tasks
 */
class modMyCuration extends \Hubzero\Module\Module
{
	/**
	 * Display module content
	 *
	 * @return     void
	 */
	public function display()
	{
		$juser = JFactory::getUser();
		$database = JFactory::getDBO();
		$config = JComponentHelper::getParams( 'com_publications' );

		// Get some classes we need
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'publication.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'master.type.php');

		$this->moduleclass = $this->params->get('moduleclass');

		// Build query
		$filters = array();
		$filters['limit'] 	 		= intval($this->params->get('limit', 10));
		$filters['start'] 	 		= 0;
		$filters['sortby']   		= 'title';
		$filters['sortdir']  		= 'ASC';
		$filters['ignore_access']   = 1;
		$filters['curator']   		= 'owner';
		$filters['dev']   	 		= 1; // get dev versions
		$filters['status']   	 	= array(5, 7); // submitted/pending

		// Instantiate
		$objP = new Publication( $database );

		// Assigned curation
		$this->rows  = $objP->getRecords($filters);

		// Push the module CSS to the template
		$this->css();

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
