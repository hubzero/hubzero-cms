<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Story\Models\Comment;
use Components\Story\Models\Discussion;
use Exception;
use stdClass;
use Request;
use Lang;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'comment.php';

/**
 * API controller for a discussion's comments
 */
class Commentsv1_0 extends ApiController
{
	/**
	 * One discussion, in tree order
	 *
	 * Served in tree order with each comment's depth, rather than nested, so a
	 * client can render it flat or indented without having to walk a structure
	 * it did not ask for.
	 *
	 * @apiMethod GET
	 * @apiUri    /story/comments/list
	 * @apiParameter {
	 * 		"name":          "discussion",
	 * 		"description":   "Discussion identifier",
	 * 		"type":          "integer",
	 * 		"required":      true
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$id = Request::getInt('discussion', 0);

		$discussion = Discussion::oneOrNew($id);

		if (!$discussion->get('id'))
		{
			throw new Exception(Lang::txt('COM_STORY_DISCUSSION_NOT_FOUND'), 404);
		}

		$response = new stdClass;
		$response->discussion = (int) $discussion->get('id');
		$response->title      = $discussion->get('title');
		$response->comments   = array();

		foreach (Comment::inDiscussion($discussion->get('id'))->rows() as $row)
		{
			$out = new stdClass;
			$out->id      = (int) $row->get('id');
			$out->parent  = (int) $row->get('parent');
			$out->depth   = (int) $row->get('depth');
			$out->subject = $row->get('subject');
			$out->comment = $row->isDeleted() ? '' : $row->get('comment');
			$out->author  = $row->authorName();
			$out->created = $row->get('created');
			$out->score   = (float) $row->get('score');
			$out->deleted = $row->isDeleted();

			$response->comments[] = $out;
		}

		$response->total = count($response->comments);

		$this->send($response);
	}
}
