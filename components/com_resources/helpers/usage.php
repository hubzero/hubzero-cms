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
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------
// Base class for resource usage
//----------------------------------------------------------


/**
 * Short description for 'ResourcesUsage'
 * 
 * Long description (if any) ...
 */
class ResourcesUsage
{

	/**
	 * Description for '_db'
	 * 
	 * @var object
	 */
	var $_db      = NULL;

	/**
	 * Description for '_resid'
	 * 
	 * @var string
	 */
	var $_resid   = NULL;

	/**
	 * Description for '_type'
	 * 
	 * @var unknown
	 */
	var $_type    = NULL;

	/**
	 * Description for 'rating'
	 * 
	 * @var string
	 */
	var $rating   = NULL;

	/**
	 * Description for 'users'
	 * 
	 * @var string
	 */
	var $users    = 'unavailable';

	/**
	 * Description for 'datetime'
	 * 
	 * @var unknown
	 */
	var $datetime = NULL;

	/**
	 * Description for 'cites'
	 * 
	 * @var string
	 */
	var $cites    = NULL;

	/**
	 * Description for 'lastcite'
	 * 
	 * @var unknown
	 */
	var $lastcite = NULL;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @param      unknown $resid Parameter description (if any) ...
	 * @param      unknown $type Parameter description (if any) ...
	 * @param      integer $rating Parameter description (if any) ...
	 * @param      integer $cites Parameter description (if any) ...
	 * @param      string $lastcite Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db, $resid, $type, $rating=0, $cites=0, $lastcite='' )
	{
		$this->_db = $db;
		$this->_resid   = $resid;
		$this->_type    = $type;
		$this->rating   = $rating;
		$this->cites    = $cites;
		$this->lastcite = $lastcite;
	}

	/**
	 * Short description for 'fetch'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $disp Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function fetch( $disp )
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
				$period  = 14;
				$caption = JText::_('Total');
				break;
		}

		$sql = "SELECT * FROM #__resource_stats WHERE resid=".$this->_resid." AND period=".$period." ORDER BY datetime DESC LIMIT 1";

		$this->_db->setQuery( $sql );
		$result = $this->_db->loadObjectList();

		$this->process($result);

		return array($caption, $period);
	}

	/**
	 * Short description for 'display'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $disp Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function display( $disp )
	{
		return true;
	}

	/**
	 * Short description for 'display_substats'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	public function display_substats()
	{
		$cls = $this->getRatingClass($this->rating);

		$html  = '<table class="usagestats" summary="Review and Citation statistics for this resource">'."\n";
		$html .= ' <caption>Reviews &amp; Citations</caption>'."\n";
		$html .= ' <tfoot>'."\n";
		$html .= '  <tr>'."\n";
		$html .= '   <td colspan="2">Google/IEEE';
		if ($this->lastcite) {
			$html .= ': updated '.JHTML::_('date', $this->lastcite, '%d %b, %Y');
		}
		$html .= '</td>'."\n";
		$html .= '  </tr>'."\n";
		$html .= ' </tfoot>'."\n";
		$html .= ' <tbody>'."\n";
		$html .= '  <tr>'."\n";
		$html .= '   <th scope="row"><abbr title="Average">Avg.</abbr> Review:</th>'."\n";
		$html .= '   <td><span class="avgrating'.$cls.'"><span>'.$this->rating.' out of 5 stars</span></span></td>'."\n";
		$html .= '  </tr>'."\n";
		$html .= '  <tr>'."\n";
		$html .= '   <th scope="row">Citations:</th>'."\n";
		$html .= '   <td>'.$this->cites.'</td>'."\n";
		$html .= '  </tr>'."\n";
		$html .= ' </tbody>'."\n";
		$html .= '</table>'."\n";
		return $html;
	}

	/**
	 * Short description for 'process'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $results Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function process( $results )
	{
		return true;
	}

	/**
	 * Short description for 'valfmt'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $val Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function valfmt($val)
	{
		if ($val != 'unavailable') {
			if ($val <= 60) {
				$val = ceil($val).' secs';
			} else if ($val > 60 && $val <= 3600) {
				$val = ceil($val/60).' mins';
			} else if ($val > 3600 && $val <= 86400) {
				$val = ceil($val/3600).' hours';
			} else {
				$val = ceil($val/84600).' days';
			}
		}
		return $val;
	}

	/**
	 * Short description for 'getRatingClass'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $rating Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function getRatingClass($rating=0)
	{
		switch ($rating)
		{
			case 0.5: $class = ' half-stars';      break;
			case 1:   $class = ' one-stars';       break;
			case 1.5: $class = ' onehalf-stars';   break;
			case 2:   $class = ' two-stars';       break;
			case 2.5: $class = ' twohalf-stars';   break;
			case 3:   $class = ' three-stars';     break;
			case 3.5: $class = ' threehalf-stars'; break;
			case 4:   $class = ' four-stars';      break;
			case 4.5: $class = ' fourhalf-stars';  break;
			case 5:   $class = ' five-stars';      break;
			case 0:
			default:  $class = ' no-stars';      break;
		}
		return $class;
	}
}

//----------------------------------------------------------
// Extended resource stats class (Tools)
//----------------------------------------------------------


/**
 * Short description for 'class'
 * 
 * Long description (if any) ...
 */
