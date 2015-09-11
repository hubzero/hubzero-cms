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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Tags plugin class for citations
 */
class plgTagsCitations extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Retrieve records for items tagged with specific tags
	 *
	 * @param      array   $tags       Tags to match records against
	 * @param      mixed   $limit      SQL record limit
	 * @param      integer $limitstart SQL record limit start
	 * @param      string  $sort       The field to sort records by
	 * @param      mixed   $areas      An array or string of areas that should retrieve records
	 * @return     mixed Returns integer when counting records, array when retrieving records
	 */
	public function onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)
	{
		$response = array(
			'name'    => $this->_name,
			'title'   => Lang::txt('PLG_TAGS_CITATIONS'),
			'total'   => 0,
			'results' => null,
			'sql'     => ''
		);

		if (empty($tags))
		{
			return $response;
		}

		$database = App::get('db');

		$ids = array();
		foreach ($tags as $tag)
		{
			$ids[] = $tag->get('id');
		}
		$ids = implode(',', $ids);

		$now = Date::toSql();

		// Build the query
		$e_count = "SELECT COUNT(f.id) FROM (SELECT e.id, COUNT(DISTINCT t.tagid) AS uniques";

		$e_fields = "SELECT e.id, e.title, e.author, e.booktitle, e.doi, e.published, e.created, e.year, e.month, e.isbn, e.journal, e.url as href,
					'citations' AS section, COUNT(DISTINCT t.tagid) AS uniques, e.volume, e.number, e.type, e.pages, e.publisher ";
		$e_from  = " FROM #__citations AS e, #__tags_object AS t"; //", #__users AS u";
		$e_where = " WHERE t.objectid=e.id AND t.tbl='citations' AND t.tagid IN ($ids)"; //e.uid=u.id AND

		$e_where .= " AND e.published=1 AND e.id!='' ";
		$e_where .= " GROUP BY e.id HAVING uniques=" . count($tags);
		$order_by  = " ORDER BY ";
		switch ($sort)
		{
			case 'title': $order_by .= 'title ASC, created';  break;
			case 'id':    $order_by .= "id DESC";             break;
			case 'date':
			default:      $order_by .= 'created DESC, title'; break;
		}
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		$database->setQuery($e_count . $e_from . $e_where . ") AS f");
		$response['total'] = $database->loadResult();

		if ($areas && $areas == $response['name'])
		{
			$database->setQuery($e_fields . $e_from . $e_where . $order_by);
			$response['results'] = $database->loadObjectList();
		}
		else
		{
			$response['sql'] = $e_fields . $e_from . $e_where;
		}

		return $response;
	}

	/**
	 * Return citation types
	 *
	 * @return     array
	 */
	public static function getTypes()
	{
		static $types;

		if (isset($types))
		{
			return $types;
		}

		require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'type.php');

		$database = App::get('db');

		$ct = new \Components\Citations\Tables\Type($database);
		$types = $ct->getType();

		return $types;
	}

	/**
	 * Static method for formatting results
	 *
	 * @param      object $row Database row
	 * @return     string HTML
	 */
	public static function out($row)
	{
		$row->author    = isset($row->alias)  ? $row->alias  : '';
		$row->booktitle = isset($row->itext)  ? $row->itext  : '';
		$row->doi       = isset($row->ftext)  ? $row->ftext  : '';
		$row->published = isset($row->state)  ? $row->state  : '';
		$row->year      = isset($row->created_by)   ? $row->created_by   : '';
		$row->month     = isset($row->modified)     ? $row->modified     : '';
		$row->isbn      = isset($row->publish_up)   ? $row->publish_up   : '';
		$row->journal   = isset($row->publish_down) ? $row->publish_down : '';
		$row->url       = isset($row->href)   ? $row->href   : '';
		$row->volume    = isset($row->params) ? $row->params : '';
		$row->number    = isset($row->rcount) ? $row->rcount : '';
		$row->type      = isset($row->data1)  ? $row->data1  : '';
		$row->pages     = isset($row->data2)  ? $row->data2  : '';
		$row->publisher = isset($row->data3)  ? $row->data3  : '';

		require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'type.php');
		require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'association.php');
		require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'format.php');
		require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php');
		$config = \Component::params('com_citations');

		switch ($config->get("citation_label", "number"))
		{
			case 'none':   $citations_label_class = 'no-label';     break;
			case 'number': $citations_label_class = 'number-label'; break;
			case 'type':   $citations_label_class = 'type-label';   break;
			case 'both':   $citations_label_class = 'both-label';   break;
		}

		$database = \App::get('db');
		$citationsFormat = new \Components\Citations\Tables\Format($database);
		$template = ($citationsFormat->getDefaultFormat()) ? $citationsFormat->getDefaultFormat()->format : null;

		$formatter = new \Components\Citations\Helpers\Format();
		$formatter->setTemplate( $template );

		// Start building the HTML
		$html  = "\t" . '<li class="citation-entry">' . "\n";
		$html .= "\t\t" . '<p class="title">';

		//are we trying wanting to direct to single citaiton view
		$citationSingleView = $config->get('citation_single_view', 1);
		if ($citationSingleView)
		{
			$html .= '<a href="' . \Route::url('index.php?option=com_citations&task=view&id=' . $row->id) . '">';
		}
		else
		{
			$html .= '<a href="' . \Route::url('index.php?option=com_citations&task=browse&type=' . $row->type . '&year=' . $row->year . '&search=' . \Hubzero\Utility\String::truncate(\Hubzero\Utility\Sanitize::stripAll(stripslashes($row->title)), 50)) . '">';
		}
		$html .= \Hubzero\Utility\String::truncate(\Hubzero\Utility\Sanitize::stripAll(stripslashes($row->title)), 200);
		$html .= '</a></p>'."\n";
		$html .= '<p class="details '. $citations_label_class . '">' . \Lang::txt('PLG_TAGS_CITATION');
		if ($config->get('citation_label', 'number') != 'none')
		{
			$types = self::getTypes();

			$type = '';
			foreach ($types as $t)
			{
				if ($t['id'] == $row->type)
				{
					$type = $t['type_title'];
				}
			}
			$type = ($type != '') ? $type : 'Generic';

			$html .= ' <span>|</span> ' . $type;
		}
		$html .= '</p>';

		require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'citation.php');
		$db = \App::get('db');
		$cc = new \Components\Citations\Tables\Citation($db);
		$cc->load($row->id);

		$html .= '<p>' . $formatter->formatCitation($cc, null, $config->get("citation_coins", 1), $config) . '</p>';
		$html .= "\t" . '</li>'."\n";

		// Return output
		return $html;
	}
}

