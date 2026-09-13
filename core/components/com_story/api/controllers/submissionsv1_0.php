<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Story\Models\Submission;
use Component;
use Exception;
use stdClass;
use Request;
use Lang;
use User;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'submission.php';

/**
 * API controller for the submissions queue
 *
 * Submitting over the API is what makes a feed-to-queue bridge or a bot
 * possible, and that is how a small hub keeps a story queue from running dry.
 */
class Submissionsv1_0 extends ApiController
{
	/**
	 * The public queue, as readers have ranked it
	 *
	 * @apiMethod GET
	 * @apiUri    /story/submissions/list
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "How many to return",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$limit = Request::getInt('limit', 25);
		$start = Request::getInt('start', 0);

		$response = new stdClass;
		$response->total       = Submission::open()->total();
		$response->submissions = array();

		$rows = Submission::open()
			->order('popularity', 'desc')
			->start($start)
			->limit($limit)
			->rows();

		foreach ($rows as $row)
		{
			$out = new stdClass;
			$out->id         = (int) $row->get('id');
			$out->subject    = $row->get('subject');
			$out->body       = $row->get('body');
			$out->url        = $row->get('url');
			$out->state      = $row->get('state');
			$out->created    = $row->get('created');
			$out->popularity = round((float) $row->get('popularity'), 2);

			$response->submissions[] = $out;
		}

		$this->send($response);
	}

	/**
	 * Suggest a story
	 *
	 * @apiMethod POST
	 * @apiUri    /story/submissions/create
	 * @apiParameter {
	 * 		"name":          "subject",
	 * 		"description":   "The headline being suggested",
	 * 		"type":          "string",
	 * 		"required":      true
	 * }
	 * @apiParameter {
	 * 		"name":          "body",
	 * 		"description":   "Why it matters",
	 * 		"type":          "string",
	 * 		"required":      false
	 * }
	 * @apiParameter {
	 * 		"name":          "url",
	 * 		"description":   "Where it was read",
	 * 		"type":          "string",
	 * 		"required":      false
	 * }
	 * @return  void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();

		$subject = trim(Request::getString('subject', ''));

		if ($subject === '')
		{
			throw new Exception(Lang::txt('COM_STORY_SUBMIT_NEEDS_A_SUBJECT'), 400);
		}

		$row = Submission::blank();
		$row->set(array(
			'subject'    => $subject,
			'body'       => Request::getString('body', ''),
			'url'        => Request::getString('url', ''),
			'topic_id'   => Request::getInt('topic_id', 0),
			'section_id' => Request::getInt('section_id', 0),
			'state'      => Submission::STATE_PENDING,
			'created_by' => (int) User::get('id'),
			'email'      => (string) User::get('email'),
			'ip'         => Request::ip()
		));

		$row->rescore(Component::params('com_story'));

		if (!$row->save())
		{
			throw new Exception(implode(', ', $row->getErrors()), 500);
		}

		$out = new stdClass;
		$out->id         = (int) $row->get('id');
		$out->subject    = $row->get('subject');
		$out->state      = $row->get('state');
		$out->popularity = round((float) $row->get('popularity'), 2);

		$this->send($out, 201);
	}
}
