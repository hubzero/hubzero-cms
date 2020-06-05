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
						<li><code>[[Redirect(http://google.com)]]</code></li>
						<li><code>[[Redirect(http://google.com, 5)]]</code> - Wait 5 seconds before redirecting.</li>
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
		$delay = intval($delay);

		// No delay time? Redirect now.
		if (!$delay)
		{
			return '<script type="text/javascript">
					window.onload = function () {
						window.location.href = "' . str_replace(array("'", '"'), array('%27', '%22'), $url) . '";
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
								window.location.href = "' . str_replace(array("'", '"'), array('%27', '%22'), $url) . '";
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
