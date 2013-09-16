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

ximport('Hubzero_Module');

/**
 * Module class for displaying popular questions
 */
class modPopularQuestions extends Hubzero_Module
{
	/**
	 * Get module contents
	 * 
	 * @return     void
	 */
	public function run()
	{
		$this->database = JFactory::getDBO();

		$this->cssId = $this->params->get('cssId');
		$this->cssClass = $this->params->get('cssClass');

		$state = $this->params->get('state', 'open');
		$limit = intval($this->params->get('limit', 5));

		switch ($state)
		{
			case 'open': $st = "a.state=0"; break;
			case 'closed': $st = "a.state=1"; break;
			case 'both':
			default: $st = ""; break;
		}

		$this->tag   = JRequest::getVar('tag', '', 'get');
		$this->style = JRequest::getVar('style', '', 'get');

		if ($this->tag)
		{
			$query = "SELECT a.id, a.subject, a.question, a.state, a.created, a.created_by, a.anonymous "
				." FROM #__answers_questions AS a, #__tags_object AS t, #__tags AS tg, #__answers_responses AS r"
				." WHERE r.qid=a.id AND a.id=t.objectid AND tg.id=t.tagid AND t.tbl='answers' AND (tg.tag='" . $this->tag . "' OR tg.raw_tag='" . $this->tag . "' OR tg.alias='" . $this->tag . "')";
		}
		else
		{
			$query = "SELECT a.id, a.subject, a.question, a.state, a.created, a.created_by, a.anonymous "
				." FROM #__answers_questions AS a, #__answers_responses AS r"
				." WHERE r.qid=a.id";
		}
		if ($st)
		{
			$query .= " AND ".$st;
		}
		$query .= " GROUP BY id ORDER BY a.helpful DESC";
		$query .= ($limit) ? " LIMIT " . $limit : "";

		$this->database->setQuery($query);
		$this->rows = $this->database->loadObjectList();

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
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet($this->module->module);

		$juser =& JFactory::getUser();

		if (!$juser->get('guest') && intval($this->params->get('cache', 0)))
		{
			$cache =& JFactory::getCache('callback');
			$cache->setCaching(1);
			$cache->setLifeTime(intval($this->params->get('cache_time', 900)));
			$cache->call(array($this, 'run'));
			echo '<!-- cached ' . date('Y-m-d H:i:s', time()) . ' -->';
			return;
		}

		$this->run();
	}
}
