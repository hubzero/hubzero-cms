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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Primary controller for the APC component
 */
class SystemControllerApc extends \Hubzero\Component\AdminController
{
	/**
	 * Controller execute method, used for selecting the correct function based on task.
	 * Defaults to the host stats view
	 *
	 * @return void
	 */
	public function execute()
	{
		// Get the task
		$this->_task = JRequest::getVar('task', '');

		// Set the version id
		$this->VERSION = '$Id: apc.php 271315 2008-12-16 07:15:07Z shire $';

		// Read optional configuration file (if it exists)
		if (file_exists(JPATH_COMPONENT . DS . 'helpers' . DS . 'apc.conf.php'))
		{
			include_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'apc.conf.php');
		}

		// Set some defaults
		$this->_defaults('DATE_FORMAT', 'Y/m/d H:i:s'); // US time format
		$this->_defaults('GRAPH_SIZE',200); // Image size

		// Rewrite $PHP_SELF to block XSS attacks
		$PHP_SELF = isset($_SERVER['PHP_SELF']) ? htmlentities(strip_tags($_SERVER['PHP_SELF'],''), ENT_QUOTES, 'UTF-8') : '';

		// Get current time
		$this->time = time();

		// Set the host info
		$host = php_uname('n');
		if ($host)
		{
			$this->host = '(' . $host . ')';
		}
		if ($_SERVER['SERVER_ADDR'])
		{
			$this->host .= ' (' . $_SERVER['SERVER_ADDR'] . ')';
		}

		// Operation constants
		define('OB_HOST_STATS',1);
		define('OB_SYS_CACHE',2);
		define('OB_USER_CACHE',3);
		define('OB_SYS_CACHE_DIR',4);
		define('OB_VERSION_CHECK',9);

		// Check validity of input variables
		$vardom=array(
			'OB'         => '/^\d+$/',               // operational mode switch
			'CC'         => '/^[01]$/',              // clear cache requested
			'DU'         => '/^.*$/',                // Delete User Key
			'SH'         => '/^[a-z0-9]+$/',         // shared object description

			'IMG'        => '/^[123]$/',             // image to generate
			'LO'         => '/^1$/',                 // login requested

			'COUNT'      => '/^\d+$/',               // number of line displayed in list
			'SCOPE'      => '/^[AD]$/',              // list view scope
			'SORT1'      => '/^[AHSMCDTZ]$/',        // first sort key
			'SORT2'      => '/^[DA]$/',              // second sort key
			'AGGR'       => '/^\d+$/',               // aggregation by dir level
			'SEARCH'     => '~^[a-zA-Z0-1/_.-]*$~'   // aggregation by dir level
		);

		// Set default cache mode
		$cache_mode = 'opcode';

		// Set cache scope
		$this->scope_list = array(
			'A' => 'cache_list',
			'D' => 'deleted_list'
		);

		// Handle POST and GET requests
		if (empty($_REQUEST))
		{
			if (!empty($_GET) && !empty($_POST))
			{
				$_REQUEST = array_merge($_GET, $_POST);
			}
			else if (!empty($_GET))
			{
				$_REQUEST = $_GET;
			}
			else if (!empty($_POST))
			{
				$_REQUEST = $_POST;
			}
			else
			{
				$_REQUEST = array();
			}
		}

		// Check parameter syntax
		foreach ($vardom as $var => $dom)
		{
			if (!isset($_REQUEST[$var]))
			{
				$MYREQUEST[$var] = NULL;
			}
			else if (!is_array($_REQUEST[$var]) && preg_match($dom.'D',$_REQUEST[$var]))
			{
				$MYREQUEST[$var] = $_REQUEST[$var];
			}
			else
			{
				$MYREQUEST[$var] = $_REQUEST[$var] = NULL;
			}
		}

