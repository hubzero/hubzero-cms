<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Citations\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Citations\Helpers;
use Components\Citations\Tables;
use Components\Citations\Models\Format as	CitationFormat;
use Request;
use Route;
use Lang;
use App;

/**
 * Controller class for citation format
 */
class Format extends AdminController
{
	/**
	 * List types
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
		if ($this->config->get('default_citation_format') != NULL)
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

		// set view variable
		$this->view->currentFormat = $currentFormat;

		//get formatter object
		$this->view->formats = CitationFormat::all();

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}


	/**
	 * Save a type
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		//get format
		$format = Request::getVar('citationFormat', array());

		// create or update custom format
		$model = CitationFormat::oneOrNew($format['id']);

		if ($model->style == 'Hub Custom' || $model->isNew() === true)
		{
			$model->set(array(
				'style' => 'Hub Custom',
				'format' => \Hubzero\Utility\Sanitize::clean($format['format'])
				));

			if (!$model->save())
			{
				// redirect with error message
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					Lang::txt('CITATION_FORMAT_NOT_SAVED'),
					'error'
				);
			}

			// after successful save, grab the ID
			$formatID = $model->id;

		}
		else
		{
			$formatID = $model->id;
		}

		// successfully set the default value, redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('CITATION_FORMAT_SAVED')
		);
	}
}

