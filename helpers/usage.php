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

namespace Components\Publications\Helpers;

include_once(__DIR__ . DS . 'usage' . DS . 'andmore.php');

/**
 * Base class for publication usage
 */
class Usage
{
	/**
	 * JDatabase
	 *
	 * @var object
	 */
	var $_db      = NULL;

	/**
	 * Publication ID
	 *
	 * @var string
	 */
	var $_pubid   = NULL;

	/**
	 * Publication master type
	 *
	 * @var unknown
	 */
	var $_type    = NULL;

	/**
	 * Resource rating
	 *
	 * @var string
	 */
	var $rating   = NULL;

	/**
	 * Number of users
	 *
	 * @var string
	 */
	var $users    = 'unavailable';

	/**
	 * Description for 'datetime'
	 *
	 * @var string
	 */
	var $datetime = NULL;

	/**
	 * Number of citations
	 *
	 * @var integer
	 */
	var $cites    = NULL;

	/**
	 * Last citation date
	 *
	 * @var string
	 */
	var $lastcite = NULL;

	/**
	 * Date format
	 *
	 * @var string
	 */
	var $dateFormat = NULL;

	/**
	 * Constructor
	 *
	 * @param      object  &$db      JDatabase
	 * @param      integer $pubid    Resource ID
	 * @param      integer $type     Resource type
	 * @param      integer $rating   Resource rating
	 * @param      integer $cites    Number of citations
	 * @param      string  $lastcite Last citation date
	 * @return     void
	 */
	public function __construct(&$db, $pubid, $type = 'files', $rating=0, $cites=0, $lastcite='')
	{
		$this->_db 		= $db;
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
	 * @param      string $disp Data time range ti display
	 * @return     array
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

		$sql = "SELECT * FROM #__publication_stats WHERE publication_id=" . $this->_pubid . "
			AND period=" . (int) $period . " ORDER BY datetime DESC LIMIT 1";

		$this->_db->setQuery($sql);
		$result = $this->_db->loadObjectList();

		$this->process($result);

		return array($caption, $period);
	}

	/**
	 * Display formatted results for a given time range
	 *
	 * @param      string $disp Time range [curr, last, year, all]
	 * @return     string
	 */
	public function display($disp)
	{
		return '';
	}

	/**
	 * Display a table for ancillary data
	 *
	 * @return     string HTML
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
	 * @param      array &$result Database records
	 * @return     boolean False if errors, true on success
	 */
	public function process($results)
	{
		return true;
	}

	/**
	 * Format a value
	 *
	 * @param      mixed $val Parameter description (if any) ...
	 * @return     mixed
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
	 * @param      integer $rating Rating (out of 5 total)
	 * @return     string
	 */
	public function getRatingClass($rating=0)
	{
		switch ($rating)
		{
			case 0.5: $class = ' half-stars';      break;
			case 1:
			case 1.0: $class = ' one-stars';       break;
			case 1.5: $class = ' onehalf-stars';   break;
			case 2:
			case 2.0: $class = ' two-stars';       break;
			case 2.5: $class = ' twohalf-stars';   break;
			case 3:
			case 3.0: $class = ' three-stars';     break;
			case 3.5: $class = ' threehalf-stars'; break;
			case 4:
			case 4.0: $class = ' four-stars';      break;
			case 4.5: $class = ' fourhalf-stars';  break;
			case 5:
			case 5.0: $class = ' five-stars';      break;
			case 0:
			case 0.0:
			default:  $class = ' no-stars';        break;
		}
		return $class;
	}
}
