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

namespace Hubzero\Base\Traits;

use Hubzero\Document\Asset\Image;
use Hubzero\Document\Asset\Javascript;
use Hubzero\Document\Asset\Stylesheet;
use Hubzero\Component\ControllerInterface;
use Hubzero\Plugin\Plugin;
use Hubzero\Module\Module;

/**
 * Asset Aware trait.
 * Adds helpers for pushing CSS and JS assets to the document.
 */
trait AssetAware
{
	/**
	 * Push CSS to the document
	 *
	 * @param   string  $stylesheet  Stylesheet or styles to add
	 * @param   string  $extension   Extension name, e.g.: com_example, mod_example, plg_example_test
	 * @param   string  $element     Plugin element. Only used for plugins and if first argument is folder name.
	 * @return  object
	 */
	public function css($stylesheet = '', $extension = null, $element = null)
	{
		$extension = $extension ?: $this->detectExtensionName();

		$attr = array(
			'type'    => 'text/css',
			'media'   => null,
			'attribs' => array()
		);

		if ($element)
		{
			if (is_string($element))
			{
				$extension = 'plg_' . $extension . '_' . $element;
			}
			else if (is_array($element))
			{
				foreach ($element as $key => $val)
				{
					if (array_key_exists($key, $attr))
					{
						$attr[$key] = $val;
					}
				}
			}
		}

		$asset = new Stylesheet($extension, $stylesheet);

		if ($asset->exists())
		{
			if ($asset->isDeclaration())
			{
				\App::get('document')->addStyleDeclaration($asset->contents());
			}
			else
			{
				\App::get('document')->addStyleSheet($asset->link(), $attr['type'], $attr['media'], $attr['attribs']);
			}
		}
		return $this;
	}

	/**
	 * Push JS to the document
	 *
	 * @param   string  $asset      Script to add
	 * @param   string  $extension  Extension name, e.g.: com_example, mod_example, plg_example_test
	 * @param   string  $element    Plugin element. Only used for plugins and if first argument is folder name.
	 * @return  object
	 */
	public function js($asset = '', $extension = null, $element = null)
	{
		$extension = $extension ?: $this->detectExtensionName();

		$attr = array(
			'type'  => 'text/javascript',
			'defer' => false,
			'async' => false
		);

		if ($element)
		{
			if (is_string($element))
			{
				$extension = 'plg_' . $extension . '_' . $element;
			}
			else if (is_array($element))
			{
				foreach ($element as $key => $val)
				{
					if (array_key_exists($key, $attr))
					{
						$attr[$key] = $val;
					}
				}
			}
		}

		$asset = new Javascript($extension, $asset);

		if ($asset->exists())
		{
			if ($asset->isDeclaration())
			{
				\App::get('document')->addScriptDeclaration($asset->contents());
			}
			else
			{
				\App::get('document')->addScript($asset->link(), $attr['type'], $attr['defer'], $attr['async']);
			}
		}
		return $this;
	}

	/**
	 * Get the path to an image
	 *
	 * @param   string  $asset      Image name
	 * @param   string  $extension  Extension name, e.g.: com_example, mod_example, plg_example_test
	 * @param   string  $element    Plugin element. Only used for plugins and if first argument is folder name.
	 * @return  string
	 */
	public function img($asset, $extension = null, $element = null)
	{
		$extension = $extension ?: $this->detectExtensionName();

		if ($element)
		{
			$extension = 'plg_' . $extension . '_' . $element;
		}

		$asset = new Image($extension, $asset);

		return $asset->link();
	}

	/**
	 * Determine the extension the view is being called from
	 *
	 * @return  string
	 */
	private function detectExtensionName()
	{
		if ($this instanceof Plugin)
		{
			return 'plg_' . $this->_type . '_' . $this->_name;
		}
		else if ($this instanceof ControllerInterface)
		{
			return \Request::getCmd('option', $this->_option);
		}
		else if ($this instanceof Module)
		{
			return $this->module->module;
		}

		return '';
	}
}
