<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Story\Models\Story;
use Components\Story\Models\Text;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'manager.php';

/**
 * API controller for stories
 */
class Storiesv1_0 extends ApiController
{
	/**
	 * What has been published
	 *
	 * @apiMethod GET
	 * @apiUri    /story/stories/list
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "How many to return",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Where to start",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$limit = Request::getInt('limit', 25);
		$start = Request::getInt('start', 0);

		// Built twice rather than copied: the count and the page are separate
		// questions, and sharing one builder between them depends on whether
		// total() leaves the query alone, which is not worth relying on.
		$response = new stdClass;
		$response->total   = Story::published()->total();
		$response->stories = array();

		$rows = Story::published()
			->order('publish_up', 'desc')
			->start($start)
			->limit($limit)
			->rows();

		foreach ($rows as $row)
		{
			$response->stories[] = $this->brief($row);
		}

		$this->send($response);
	}

	/**
	 * One story, with its prose
	 *
	 * @apiMethod GET
	 * @apiUri    /story/stories/read
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Story identifier",
	 * 		"type":          "integer",
	 * 		"required":      true
	 * }
	 * @return  void
	 */
	public function readTask()
	{
		$id  = Request::getInt('id', 0);
		$row = Story::oneOrNew($id);

		if (!$row->get('id') || $row->get('state') != Story::STATE_PUBLISHED)
		{
			throw new Exception(Lang::txt('COM_STORY_NOT_FOUND'), 404);
		}

		$story = $this->brief($row);
		$text  = $row->text();

		$story->intro   = $text->get('intro');
		$story->body    = $text->get('body');
		$story->related = $text->get('related');

		$this->send($story);
	}

	/**
	 * The shape a story takes over the wire
	 *
	 * Deliberately the same for a listing and for a single story, so a client
	 * that can read one can read the other without a second parser.
	 *
	 * @param   object  $row
	 * @return  object
	 */
	protected function brief($row)
	{
		$out = new stdClass;
		$out->id            = (int) $row->get('id');
		$out->title         = $row->get('title');
		$out->kicker        = $row->get('kicker');
		$out->alias         = $row->get('alias');
		$out->published     = $row->get('publish_up');
		$out->hits          = (int) $row->get('hits');
		$out->comment_count = (int) $row->get('comment_count');
		$out->url           = str_replace('/api', '', rtrim(Request::root(), '/') . '/' . ltrim(Route::url($row->link()), '/'));

		return $out;
	}
}
