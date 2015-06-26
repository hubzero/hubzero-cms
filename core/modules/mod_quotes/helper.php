<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 * All rights reserved.
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

namespace Modules\Quotes;

use Hubzero\Module\Module;
use Components\Feedback\Tables\Quote;
use Component;
use Request;
use Date;

/**
 * Module class for displaying quotes
 */
class Helper extends Module
{
	/**
	 * Get module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		require_once(Component::path('com_feedback') . DS . 'tables' . DS . 'quote.php');

		$this->database = \App::get('db');

		//Get the admin configured settings
		$this->filters = array(
			'limit'         => trim($this->params->get('maxquotes')),
			'id'            => Request::getInt('quoteid', 0),
			'notable_quote' => 1
		);

		// Get quotes
		$sq = new Quote($this->database);
		$this->quotes = $sq->find('list', $this->filters);

		$feedbackConfig = Component::params('com_feedback');
		$this->path = trim($feedbackConfig->get('uploadpath', '/site/quotes'), DS) . DS;

		require $this->getLayoutPath($this->module->module);
	}

	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		// Push the module CSS to the template
		$this->css()
		     ->js();

		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}
}
