<?php
/**
 * @package     hubzero-cms
 * @author      Steve Snyder <snyder13@purdue.edu>
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

if (!function_exists('stem'))
{
	function stem($str) { return $str; }
}

ini_set('display_errors', 1);

require 'include.php';

jimport('joomla.application.component.controller');

class YSearchController extends JController
{
	public function display()
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$pathway->addItem('Search', '/search');

		$terms = new YSearchModelTerms(JRequest::getString('terms'));
		JFactory::getDocument()->setTitle($terms->is_set() ? 'Search results for \''.htmlspecialchars($terms->get_raw(), ENT_NOQUOTES).'\'' : 'Search');
		
		$app =& JFactory::getApplication();
		$results = new YSearchModelResultSet($terms);
		$results->set_limit($app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int'));
		$results->set_offset(JRequest::getInt('limitstart', 0));
		$results->collect(JRequest::getBool('force-generic'));

		$view =& $this->getView('', JRequest::getCmd('format', 'html'), '');
		$view->set_application($app);
		$view->set_terms($terms);
		$view->set_results($results);
		$view->display();
	}
}

$controller = new YSearchController();
$controller->execute(JRequest::getCmd('task'));

