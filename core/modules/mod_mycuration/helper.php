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

namespace Modules\MyCuration;

use Hubzero\Module\Module;
use Publication;
use Component;

/**
 * Module class for displaying a user's publication curation tasks
 */
class Helper extends Module
{
	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		$database = \App::get('db');
		$config   = Component::params('com_publications');

		// Get some classes we need
		require_once(Component::path('com_publications') . DS . 'tables' . DS . 'publication.php');
		require_once(Component::path('com_publications') . DS . 'tables' . DS . 'master.type.php');

		$this->moduleclass = $this->params->get('moduleclass');

		// Build query
		$filters = array(
			'limit'         => intval($this->params->get('limit', 10)),
			'start'         => 0,
			'sortby'        => 'title',
			'sortdir'       => 'ASC',
			'ignore_access' => 1,
			'curator'       => 'owner',
			'dev'           => 1, // get dev versions
			'status'        => array(5, 7) // submitted/pending
		);

		// Instantiate
		$objP = new \Components\Publications\Tables\Publication($database);

		// Assigned curation
		$this->rows = $objP->getRecords($filters);

		require $this->getLayoutPath();
	}
}
