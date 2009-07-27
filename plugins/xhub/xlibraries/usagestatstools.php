<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class UsageStatsTools
{
	var $db       = NULL;
	var $resid    = NULL;
	var $users    = array(1=>-1, 2=>-1, 12=>-1);
	var $jobs     = array(1=>-1, 2=>-1, 12=>-1);
	var $avg_wall = array(1=>-1, 2=>-1, 12=>-1);
	var $tot_wall = array(1=>-1, 2=>-1, 12=>-1);
	var $avg_cpu  = array(1=>-1, 2=>-1, 12=>-1);
	var $tot_cpu  = array(1=>-1, 2=>-1, 12=>-1);
	var $avg_exec = array(1=>-1, 2=>-1, 12=>-1);
	var $tot_exec = array(1=>-1, 2=>-1, 12=>-1);
	var $datetime = NULL;

	//-----------
	
	function UsageStatsTools( &$db, $resid )
	{
		$this->db = $db;
		$this->resid = $resid;
	}
	
	//-----------

	function display( $disp='YEAR' )
	{
		switch(strtoupper($disp))
		{
			case 'CURR': 
				$period  = 1;
				$caption = 'Current Month';
				break;
			case 'LAST': 
				$period  = 2;
				$caption = 'Last Month';
				break;
			case 'YEAR': 
				$period  = 12;
				$caption = 'Last 12 Months';
				break;
		}

		$this->fetch($period);

		$stats_computed_on = JHTML::_('date', $this->datetime, '%d %b, %Y');
		
		$html  = '<table class="usagestats" summary="Statistics for this resource">'."\n";
		$html .= ' <caption>'.$caption.'</caption>'."\n";
		$html .= ' <tfoot>'."\n";
		$html .= '  <tr>'."\n";
		$html .= '   <td colspan="2">Stats computed on '.$stats_computed_on.'</td>'."\n";
		$html .= '  </tr>'."\n";
		$html .= ' </tfoot>'."\n";
		$html .= ' <tbody>'."\n";
		$html .= '  <tr>'."\n";
		$html .= '   <th scope="row">Users:</th>'."\n";
		$html .= '   <td>'.$this->users[$period].'</td>'."\n";
		$html .= '  </tr>'."\n";
		$html .= '  <tr>'."\n";
		$html .= '   <th scope="row">Jobs:</th>'."\n";
		$html .= '   <td>'.$this->jobs[$period].'</td>'."\n";
		$html .= '  </tr>'."\n";
		$html .= '  <tr>'."\n";
		$html .= '   <th scope="row">Avg. <abbr title="execution">exec</abbr>. time:</th>'."\n";
		$html .= '   <td>'.$this->valfmt($this->avg_exec[$period]).'</td>'."\n";
		$html .= '  </tr>'."\n";
		//$html .= '  <tr>'."\n";
		//$html .= '   <th scope="row">Total CPU time:</th>'."\n";
		//$html .= '   <td>'.$this->valfmt($this->tot_exec[$period]).'</td>'."\n";
		//$html .= '  </tr>'."\n";
		$html .= ' </tbody>'."\n";
		$html .= '</table>'."\n";
		
		return $html;
	}

	//-----------

	function fetch($period) 
	{
		$sql = "SELECT * FROM org_mos_resource_stats WHERE resid=".$this->resid." AND period=".$period;

		$this->db->setQuery( $sql );
		$result = $this->db->loadObjectList();
		
		if($result) {
			foreach($result as $row)
			{
				$this->users[$period]    = $row->users;
				$this->jobs[$period]     = $row->jobs;
				$this->avg_wall[$period] = $row->avg_wall;
				$this->tot_wall[$period] = $row->tot_wall;
				$this->avg_cpu[$period]  = $row->avg_cpu;
				$this->tot_cpu[$period]  = $row->tot_cpu;
				$this->datetime          = $row->datetime;
				
				if ($this->avg_cpu[$period] == 0) {
					$this->avg_exec[$period] = $this->avg_wall[$period];
				} else {
					$this->avg_exec[$period] = $this->avg_cpu[$period];
				}
				if ($this->tot_cpu[$period] == 0) {
					$this->tot_exec[$period] = $this->tot_wall[$period];
				} else {
					$this->tot_exec[$period] = $this->tot_cpu[$period];
				}
			}
		}
	}
	
	//-----------
	
	function valfmt($val) 
	{
		if ($val <= 60) {
			$val = ceil($val).' secs';
		} else if ($val > 60 && $val <= 3600) {
			$val = ceil($val/60).' mins';
		} else if ($val > 3600 && $val <= 86400) {
			$val = ceil($val/3600).' hours';
		} else {
			$val = ceil($val/84600).' days';
		}
		return $val;
	}
}
?>