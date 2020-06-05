<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros\Group;

require_once dirname(__DIR__) . '/group.php';

use Plugins\Content\Formathtml\Macros\GroupMacro;

/**
 * Group events Macro
 */
class DefaultHomePage extends GroupMacro
{
	/**
	 * Allow macro in partial parsing?
	 *
	 * @var string
	 */
	public $allowPartial = false;

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['html']  = '<p>Displays group members.</p>';
		$txt['html'] .= '<p>Examples:</p>
							<ul>
								<li><code>[[Group.DefaultHome()]]</code></li>
							</ul>';

		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		// check if we can render
		if (!parent::canRender())
		{
			return \Lang::txt('[This macro is designed for Groups only]');
		}

		// add required helper lib
		require_once \Component::path('com_groups') . DS . 'helpers' . DS . 'pages.php';

		// get default home page 
		$html = \Components\Groups\Helpers\Pages::getDefaultHomePage($this->group);

		//return rendered events
		return $html;
	}
}
