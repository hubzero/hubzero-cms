<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Solr\Filters;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Date;

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
	 * @param   array   $dateValues  list of options currently selected
	 * @return  string
	 */
	public function renderHtml($counts, $dateValues)
	{
		$minDate = $this->params->get('minDate');
		$maxDate = $this->params->get('maxDate');
		$minDateString = !empty($minDate) ? 'data-mindate="' . htmlspecialchars((string) $minDate, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" ' : '';
		$maxDateString = !empty($maxDate) ? 'data-maxdate="' . htmlspecialchars((string) $maxDate, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" ' : '';
		// is_scalar, because these come from Request::getArray('filters') and
		// nothing coerces the leaves: filters[<field>][startdate][]=x hands an
		// array to htmlspecialchars, which is a TypeError on PHP 8 and takes
		// the search page down for an unauthenticated visitor.
		//
		// ENT_SUBSTITUTE, because naming ENT_QUOTES alone drops the default
		// PHP 8.1 added, and a stored value with invalid UTF-8 would then
		// render as an empty string instead of rendering.
		$startdate = (isset($dateValues['startdate']) && is_scalar($dateValues['startdate']))
			? htmlspecialchars((string) $dateValues['startdate'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '';
		$enddate = (isset($dateValues['enddate']) && is_scalar($dateValues['enddate']))
			? htmlspecialchars((string) $dateValues['enddate'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '';
		$html = '<ul><li><fieldset class="search-filters"><legend>' . htmlspecialchars((string) $this->label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</legend>';
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
		// is_scalar for the same reason as renderHtml() above, on the same
		// unauthenticated request: filters[<field>][startdate][]=x makes this an
		// array, and Date::of(array) is a TypeError on PHP 8.
		// Date::of() throws on a string DateTime cannot parse; an open bound is
		// the right answer for garbage, not an exception page.
		$startdate = '*';
		$enddate   = '*';
		try
		{
			if (!empty($selectedValues['startdate']) && is_scalar($selectedValues['startdate']))
			{
				$startdate = Date::of((string) $selectedValues['startdate'])->format('Y-m-d\TH:i:s.999\Z');
			}
		}
		catch (\Exception $e)
		{
			$startdate = '*';
		}
		try
		{
			if (!empty($selectedValues['enddate']) && is_scalar($selectedValues['enddate']))
			{
				$enddate = Date::of((string) $selectedValues['enddate'])->format('Y-m-d\TH:i:s.999\Z');
			}
		}
		catch (\Exception $e)
		{
			$enddate = '*';
		}
		$facetString = '(' . $filterField . ':[' . $startdate . ' TO ' . $enddate . '])';
		$query->addFilter($queryName, $facetString, array(strtolower($filterField) . '_type'));
		return true;
	}
}
