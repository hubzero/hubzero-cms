<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Search pages need to use a special paginator that is less specific about how
 * many results there are.
 * The default site paginator says something like "Viewing 20 results out of 433"
 * which is only accurate here in a sense... that is the number of top-level <li>,
 * but some of them have nested results in them, so the actual result count shown
 * at the top of the page might be something like 470. To keep people from
 * wondering where their missing results are we just show them how many pages
 * they have to go through.
 * Sometimes it is possible to see the discrepency by multiplying the number of
 * pages by the per-page setting, but I don't think anyone has ever bothered to
 * to do so.
 */
class SearchPages
{

	/**
	 * Description for 'total'
	 *
	 * @var number
	 */
	private $total, $offset, $limit;

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $total Parameter description (if any) ...
	 * @param      unknown $offset Parameter description (if any) ...
	 * @param      unknown $limit Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($total, $offset, $limit)
	{
		$this->total = $total;
		$this->offset = $offset;
		$this->limit = $limit;
	}

	/**
	 * Short description for 'link_to'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $update Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function link_to($update)
	{
		$get = array();
		foreach (array_merge($_GET, array('limit' => $this->limit, 'limitstart' => $this->offset), $update) as $k=>$v)
		{
			if (!is_array($v) && !in_array($k, array('option', 'Itemid')))
			{
				$get[] = $k . '=' . urlencode($v);
			}
		}

		return '/' . preg_replace('/^com_/', '', $_GET['option']) . '/?' . join('&amp;', $get);
	}

	/**
	 * Short description for 'getListFooter'
	 *
	 * Long description (if any) ...
	 *
	 * @return     mixed Return description (if any) ...
	 */
	public function getListFooter()
	{
		$html = array();
		$html[] = '<div class="search-pages">';
		if ($this->limit != 0)
		{
			$html[] = '<p>Pages: ';
			$current_page = $this->offset / $this->limit + 1;
			$total_pages = ceil($this->total / $this->limit);
			for ($page_num = 1; $page_num < $current_page; ++$page_num)
			{
				$html[] = '<a href="'.$this->link_to(array('limitstart' => $this->limit * ($page_num - 1))).'">'.$page_num.'</a>';
			}
			$html[] = '<strong>'.$current_page.'</strong>';
			for ($page_num = $current_page + 1; $page_num <= $total_pages; ++$page_num)
			{
				$html[] = '<a href="'.$this->link_to(array('limitstart' => $this->limit * ($page_num - 1))).'">'.$page_num.'</a>';
			}
			$html[] = '</p>';
		}
		$html[] = '</div>';

		$html[] = '<div class="search-per-page">';
		$html[] = '<form action="'.$this->link_to(array()).'" method="get">';
		$html[] = '<p>';
		$html[] = '<select class="search-per-page-selector" name="limit">';
		foreach (array('5', '10', '15', '20', '30', '50', '100') as $per_page)
		{
			$html[] = '<option value="'.$per_page.'"'.($per_page == $this->limit ? ' selected="selected"' : '').'>'.$per_page.' results per page</option>';
		}
		$html[] = '<option value="0"'.($this->limit == 0 ? ' selected="select"' : '').'>Show all results</option>';
		$html[] = '</select>';
		$html[] = '<input type="hidden" name="terms" value="'.(array_key_exists('terms', $_GET) ? str_replace('"', '&quot;', $_GET['terms']) : '').'" />';
		$html[] = '<input type="submit" class="search-per-page-submitter" value="Go" />';
		$html[] = '</p>';
		$html[] = '</form>';
		$html[] = '</div>';

		return join("\n", $html);
	}
}

