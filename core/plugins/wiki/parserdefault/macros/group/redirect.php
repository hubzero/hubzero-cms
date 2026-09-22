<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parserdefault\Macros\Group;

require_once dirname(__DIR__) . DS . 'group.php';

use Plugins\Wiki\Parserdefault\Macros\GroupMacro;

/**
 * Group events Macro
 */
class Redirect extends GroupMacro
{
	/**
	 * Allow macro in partial parsing?
	 *
	 * @var string
	 */
	public $allowPartial = true;

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return  array
	 */
	public function description()
	{
		return '<p>Redirects to a URL with an optional delay (in seconds).</p>
				<p>Examples:</p>
					<ul>
						<li><code>[[Group.Redirect(https://google.com)]]</code></li>
						<li><code>[[Group.Redirect(https://google.com, 5)]]</code> - Wait 5 seconds before redirecting.</li>
					</ul>';
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		// Check if we can render
		if (!parent::canRender())
		{
			return \Lang::txt('[This macro is designed for Groups only]');
		}

		// Get the arguments
		$args = $this->getArgs();

		// No arguments passed? Can't do anything.
		if (empty($args))
		{
			return;
		}

		// Clean up the args
		$args = array_map('trim', $args);
		@list($url, $delay) = $args;

		// As the site Redirect macro: the target is a macro argument, so a
		// javascript: or data: URL here navigates the page to script the author
		// supplied. Test a control-character-stripped copy, because browsers
		// ignore those inside a scheme ("java\tscript:" would otherwise pass).
		$chk = preg_replace('/[\x00-\x20]+/', '', (string) $url);

		if (preg_match('#^[a-z][a-z0-9+.-]*:#i', $chk) && !preg_match('#^https?://#i', $chk))
		{
			$url = '';
		}

		// Nothing safe to redirect to (an empty target would reload forever)
		if ($url === '' || $url === null)
		{
			return '';
		}

		$delay = intval($delay);

		// No delay time? Redirect now.
		if (!$delay)
		{
			return \App::redirect($url);
		}

		// Delayed redirect. json_encode is the correct sink for a JS string
		// literal; the quote replacement it replaces left the value inside a
		// double-quoted string where a backslash or newline still broke out.
		return '<script type="text/javascript">setTimeout(function () { window.location.href = ' . json_encode((string) $url, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . '; }, ' . ($delay * 1000) . ');</script>
				<p class="warning">' . \Lang::txt('This page will redirect in %s seconds', $delay) . '</p>';
	}
}
