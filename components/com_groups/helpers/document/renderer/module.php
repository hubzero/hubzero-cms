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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class GroupsHelperDocumentRendererModule extends GroupsHelperDocumentRenderer
{
	/**
	 * Render Module to group template or page
	 *
	 * @param    string
	 */
	public function render()
	{
		// var to hold content
		$content = '';

		// get the page id
		$pageid = ($this->page) ? $this->page->get('id') : null;

		// get the list of modules
		$groupModuleArchive = GroupsModelModuleArchive::getInstance();
		$modules = $groupModuleArchive->modules('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'state'     => array(1),
			'approved'  => ($this->allMods == 1) ? array(0,1) : array(1),
			'title'     => $this->params->title,
			'orderby'   => 'ordering DESC'
		));

		// make sure we have a module
		if ($modules->count() < 1)
		{
			return $content;
		}

		// get first module
		$module = $modules->first();

		// make sure module can be displayed
		if ($module->displayOnPage($pageid))
		{
			$content .= $module->content('parsed');
		}

		//return final output
		return $content;
	}
}