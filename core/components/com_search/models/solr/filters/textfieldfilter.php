<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Solr\Filters;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;

/**
 * Database model for filters of the type list
 *
 * @uses  \Hubzero\Database\Relational
 */
class Textfieldfilter extends Filter
{
	/**
	 * Render form fields on the filter list
	 *
	 * @param   array   $counts  counts retrieved from solr search
	 * @param   string   $textValue  list of options currently selected
	 * @return  string
	 */
	public function renderHtml($counts, $textValue = '')
	{
		$textValue = empty($textValue) ? '' : htmlentities($textValue);
		$html = '<ul><li><fieldset class="search-filters"><legend>' . $this->label . '</legend>';
		$html .= '<input type="text" class="" name="filters[' .
			$this->field . ']" value="' . $textValue . '"/>';
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
		$filterField = strtolower($this->field);
		$textValue = isset($selectedFilters[$filterField]) ? $selectedFilters[$filterField] : '';
		if (empty($textValue))
		{
			return false;
		}
		if ((strpos($textValue, ' AND ') === false) &&
			(strpos($textValue, ' OR ') === false) &&
			(strpos($textValue, '"') === false))
		{
			$textValue = str_replace(' ', ' AND ', $textValue);
		}
		$facetString = '(' . $filterField . ':(' . $textValue . '))';
		$queryName = ucfirst($filterField) . '_' . $this->get('id');
		$query->addFilter($queryName, $facetString, array(strtolower($filterField) . '_type'));
		return true;
	}
}
