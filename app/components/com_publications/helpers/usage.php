<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Helpers;

use Lang;
use Date;

include_once __DIR__ . DS . 'usage' . DS . 'andmore.php';

/**
 * Base class for publication usage
 */
class Usage
{
	/**
	 * Database
	 *
	 * @var object
	 */
	public $_db = null;

	/**
	 * Publication ID
	 *
	 * @var string
	 */
	public $_pubid = null;

	/**
	 * Publication master type
	 *
	 * @var unknown
	 */
	public $_type = null;

	/**
	 * Resource rating
	 *
	 * @var string
	 */
	public $rating = null;

	/**
	 * Number of users
	 *
	 * @var string
	 */
	public $users = 'unavailable';

	/**
	 * Description for 'datetime'
	 *
	 * @var string
	 */
	public $datetime = null;

	/**
	 * Number of citations
	 *
	 * @var integer
	 */
	public $cites    = null;

	/**
	 * Last citation date
	 *
	 * @var string
	 */
	public $lastcite = null;

	/**
	 * Date format
	 *
	 * @var string
	 */
	public $dateFormat = null;

	/**
	 * Constructor
	 *
	 * @param   object   &$db       Database
	 * @param   integer  $pubid     Publication ID
	 * @param   integer  $type      Publication type
	 * @param   integer  $rating    Publication rating
	 * @param   integer  $cites     Number of citations
	 * @param   string   $lastcite  Last citation date
	 * @return  void
	 */
	public function __construct(&$db, $pubid, $type = 'files', $rating=0, $cites=0, $lastcite='')
	{
		$this->_db      = $db;
		$this->_pubid   = $pubid;
		$this->_type    = $type;
		$this->rating   = $rating;
		$this->cites    = $cites;
		$this->lastcite = $lastcite;

		$this->dateFormat = 'd M Y';
	}

	/**
	 * Fetch data for a particular time range
	 *
	 * @param   string  $disp  Data time range ti display
	 * @return  array
	 */
	public function fetch($disp)
	{
		switch (strtoupper($disp))
		{
			case 'CURR':
				$period  = 1;
				$caption = Lang::txt('Current Month');
			break;

			case 'LAST':
				$period  = 2;
				$caption = Lang::txt('Last Month');
			break;

			case 'YEAR':
				$period  = 12;
				$caption = Lang::txt('Last 12 Months');
			break;

			case 'ALL':
			default:
				$period  = 14;
				$caption = Lang::txt('Total');
			break;
		}

		$sql = "SELECT * FROM `#__publication_stats` WHERE publication_id=" . $this->_db->quote($this->_pubid) . "
			AND period=" . $this->_db->quote((int) $period) . " ORDER BY datetime DESC LIMIT 1";

		$this->_db->setQuery($sql);
		$result = $this->_db->loadObjectList();

		$this->process($result);

		return array($caption, $period);
	}

	/**
	 * Display formatted results for a given time range
	 *
	 * @param   string  $disp  Time range [curr, last, year, all]
	 * @return  string
	 */
	public function display($disp)
	{
		return '';
	}

	/**
	 * Display a table for ancillary data
	 *
	 * @return  string  HTML
	 */
	public function display_substats()
	{
		$html  = '<table class="usagestats" summary="Review and Citation statistics for this publication">' . "\n";
		$html .= ' <caption>Reviews &amp; Citations</caption>' . "\n";
		$html .= ' <tfoot>' . "\n";
		$html .= '  <tr>' . "\n";
		$html .= '   <td colspan="2">Google/IEEE';
		if ($this->lastcite)
		{
			$html .= ': updated ' . Date::of($this->lastcite)->toLocal($this->dateFormat);
		}
		$html .= '</td>' . "\n";
		$html .= '  </tr>' . "\n";
		$html .= ' </tfoot>' . "\n";
		$html .= ' <tbody>' . "\n";
		$html .= '  <tr>' . "\n";
		$html .= '   <th scope="row"><abbr title="Average">Avg.</abbr> Review:</th>' . "\n";
		$html .= '   <td><span class="avgrating' . $this->getRatingClass($this->rating) . '"><span>' . $this->rating . ' out of 5 stars</span></span></td>' . "\n";
		$html .= '  </tr>' . "\n";
		$html .= '  <tr>' . "\n";
		$html .= '   <th scope="row">Citations:</th>' . "\n";
		$html .= '   <td>' . $this->cites . '</td>' . "\n";
		$html .= '  </tr>' . "\n";
		$html .= ' </tbody>' . "\n";
		$html .= '</table>' . "\n";
		return $html;
	}

	/**
	 * Push database results to $this for internal use
	 *
	 * @param   array    $result  Database records
	 * @return  boolean  False if errors, true on success
	 */
	public function process($results)
	{
		return true;
	}

	/**
	 * Format a value
	 *
	 * @param    mixed  $val
	 * @return   mixed
	 */
	public function valfmt($val)
	{
		if ($val != 'unavailable')
		{
			if ($val <= 60)
			{
				$val = ceil($val) . ' secs';
			}
			else if ($val > 60 && $val <= 3600)
			{
				$val = ceil($val/60) . ' mins';
			}
			else if ($val > 3600 && $val <= 86400)
			{
				$val = ceil($val/3600) . ' hours';
			}
			else
			{
				$val = ceil($val/84600) . ' days';
			}
		}
		return $val;
	}

	/**
	 * Get the classname for a rating value
	 *
	 * @param   integer  $rating  Rating (out of 5 total)
	 * @return  string
	 */
	public function getRatingClass($rating=0)
	{
		switch ($rating)
		{
			case 0.5:
				$class = ' half-stars';
				break;
			case 1:
			case 1.0:
				$class = ' one-stars';
				break;
			case 1.5:
				$class = ' onehalf-stars';
				break;
			case 2:
			case 2.0:
				$class = ' two-stars';
				break;
			case 2.5:
				$class = ' twohalf-stars';
				break;
			case 3:
			case 3.0:
				$class = ' three-stars';
				break;
			case 3.5:
				$class = ' threehalf-stars';
				break;
			case 4:
			case 4.0:
				$class = ' four-stars';
				break;
			case 4.5:
				$class = ' fourhalf-stars';
				break;
			case 5:
			case 5.0:
				$class = ' five-stars';
				break;
			case 0:
			case 0.0:
			default:
				$class = ' no-stars';
				break;
		}
		return $class;
	}
}
