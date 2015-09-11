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

namespace Modules\RandomQuote;

use Hubzero\Module\Module;
use Components\Feedback\Tables\Quote;

/**
 * Module class for displaying a random quote
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
		require_once(\Component::path('com_feedback') . DS . 'tables' . DS . 'quote.php');

		$database = \App::get('db');

		//Get the admin configured settings
		$this->charlimit  = $this->params->get('charlimit', 150);
		$this->showauthor = $this->params->get('show_author', 1);
		$this->showall    = $this->params->get('show_all_link', 1);

		$quotesrc = $this->params->get('quotesrc', 'miniquote');

		// Get quotes
		$sq = new Quote($database);
		$quote = $sq->find('one', array(
			'limit'         => 1,
			'notable_quote' => ($this->params->get('quotepool') == 'notable_quotes' ?  1 : 0),
			'miniquote'     => ($quotesrc == 'miniquote' ?  1 : 0),
			'sort'          => 'RAND()',
			'sort_Dir'      => ''
		));

		if ($quote)
		{
			$this->quote_to_show = ($quotesrc == 'miniquote') ? stripslashes($quote->miniquote) : stripslashes($quote->short_quote);
		}
		else
		{
			$this->quote_to_show = '';
		}
		$this->quote = $quote;

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}

	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		// Push some CSS to the template
		$this->css();

		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}
}