		// Check parameter sematics
		if (empty($MYREQUEST['SCOPE'])) $MYREQUEST['SCOPE'] = "A";
		if (empty($MYREQUEST['SORT1'])) $MYREQUEST['SORT1'] = "H";
		if (empty($MYREQUEST['SORT2'])) $MYREQUEST['SORT2'] = "D";
		if (empty($MYREQUEST['OB']))	$MYREQUEST['OB'] = OB_HOST_STATS;
		if (!isset($MYREQUEST['COUNT'])) $MYREQUEST['COUNT'] = 20;
		if (!isset($this->scope_list[$MYREQUEST['SCOPE']])) $MYREQUEST['SCOPE'] = 'A';

		$task = (!empty($this->_task)) ? "&task={$this->_task}" : '';

		$this->MY_SELF =
			"$PHP_SELF".
			"?option=" . $this->_option .
			$task .
			"&SCOPE=" . $MYREQUEST['SCOPE'] .
			"&SORT1=" . $MYREQUEST['SORT1'] .
			"&SORT2=" . $MYREQUEST['SORT2'] .
			"&COUNT=" . $MYREQUEST['COUNT'];
		$this->MY_SELF_WO_SORT =
			"$PHP_SELF" .
			"?option=" . $this->_option .
			$task .
			"&SCOPE=" . $MYREQUEST['SCOPE'] .
			"&COUNT=" . $MYREQUEST['COUNT'];

		// Select cache mode
		if ($MYREQUEST['OB'] == OB_USER_CACHE)
		{
			$cache_mode = 'user';
		}

		if (!empty($MYREQUEST['DU']))
		{
			apc_delete($MYREQUEST['DU']);
		}

		if (!function_exists('apc_cache_info') || !($this->cache = @apc_cache_info($cache_mode)))
		{
			$this->setError(JText::_('No cache info available.  APC does not appear to be running.'));
			JRequest::setVar('task', 'error');
			parent::execute();
			return;
		}

		// Avoid division by 0 errors on a cache clear
		if (!$this->cache['num_hits'])
		{
			$this->cache['num_hits'] = 1;
			$this->time++;
		}

		// Make a few things available to everyone
		$this->MYREQUEST  = $MYREQUEST;
		$this->cache_user = apc_cache_info('user', 1);
		$this->mem        = apc_sma_info();
		$this->cache_mode = $cache_mode;

		$this->registerTask('__default', 'host');

