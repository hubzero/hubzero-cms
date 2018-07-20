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

namespace Components\Citations\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Citations\Models\Format as CitationFormat;
use Request;
use Notify;
use Lang;
use App;

/**
 * Controller class for citation format
 */
class Format extends AdminController
{
	/**
	 * List formats
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// get the first item, will use as default if not set.
		$firstResult = CitationFormat::all()
			->where('style', 'NOT LIKE', 'custom-group-%')
			->limit(1)
			->row();

		// see if the component config has a value.
		if ($this->config->get('default_citation_format') != null)
		{
			$currentFormat = CitationFormat::all()
				->where('style', 'LIKE', strtolower($this->config->get('default_citation_format')))
				->limit(1)
				->row();
		}
		else
		{
			$currentFormat = $firstResult;
		}

		// Get formatter object
		$formats = CitationFormat::all();

		// Output the HTML
		$this->view
			->set('currentFormat', $currentFormat)
			->set('formats', $formats)
			->display();
	}

	/**
	 * Save a format
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		//get format
		$format = Request::getArray('citationFormat', array());

		// create or update custom format
		$model = CitationFormat::oneOrNew($format['id']);

		if ($model->style == 'Hub Custom' || $model->isNew() === true)
		{
			$model->set(array(
				'style'  => 'Hub Custom',
				'format' => \Hubzero\Utility\Sanitize::clean($format['format'])
			));
		}
		else
		{
			$model->set(array(
				'format' => \Hubzero\Utility\Sanitize::clean($format['format'])
			));
		}

		if (!$model->save())
		{
			// redirect with error message
			Notify::error($model->getError());
		}
		else
		{
			Notify::success(Lang::txt('CITATION_FORMAT_SAVED') . ' ' . $model->style);
		}

		// successfully set the default value, redirect
		$this->cancelTask();
	}
}
