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

require_once(PATH_CORE . DS . 'components' . DS . 'com_answers' . DS . 'models' . DS . 'question.php');

/**
 * API controller class for support tickets
 */
class AnswersControllerApi extends \Hubzero\Component\ApiController
{
	/**
	 * Execute a request
	 *
	 * @return  void
	 */
	public function execute()
	{
		//JLoader::import('joomla.environment.request');
		//JLoader::import('joomla.application.component.helper');

		$this->config   = Component::params('com_answers');
		$this->database = \JFactory::getDBO();

		switch ($this->segments[0])
		{
			case 'search':    $this->questionsTask();  break;
			case 'questions': $this->questionsTask();  break;

			default:
				$this->serviceTask();
			break;
		}
	}

	/**
	 * Method to report errors. creates error node for response body as well
	 *
	 * @param   integer  $code     Error Code
	 * @param   string   $message  Error Message
	 * @param   string   $format   Error Response Format
	 * @return  void
	 */
	private function errorMessage($code, $message, $format = 'json')
	{
		//build error code and message
		$object = new stdClass();
		$object->error->code    = $code;
		$object->error->message = $message;

		//set http status code and reason
		$this->getResponse()
		     ->setErrorMessage($object->error->code, $object->error->message);

		//add error to message body
		$this->setMessageType(Request::getWord('format', $format));
		$this->setMessage($object);
	}

	/**
	 * Documents available API tasks and their options
	 *
	 * @return  void
	 */
	public function serviceTask()
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

		$this->setMessage($response);
	}

	/**
	 * Displays a list of questions
	 *
	 * @return  void
	 */
	private function questionsTask()
	{
		$model = new \Components\Answers\Tables\Question($this->database);

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

		$this->setMessageType(Request::getWord('format', 'json'));
		$this->setMessage($response);
	}
}