class ToolStats extends ResourcesUsage
{

	/**
	 * Description for 'jobs'
	 * 
	 * @var string
	 */
	var $jobs     = 'unavailable';

	/**
	 * Description for 'avg_wall'
	 * 
	 * @var string
	 */
	var $avg_wall = 'unavailable';

	/**
	 * Description for 'tot_wall'
	 * 
	 * @var string
	 */
	var $tot_wall = 'unavailable';

	/**
	 * Description for 'avg_cpu'
	 * 
	 * @var mixed
	 */
	var $avg_cpu  = 'unavailable';

	/**
	 * Description for 'tot_cpu'
	 * 
	 * @var mixed
	 */
	var $tot_cpu  = 'unavailable';

	/**
	 * Description for 'avg_exec'
	 * 
	 * @var string
	 */
	var $avg_exec = 'unavailable';

	/**
	 * Description for 'tot_exec'
	 * 
	 * @var string
	 */
	var $tot_exec = 'unavailable';

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @param      unknown $resid Parameter description (if any) ...
	 * @param      unknown $type Parameter description (if any) ...
	 * @param      integer $rating Parameter description (if any) ...
	 * @param      integer $cites Parameter description (if any) ...
	 * @param      string $lastcite Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db, $resid, $type, $rating=0, $cites=0, $lastcite='' )
	{
		parent::__construct( $db, $resid, $type, $rating, $cites, $lastcite );
	}

	/**
	 * Short description for 'display'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $disp Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function display( $disp='ALL' )
	{
		list($caption, $period) = $this->fetch($disp);

		$html = '';
		if ($this->users != 'unavailable' && $this->jobs != 'unavailable' && $this->avg_exec != 'unavailable') {
			$html .= '<table class="usagestats" summary="'.JText::_('Statistics for this resource').'">'."\n";
			$html .= ' <caption>'.JText::_('Usage Stats').'</caption>'."\n";
			$html .= ' <tfoot>'."\n";
			$html .= '  <tr>'."\n";
			$html .= '   <td colspan="2">'.$caption;
			if ($this->datetime) {
				$html .= ': '.JText::_('updated').' '.JHTML::_('date', $this->datetime, '%d %b, %Y');
			}
			$html .= '</td>'."\n";
			$html .= '  </tr>'."\n";
			$html .= ' </tfoot>'."\n";
			$html .= ' <tbody>'."\n";
			if ($this->users != 'unavailable') {
				$html .= '  <tr>'."\n";
				$html .= '   <th scope="row">'.JText::_('Users').':</th>'."\n";
				$html .= '   <td>'.$this->users.'</td>'."\n";
				$html .= '  </tr>'."\n";
			}
			if ($this->jobs != 'unavailable') {
				$html .= '  <tr>'."\n";
				$html .= '   <th scope="row">'.JText::_('Jobs').':</th>'."\n";
				$html .= '   <td>'.$this->jobs.'</td>'."\n";
				$html .= '  </tr>'."\n";
			}
			if ($this->avg_exec != 'unavailable') {
				$html .= '  <tr>'."\n";
				$html .= '   <th scope="row"><abbr title="Average">Avg.</abbr> <abbr title="execution">exec.</abbr> time:</th>'."\n";
				$html .= '   <td>'.$this->valfmt($this->avg_exec).'</td>'."\n";
				$html .= '  </tr>'."\n";
			}
			$html .= ' </tbody>'."\n";
			$html .= '</table>'."\n";
		}
		$html .= $this->display_substats();

		return $html;
	}

	/**
	 * Short description for 'process'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array &$result Parameter description (if any) ...
	 * @return     void
	 */
	public function process( &$result )
	{
		if ($result) {
			foreach ($result as $row)
			{
				$this->users    = $row->users;
				$this->jobs     = $row->jobs;
				$this->avg_wall = $row->avg_wall;
				$this->tot_wall = $row->tot_wall;
				$this->avg_cpu  = $row->avg_cpu;
				$this->tot_cpu  = $row->tot_cpu;
				$this->datetime = $row->datetime;

				// Changed by Swaroop on 06/25/2007: Avg. exec. time = Avg. wall time
				if ($this->avg_cpu == 0) {
					$this->avg_exec = $this->avg_wall;
				} else {
			    	$this->avg_exec = $this->avg_cpu;
				}
				# $this->avg_exec = $this->avg_wall;

				if ($this->tot_cpu == 0) {
					$this->tot_exec = $this->tot_wall;
				} else {
					$this->tot_exec = $this->tot_cpu;
				}
			}
		}
	}
}

