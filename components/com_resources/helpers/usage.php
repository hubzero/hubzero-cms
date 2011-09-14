<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------
// Base class for resource usage
//----------------------------------------------------------

class ResourcesUsage
{
	var $_db      = NULL;
	var $_resid   = NULL;
	var $_type    = NULL;

	var $rating   = NULL;
	var $users    = 'unavailable';
	var $datetime = NULL;

	var $cites    = NULL;
	var $lastcite = NULL;

	public function __construct( &$db, $resid, $type, $rating=0, $cites=0, $lastcite='' )
	{
		$this->_db = $db;
		$this->_resid   = $resid;
		$this->_type    = $type;
		$this->rating   = $rating;
		$this->cites    = $cites;
		$this->lastcite = $lastcite;
	}

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

	public function display( $disp )
	{
		return true;
	}

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

	public function process( $results )
	{
		return true;
	}

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

class ToolStats extends ResourcesUsage
{
	var $jobs     = 'unavailable';
	var $avg_wall = 'unavailable';
	var $tot_wall = 'unavailable';
	var $avg_cpu  = 'unavailable';
	var $tot_cpu  = 'unavailable';
	var $avg_exec = 'unavailable';
	var $tot_exec = 'unavailable';

	public function __construct( &$db, $resid, $type, $rating=0, $cites=0, $lastcite='' )
	{
		parent::__construct( $db, $resid, $type, $rating, $cites, $lastcite );
	}

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

class AndmoreStats extends ResourcesUsage
{
	var $views    = 'unavailable';
	var $avg_view = 'unavailable';
	var $tot_view = 'unavailable';

	public function __construct( &$db, $resid, $type, $rating=0, $cites=0, $lastcite='' )
	{
		parent::__construct( $db, $resid, $type, $rating, $cites, $lastcite );
	}

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

