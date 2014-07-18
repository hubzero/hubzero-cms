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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Base class for resource usage
 */
class ResourcesUsage
{
	/**
	 * JDatabase
	 *
	 * @var object
	 */
	var $_db      = NULL;

	/**
	 * Resource ID
	 *
	 * @var string
	 */
	var $_resid   = NULL;

	/**
	 * Resource type
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
	 * @param      integer $resid    Resource ID
	 * @param      integer $type     Resource type
	 * @param      integer $rating   Resource rating
	 * @param      integer $cites    Number of citations
	 * @param      string  $lastcite Last citation date
	 * @return     void
	 */
	public function __construct(&$db, $resid, $type, $rating=0, $cites=0, $lastcite='')
	{
		$this->_db = $db;
		$this->_resid   = $resid;
		$this->_type    = $type;
		$this->rating   = $rating;
		$this->cites    = $cites;
		$this->lastcite = $lastcite;

		$this->dateFormat = JText::_('DATE_FORMAT_HZ1');
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
				$caption = JText::_('Current Month');
			break;

			case 'LAST':
				$period  = 2;
				$caption = JText::_('Last Month');
			break;

			case 'YEAR':
				$period  = 12;
				$caption = JText::_('Last 12 Months');
			break;

			case 'ALL':
			default:
				$period  = 14;
				$caption = JText::_('Total');
			break;
		}

		$sql = "SELECT * FROM #__resource_stats WHERE resid=" . $this->_resid . " AND period=" . (int) $period . " ORDER BY datetime DESC LIMIT 1";

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
		$html  = '<table class="usagestats" summary="Review and Citation statistics for this resource">' . "\n";
		$html .= ' <caption>Reviews &amp; Citations</caption>' . "\n";
		/*$html .= ' <tfoot>' . "\n";
		$html .= '  <tr>' . "\n";
		$html .= '   <td colspan="2">Google/IEEE';
		if ($this->lastcite)
		{
			$html .= ': updated '.JHTML::_('date', $this->lastcite, $this->dateFormat);
		}
		$html .= '</td>' . "\n";
		$html .= '  </tr>' . "\n";
		$html .= ' </tfoot>' . "\n";*/
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
	public function process($result)
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

/**
 * Extended resource stats class (Tools)
 */
class ToolStats extends ResourcesUsage
{
	/**
	 * Number of jobs
	 *
	 * @var string
	 */
	var $jobs     = 'unavailable';

	/**
	 * Average wall time
	 *
	 * @var string
	 */
	var $avg_wall = 'unavailable';

	/**
	 * Total wall time
	 *
	 * @var string
	 */
	var $tot_wall = 'unavailable';

	/**
	 * Average CPU time
	 *
	 * @var mixed
	 */
	var $avg_cpu  = 'unavailable';

	/**
	 * Total CPU time
	 *
	 * @var mixed
	 */
	var $tot_cpu  = 'unavailable';

	/**
	 * Average execution time
	 *
	 * @var string
	 */
	var $avg_exec = 'unavailable';

	/**
	 * Total execution time
	 *
	 * @var string
	 */
	var $tot_exec = 'unavailable';

	/**
	 * Constructor
	 *
	 * @param      object  &$db      JDatabase
	 * @param      integer $resid    Resource ID
	 * @param      integer $type     Resource type
	 * @param      integer $rating   Resource rating
	 * @param      integer $cites    Number of citations
	 * @param      string  $lastcite Last citation date
	 * @return     void
	 */
	public function __construct(&$db, $resid, $type, $rating=0, $cites=0, $lastcite='')
	{
		parent::__construct($db, $resid, $type, $rating, $cites, $lastcite);
	}

	/**
	 * Display formatted results for a given time range
	 *
	 * @param      string $disp Time range [curr, last, year, all]
	 * @return     string
	 */
	public function display($disp='ALL')
	{
		list($caption, $period) = $this->fetch($disp);

		$html = '';
		if ($this->users != 'unavailable' && $this->jobs != 'unavailable' && $this->avg_exec != 'unavailable')
		{
			$html .= '<table class="usagestats" summary="' . JText::_('Statistics for this resource') . '">' . "\n";
			$html .= ' <caption>' . JText::_('Usage Stats') . '</caption>' . "\n";
			$html .= ' <tfoot>' . "\n";
			$html .= '  <tr>' . "\n";
			$html .= '   <td colspan="2">' . $caption;
			if ($this->datetime)
			{
				$html .= ': ' . JText::_('updated') . ' ' . JHTML::_('date', $this->datetime, $this->dateFormat);
			}
			$html .= '</td>' . "\n";
			$html .= '  </tr>' . "\n";
			$html .= ' </tfoot>' . "\n";
			$html .= ' <tbody>' . "\n";
			if ($this->users != 'unavailable')
			{
				$html .= '  <tr>' . "\n";
				$html .= '   <th scope="row">'.JText::_('Users').':</th>' . "\n";
				$html .= '   <td>' . $this->users . '</td>' . "\n";
				$html .= '  </tr>' . "\n";
			}
			if ($this->jobs != 'unavailable')
			{
				$html .= '  <tr>' . "\n";
				$html .= '   <th scope="row">'.JText::_('Jobs').':</th>' . "\n";
				$html .= '   <td>' . $this->jobs . '</td>' . "\n";
				$html .= '  </tr>' . "\n";
			}
			if ($this->avg_exec != 'unavailable')
			{
				$html .= '  <tr>' . "\n";
				$html .= '   <th scope="row"><abbr title="Average">Avg.</abbr> <abbr title="execution">exec.</abbr> time:</th>' . "\n";
				$html .= '   <td>' . $this->valfmt($this->avg_exec) . '</td>' . "\n";
				$html .= '  </tr>' . "\n";
			}
			$html .= ' </tbody>' . "\n";
			$html .= '</table>' . "\n";
		}
		$html .= $this->display_substats();

		return $html;
	}