//----------------------------------------------------------
// Extended resource stats class (And More)
//----------------------------------------------------------


/**
 * Short description for 'AndmoreStats'
 * 
 * Long description (if any) ...
 */
class AndmoreStats extends ResourcesUsage
{

	/**
	 * Description for 'views'
	 * 
	 * @var string
	 */
	var $views    = 'unavailable';

	/**
	 * Description for 'avg_view'
	 * 
	 * @var string
	 */
	var $avg_view = 'unavailable';

	/**
	 * Description for 'tot_view'
	 * 
	 * @var string
	 */
	var $tot_view = 'unavailable';

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @param      unknown $resid Parameter description (if any) ...
	 * @param      unknown $type Parameter description (if any) ...
	 * @param      integer $rating Parameter description (if any) ...
	 * @param      integer $cites Parameter description (if any) ...
	 * @param      string $lastcite Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db, $resid, $type, $rating=0, $cites=0, $lastcite='' )
	{
		parent::__construct( $db, $resid, $type, $rating, $cites, $lastcite );
	}

	/**
	 * Short description for 'display'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $disp Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function display( $disp='ALL' )
	{
		list($caption, $period) = $this->fetch($disp);

		if ($this->_type == 1) {
			$vlabel  = JText::_('Views');
			$avlabel = JText::_('Avg. view time');
		} else {
			$vlabel = JText::_('Downloads');
			$avlabel = JText::_('Avg. downloads');
		}

		$html = '';
		if ($this->users != 'unavailable' && $this->avg_view != 'unavailable') {
			$html .= '<table class="usagestats" summary="'.JText::_('Statistics for this resource').'">'."\n";
			$html .= ' <caption>'.JText::_('Usage Stats').'</caption>'."\n";
			$html .= ' <tfoot>'."\n";
			$html .= '  <tr>'."\n";
			$html .= '   <td colspan="2">'.$caption;
			if ($this->datetime) {
				$html .= ': '.JText::_('updated').' '.JHTML::_('date', $this->datetime, '%d %b, %Y');
			}
			$html .= '</td>'."\n";
			$html .= '  </tr>'."\n";
			$html .= ' </tfoot>'."\n";
			$html .= ' <tbody>'."\n";
			if ($this->users != 'unavailable') {
				$html .= '  <tr>'."\n";
				$html .= '   <th scope="row">'.JText::_('Users').':</th>'."\n";
				$html .= '   <td>'.$this->users.'</td>'."\n";
				$html .= '  </tr>'."\n";
			}
			/*if ($this->views != 'unavailable') {
				$html .= '  <tr>'."\n";
				$html .= '   <th scope="row">'.$vlabel.':</th>'."\n";
				$html .= '   <td>'.$this->views.'</td>'."\n";
				$html .= '  </tr>'."\n";
			}
			if ($this->avg_view != 'unavailable') {
				$html .= '  <tr>'."\n";
				$html .= '   <th scope="row">'.$avlabel.':</th>'."\n";
				$html .= '   <td>'.$this->valfmt($this->avg_view).'</td>'."\n";
				$html .= '  </tr>'."\n";
			}*/
			$html .= ' </tbody>'."\n";
			$html .= '</table>'."\n";
		}
		$html .= $this->display_substats();

		return $html;
	}

	/**
	 * Short description for 'process'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array &$result Parameter description (if any) ...
	 * @return     void
	 */
	public function process( &$result )
	{
		if ($result) {
			foreach ($result as $row)
			{
				$this->users    = $row->users;
				$this->views    = $row->jobs;
				$this->datetime = $row->datetime;

				if ($row->avg_cpu == 0) {
					$this->avg_view = $row->avg_wall;
				} else {
					$this->avg_view = $row->avg_cpu;
				}

				if ($row->tot_cpu == 0) {
					$this->tot_view = $row->tot_wall;
				} else {
					$this->tot_view = $row->tot_cpu;
				}
			}
		}
	}
}

