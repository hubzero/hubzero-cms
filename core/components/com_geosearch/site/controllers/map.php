<?php
/**
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
 * @author    Brandon Beatty, Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Components\Geosearch\Tables;

defined('_HZEXEC_') or die();

/**
 * Display HABRI Members on Google Map
 */
class GeosearchControllerMap extends \Hubzero\Component\SiteController
{
	/**
	 * display
	 */
	public function displayTask()
	{
		Pathway::append(
			Lang::txt('COM_GEOSEARCH_TITLE'),
			'index.php?option=' . $this->_option
		);

		Document::setTitle(Lang::txt('COM_GEOSEARCH_TITLE'));

		$filters          = array();
		$filters['limit'] = 1000; //Request::getInt('limit', 1000, 'request');
		$filters['start'] = 0; //Request::getInt('limitstart', 0, 'request');
		$resources        = Request::getVar('resource', '', 'request');
		$tags             = trim(Request::getString('tags', '', 'request'));
		$distance         = Request::getInt('distance', '', 'request');
		$location         = Request::getVar('location', '', 'request');
		$unit             = Request::getVar('dist_units', '', 'request');

		$this->view->display();
	}


	/**
	 * get marker coordinates
	 */
	public function getmarkersTask()
	{
		$checked = Request::getVar('checked', array(), 'request');
		$tags = trim(Request::getString('tags', '', 'request'));
		$resources = Request::get('resources', array());

		$filters = array();
		$filters['scope'] = $resources;

		// get markers object
		$GM = new \Components\Geosearch\Tables\GeosearchMarkers($this->database);

		echo $GM->getMarkers($filters);
		exit();
	}

	public function reportMarkerTask()
	{
		$id = Request::get('markerID', 0);
		$guest = User::isGuest();

		if ($id > 0 && !$guest)
		{
			$db = App::get('db');
			$query = "UPDATE #__geosearch_markers SET review = 1 WHERE id = {$id};";
			$db->setQuery($query);
			$db->query();
		}
		exit();
	}
}