	/**
	 * Push database results to $this for internal use
	 *
	 * @param      array &$result Database records
	 * @return     boolean False if errors, true on success
	 */
	public function process($result)
	{
		if ($result)
		{
			foreach ($result as $row)
			{
				$this->users    = $row->users;
				$this->jobs     = $row->jobs;
				$this->avg_wall = $row->avg_wall;
				$this->tot_wall = $row->tot_wall;
				$this->avg_cpu  = $row->avg_cpu;
				$this->tot_cpu  = $row->tot_cpu;
				$this->datetime = $row->processed_on;

				// Changed by Swaroop on 06/25/2007: Avg. exec. time = Avg. wall time
				if ($this->avg_cpu == 0)
				{
					$this->avg_exec = $this->avg_wall;
				}
				else
				{
					$this->avg_exec = $this->avg_cpu;
				}
				# $this->avg_exec = $this->avg_wall;

				if ($this->tot_cpu == 0)
				{
					$this->tot_exec = $this->tot_wall;
				}
				else
				{
					$this->tot_exec = $this->tot_cpu;
				}
			}
			return true;
		}
		return false;
	}
}

/**
 * Extended resource stats class (And More)
 */
class AndmoreStats extends ResourcesUsage
{
	/**
	 * Number of views
	 *
	 * @var string
	 */
	var $views    = 'unavailable';

	/**
	 * Average view time
	 *
	 * @var string
	 */
	var $avg_view = 'unavailable';

	/**
	 * Total views
	 *
	 * @var string
	 */
	var $tot_view = 'unavailable';

	/**
	 * Constructor
	 *
	 * @param      object  &$db      JDatabase
	 * @param      integer $resid    Resource ID
	 * @param      integer $type     Resource type
	 * @param      integer $rating   Resource rating
	 * @param      integer $cites    Number of citations
	 * @param      string  $lastcite Last citation date
	 * @return     void
	 */
	public function __construct(&$db, $resid, $type, $rating=0, $cites=0, $lastcite='')
	{
		parent::__construct($db, $resid, $type, $rating, $cites, $lastcite);
	}

	/**
	 * Display formatted results for a given time range
	 *
	 * @param      string $disp Time range [curr, last, year, all]
	 * @return     string
	 */
	public function display($disp='ALL')
	{
		list($caption, $period) = $this->fetch($disp);

		if ($this->_type == 1)
		{
			$vlabel  = JText::_('Views');
			$avlabel = JText::_('Avg. view time');
		}
		else
		{
			$vlabel = JText::_('Downloads');
			$avlabel = JText::_('Avg. downloads');
		}

		$html = '';
		if ($this->users != 'unavailable' && $this->avg_view != 'unavailable')
		{
			$html .= '<table class="usagestats" summary="' . JText::_('Statistics for this resource') . '">' . "\n";
			$html .= ' <caption>'.JText::_('Usage Stats') . '</caption>' . "\n";
			$html .= ' <tfoot>' . "\n";
			$html .= '  <tr>' . "\n";
			$html .= '   <td colspan="2">' . $caption;
			if ($this->datetime)
			{
				$html .= ': ' . JText::_('updated') . ' ' . JHTML::_('date', $this->datetime, $this->dateFormat);
			}
			$html .= '</td>' . "\n";
			$html .= '  </tr>' . "\n";
			$html .= ' </tfoot>' . "\n";
			$html .= ' <tbody>' . "\n";
			if ($this->users != 'unavailable')
			{
				$html .= '  <tr>' . "\n";
				$html .= '   <th scope="row">'.JText::_('Users').':</th>' . "\n";
				$html .= '   <td>' . $this->users . '</td>' . "\n";
				$html .= '  </tr>' . "\n";
			}
			/*if ($this->views != 'unavailable')
			{
				$html .= '  <tr>' . "\n";
				$html .= '   <th scope="row">'.$vlabel.':</th>' . "\n";
				$html .= '   <td>'.$this->views.'</td>' . "\n";
				$html .= '  </tr>' . "\n";
			}
			if ($this->avg_view != 'unavailable')
			{
				$html .= '  <tr>' . "\n";
				$html .= '   <th scope="row">'.$avlabel.':</th>' . "\n";
				$html .= '   <td>'.$this->valfmt($this->avg_view).'</td>' . "\n";
				$html .= '  </tr>' . "\n";
			}*/
			$html .= ' </tbody>' . "\n";
			$html .= '</table>' . "\n";
		}
		$html .= $this->display_substats();

		return $html;
	}

	/**
	 * Push database results to $this for internal use
	 *
	 * @param      array &$result Database results
	 * @return     void
	 */
	public function process($result)
	{
		if ($result)
		{
			foreach ($result as $row)
			{
				$this->users    = $row->users;
				$this->views    = $row->jobs;
				$this->datetime = $row->processed_on;

				if ($row->avg_cpu == 0)
				{
					$this->avg_view = $row->avg_wall;
				}
				else
				{
					$this->avg_view = $row->avg_cpu;
				}

				if ($row->tot_cpu == 0)
				{
					$this->tot_view = $row->tot_wall;
				}
				else
				{
					$this->tot_view = $row->tot_cpu;
				}
			}
		}
	}
}

