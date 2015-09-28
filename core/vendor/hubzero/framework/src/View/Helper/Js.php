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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

use Hubzero\Document\Asset\Javascript;
use Hubzero\Plugin\View as PluginView;
use Document;
use Request;

/**
 * Helper for pushing scripts to the document.
 */
class Js extends AbstractHelper
{
	/**
	 * Push JS to the document
	 *
	 * @param   string  $asset      Script to add
	 * @param   string  $extension  Extension name, e.g.: com_example, mod_example, plg_example_test
	 * @param   string  $element    Plugin element. Only used for plugins and if first argument is folder name.
	 * @return  object
	 */
	public function __invoke($asset = '', $extension = null, $element = null)
	{
		$extension = $extension ?: $this->_extension();

		if ($element)
		{
			$extension = 'plg_' . $extension . '_' . $element;
		}

		$asset = new Javascript($extension, $asset);

		if ($asset->exists())
		{
			if ($asset->isDeclaration())
			{
				Document::addScriptDeclaration($asset->contents());
			}
			else
			{
				Document::addScript($asset->link());
			}
		}
		return $this->getView();
	}

	/**
	 * Determine the extension the view is being called from
	 *
	 * @return  string
	 */
	private function _extension()
	{
		if ($this->getView() instanceof PluginView)
		{
			return 'plg_' . $this->getView()->getFolder() . '_' . $this->getView()->getElement();
		}

		return $this->getView()->get('option', Request::getCmd('option'));
	}
}
