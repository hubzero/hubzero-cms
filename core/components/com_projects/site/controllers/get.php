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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Projects\Tables;

/**
 * Projects Public links controller class
 */
class Get extends SiteController
{
	/**
	 * Pub view for project files, notes etc.
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		require_once(PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'publicstamp.php');

		// Incoming
		$stamp = Request::getVar( 's', '' );

		// Clean up stamp value (only numbers and letters)
		$regex  = array('/[^a-zA-Z0-9]/');
		$stamp  = preg_replace($regex, '', $stamp);

		// Load item reference
		$objSt = new Tables\Stamp( $this->database );
		if (!$stamp || !$objSt->loadItem($stamp))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
			return;
		}

		// Can only serve files or notes at the moment
		if (!in_array($objSt->type, array('files', 'notes', 'publications')))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
			return;
		}

		// Serve requested item
		$content = Event::trigger('projects.serve', array($objSt->type, $objSt->projectid, $objSt->reference));

		// Output
		foreach ($content as $out)
		{
			if ($out)
			{
				return $out;
			}
		}

		// Redirect if nothing fetched
		App::redirect(
			Route::url('index.php?option=' . $this->_option)
		);
	}
}
