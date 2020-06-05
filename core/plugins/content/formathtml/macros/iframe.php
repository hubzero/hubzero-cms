<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;

/**
 * Macro class for displaying an Iframe
 */
class Iframe extends Macro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		// use host for example, that way  its not block
		$host = 'https://' . \Request::getString('HTTP_HOST', '', 'server');

		$txt = array();
		$txt['wiki'] = 'Embeds an Iframe into the Page';
		$txt['html'] = '<p>Embeds an iframe into the page.</p>
						<p>Examples:</p>
						<ul>
							<li><code>[[Iframe(' . $host . ')]]</code></li>
							<li><code>[[Iframe(' . $host . ', 640, 380)]] - width 640px, height 380px</code></li>
						</ul>
						<p>Displays:</p>
						<iframe src="'. $host.'" width="640px" height="380px" border="0"></iframe>';

		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		//get the args passed in
		$content = $this->args;

		// defaults
		$default_width = 640;
		$default_height = 380;

		// args will be null if the macro is called without parenthesis.
		if (!$content)
		{
			return '';
		}

		// split up the args
		$args = array_map('trim', explode(',', $content));
		$url  = $args[0];

		// did user pass width/height args
		$width  = (isset($args[1]) && $args[1] != '') ? $args[1] : $default_width;
		$height = (isset($args[2]) && $args[2] != '') ? $args[2] : $default_height;

		//return the emdeded youtube video
		return '<iframe src="' . $url . '" width="' . $width . '" height="' . $height . '" frameborder="0" allowfullscreen="true" allowtransparency="true"></iframe>';
	}
}
