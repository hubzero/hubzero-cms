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

namespace Components\Search\Models\Solr;

use Hubzero\Database\Relational;

/**
 * Database model for search blacklist
 *
 * @uses  \Hubzero\Database\Relational
 */
class Facet extends Relational
{
	/**
	 * Table name
	 * 
	 * @var  string
	 */
	protected $table = '#__solr_search_facets';

	/**
	 * children 
	 * 
	 * @return  object
	 */
	public function children()
	{
		return $this->oneToMany('Facet', 'parent_id');
	}

	/**
	 * toplevel 
	 * 
	 * @return void
	 */
	public function toplevel()
	{
		if (!$this->isNew())
		{
			$tops = $this->all()
				->where('id', '!=', $this->id)
				->rows();
		}
		else
		{
			$tops = $this->all()
				->rows();
		}

		return $tops;
	}

	/**
	 * Convert facet name to solr query safe name
	 *
	 * @return  string  name of query
	 */
	public function getQueryName()
	{
		$name = str_replace(' ', '_', $this->name);
		return $name;
	}

	/**
	 * Get parent facet
	 *
	 * @return  object  Components\Search\Models\Solr\Facet 
	 */
	public function parentFacet()
	{
		return $this->oneToOne('Facet', 'id', 'parent_id');
	}

	/**
	 * Checks if current Facet has a parent
	 *
	 * @return  boolean
	 */
	public function hasParent()
	{
		if ($this->parentFacet->get('id') > 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Automatically merge current facet string with parent
	 *
	 * @return  string
	 */
	public function transformFacet()
	{
		if ($this->hasParent())
		{
			$facet = '(' . $this->parentFacet->facet . ') AND (' . $this->get('facet') . ')';
		}
		else
		{
			$facet = $this->get('facet');
		}
		return $facet;
	}

	/**
	 * Build HTML list of current item and its nested children
	 *
	 * @param   array   $counts      prefetched solr array of counts of all facets
	 * @param   int     $activeType  id of currently selected facet
	 * @param   string  $terms       search terms currently applied ot the search
	 * @param   string  $childTerms  any currently applied filters
	 * @return  string  HTML list with links to apply a facet with currently selected searchTerms
	 */
	public function formatWithCounts($counts, $activeType = null, $terms = null, $childTerms = null)
	{
		$countIndex = $this->getQueryName();
		$count = isset($counts[$countIndex]) ? $counts[$countIndex] : 0;

		$html = '';

		if ($count > 0)
		{
			$class = ($activeType == $this->id) ? 'class="active"' : '';
			$link = Route::url('index.php?option=com_search&terms=' . $terms . '&type=' . $this->id . $childTerms);

			$html .= '<li><a ' . $class . ' href="' . $link . '" data-type=' . $this->id . '>';
			$html .= $this->name . '<span class="item-count">' . $count . '</span></a>';
			if ($this->children->count() > 0)
			{
				$html .= '<ul>';
				foreach ($this->children as $child)
				{
					$html .= $child->formatWithCounts($counts, $activeType, $terms, $childTerms);
				}
				$html .= '</ul>';
			}
			$html .= '</li>';
		}

		return $html;
	}
}
