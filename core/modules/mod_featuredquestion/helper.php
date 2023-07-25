<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Featuredquestion;

use Hubzero\Module\Module;
use Components\Answers\Models\Question;
use Component;

/**
 * Module class for displaying a random featured question
 */
class Helper extends Module
{
	/**
	 * Generate module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		require_once Component::path('com_answers') . DS . 'models' . DS . 'question.php';

		// randomly choose one
		$rows = Question::all()
			->select('id')
			->whereEquals('state', 0)
			->ordered()
			->rows()
			->toArray();

		$key = array_rand($rows);
		$row = Question::oneOrNew($rows[$key]['id']);

		// Did we have a result to display?
		if ($row->get('id'))
		{
			$this->cls = trim($this->params->get('moduleclass_sfx',''));
			$this->txt_length = trim($this->params->get('txt_length',''));

			$this->row = $row;

			$config = Component::params('com_answers');

			$this->thumb = DS . trim($this->params->get('defaultpic', '/core/modules/mod_featuredquestion/assets/img/question_thumb.gif'), DS);
			if ($this->thumb == '/modules/mod_featuredquestion/question_thumb.gif')
			{
				$this->thumb = '/core/modules/mod_featuredquestion/assets/img/question_thumb.gif';
			}

			require $this->getLayoutPath();
		}
	}

	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}
}
