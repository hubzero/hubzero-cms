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

namespace Modules\Partners;

use Hubzero\Module\Module;
use Components\Partners\Models\Partner;

include_once \Component::path('com_partners') . DS . 'models' . DS . 'partner.php';

/**
 * Module class for displaying a list of partners
 */
class Helper extends Module
{
	/**
	 * Get groups for a user
	 *
	 * @param   integer  $uid   User ID
	 * @param   string   $type  Membership type to return groups for
	 * @return  array
	 */
	private function _getPartners()
	{
		return Partner::all()->paginated()->ordered();
	}

	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		// Get the module parameters
		$this->moduleclass = $this->params->get('moduleclass');
		$this->limit = intval($this->params->get('limit', 100));
		$this->featured = $this->params->get('featured');

		// Get the partners
		$this->partners = $this->_getPartners();

		require $this->getLayoutPath();
	}
}
