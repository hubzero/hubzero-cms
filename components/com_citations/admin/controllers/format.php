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
		//get current format
		$citationsFormat = new Tables\Format($this->database);
		$this->view->currentFormat = $citationsFormat->getDefaultFormat();

		//get formatter object
		$cf = new Helpers\Format();
		$this->view->apaFormat  = $cf->getDefaultFormat('apa');
		$this->view->ieeeFormat = $cf->getDefaultFormat('ieee');

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
		Request::checkToken() or exit('Invalid Token');

		//get format
		$format = Request::getVar('format', array());

		$citationsFormat = new Tables\Format($this->database);
		$citationsFormat->save($format);

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('CITATION_FORMAT_SAVED')
		);
	}
}

