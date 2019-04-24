<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\PopularQuestions;

use Hubzero\Module\Module;
use Components\Answers\Models\Question;
use Components\Answers\Models\Tags;
use Component;
use Request;
use Date;

/**
 * Module class for displaying popular questions
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
		$this->cssId    = $this->params->get('cssId');
		$this->cssClass = $this->params->get('cssClass');

		$this->tag   = Request::getString('tag', '', 'get');
		$this->style = Request::getString('style', '', 'get');

		require_once Component::path('com_answers') . DS . 'models' . DS . 'question.php';

		$records = Question::all();

		switch ($this->params->get('state', 'open'))
		{
			case 'open':
				$records->whereEquals('state', 0);
				break;
			case 'closed':
				$records->whereEquals('state', 1);
				break;
			case 'both':
			default:
				$records->where('state', '<', 2);
				break;
		}

		if ($this->tag)
		{
			$cloud = new Tags();
			$tags = $cloud->parse($this->tag);

			$records
				->select('#__answers_questions.*')
				->join('#__tags_object', '#__tags_object.objectid', '#__answers_questions.id')
				->join('#__tags', '#__tags.id', '#__tags_object.tagid')
				->whereEquals('#__tags_object.tbl', 'answers')
				->whereIn('#__tags.tag', $tags);
		}

		$this->rows = $records
			->limit(intval($this->params->get('limit', 5)))
			->order('helpful', 'desc')
			->ordered()
			->rows();

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
