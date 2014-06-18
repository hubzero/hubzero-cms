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
 * Module class for displaying a random featured question
 */
class modFeaturedquestion extends \Hubzero\Module\Module
{
	/**
	 * Generate module contents
	 *
	 * @return     void
	 */
	public function run()
	{
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'models' . DS . 'question.php');

		$database = JFactory::getDBO();
		$row = null;

		// randomly choose one
		$filters = array();
		$filters['limit'] = 1;
		$filters['start']    = 0;
		$filters['sortby']   = 'random';
		$filters['tag']      = '';
		$filters['filterby'] = 'open';
		$filters['created_before'] = date('Y-m-d', mktime(0, 0, 0, date('m'), (date('d')+7), date('Y'))) . ' 00:00:00';

		$mp = new AnswersTableQuestion($database);

		$rows = $mp->getResults($filters);
		if (count($rows) > 0)
		{
			$row = $rows[0];
		}

		// Did we have a result to display?
		if ($row)
		{
			$this->cls = trim($this->params->get('moduleclass_sfx'));
			$this->txt_length = trim($this->params->get('txt_length'));

			$this->row = $row;

			$config = JComponentHelper::getParams('com_answers');

			$this->thumb = DS . trim($this->params->get('defaultpic'), DS);

			require(JModuleHelper::getLayoutPath($this->module->module));
		}
	}

	/**
	 * Display module contents
	 *
	 * @return     void
	 */
	public function display()
	{
		$debug = (defined('JDEBUG') && JDEBUG ? true : false);

		if (!$debug && intval($this->params->get('cache', 0)))
		{
			$cache = JFactory::getCache('callback');
			$cache->setCaching(1);
			$cache->setLifeTime(intval($this->params->get('cache_time', 900)));
			$cache->call(array($this, 'run'));
			echo '<!-- cached ' . JFactory::getDate() . ' -->';
			return;
		}

		$this->run();
	}
}

