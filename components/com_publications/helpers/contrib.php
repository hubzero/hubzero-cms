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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Publication contrib process html helper class
 */
class PublicationContribHelper
{
	/**
	 * Include status bar - publication steps/sections/version navigation
	 *
	 * @return     array
	 */
	public function drawStatusBar($item, $step = NULL, $showSubSteps = false, $review = 0)
	{
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'publications',
				'name'		=>'edit',
				'layout'	=>'statusbar'
			)
		);
		$view->row 			 = $item->row;
		$view->version 		 = $item->version;
		$view->panels 		 = $item->panels;
		$view->active 		 = isset($item->active) ? $item->active : NULL;
		$view->move 		 = isset($item->move) ? $item->move : 0;
		$view->step 		 = $step;
		$view->lastpane 	 = $item->lastpane;
		$view->option 		 = $item->option;
		$view->project 		 = $item->project;
		$view->current_idx 	 = $item->current_idx;
		$view->last_idx 	 = $item->last_idx;
		$view->checked 		 = $item->checked;
		$view->url 			 = $item->url;
		$view->review		 = $review;
		$view->show_substeps = $showSubSteps;
		$view->display();
	}
}
