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

namespace Modules\Featuredblog;

use Hubzero\Module\Module;
use Components\Blog\Tables\Entry;
use JFactory;

/**
 * Module class for displaying a random, featured blog entry
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}

	/**
	 * Build module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		include_once(PATH_CORE . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'entry.php');

		$database = JFactory::getDBO();

		$this->row = null;

		$filters = array(
			'limit'      => 1,
			'show'       => trim($this->params->get('show')),
			'start'      => 0,
			'state'      => 'public',
			'sort'       => "RAND()",
			'sort_Dir'   => '',
			'search'     => '',
			'scope'      => 'member',
			'scope_id'   => 0,
			'authorized' => false
		);

		$mp = new Entry($database);

		// Did we have a result to display?
		if ($row = $mp->find('one', $filters))
		{
			$this->row = $row;
			$this->cls = trim($this->params->get('moduleclass_sfx'));
			$this->txt_length = trim($this->params->get('txt_length'));

			require $this->getLayoutPath();
		}
	}
}

