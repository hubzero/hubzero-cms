<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'question.php';

/**
 * API controller class for Questions
 */
class Questionsv1_0 extends ApiController
{
	/**
	 * Display a list of questions
	 *
	 * @apiMethod GET
	 * @apiUri    /answers/questions/list
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "search",
	 * 		"description":   "A word or phrase to search for.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "sort",
	 * 		"description":   "Field to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "created",
	 * 		"allowedValues": "created, title, alias, id, publish_up, publish_down, state"
	 * }
	 * @apiParameter {
	 * 		"name":          "sort_Dir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "desc",
	 * 		"allowedValues": "asc, desc"
	 * }
	 * @return    void
	 */
	public function listTask()
	{
		$filters = array(
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'search'     => Request::getVar('search', ''),
			'state'      => Request::getInt('state', -1),
			'sort'       => Request::getWord('sort', 'created'),
			'sort_Dir'   => strtoupper(Request::getWord('sortDir', 'DESC'))
		);

		$records = Question::all();

		if ($filters['search'])
		{
			$filters['search'] = strtolower((string)$filters['search']);

			$records->whereLike('subject', $filters['search'], 1)
					->orWhereLike('question', $filters['search'], 1)
					->resetDepth();
		}

		if ($filters['state'] >= 0)
		{
			$records->whereEquals('state', $filters['state']);
		}

		$rows = $records
			->limit($filters['limit'])
			->ordered('sort', 'sortDir')
			->paginated()
			->rows();

		$response = new stdClass;
		$response->questions = array();
		$response->total     = $rows->count();

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			foreach ($rows as $question)
			{
				$obj = new stdClass;
				$obj->id      = $question->get('id');
				$obj->subject = $question->get('subject');
				$obj->quesion = $question->get('question');
				$obj->state   = $question->get('state');
				$obj->created = with(new Date($question->get('created')))->format('Y-m-d\TH:i:s\Z');
				$obj->created_by = $question->get('created_by');
				$obj->url     = str_replace('/api', '', $base . '/' . ltrim(Route::url($question->link()), '/'));
				$obj->responses = $question->responses()->total();

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
	 * @apiParameter {
	 * 		"name":        "email",
	 * 		"description": "Notify user of responses",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "anonymous",
	 * 		"description": "List author as anonymous or not",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "subject",
	 * 		"description": "Short, one-line question",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "question",
	 * 		"description": "Longer, detailed question",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "created",
	 * 		"description": "Created timestamp (YYYY-MM-DD HH:mm:ss)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "now"
	 * }
	 * @apiParameter {
	 * 		"name":        "crated_by",
	 * 		"description": "User ID of entry creator",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "state",
	 * 		"description": "Published state (0 = unpublished, 1 = published)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "reward",
	 * 		"description": "Reward points",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "tags",
	 * 		"description": "Comma-separated list of tags",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
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
			'created'    => Request::getVar('created', with(new Date('now'))->toSql(), 'post'),
			'created_by' => Request::getInt('created_by', User::get('id'), 'post'),
			'state'      => Request::getInt('state', 0, 'post'),
			'reward'     => Request::getInt('reward', 0, 'post')
		);

		$row = new Question();

		if (!$row->set($fields))
		{
			throw new Exception(Lang::txt('COM_ANSWERS_ERROR_BINDING_DATA'), 500);
		}

		$row->set('email', (isset($fields['email']) ? 1 : 0));
		$row->set('anonymous', (isset($fields['anonymous']) ? 1 : 0));

		if (!$row->save())
		{
			throw new Exception(Lang::txt('COM_ANSWERS_ERROR_SAVING_DATA'), 500);
		}

		$tags = Request::getVar('tags', null, 'post');

		if (isset($tags))
		{
			if (!$row->tag($tags, $fields['created_by']))
			{
				throw new Exception(Lang::txt('COM_ANSWERS_ERROR_SAVING_TAGS'), 500);
			}
		}

		$this->send($row->toObject());
	}

	/**
	 * Retrieve a question
	 *
	 * @apiMethod GET
	 * @apiUri    /answers/questions/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Question identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	 * @return    void
	 */
	public function readTask()
	{
		$id = Request::getInt('id', 0);

		$row = Question::oneOrFail($id);

		if (!$row->get('id'))
		{
			throw new Exception(Lang::txt('COM_ANSWERS_ERROR_MISSING_RECORD'), 404);
		}

		$row->set('created', with(new Date($row->get('created')))->format('Y-m-d\TH:i:s\Z'));

		$this->send($row->toObject());
	}

	/**
	 * Update a question
	 *
	 * @apiMethod PUT
	 * @apiUri    /answers/questions/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Question identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "email",
	 * 		"description": "Notify user of responses",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "anonymous",
	 * 		"description": "List author as anonymous or not",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "subject",
	 * 		"description": "Short, one-line question",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "question",
	 * 		"description": "Longer, detailed question",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "created",
	 * 		"description": "Created timestamp (YYYY-MM-DD HH:mm:ss)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "crated_by",
	 * 		"description": "User ID of entry creator",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "state",
	 * 		"description": "Published state (0 = unpublished, 1 = published)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "reward",
	 * 		"description": "Reward points",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "tags",
	 * 		"description": "Comma-separated list of tags",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function updateTask()
	{
		$this->requiresAuthentication();

		$id = Request::getInt('id', 0);

		$row = Question::oneOrFail($id);

		if (!$row->get('id'))
		{
			throw new Exception(Lang::txt('COM_ANSWERS_ERROR_MISSING_RECORD'), 404);
		}

		$fields = array(
			'email'      => Request::getInt('email', $row->get('email')),
			'anonymous'  => Request::getInt('anonymous', $row->get('anonymous')),
			'subject'    => Request::getVar('subject', $row->get('subject')),
			'question'   => Request::getVar('question', $row->get('question')),
			'created'    => Request::getVar('created', $row->get('created')),
			'created_by' => Request::getInt('created_by', $row->get('created_by')),
			'state'      => Request::getInt('state', $row->get('state')),
			'reward'     => Request::getInt('reward', $row->get('reward'))
		);

		if (!$row->set($fields))
		{
			throw new Exception(Lang::txt('COM_ANSWERS_ERROR_BINDING_DATA'), 422);
		}

		$row->set('email', (isset($fields['email']) ? 1 : 0));
		$row->set('anonymous', (isset($fields['anonymous']) ? 1 : 0));

		if (!$row->save())
		{
			throw new Exception(Lang::txt('COM_ANSWERS_ERROR_SAVING_DATA'), 500);
		}

		$tags = Request::getVar('tags', null);

		if (isset($tags))
		{
			if (!$row->tag($tags, $fields['created_by']))
			{
				throw new Exception(Lang::txt('COM_ANSWERS_ERROR_SAVING_TAGS'), 500);
			}
		}

		$this->send($row->toObject());
	}

	/**
	 * Delete a question
	 *
	 * @apiMethod DELETE
	 * @apiUri    /answers/questions/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Question identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
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
			$row = Question::oneOrNew(intval($id));

			if (!$row->get('id'))
			{
				throw new Exception(Lang::txt('COM_ANSWERS_ERROR_MISSING_RECORD'), 404);
			}

			if (!$row->destroy())
			{
				throw new Exception($row->getError(), 500);
			}
		}

		$this->send(null, 204);
	}
}
