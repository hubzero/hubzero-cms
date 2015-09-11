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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Featuredquestion;

use Hubzero\Module\Module;
use Components\Answers\Tables\Question;
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
		require_once(Component::path('com_answers') . DS . 'models' . DS . 'question.php');

		$database = \App::get('db');
		$row = null;

		// randomly choose one
		$filters = array(
			'limit'    => 1,
			'start'    => 0,
			'sortby'   => 'random',
			'tag'      => '',
			'filterby' => 'open',
			'created_before' => gmdate('Y-m-d', mktime(0, 0, 0, gmdate('m'), (gmdate('d')+7), gmdate('Y'))) . ' 00:00:00'
		);

		$mp = new Question($database);

		$rows = $mp->getResults($filters);
		if (count($rows) > 0)
		{
			$row = $rows[0];
		}

		// Did we have a result to display?
		if ($row)
		{
			$this->cls = trim($this->params->get('moduleclass_sfx'));
			$this->txt_length = trim($this->params->get('txt_length'));

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

