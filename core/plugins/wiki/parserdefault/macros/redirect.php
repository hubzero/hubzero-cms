<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Redirect Macro
 */
class RedirectMacro extends WikiMacro
{
	/**
	 * Allow macro in partial parsing?
	 *
	 * @var  string
	 */
	public $allowPartial = false;

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
						<li><code>[[Redirect(https://google.com)]]</code></li>
						<li><code>[[Redirect(https://google.com, 5)]]</code> - Wait 5 seconds before redirecting.</li>
					</ul>';
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
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

		// Only allow http(s) or site-relative targets; reject javascript:/data: etc.
		// (tested on a copy with control characters removed: browsers ignore them
		// inside a scheme, so "java\tscript:" would otherwise slip through)
		$chk = preg_replace('/[\x00-\x20]+/', '', (string) $url);
		if (preg_match('#^[a-z][a-z0-9+.-]*:#i', $chk) && !preg_match('#^https?://#i', $chk))
		{
			$url = '';
		}

		// Nothing safe to redirect to (an empty target would reload this page forever)
		if ($url === '' || $url === null)
		{
			return '';
		}
		$delay = intval($delay);

		// No delay time? Redirect now.
		if (!$delay)
		{
			return '<script type="text/javascript">
					window.onload = function () {
						window.location.href = ' . json_encode((string) $url, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';
					};
				</script>';
		}

		// Delayed redirect
		return '<script type="text/javascript">
					window.onload = function () {
						var timer = ' . $delay . ';
						setInterval(function () {
							timer--;
							if (timer <= 0) {
								window.location.href = ' . json_encode((string) $url, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';
							}
							document.getElementById("redirectTimer").innerHTML = timer;
						}, 1000);
					};
				</script>
				<p class="warning">' . \Lang::txt('This page will redirect in <span id="redirectTimer">%s</span> seconds', $delay) . '</p>';
	}

	/**
	 * Get macro args
	 *
	 * @return  array  List of arguments
	 */
	protected function getArgs()
	{
		return explode(',', $this->args);
	}
}
