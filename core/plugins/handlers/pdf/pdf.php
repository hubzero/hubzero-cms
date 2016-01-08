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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Plugin\Plugin;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin class for pdf file handling
 */
class plgHandlersPdf extends Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Determines if the given collection can be handled by this plugin
	 *
	 * @param   \Hubzero\Filesystem\Collection  $collection  The file collection to assess
	 * @return  void
	 **/
	public function canHandle(\Hubzero\Filesystem\Collection $collection)
	{
		// We can handle 1 pdf file
		$need = [
			'pdf' => 1
		];

		// Check extension to make sure we can proceed
		if (!$collection->hasExtensions($need))
		{
			return false;
		}

		return true;
	}

	/**
	 * Handles view events for pdf files
	 *
	 * @param   \Hubzero\Filesystem\Collection  $collection  The file collection to view
	 * @return  void
	 **/
	public function onHandleView(\Hubzero\Filesystem\Collection $collection)
	{
		if (!$this->canHandle($collection))
		{
			return false;
		}

		// Create view
		$view = new \Hubzero\Plugin\View([
			'folder'  => 'handlers',
			'element' => 'pdf',
			'name'    => 'pdf',
			'layout'  => 'view'
		]);

		$view->file = $collection->findFirstWithExtension('pdf');

		return $view;
	}
}