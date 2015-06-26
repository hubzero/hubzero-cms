<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Newsletter\Api\Controllers;

use Components\Newsletter\Tables\Newsletter;
use Hubzero\Component\ApiController;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;

$base = dirname(dirname(__DIR__)) . DS . 'tables' . DS;

require_once($base . 'newsletter.php');
require_once($base . 'template.php');
require_once($base . 'primary.php');
require_once($base . 'secondary.php');

/**
 * API controller class for newsletters
 */
class Newslettersv1_0 extends ApiController
{
	/**
	 * Return data for the current newsletter
	 *
	 * @apiMethod GET
	 * @apiUri    /newsletters/current
	 * @return    void
	 */
	public function currentTask()
	{
		$result = array();

		$database = \App::get('db');
		$newsletterNewsletter = new Newsletter($database);

		// get the current newsletter
		$newsletter = $newsletterNewsletter->getCurrentNewsletter();

		// build the newsletter based on campaign
		$result['id']      = $newsletter->issue;
		$result['title']   = $newsletter->name;
		$result['content'] = $newsletterNewsletter->buildNewsletter($newsletter);

		$obj = new stdClass();
		$obj->newsletter = $result;

		$this->send($obj);
	}

	/**
	 * Return data for newsletters
	 *
	 * @apiMethod GET
	 * @apiUri    /newsletters/list
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       5
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @return    void
	 */
	public function listTask()
	{
		$limit = Request::getInt('limit', 5);
		$start = Request::getInt('start', 0);

		$database = \App::get('db');
		$newsletterNewsletter = new Newsletter($database);

		$newsletters = $newsletterNewsletter->getNewsletters(null, true);

		$obj = new stdClass();
		$obj->newsletters = $newsletters;

		$this->send($obj);
	}

	/**
	 * Return data for past newsletters
	 *
	 * @apiMethod GET
	 * @apiUri    /newsletters/archive
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       5
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @return    void
	 */
	public function archiveTask()
	{
		$limit = Request::getInt('limit', 5);
		$start = Request::getInt('start', 0);

		$result = array();

		$database = \App::get('db');
		$newsletterNewsletter = new Newsletter($database);

		// get newsletters
		$newsletters = $newsletterNewsletter->getNewsletters();

		// add newsletter details to return array
		foreach ($newsletters as $k => $newsletter)
		{
			$result[$k]['id']      = $newsletter->issue;
			$result[$k]['title']   = $newsletter->name;
			$result[$k]['content'] = $newsletterNewsletter->buildNewsletter($newsletter);
		}

		$obj = new stdClass();
		$obj->newsletters = $result;

		$this->send($obj);
	}
}
