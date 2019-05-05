<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * A wiki macro for embedding links
 */
class LinkMacro extends WikiMacro
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
	 * @return     string
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = "Embed a link.

Examples:

{{{
[[Link(/SomePage SomePage)]]
}}}
";
		$txt['html'] = '<p>Embed a link.</p>
<p>Examples:</p>
<ul>
<li><code>[[Link(/SomePage SomePage)]]</code></li>
</ul>';

		return $txt['html'];
	}

	/**
	 * Generate macro output based on passed arguments
	 *
	 * @return     string HTML image tag on success or error message on failure
	 */
	public function render()
	{
		$content = $this->args;

		// args will be null if the macro is called without parenthesis.
		if (!$content)
		{
			return '';
		}

		$cls = 'wiki';

		// Parse arguments
		// We expect the 1st argument to be a filename
		$args  = explode(' ', $content);
		$href  = array_shift($args);
		$title = (count($args) > 0) ? implode(' ', $args) : $href;

		$title = preg_replace('/\(.*?\)/', '', $title);
		$title = preg_replace('/^.*?\:/', '', $title);

		return '<a class="' . $cls . '" href="' . $href . '">' . trim($title) . '</a>';
	}
}
