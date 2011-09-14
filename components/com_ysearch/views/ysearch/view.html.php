<?php
/**
 * @package     hubzero-cms
 * @author      Steve Snyder <snyder13@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 */

require dirname(__FILE__).'/../../models/search_pages.php';

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class YSearchViewYSearch extends JView
{
	protected $terms, $debug = array(), $results, $app;

	public function set_terms($terms) { $this->terms = $terms; }
	public function set_results($results) { $this->results = $results; }
	public function set_application(&$app) { $this->app =& $app; }

	public function display()
	{
		$this->url_terms = urlencode($this->terms->get_raw_without_section());
		@list($this->plugin, $this->section) = $this->terms->get_section();
		$this->pagination = new SearchPages($this->results->get_plugin_list_count(), $this->results->get_offset(), $this->results->get_limit());
		parent::display();
	}

	protected function attr($key, $val)
	{
		if (!empty($val))
			echo "$key=\"".str_replace('"', '&quot;', $val).'" ';
	}

	protected function html($html) { echo htmlentities($html); }

	public function debug($str)
	{
		$this->debug[] = $str;
	}

	public function debug_var($name, $var)
	{
		$this->debug('<b>'.$name.'</b>: '.var_export($var, true));
	}
}

