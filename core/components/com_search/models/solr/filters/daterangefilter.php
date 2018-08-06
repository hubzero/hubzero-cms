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
 * @since     2.1.4
 */

namespace Components\Search\Models\Solr\Filters;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;

/**
 * Database model for filters of the type list
 *
 * @uses  \Hubzero\Database\Relational
 */
class Daterangefilter extends Filter
{
	/**
	 * Render form fields on the filter list
	 *
	 * @param   array   $counts  counts retrieved from solr search
	 * @param   array   $selectedOptions  list of options currently selected
	 * @return  string
	 */
	public function renderHtml($counts, $dateValues)
	{
		$minDate = $this->params->get('minDate');
		$maxDate = $this->params->get('maxDate');
		$minDateString = !empty($minDate) ? 'data-mindate="' . $minDate . '" ' : '';
		$maxDateString = !empty($maxDate) ? 'data-maxdate="' . $maxDate . '" ' : '';
		$startdate = isset($dateValues['startdate']) ? $dateValues['startdate'] : '';
		$enddate = isset($dateValues['enddate']) ? $dateValues['enddate'] : '';
		$html = '<ul><li><fieldset class="search-filters"><legend>' . $this->label . '</legend>';
		$html .= '<label>Start Date</label><input type="text" class="option datetimepicker" name="filters[' .
			$this->field . '][startdate]"' . $minDateString . ' value="' . $startdate . '" autocomplete="off"/>';
		$html .= '<label>End Date</label><input type="text" class="input option datetimepicker" name="filters[' .
			$this->field . '][enddate]"' . $maxDateString . ' value="' . $enddate . '" autocomplete="off"/>';
		$html .= '</li></ul></fieldset>';
		return $html;
	}

	/**
	 * Add selected filters to solr query
	 *
	 * @param   object   $query  Solarium object that builds the solr query
	 * @param   array   $selectedFilters  list of options currently selected
	 * @return  string
	 */
	public function applyFilters($query, $selectedFilters)
	{
		$filterField = strtolower($this->get('field'));
		$selectedValues = isset($selectedFilters[$filterField]) ? $selectedFilters[$filterField] : array();
		if (empty($selectedValues))
		{
			return false;
		}
		$queryName = ucfirst($filterField) . '_' . $this->get('id');
		$startdate = !empty($selectedValues['startdate']) ? Date::of($selectedValues['startdate'])->format('Y-m-d\TH:i:s.999\Z') : '*';
		$enddate = !empty($selectedValues['enddate']) ? Date::of($selectedValues['enddate'])->format('Y-m-d\TH:i:s.999\Z') : '*';
		$facetString = '(' . $filterField . ':[' . $startdate . ' TO ' . $enddate . '])';
		$query->addFilter($queryName, $facetString, array(strtolower($filterField) . '_type'));
		return true;
	}
}
