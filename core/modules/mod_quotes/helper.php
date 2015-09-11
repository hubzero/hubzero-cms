<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
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

namespace Modules\Quotes;

use Hubzero\Module\Module;
use Components\Feedback\Tables\Quote;
use Component;
use Request;
use Date;

/**
 * Module class for displaying quotes
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
		require_once(Component::path('com_feedback') . DS . 'tables' . DS . 'quote.php');

		$this->database = \App::get('db');

		//Get the admin configured settings
		$this->filters = array(
			'limit'         => trim($this->params->get('maxquotes')),
			'id'            => Request::getInt('quoteid', 0),
			'notable_quote' => 1
		);

		// Get quotes
		$sq = new Quote($this->database);
		$this->quotes = $sq->find('list', $this->filters);

		$feedbackConfig = Component::params('com_feedback');
		$this->path = trim($feedbackConfig->get('uploadpath', '/site/quotes'), DS) . DS;

		require $this->getLayoutPath($this->module->module);
	}

	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		// Push the module CSS to the template
		$this->css()
		     ->js();

		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}
}
