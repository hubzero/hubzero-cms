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

namespace Modules\Tools;

use Hubzero\Module\Module;
use Components\Resources\Models\Entry;

include_once \Component::path('com_resources') . DS . 'models' . DS . 'entry.php';

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
	private function _getTools()
	{
		// Build query
		$filters = array();
		$filters['now'] = date('Y-m-d H:i:s', time() + 0 * 60 * 60);
		$filters['sortby'] = 'date';
		$filters['access'] = 'all';
		$filters['authorized'] = '';
		$filters['select'] = 'records';
		$filters['limit'] = $this->limit;
		$filters['limitstart'] = 0;
		$filters['type'] = 7; // Tools
		$filters['published'] = \Components\Resources\Models\Entry::STATE_PUBLISHED;

		if ($this->featured)
		{					
			$filters['tag'] = 'featured';
		}

		$rows = \Components\Resources\Models\Entry::allWithFilters($filters);
        
		return $rows;
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
		$this->featured = $this->params->get('featured', 0);
		$this->logosonly = $this->params->get('view', 0);

		// Get the partners
		$this->tools = $this->_getTools();

		require $this->getLayoutPath();
	}
}
