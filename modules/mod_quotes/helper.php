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
 * Module class for displaying quotes
 */
class modQuotes extends \Hubzero\Module\Module
{
	/**
	 * Get module contents
	 *
	 * @return     void
	 */
	public function run()
	{
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_feedback' . DS . 'tables' . DS . 'quotes.php');

		$this->database = JFactory::getDBO();

		//Get the admin configured settings
		$this->filters = array(
			'limit'         => trim($this->params->get('maxquotes')),
			'id'            => JRequest::getInt('quoteid', 0),
			'notable_quote' => 1
		);

		// Get quotes
		$sq = new FeedbackQuotes($this->database);
		$this->quotes = $sq->find('list', $this->filters);

		$feedbackConfig = JComponentHelper::getParams('com_feedback');
		$this->path = trim($feedbackConfig->get('uploadpath', '/site/quotes'), DS) . DS;

		require(JModuleHelper::getLayoutPath($this->module->module));
	}

	/**
	 * Display module content
	 *
	 * @return     void
	 */
	public function display()
	{
		// Push the module CSS to the template
		$this->css()
		     ->js();

		$debug = (defined('JDEBUG') && JDEBUG ? true : false);

		if (!$debug && intval($this->params->get('cache', 0)))
		{
			$cache = JFactory::getCache('callback');
			$cache->setCaching(1);
			$cache->setLifeTime(intval($this->params->get('cache_time', 15)));
			$cache->call(array($this, 'run'));
			echo '<!-- cached ' . JFactory::getDate() . ' -->';
			return;
		}

		$this->run();
	}
}
