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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\BillboardsDisplay;

use Hubzero\Module\Module;

/**
 * Mod_Billboards helper class, used to query for billboards and contains the display method
 */
class Helper extends Module
{
	/**
	 * Get the list of billboads in the selected collection
	 *
	 * @return retrieved rows
	 */
	private function _getList()
	{
		$db = \App::get('db');

		// Get the correct billboards collection to display from the parameters
		$collection = (int) $this->params->get('collection', 1);

		// Query to grab all the buildboards associated with the selected collection
		// Make sure we only grab published billboards
		$query = 'SELECT b.*, c.*' .
				' FROM #__billboards_billboards as b, #__billboards_collections as c' .
				' WHERE c.id = b.collection_id' .
				' AND published = 1' .
				' AND b.collection_id = ' . $collection .
				' ORDER BY `ordering` ASC';
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	/**
	 * Display method
	 * Used to add CSS for each slide as well as the javascript file(s) and the parameterized function
	 *
	 * @return void
	 */
	public function display()
	{
		$this->css();

		// Get the billboard slides
		$this->slides = $this->_getList();

		// Get the billboard background location from the billboards parameters
		$params = \Component::params('com_billboards');
		$image_location = $params->get('image_location', '/app/site/media/images/billboards/');
		if ($image_location == '/site/media/images/billboards/')
		{
			$image_location = '/app' . $image_location;
		}
		$this->image_location = $image_location;

		foreach ($this->slides as $slide)
		{
			$background = $slide->background_img;
		}

		parent::display();
	}
}