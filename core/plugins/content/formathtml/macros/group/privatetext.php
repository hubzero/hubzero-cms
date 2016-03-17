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

namespace Plugins\Content\Formathtml\Macros\Group;

require_once dirname(__DIR__) . DS . 'group.php';

use Plugins\Content\Formathtml\Macros\GroupMacro;

/**
 * Group private Macro
 */
class Privatetext extends GroupMacro
{
	/**
	 * Allow macro in partial parsing?
	 *
	 * @var  bool
	 */
	public $allowPartial = true;

	/**
	 * Tag set opened?
	 *
	 * @var  bool
	 */
	private $open = false;

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['html']  = '<p>Display private content. Note: Content wrapped in this macro will <strong>only</strong> be displayed to logged-in members of the group.</p>';
		$txt['html'] .= '<p>Examples:</p>
							<ul>
								<li><code>[[Group.Privatetext(start)]]Only members can see this[[Group.Privatetext(end)]]</code></li>
							</ul>';

		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		// check if we can render
		if (!parent::canRender())
		{
			return \Lang::txt('[This macro is designed for Groups only]');
		}

		// get args
		$arg = strtolower($this->getArgument(0));

		if (!$arg && !$this->open)
		{
			return;
		}

		if (in_array($arg, array('start', 'open', 'begin')))
		{
			$this->open = true;

			return '<private>';
		}

		$this->open = false;

		return '</private>';
	}

	/**
	 * Post process text
	 *
	 * @param   string  $text
	 * @return  string
	 */
	public function postProcess($text)
	{
		if (\User::isGuest() || !in_array(\User::get('id'), $this->group->get('members')))
		{
			$text = preg_replace('/<private>(.*?)<\/private>/iusm', '', $text);
		}

		$text = str_replace(array('<private>', '</private>'), '', $text);

		return $text;
	}
}
