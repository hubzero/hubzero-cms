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

namespace Modules\Search;

use Hubzero\Module\Module;
use Document;
use Request;
use Config;
use Route;
use Lang;

/**
 * Module class for displaying a search form
 */
class Helper extends Module
{
	/**
	 * Number of instances of the module
	 *
	 * @var  integer
	 */
	public static $instances = 0;

	/**
	 * Display the search form
	 *
	 * @return  void
	 */
	public function display()
	{
		self::$instances++;

		if ($this->params->get('opensearch', 0))
		{
			$ostitle = $this->params->get('opensearch_title', Lang::txt('MOD_SEARCH_SEARCHBUTTON_TEXT') . ' ' . Config::get('sitename'));

			Document::addHeadLink(
				Request::base() . Route::url('&option=com_search&format=opensearch'),
				'search',
				'rel',
				array('title' => htmlspecialchars($ostitle), 'type' => 'application/opensearchdescription+xml')
			);
		}

		//$upper_limit = Lang::getUpperLimitSearchWord();
		//$maxlength   = $upper_limit;

		$params          = $this->params;
		$button          = $this->params->get('button', '');
		$button_pos      = $this->params->get('button_pos', 'right');
		$button_text     = htmlspecialchars($this->params->get('button_text', Lang::txt('MOD_SEARCH_SEARCHBUTTON_TEXT')));
		$width           = intval($this->params->get('width', 20));
		$text            = htmlspecialchars($this->params->get('text', Lang::txt('MOD_SEARCH_SEARCHBOX_TEXT')));
		$label           = htmlspecialchars($this->params->get('label', Lang::txt('MOD_SEARCH_LABEL_TEXT')));
		$moduleclass_sfx = htmlspecialchars($this->params->get('moduleclass_sfx'));

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}
}
