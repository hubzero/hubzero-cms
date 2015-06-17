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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Answers\Api\Controllers;

use Components\Answers\Models\Question;
use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;
use User;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'question.php');

/**
 * API controller class for Questions
 */
class Questionsv1_0 extends ApiController
{
	/**
	 * Documents available API tasks and their options
	 *
	 * @apiMethod GET
	 * @apiUri    /answers
	 * @return    void
	 */
	public function indexTask()
	{
		$response = new stdClass();
		$response->component = 'answers';
		$response->tasks = array(
			'questions' => array(
				'description' => Lang::txt('Get a list of questions.'),
				'parameters'  => array(
					'search' => array(
						'description' => Lang::txt('A word or phrase to search for.'),
						'type'        => 'string',
						'default'     => 'null'
					),
					'filterby' => array(
						'description' => Lang::txt('Filter results by question status.'),
						'type'        => 'string',
						'default'     => 'all',
						'accepts'     => array('all', 'open', 'closed')
					),
					'sort' => array(
						'description' => Lang::txt('Sorting to be applied to the records.'),
						'type'        => 'string',
						'default'     => 'date',
						'accepts'     => array('created', 'helpful', 'reward', 'state')
					),
					'sort_Dir' => array(
						'description' => Lang::txt('Direction to sort records by.'),
						'type'        => 'string',
						'default'     => 'desc',
						'accepts'     => array('asc', 'desc')
					),
				),
			),
		);

		$this->send($response);
	}

	/**
	 * Displays a list of questions
	 *
	 * @apiMethod GET
	 * @apiUri    /answers/questions
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "search",
	 * 		"description":   "A word or phrase to search for.",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "sort",
	 * 		"description":   "Field to sort results by.",
	 * 		"required":      false,
	 *      "default":       "created"
	 * 		"allowedValues": "created, title, alias, id, publish_up, publish_down, state"
	 * }
	 * @apiParameter {
	 * 		"name":          "sort_Dir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"required":      false,
	 * 		"default":       "desc"
	 * 		"allowedValues": "asc, desc"
	 * }
	 * @return    void
	 */
	public function showTask()
	{
		$database = \JFactory::getDBO();
		$model = new \Components\Answers\Tables\Question($database);

		$filters = array(
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'search'     => Request::getVar('search', ''),
			'filterby'   => Request::getword('filterby', ''),
			'sortby'     => Request::getWord('sort', 'date'),
			'sort_Dir'   => strtoupper(Request::getWord('sortDir', 'DESC'))
		);

		$response = new stdClass;
		$response->questions = array();
		$response->total = $model->getCount($filters);

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			foreach ($model->getResults($filters) as $i => $q)
			{
				$question = new \Components\Answers\Models\Question($q);

				$obj = new stdClass;
				$obj->id      = $question->get('id');
				$obj->subject = $question->subject();
				$obj->quesion = $question->content();
				$obj->state   = $question->get('state');
				$obj->url     = str_replace('/api', '', $base . '/' . ltrim(Route::url($question->link()), '/'));
				$obj->responses = $question->comments('count');

				$response->questions[] = $obj;
			}
		}

		$response->success = true;

		$this->send($response);
	}

	/**
	 * Create a new question
	 *
	 * @apiMethod POST
	 * @apiUri    /answers/questions
	 * @return    void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();

		$fields = array(
			'email'      => Request::getInt('email', 0, 'post'),
			'anonymous'  => Request::getInt('anonymous', 0, 'post'),
			'subject'    => Request::getVar('subject', null, 'post', 'none', 2),
			'question'   => Request::getVar('question', null, 'post', 'none', 2),
			'created'    => Request::getVar('created', new Date('now'), 'post'),
			'created_by' => Request::getInt('created_by', 0, 'post'),
			'state'      => Request::getInt('state', 0, 'post'),
			'reward'     => Request::getInt('reward', 0, 'post'),
			'tags'       => Request::getVar('tags', null, 'post')
		);

		$row = new Question();

		if (!$row->bind($fields))
		{
			throw new Exception(Lang::txt('COM_ANSWERS_ERROR_BINDING_DATA'), 500);
		}

		$row->set('email', (isset($fields['email']) ? 1 : 0));
		$row->set('anonymous', (isset($fields['anonymous']) ? 1 : 0));

		if (!$row->store(true))
		{
			throw new Exception(Lang::txt('COM_ANSWERS_ERROR_SAVING_DATA'), 500);
		}

		if (isset($fields['tags']))
		{
			if (!$row->tag($fields['tags'], User::get('id')))
			{
				throw new Exception(Lang::txt('COM_ANSWERS_ERROR_SAVING_TAGS'), 500);
			}
		}

		$this->send($row);
	}

	/**
	 * Update a question
	 *
	 * @apiMethod PUT
	 * @apiUri    /answers/questions/{id}
	 * @return    void
	 */
	public function updateTask()
	{
		$this->requiresAuthentication();

		$fields = array(
			'id'         => Request::getInt('id', 0, 'post'),
			'email'      => Request::getInt('email', 0, 'post'),
			'anonymous'  => Request::getInt('anonymous', 0, 'post'),
			'subject'    => Request::getVar('subject', null, 'post', 'none', 2),
			'question'   => Request::getVar('question', null, 'post', 'none', 2),
			'created'    => Request::getVar('created', new Date('now'), 'post'),
			'created_by' => Request::getInt('created_by', 0, 'post'),
			'state'      => Request::getInt('state', 0, 'post'),
			'reward'     => Request::getInt('reward', 0, 'post'),
			'tags'       => Request::getVar('tags', null, 'post')
		);

		$row = new Question($fields['id']);

		if (!$row->exists())
		{
			throw new Exception(Lang::txt('COM_ANSWERS_ERROR_MISSING_RECORD'), 404);
		}

		if (!$row->bind($fields))
		{
			throw new Exception(Lang::txt('COM_ANSWERS_ERROR_BINDING_DATA'), 422);
		}

		$row->set('email', (isset($fields['email']) ? 1 : 0));
		$row->set('anonymous', (isset($fields['anonymous']) ? 1 : 0));

		if (!$row->store(true))
		{
			throw new Exception(Lang::txt('COM_ANSWERS_ERROR_SAVING_DATA'), 500);
		}

		if (isset($fields['tags']))
		{
			if (!$row->tag($fields['tags'], User::get('id')))
			{
				throw new Exception(Lang::txt('COM_ANSWERS_ERROR_SAVING_TAGS'), 500);
			}
		}

		$this->send($row);
	}

	/**
	 * Delete a question
	 *
	 * @apiMethod DELETE
	 * @apiUri    /answers/questions/{id}
	 * @return    void
	 */
	public function deleteTask()
	{
		$this->requiresAuthentication();

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) <= 0)
		{
			throw new Exception(Lang::txt('COM_ANSWERS_ERROR_MISSING_ID'), 500);
		}

		foreach ($ids as $id)
		{
			$row = new Question(intval($id));

			if (!$row->exists())
			{
				throw new Exception(Lang::txt('COM_ANSWERS_ERROR_MISSING_RECORD'), 404);
			}

			if (!$row->delete())
			{
				throw new Exception($row->getError(), 500);
			}
		}

		$this->send(null, 204);
	}
}
