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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class for displaying all available macros and their documentation
 */
class MacrolistMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Displays a list of all installed Wiki macros, including documentation if available. Optionally, the name of a specific macro can be provided as an argument. In that case, only the documentation for that macro will be rendered.';
		$txt['html'] = '<p>Displays a list of all installed Wiki macros, including documentation if available. Optionally, the name of a specific macro can be provided as an argument. In that case, only the documentation for that macro will be rendered.</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		$path = dirname(__FILE__);

		$d = @dir($path);

		$macros = array();

		if ($d)
		{
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file($path . DS . $entry) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html')
				{
					if (preg_match("#php#i", $entry))
					{
						$macros[] = $entry;
					}
				}
			}

			$d->close();
		}

		$m = array();
		foreach ($macros as $f)
		{
			include_once($path . DS . $f);

			$f = str_replace('.php', '', $f);

			$macroname = ucfirst($f) . 'Macro';

			if (class_exists($macroname))
			{
				$macro = new $macroname();

				$macroname = substr($macroname, 0, (strlen($macroname) - 5));
				$m[strtolower($macroname)] = '<dt><a name="' . strtolower($macroname) . '"></a><code>&#91;&#91;' . $macroname . '(args)&#93;&#93;</code></dt><dd>' . $macro->description() . '</dd>';
			}
		}
		asort($m);

		$txt  = '<dl>' . "\n";
		$txt .= implode("\n", $m);
		$txt .= '</dl>' . "\n";

		return $txt;
	}
}

