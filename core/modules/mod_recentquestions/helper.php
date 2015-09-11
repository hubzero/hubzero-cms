<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\RecentQuestions;

use Hubzero\Module\Module;
use Components\Answers\Models\Question;
use Request;
use Date;

/**
 * Module class for displaying recent questions
 */
class Helper extends Module
{
	/**
	 * Get module contents
	 * 
	 * @return  void
	 */
	public function run()
	{
		$this->database = \App::get('db');

		$this->cssId    = $this->params->get('cssId');
		$this->cssClass = $this->params->get('cssClass');

		$state = $this->params->get('state', 'open');
		$limit = intval($this->params->get('limit', 5));

		switch ($state)
		{
			case 'open':   $st = "a.state=0"; break;
			case 'closed': $st = "a.state=1"; break;
			case 'both':   $st = "a.state<2"; break;
			default:       $st = ""; break;
		}

		$this->tag   = Request::getVar('tag', '', 'get');
		$this->style = Request::getVar('style', '', 'get');

		if ($this->tag)
		{
			$query = "SELECT a.id, a.subject, a.question, a.state, a.created, a.created_by, a.anonymous, (SELECT COUNT(*) FROM `#__answers_responses` AS r WHERE r.question_id=a.id) AS rcount"
				." FROM #__answers_questions AS a, #__tags_object AS t, #__tags AS tg"
				." WHERE a.id=t.objectid AND tg.id=t.tagid AND t.tbl='answers' AND (tg.tag=" . $this->database->quote($this->tag) . " OR tg.raw_tag=" . $this->database->quote($this->tag) . ")";
			if ($st)
			{
				$query .= " AND " . $st;
			}
		}
		else
		{
			$query = "SELECT a.id, a.subject, a.question, a.state, a.created, a.created_by, a.anonymous, (SELECT COUNT(*) FROM `#__answers_responses` AS r WHERE r.question_id=a.id) AS rcount"
				." FROM #__answers_questions AS a";
			if ($st)
			{
				$query .= " WHERE " . $st;
			}
		}
		$query .= " ORDER BY a.created DESC";
		$query .= ($limit) ? " LIMIT " . $limit : "";

		$this->database->setQuery($query);
		$this->rows = $this->database->loadObjectList();

		if ($this->rows)
		{
			require_once(\Component::path('com_answers') . DS . 'models' . DS . 'question.php');

			foreach ($this->rows as $k => $row)
			{
				$this->rows[$k] = new Question($row);
			}
		}

		require $this->getLayoutPath();
	}

	/**
	 * Display module content
	 * 
	 * @return  void
	 */
	public function display()
	{
		// Push the module CSS to the template
		$this->css();

		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}
}
