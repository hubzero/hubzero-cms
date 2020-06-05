<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