		parent::execute();
	}

	/**
	 * View host stats
	 *
	 * @return void
	 */
	public function errorTask()
	{
		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * View host stats
	 *
	 * @return void
	 */
	public function hostTask()
	{
		// Add stylesheet
		$doc = JFactory::getDocument();
		$doc->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'apc.css');

		// A few variables to grab from outside (to compute other values)
		$cache      = $this->cache;
		$cache_user = $this->cache_user;
		$mem        = $this->mem;
		$time       = $this->time;

		// A few variables for the view
		$this->view->mem_size         = $mem['num_seg']*$mem['seg_size'];
		$this->view->mem_avail        = $mem['avail_mem'];
		$this->view->bmem_avail       = $this->_bsize($mem['avail_mem']);
		$this->view->mem_used         = $this->view->mem_size-$this->view->mem_avail;
		$this->view->bmem_used        = $this->_bsize($this->view->mem_size-$this->view->mem_avail);
		$this->view->seg_size         = $this->_bsize($mem['seg_size']);
		$this->view->req_rate         = round(($cache['num_hits']+$cache['num_misses'])/($time-$cache['start_time']), 2);
		$this->view->hit_rate         = round(($cache['num_hits'])/($time-$cache['start_time']), 2);
		$this->view->miss_rate        = round(($cache['num_misses'])/($time-$cache['start_time']), 2);
		$this->view->insert_rate      = round(($cache['num_inserts'])/($time-$cache['start_time']), 2);
		$this->view->req_rate_user    = round(($cache_user['num_hits']+$cache_user['num_misses'])/($time-$cache_user['start_time']), 2);
		$this->view->hit_rate_user    = round(($cache_user['num_hits'])/($time-$cache_user['start_time']), 2);
		$this->view->miss_rate_user   = round(($cache_user['num_misses'])/($time-$cache_user['start_time']), 2);
		$this->view->insert_rate_user = round(($cache_user['num_inserts'])/($time-$cache_user['start_time']), 2);
		$this->view->apcversion       = phpversion('apc');
		$this->view->phpversion       = phpversion();
		$this->view->number_files     = $cache['num_entries'];
		$this->view->size_files       = $this->_bsize($cache['mem_size']);
		$this->view->number_vars      = $cache_user['num_entries'];
		$this->view->size_vars        = $this->_bsize($cache_user['mem_size']);
		$this->view->host             = $this->host;
		$this->view->duration         = $this->_duration($cache['start_time']);
		$this->view->cache            = $this->cache;
		$this->view->cache_user       = $this->cache_user;
		$this->view->mem              = $this->mem;
		$this->view->graphics_avail   = $this->graphics_avail();
		$this->view->time             = $this->time;
		$this->view->cache_mode       = $this->cache_mode;

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * View system cache entries
	 *
	 * @return void
	 */
	public function systemTask()
	{
		// Add stylesheet
		$doc = JFactory::getDocument();
		$doc->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'apc.css');

		// Instantiate a new view
		$this->view->MYREQUEST       = $this->MYREQUEST;
		$this->view->MY_SELF         = $this->MY_SELF;
		$this->view->MY_SELF_WO_SORT = $this->MY_SELF_WO_SORT;
		$this->view->cache           = $this->cache;
		$this->view->scope_list      = $this->scope_list;

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * View version info
	 *
	 * @return void
	 */
	public function versionTask()
	{
		// Add stylesheet
		$doc = JFactory::getDocument();
		$doc->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'apc.css');

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * View user cache entries
	 *
	 * @return void
	 */
	public function userTask()
	{
		// Add stylesheet
		$doc = JFactory::getDocument();
		$doc->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'apc.css');

		$this->view->MYREQUEST       = $this->MYREQUEST;
		$this->view->MY_SELF         = $this->MY_SELF;
		$this->view->MY_SELF_WO_SORT = $this->MY_SELF_WO_SORT;
		$this->view->cache           = $this->cache;
		$this->view->scope_list      = $this->scope_list;

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * View directory cache entries
	 *
	 * @return void
	 */
	public function dircacheTask()
	{
		// Add stylesheet
		$doc = JFactory::getDocument();
		$doc->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'apc.css');

		$this->view->MYREQUEST       = $this->MYREQUEST;
		$this->view->MY_SELF         = $this->MY_SELF;
		$this->view->MY_SELF_WO_SORT = $this->MY_SELF_WO_SORT;
		$this->view->cache           = $this->cache;
		$this->view->scope_list      = $this->scope_list;

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Clear cache
	 *
	 * @return void
	 */
	public function clrcacheTask()
	{
		apc_clear_cache($cache_mode);

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=host',
			JText::_('Cache cleared')
		);
	}

	/**
	 * define if not defined
	 *
	 * @return void
	 */
	private function _defaults($d, $v)
	{
		if (!defined($d)) define($d, $v);
	}

	/**
	 * Pretty printer for byte values
	 *
	 * @param    integer $s Byte value
	 * @return   string
	 */
	private function _bsize($s)
	{
		foreach (array('','K','M','G') as $i => $k)
		{
			if ($s < 1024) break;
			$s/=1024;
		}
		return sprintf("%5.1f %sBytes",$s,$k);
	}

	/**
	 * Make timestamp human readable
	 *
	 * @param    integer $ts Time stamp
	 * @return   string
	 */
	private function _duration($ts)
	{
		$time  = $this->time;
		$years = (int)((($time - $ts)/(7*86400))/52.177457);
		$rem   = (int)(($time-$ts)-($years * 52.177457 * 7 * 86400));
		$weeks = (int)(($rem)/(7*86400));
		$days  = (int)(($rem)/86400) - $weeks*7;
		$hours = (int)(($rem)/3600) - $days*24 - $weeks*7*24;
		$mins  = (int)(($rem)/60) - $hours*60 - $days*24*60 - $weeks*7*24*60;
		$str   = '';
		if ($years==1) $str .= "$years year, ";
		if ($years>1) $str .= "$years years, ";
		if ($weeks==1) $str .= "$weeks week, ";
		if ($weeks>1) $str .= "$weeks weeks, ";
		if ($days==1) $str .= "$days day,";
		if ($days>1) $str .= "$days days,";
		if ($hours == 1) $str .= " $hours hour and";
		if ($hours>1) $str .= " $hours hours and";
		if ($mins == 1) $str .= " 1 minute";
		else $str .= " $mins minutes";

		return $str;
	}

	/**
	 * Check if GD extension is loaded
	 *
	 * @return boolean
	 */
	private function graphics_avail()
	{
		return extension_loaded('gd');
	}

	/**
	 * Create graphics
	 *
	 * @return void
	 */
	public function mkimageTask()
	{
		if (!$this->graphics_avail())
		{
			exit(0);
		}

		function block_sort($array1, $array2)
		{
			if ($array1['offset'] > $array2['offset'])
			{
				return 1;
			}
			else
			{
				return -1;
			}
		}

		function fill_arc($im, $centerX, $centerY, $diameter, $start, $end, $color1, $color2, $text='', $placeindex=0)
		{
			$r = $diameter/2;
			$w=deg2rad((360+$start+($end-$start)/2)%360);

			if (function_exists("imagefilledarc"))
			{
				// exists only if GD 2.0.1 is avaliable
				imagefilledarc($im, $centerX+1, $centerY+1, $diameter, $diameter, $start, $end, $color1, IMG_ARC_PIE);
				imagefilledarc($im, $centerX, $centerY, $diameter, $diameter, $start, $end, $color2, IMG_ARC_PIE);
				imagefilledarc($im, $centerX, $centerY, $diameter, $diameter, $start, $end, $color1, IMG_ARC_NOFILL|IMG_ARC_EDGED);
			}
			else
			{
				imagearc($im, $centerX, $centerY, $diameter, $diameter, $start, $end, $color2);
				imageline($im, $centerX, $centerY, $centerX + cos(deg2rad($start)) * $r, $centerY + sin(deg2rad($start)) * $r, $color2);
				imageline($im, $centerX, $centerY, $centerX + cos(deg2rad($start+1)) * $r, $centerY + sin(deg2rad($start)) * $r, $color2);
				imageline($im, $centerX, $centerY, $centerX + cos(deg2rad($end-1))   * $r, $centerY + sin(deg2rad($end))   * $r, $color2);
				imageline($im, $centerX, $centerY, $centerX + cos(deg2rad($end))   * $r, $centerY + sin(deg2rad($end))   * $r, $color2);
				imagefill($im,$centerX + $r*cos($w)/2, $centerY + $r*sin($w)/2, $color2);
			}
			if ($text)
			{
				if ($placeindex>0)
				{
					imageline($im,$centerX + $r*cos($w)/2, $centerY + $r*sin($w)/2,$diameter, $placeindex*12,$color1);
					imagestring($im,4,$diameter, $placeindex*12,$text,$color1);
				}
				else
				{
					imagestring($im,4,$centerX + $r*cos($w)/2, $centerY + $r*sin($w)/2,$text,$color1);
				}
			}
		}

		function text_arc($im, $centerX, $centerY, $diameter, $start, $end, $color1,$text,$placeindex=0)
		{
			$r=$diameter/2;
			$w=deg2rad((360+$start+($end-$start)/2)%360);

			if ($placeindex>0)
			{
				imageline($im,$centerX + $r*cos($w)/2, $centerY + $r*sin($w)/2,$diameter, $placeindex*12,$color1);
				imagestring($im,4,$diameter, $placeindex*12,$text,$color1);
			}
			else
			{
				imagestring($im,4,$centerX + $r*cos($w)/2, $centerY + $r*sin($w)/2,$text,$color1);
			}
		}

		function fill_box($im, $x, $y, $w, $h, $color1, $color2,$text='',$placeindex='')
		{
			global $col_black;
			$x1=$x+$w-1;
			$y1=$y+$h-1;

			imagerectangle($im, $x, $y1, $x1+1, $y+1, $col_black);
			if($y1>$y) imagefilledrectangle($im, $x, $y, $x1, $y1, $color2);
			else imagefilledrectangle($im, $x, $y1, $x1, $y, $color2);
			imagerectangle($im, $x, $y1, $x1, $y, $color1);
			if ($text)
			{
				if ($placeindex>0)
				{
					if ($placeindex<16)
					{
						$px=5;
						$py=$placeindex*12+6;
						imagefilledrectangle($im, $px+90, $py+3, $px+90-4, $py-3, $color2);
						imageline($im,$x,$y+$h/2,$px+90,$py,$color2);
						imagestring($im,2,$px,$py-6,$text,$color1);
					}
					else
					{
						if ($placeindex<31)
						{
							$px=$x+40*2;
							$py=($placeindex-15)*12+6;
						}
						else
						{
							$px=$x+40*2+100*intval(($placeindex-15)/15);
							$py=($placeindex%15)*12+6;
						}
						imagefilledrectangle($im, $px, $py+3, $px-4, $py-3, $color2);
						imageline($im,$x+$w,$y+$h/2,$px,$py,$color2);
						imagestring($im,2,$px+2,$py-6,$text,$color1);
					}
				}
				else
				{
					imagestring($im,4,$x+5,$y1-16,$text,$color1);
				}
			}
		}

		$MYREQUEST = $this->MYREQUEST;
		$mem = $this->mem;
		$cache = $this->cache;

		$size = GRAPH_SIZE; // image size
		if ($MYREQUEST['IMG']==3)
			$image = imagecreate(2*$size+150, $size+10);
		else
			$image = imagecreate($size+50, $size+10);

		$col_white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
		$col_red   = imagecolorallocate($image, 0xD0, 0x60,  0x30);
		$col_green = imagecolorallocate($image, 0x60, 0xF0, 0x60);
		$col_black = imagecolorallocate($image,   0,   0,   0);
		imagecolortransparent($image,$col_white);

		switch ($MYREQUEST['IMG'])
		{
			case 1:
				$s=$mem['num_seg']*$mem['seg_size'];
				$a=$mem['avail_mem'];
				$x=$y=$size/2;
				$fuzz = 0.000001;

				// This block of code creates the pie chart.  It is a lot more complex than you
				// would expect because we try to visualize any memory fragmentation as well.
				$angle_from = 0;
				$string_placement=array();
				for($i=0; $i<$mem['num_seg']; $i++)
				{
					$ptr = 0;
					$free = $mem['block_lists'][$i];
					uasort($free, 'block_sort');
					foreach($free as $block) {
						if($block['offset']!=$ptr)
						{
							$angle_to = $angle_from+($block['offset']-$ptr)/$s;
							if(($angle_to+$fuzz)>1) $angle_to = 1;
							if( ($angle_to*360) - ($angle_from*360) >= 1)
							{
								fill_arc($image,$x,$y,$size,$angle_from*360,$angle_to*360,$col_black,$col_red);
								if (($angle_to-$angle_from)>0.05)
								{
									array_push($string_placement, array($angle_from,$angle_to));
								}
							}
							$angle_from = $angle_to;
						}
						$angle_to = $angle_from+($block['size'])/$s;
						if(($angle_to+$fuzz)>1) $angle_to = 1;
						if( ($angle_to*360) - ($angle_from*360) >= 1)
						{
							fill_arc($image,$x,$y,$size,$angle_from*360,$angle_to*360,$col_black,$col_green);
							if (($angle_to-$angle_from)>0.05)
							{
								array_push($string_placement, array($angle_from,$angle_to));
							}
						}
						$angle_from = $angle_to;
						$ptr = $block['offset']+$block['size'];
					}
					if ($ptr < $mem['seg_size'])
					{
						$angle_to = $angle_from + ($mem['seg_size'] - $ptr)/$s;
						if(($angle_to+$fuzz)>1) $angle_to = 1;
						fill_arc($image,$x,$y,$size,$angle_from*360,$angle_to*360,$col_black,$col_red);
						if (($angle_to-$angle_from)>0.05)
						{
							array_push($string_placement, array($angle_from,$angle_to));
						}
					}
				}
				foreach ($string_placement as $angle)
				{
					text_arc($image,$x,$y,$size,$angle[0]*360,$angle[1]*360,$col_black,$this->_bsize($s*($angle[1]-$angle[0])));
				}
				break;

			case 2:
				$s=$cache['num_hits']+$cache['num_misses'];
				$a=$cache['num_hits'];

				fill_box($image, 30,$size,50,-$a*($size-21)/$s,$col_black,$col_green,sprintf("%.1f%%",$cache['num_hits']*100/$s));
				fill_box($image,130,$size,50,-max(4,($s-$a)*($size-21)/$s),$col_black,$col_red,sprintf("%.1f%%",$cache['num_misses']*100/$s));
				break;

			case 3:
				$s=$mem['num_seg']*$mem['seg_size'];
				$a=$mem['avail_mem'];
				$x=130;
				$y=1;
				$j=1;

				// This block of code creates the bar chart.  It is a lot more complex than you
				// would expect because we try to visualize any memory fragmentation as well.
				for($i=0; $i<$mem['num_seg']; $i++)
				{
					$ptr = 0;
					$free = $mem['block_lists'][$i];
					uasort($free, 'block_sort');
					foreach($free as $block)
					{
						if($block['offset']!=$ptr)
						{
							$h=(GRAPH_SIZE-5)*($block['offset']-$ptr)/$s;
							if ($h>0)
							{
								$j++;
								if($j<75) fill_box($image,$x,$y,50,$h,$col_black,$col_red,$this->_bsize($block['offset']-$ptr),$j);
								else fill_box($image,$x,$y,50,$h,$col_black,$col_red);
							}
							$y+=$h;
						}
						$h=(GRAPH_SIZE-5)*($block['size'])/$s;
						if ($h>0)
						{
							$j++;
							if($j<75) fill_box($image,$x,$y,50,$h,$col_black,$col_green,$this->_bsize($block['size']),$j);
							else fill_box($image,$x,$y,50,$h,$col_black,$col_green);
						}
						$y+=$h;
						$ptr = $block['offset']+$block['size'];
					}
					if ($ptr < $mem['seg_size'])
					{
						$h = (GRAPH_SIZE-5) * ($mem['seg_size'] - $ptr) / $s;
						if ($h > 0)
						{
							fill_box($image,$x,$y,50,$h,$col_black,$col_red,_bsize($mem['seg_size']-$ptr),$j++);
						}
					}
				}
				break;

			case 4:
				$s=$cache['num_hits']+$cache['num_misses'];
				$a=$cache['num_hits'];

				fill_box($image, 30,$size,50,-$a*($size-21)/$s,$col_black,$col_green,sprintf("%.1f%%",$cache['num_hits']*100/$s));
				fill_box($image,130,$size,50,-max(4,($s-$a)*($size-21)/$s),$col_black,$col_red,sprintf("%.1f%%",$cache['num_misses']*100/$s));
				break;
		}

		// Output image and exit
		header("Content-type: image/png");
		imagepng($image);
		exit;
	}
}