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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$dateFormat = '%b %d, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'M d, Y';
	$tz = false;
}


if (!defined('n')) {

/**
 * Description for ''n''
 */
	define('n',"\n");

/**
 * Description for ''t''
 */
	define('t',"\t");

/**
 * Description for ''r''
 */
	define('r',"\r");

/**
 * Description for ''a''
 */
	define('a','&amp;');
}

/**
 * Html helper class
 */
class ProjectsHtml 
{
	//----------------------------------------------------------
	// Date/time management
	//----------------------------------------------------------
	
	/**
	 * Format time
	 * 
	 * @param      string $value
	 * @param      string $format
	 * @return     string
	 */
	public function valformat($value, $format) 
	{
		if ($format == 1) 
		{
			return(number_format($value));
		} 
		elseif ($format == 2 || $format == 3) 
		{
			if ($format == 2) 
			{
				$min = round($value / 60);
			} else 
			{
				$min = floor($value / 60);
				$sec = $value - ($min * 60);
			}
			$hr = floor($min / 60);
			$min -= ($hr * 60);
			$day = floor($hr / 24);
			$hr -= ($day * 24);
			if ($day == 1) 
			{
				$day = "1 day, ";
			} 
			elseif ($day > 1) 
			{
				$day = number_format($day) . " days, ";
			} 
			else 
			{
				$day = "";
			}
			if ($format == 2) 
			{
				return(sprintf("%s%d:%02d", $day, $hr, $min));
			} 
			else 
			{
				return(sprintf("%s%d:%02d:%02d", $day, $hr, $min, $sec));
			}
		} 
		else 
		{
			return($value);
		}
	}
	
	/**
	 * Specially formatted time display
	 * 
	 * @param      string 	$time
	 * @param      boolean 	$full	Return detailed date/time?
	 * @return     string
	 */
	public function formatTime($time, $full = false) 
	{
		$parsed 	= date_parse($time);
		$timestamp	= strtotime($time);

		$now = date( 'Y-m-d H:i:s' );
		$current  	= date_parse($now);
		
		if ($full)
		{
			return date('g:i A M j, Y', $timestamp);
		}
		
		if ($current['year'] == $parsed['year'])
		{
			if ($current['month'] == $parsed['month'] && $current['day'] == $parsed['day'])
			{
				return date('g:i A', $timestamp);
			}
			else
			{
				return date('M j', $timestamp);
			}
		}
		else
		{
			return date('M j, Y', $timestamp);
		}
	}
	
	/**
	 * Time elapsed from moment
	 * 
	 * @param      string $timestamp
	 * @return     string
	 */
	public function timeAgo($timestamp) 
	{
		$timestamp = Hubzero_View_Helper_Html::mkt($timestamp);
		$text = Hubzero_View_Helper_Html::timeAgoo($timestamp);
		
		$parts = explode(' ',$text);

		$text  = $parts[0].' '.$parts[1];
		if ($text == '0 seconds' || $parts[0] < 0) 
		{
			$text = JText::_('COM_PROJECTS_JUST_A_MOMENT');
		}

		return $text;
	}
	
	/**
	 * TGet time difference
	 * 
	 * @param      string $difference
	 * @return     string
	 */
	public function timeDifference ($difference)
	{
		// Set the periods of time
		$periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');

		// Set the number of seconds per period
		$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

		// Determine which period we should use, based on the number of seconds lapsed.
		// If the difference divided by the seconds is more than 1, we use that. Eg 1 year / 1 decade = 0.1, so we move on
		// Go from decades backwards to seconds
		for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $difference / $lengths[$val]) <= 1); $val--);

		// Ensure the script has found a match
		if ($val < 0) 
		{
			$val = 0;
		}
		
		// Set the current value to be floored
		$number = floor($number);

		// If required create a plural
		if ($number != 1) 
		{
			$periods[$val] .= 's';
		}

		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);
		
		$parts = explode(' ', $text);

		$text  = $parts[0] . ' ' . $parts[1];
		if ($text == '0 seconds') 
		{
			$text = JText::_('COM_PROJECTS_JUST_A_MOMENT');
		}

		return $text;
	}
	
	/**
	 * Time current moment
	 * 
	 * @param      string $timestamp
	 * @return     string
	 */
	public function timeFromNow ($timestamp)
	{
		// Store the current time
		$current_time = time();

		// Determine the difference, between the time now and the timestamp
		$difference =  strtotime($timestamp) - $current_time;

		return ProjectsHtml::timeDifference($difference);
	}
	
	//----------------------------------------------------------
	// File management
	//----------------------------------------------------------
	
	/**
	 * Get file attributes
	 * 
	 * @param      string $path
	 * @param      string $base_path
	 * @param      string $get
	 * @param      string $prefix
	 * @return     string
	 */
	public function getFileAttribs( $path = '', $base_path = '', $get = '', $prefix = JPATH_ROOT )
	{
		if (!$path) 
		{
			return '';
		}
				
		// Get extension
		if ($get == 'ext') 
		{
			$ext = explode('.', basename($path));
			$ext = count($ext) > 1 ? end($ext) : '';
			return strtoupper($ext);
		}
		
		$path = DS . trim($path, DS);
		if ($base_path)
		{
			$base_path = DS . trim($base_path, DS);	
		}
		
		if (substr($path, 0, strlen($base_path)) == $base_path) 
		{
			// Do nothing
		} 
		else 
		{
			$path = $base_path . $path;
		}
		$path = $prefix . $path;
	
		$fs = '';
		
		// Get the file size if the file exist
		if (file_exists( $path )) 
		{
			try
			{
				$fs = filesize( $path );
			}
			catch (Exception $e)
			{
				// could not get file size
			}			
		}
		$fs = ProjectsHtml::formatSize($fs);
		return ($fs) ? $fs : '';

	}
	
	/**
	 * Get directory size
	 * 
	 * @param      string $directory
	 * @return     string
	 */
	public function getDirSize ($directory = '') 
	{
		if(!$directory) 
		{
			return 0;
		}
		$dirSize=0;

		if (!$dh=opendir($directory))
		{
			return false;
		}

		while ($file = readdir($dh))
		{
			if($file == "." || $file == "..")
			{
				continue;
			}

			if (is_file($directory."/".$file))
			{
				$dirSize += filesize($directory."/".$file);
			}

			if (is_dir($directory."/".$file))
			{
				$dirSize += ProjectsHtml::getDirSize($directory."/".$file);
			}
		}

		closedir($dh);

		return $dirSize;
	}
	
	/**
	 * Format size
	 * 
	 * @param      int $file_size
	 * @param      int $round
	 * @return     string
	 */
	public function formatSize($file_size, $round = 0) 
	{
		if ($file_size >= 1073741824) 
	//	if ($file_size >= 107374182) 
		{
			$file_size = round(($file_size / 1073741824 * 100), $round) / 100 . 'GB';
		} 
		elseif ($file_size >= 1048576) 
		{
			$file_size = round(($file_size / 1048576 * 100), $round) / 100 . 'MB';
		} 
		elseif ($file_size >= 1024) 
		{
			$file_size = round(($file_size / 1024 * 100) / 100, $round) . 'KB';
		} 
		elseif ($file_size < 1024) 
		{
			$file_size = $file_size . 'b';
		}

		return $file_size;
	}
	
	/**
	 * Convert file size
	 * 
	 * @param      int $file_size
	 * @param      string $from
	 * @param      string $to
	 * @param      string $round
	 * @return     string
	 */
	public function convertSize($file_size, $from = 'b', $to = 'GB', $round = 0) 
	{
		$file_size = str_replace(' ', '', $file_size);

		if($from == 'b') 
		{
			if ($to == 'GB') 
			{
				$file_size = round(($file_size / 1073741824 * 100), $round) / 100;
			} 
			elseif ($to == 'MB') 
			{
				$file_size = round(($file_size / 1048576 * 100), $round) / 100 ;
			} 
			elseif ($to == 'KB') 
			{
				$file_size = round(($file_size / 1024 * 100) / 100, $round);
			} 
		}
		else if($from == 'GB') 
		{
			if ($to == 'b') 
			{
				$file_size = $file_size * 1073741824;
			} 
			if ($to == 'KB') 
			{
				$file_size = $file_size * 1048576;
			}
			if ($to == 'MB') 
			{
				$file_size = $file_size * 1024;
			}
		}

		return $file_size;
	}
	
	/**
	 * Get google icon image
	 * 
	 * @param      string $mimeType
	 * @param      boolean $include_dir
	 * @param      string $icon
	 * @return     string
	 */
	public function getGoogleIcon ($mimeType, $include_dir = 1, $icon = '') 
	{
		switch (strtolower($mimeType)) 
		{
			case 'application/vnd.google-apps.presentation':
				$icon = 'presentation';
				break;
				
			case 'application/vnd.google-apps.spreadsheet':
				$icon = 'sheet';
				break;
				
			case 'application/vnd.google-apps.document':
				$icon = 'doc';
				break;
				
			case 'application/vnd.google-apps.drawing':
				$icon = 'drawing';
				break;
				
			case 'application/vnd.google-apps.form':
				$icon = 'form';
				break;
				
			case 'application/vnd.google-apps.folder':
				$icon = 'folder';
				break;
						
			default: 
				$icon = 'gdrive';
				break;				
		}
		
		if ($include_dir)
		{
			$icon = "/plugins/projects/files/images/google/" . $icon . '.gif';
		}
		return $icon;
		
	}
	
	/**
	 * Get file icon image
	 * 
	 * @param      string $ext
	 * @param      boolean $include_dir
	 * @param      string $icon
	 * @return     string
	 */
	public function getFileIcon ($ext, $include_dir = 1, $icon = '') 
	{
		switch (strtolower($ext)) 
		{
			case 'pdf':
				$icon = 'page_white_acrobat';
				break;
			case 'txt':
			case 'css':
			case 'rtf':
			case 'sty':
			case 'cls':
			case 'log':
				$icon = 'page_white_text';
				break;
			case 'sql':
				$icon = 'page_white_sql';
				break;
			case 'dmg':
			case 'exe':
				$icon = 'page_white_gear';
				break;
			case 'eps':
			case 'ai':
			case 'wmf':
				$icon = 'page_white_vector';
				break;
			case 'php':
				$icon = 'page_white_php';
				break;
			case 'tex':
			case 'ltx':
				$icon = 'page_white_tex';
				break;
			case 'swf':
				$icon = 'page_white_flash';
				break;
			case 'key':
				$icon = 'page_white_keynote';
				break;
			case 'numbers':
				$icon = 'page_white_numbers';
				break;
			case 'pages':
				$icon = 'page_white_pages';
				break;
			case 'html':
			case 'htm':
				$icon = 'page_white_code';
				break;
			case 'xls':
			case 'xlsx':
			case 'tsv':
			case 'csv':
			case 'ods':
				$icon = 'page_white_excel';
				break;
			case 'ppt':
			case 'pptx':
			case 'pps':
				$icon = 'page_white_powerpoint';
				break;
			case 'mov':
			case 'mp4':
			case 'm4v':
			case 'avi':
				$icon = 'page_white_film';
				break;
			case 'jpg':
			case 'jpeg':
			case 'gif':
			case 'tiff':
			case 'bmp':
			case 'png':
				$icon = 'page_white_picture';
				break;
			case 'mp3':
			case 'aiff':
			case 'm4a':
			case 'wav':
				$icon = 'page_white_sound';
				break;
			case 'zip':
			case 'rar':
			case 'gz':
			case 'sit':
			case 'sitx':
			case 'zipx':
			case 'tar':
			case '7z':
				$icon = 'page_white_compressed';
				break;
			case 'doc':
			case 'docx':
				$icon = 'page_white_word';
				break;
			case 'folder':
				$icon = 'folder';
				break;
			default: 
				$icon = 'page_white';
				break;
		}
		
		if ($include_dir)
		{
			$icon = "/plugins/projects/files/images/" . $icon . '.gif';
		}
		return $icon;
	}
	
	//----------------------------------------------------------
	// Project page elements
	//----------------------------------------------------------
	
	/**
	 * Embed project image
	 * 
	 * @param      object $view
	 * @return     string HTML
	 */
	public function embedProjectImage( $view )
	{ 
		$path = DS . trim($view->config->get('imagepath', '/site/projects'), DS) . DS . $view->project->alias . DS . 'images';
		$image  = $view->project->picture 
			&& file_exists( JPATH_ROOT . $path . DS . $view->project->picture ) 
			? $path . DS . $view->project->picture 
			: ProjectsHtml::getThumbSrc($view->project->id, 
				$view->project->alias, $view->project->picture, $view->config);
		?>
	<div id="pimage">
		<a href="<?php echo JRoute::_('index.php?option=' . $view->option . a . 'alias='
		.$view->project->alias); ?>" title="<?php echo $view->project->title . ' - ' 
		. JText::_('COM_PROJECTS_VIEW_UPDATES'); ?>">
			<img src="<?php echo $image;  ?>" alt="<?php echo $view->project->title; ?>" /></a>
	</div>	
	<?php 
	}
	
	/**
	 * Write member options
	 * 
	 * @param      object $view
	 * @return     string HTML
	 */
	public function writeMemberOptions ( $view ) 
	{ 
		$options = '';
		$role    = JText::_('COM_PROJECTS_PROJECT') . ' <span>';
		
		switch ($view->project->role)
		{
			case 1:
				$role .= JText::_('COM_PROJECTS_LABEL_OWNER');
				$options .= '<li><a href="' . JRoute::_('index.php?option=' 
						 . $view->option . a . 'task=edit' . a . 'alias=' 
						 . $view->project->alias) . '">' . JText::_('COM_PROJECTS_EDIT_PROJECT') . '</a></li>';
				$options .= '<li><a href="' . JRoute::_('index.php?option=' 
						 . $view->option . a . 'task=edit' . a . 'alias=' 
						 . $view->project->alias) . '?edit=team">' . JText::_('COM_PROJECTS_INVITE_PEOPLE') . '</a></li>';
				break;
			default:
				$role .= JText::_('COM_PROJECTS_LABEL_COLLABORATOR');
		}
		$role 	.= '</span>';
		
		if (!$view->project->private) 
		{ 
			$options .= '<li><a href="' . JRoute::_('index.php?option=' 
					 . $view->option . a . 'alias=' 
					 . $view->project->alias) . '?preview=1">' 
					 . JText::_('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE') . '</a></li>';
		}
		if (isset($view->project->counts['team']) && $view->project->counts['team'] > 1) 
		{
			$options .= '<li><a href="' . JRoute::_('index.php?option=' 
					 . $view->option . a . 'alias=' 
					 . $view->project->alias . a. 'active=team') . '?action=quit">' 
					 . JText::_('COM_PROJECTS_LEAVE_PROJECT') . '</a></li>';
		} 
		
		$html = "\n" . t.t. '<ul id="member_options">' . "\n";
		$html.= t.t.' <li>' . ucfirst($role) . "\n";
		$html.= t.t.' 	<div id="options-dock">' . "\n";
		$html.= t.t.' 		<div>' . "\n";
		$html.= t.t.' 			<p>' . JText::_('COM_PROJECTS_JOINED') 
				. ' ' . JHTML::_('date', $view->project->since, $dateFormat, $tz) . '</p>' . "\n";
		if ($options) 
		{ 
			$html.= t.t.'			<ul>' . "\n";
			$html.= t.t.t.t.t.t. $options . "\n";
			$html.= t.t.'			</ul>' . "\n";	
		}
		$html.= t.t.' 		</div>' . "\n";
		$html.= t.t.' 	</div>' . "\n";
		$html.= t.t.' </li>' . "\n";
		$html.= t.t.'</ul>' . "\n";
		echo $html;		
	}
	
	/**
	 * Write project header
	 * 
	 * @param      object $view
	 * @param      boolean $back
	 * @param      boolean $underline
	 * @param      int $show_privacy
	 * @param      boolean $show_pic
	 * @return     string HTML
	 */
	public function writeProjectHeader ($view, $back = 0, $underline = 0, $show_privacy = 0, $show_pic = 1) 
	{
		// Use alias or id in urls?
		$use_alias = $view->config->get('use_alias', 0);
		$goto  = $use_alias ? 'alias='.$view->project->alias : 'id='.$view->project->id;
		$privacy_txt = $view->project->private ? JText::_('COM_PROJECTS_PRIVATE') : JText::_('COM_PROJECTS_PUBLIC');
		
		if($view->project->private) 
		{
			$privacy = '<span class="private">' . ucfirst($privacy_txt) . '</span>';
		}
		else 
		{
			$privacy = '<a href="' . JRoute::_('index.php?option=' . $view->option . a . $goto) 
					. '/?preview=1" title="' . JText::_('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE') . '">'
					. ucfirst($privacy_txt) . '</a>'; 
		}
		
		$start = ($show_privacy == 2 && $view->project->owner) 
				? '<span class="h-privacy">' .$privacy . '</span> ' . strtolower(JText::_('COM_PROJECTS_PROJECT'))
				: ucfirst(JText::_('COM_PROJECTS_PROJECT'));
	?>	
	<div id="content-header" <?php if(!$show_pic) { echo 'class="nopic"'; } ?>>
		<?php if($show_pic) { ?>
		<div class="pthumb"><a href="<?php echo JRoute::_('index.php?option='.$view->option.a.$goto); ?>" title="<?php echo JText::_('COM_PROJECTS_VIEW_UPDATES'); ?>"><img src="<?php echo ProjectsHtml::getThumbSrc($view->project->id, $view->project->alias, $view->project->picture, $view->config); ?>" alt="<?php echo $view->project->title; ?>" /></a></div>
		<?php } ?>
		<div class="ptitle">
			<h2><a href="<?php echo JRoute::_('index.php?option='.$view->option.a.$goto); ?>"><?php echo Hubzero_View_Helper_Html::shortenText($view->project->title, 50, 0); ?> <span>(<?php echo $view->project->alias; ?>)</span></a></h2>
			<?php if($back)  { ?>
			<h3 class="returnln"><?php echo JText::_('COM_PROJECTS_RETURN_TO'); ?> <a href="<?php echo JRoute::_('index.php?option='.$view->option.a.$goto); ?>"><?php echo JText::_('COM_PROJECTS_PROJECT_PAGE'); ?></a></h3>
			<?php } else { ?>
			<h3 <?php if($underline) { echo 'class="returnln"'; } ?>><?php echo $start .' '.JText::_('COM_PROJECTS_BY').' '; 
			if($view->project->owned_by_group) 
			{	
				$group = Hubzero_Group::getInstance( $view->project->owned_by_group );	
				if($group) 
				{
					echo ' '.JText::_('COM_PROJECTS_GROUP').' <a href="/groups/'.$group->get('cn').'">'.$group->get('cn').'</a>';
				}
				else 
				{
					echo JText::_('COM_PROJECTS_UNKNOWN').' '.JText::_('COM_PROJECTS_GROUP');
				}		
			}
			else 
			{
				echo '<a href="/members/'.$view->project->created_by_user.'">'.$view->project->fullname.'</a>';	
			//	echo '<span class="prominent">'.$view->project->fullname.'</span>';	
			}
			?>
			<?php if($show_privacy == 1) { ?>
				<span class="privacy <?php if($view->project->private) { echo 'private'; } ?>"><?php if(!$view->project->private) {  ?><a href="<?php echo JRoute::_('index.php?option='.$view->option.a.$goto).'/?preview=1'; ?>"><?php } ?><?php echo $privacy_txt; ?><?php if(!$view->project->private) {  ?></a><?php } ?> <?php echo strtolower(JText::_('COM_PROJECTS_PROJECT')); ?>
				</span>
			<?php } ?>
			</h3>
			<?php } ?>
		</div>
	</div><!-- / #content-header -->
	<?php 
	}
	
	/**
	 * Get project thumbnail
	 * 
	 * @param      int $id
	 * @param      string $alias
	 * @param      string $picname
	 * @param      array $config
	 * @return     string HTML
	 */
	public function getThumbSrc( $id, $alias, $picname = '', $config )
	{		
		$src  = '';
		$path = DS . trim($config->get('imagepath', '/site/projects'), DS) . DS . $alias . DS . 'images';
	
		if ($picname) 
		{
			$ih = new ProjectsImgHandler();
			$thumb = $ih->createThumbName($picname);
			$src = $thumb && file_exists( JPATH_ROOT . $path . DS . $thumb ) ? $path . DS . $thumb :  '';
		}
		if (!$src) 
		{
			$src = $config->get('defaultpic');
		}
		
		return $src;
	}
	
	//----------------------------------------------------------
	// Misc
	//----------------------------------------------------------
	
	/**
	 * Show 'no preview' message
	 * 
	 * @param      string $msg
	 * @return     string HTML
	 */
	public function showNoPreviewMessage( $msg = '' ) 
	{
		$msg = $msg ? $msg : JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_PREVIEW_NO_CONTENT');
		return '<p class="pale">'.$msg.'</p>';
	}
	
	/**
	 * Generate random code
	 * 
	 * @param      int $minlength
	 * @param      int $maxlength
	 * @param      boolean $usespecial
	 * @param      boolean $usenumbers
	 * @param      boolean $useletters
	 * @return     string HTML
	 */	
	public function generateCode( $minlength = 10, $maxlength = 10, $usespecial = 0, $usenumbers = 0, $useletters = 1, $mixedcaps = false )
	{	
		$key = '';
		$charset = '';
		if ($useletters) $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";		
		if ($useletters && $mixedcaps) $charset .= "abcdefghijklmnopqrstuvwxyz";
		if ($usenumbers) $charset .= "0123456789";
		if ($usespecial) $charset .= "~@#$%^*()_+-={}|][";
		if ($minlength > $maxlength) $length = mt_rand ($maxlength, $minlength);
		else $length = mt_rand ($minlength, $maxlength);
		for ($i=0; $i<$length; $i++) $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
		return $key;
	}
	
	/**
	 * Clean up text
	 * 
	 * @param      string $in
	 * @return     string
	 */		
	public function cleanText ($in = '') 
	{	
		$in = stripslashes($in);
		$in = str_replace('&quote;','&quot;',$in);
		$in = htmlspecialchars($in);

		if (!strstr( $in, '</p>' ) && !strstr( $in, '<pre class="wiki">' )) 
		{
			$in = str_replace("<br />","",$in);
		}
		return $in;
	}
	
	/**
	 * Replace urls in text
	 * 
	 * @param      string $string
	 * @param      string $rel
	 * @return     string HTML
	 */		
	public function replaceUrls($string, $rel = 'nofollow') 
	{
	    $host = "([a-z\d][-a-z\d]*[a-z\d]\.)+[a-z][-a-z\d]*[a-z]";
	    $port = "(:\d{1,})?";
	    $path = "(\/[^?<>\#\"\s]+)?";
	    $query = "(\?[^<>\#\"\s]+)?";
	    return preg_replace("#((ht|f)tps?:\/\/{$host}{$port}{$path}{$query})#i", "<a href=\"$1\" rel=\"{$rel}\">$1</a>", $string);
	}
	
	/**
	 * Search for value in array
	 * 
	 * @param      string $needle
	 * @param      string $haystack
	 * @return     boolean
	 */	
	public function myArraySearch( $needle, $haystack ) 
	{
	    if (empty($needle) || empty($haystack)) 
		{
            return false;
        }

        foreach ($haystack as $key => $value) 
		{
            $exists = 0;
            foreach ($needle as $nkey => $nvalue) 
			{
                if (!empty($value->$nkey) && $value->$nkey == $nvalue) 
				{
                    $exists = 1;
                } 
				else 
				{
                    $exists = 0;
                }
            }
            if ($exists) return $key;
        }

        return false;
	}
	
	/**
	 * Get appended to file name random string
	 * 
	 * @param      string $path
	 * 
	 * @return     string
	 */
	public function getAppendedNumber ( $path = null )
	{
		$append = '';
		
		$dirname 	= dirname($path);
		$filename 	= basename($path);
		$name 		= '';
		$file = explode('.', $filename);
		
		$n = count($file);
		if ($n > 1) 
		{
			$name = $file[$n-2];
		}
		else
		{
			$name = $path;
		}
		
		$parts = explode('-', $name);
		if (count($parts) > 1)
		{
			$append = intval(end($parts));
		}
		
		return $append;
	}
	
	/**
	 * Replace file ending
	 * 
	 * @param      string $path
	 * @param      string $end
	 * @param      string $delim
	 * @return     string
	 */
	public function cleanFileNum ( $path = null, $end = '', $delim = '-' )
	{
		$newpath = $path;
		
		if ($end)
		{
			$file = explode('.', $path);
			$n = count($file);
			$ext = '';
			if ($n > 1) 
			{
				$name = $file[$n-2];
				$ext  = array_pop($file);			
			}
			else
			{
				$name = $path;
			}
			
			$parts = explode($delim, $name);
			if (count($parts) > 1)
			{
				$oldnum = intval(end($parts));
				if ($oldnum == $end)
				{
					$out = array_pop($parts);
					$name = implode('', $parts);
				}
			}
			
			$newpath = $ext ? $name . '.' . $ext : $name;
		}
		
		return $newpath;
	}
	
	/**
	 * Append random string to file name
	 * 
	 * @param      string $path
	 * @param      string $append
	 * @param      string $ext
	 * @return     string
	 */
	public function fixFileName ( $path = null, $append = '', $ext = '' )
	{
		if (!$path) 
		{
			$this->setError( JText::_('No path set.') );
			return false;
		}
		if (!$append) 
		{
			return $path;
		}
		
		$newname 	= '';
		$dirname 	= dirname($path);
		$filename 	= basename($path);
		
		$file = explode('.', $filename);
		$n = count($file);
		if ($n > 1) 
		{
			$file[$n-2] .= $append;
			
			$end = array_pop($file);
			$file[] = $end;
			$filename = implode('.',$file);
		}
		else
		{
			$filename = $filename . $append;
		}
		
		if ($ext)
		{
			$filename = $filename . '.' . $ext;
		}
		
		$newname = $dirname && $dirname != '.' ? $dirname . DS . $filename : $filename;
		
		return $newname;
	}
	
	/**
	 * Return filename without extension
	 * 
	 * @param      string  $file      String to shorten
	 * @return     string 
	 */
	public function takeOutExt($file = '')
	{
		// Take out extention
		if ($file)
		{
			$parts = explode('.', $file);			
			
			if (count($parts) > 1) 
			{
				$end = array_pop($parts);
			}
			
			if (count($parts) > 1) 
			{
				$end = array_pop($parts);
			}
			
			$file = implode($parts);
		}
		
		return $file;
	}
	
	/**
	 * Shorten a string to a max length, preserving whole words
	 * 
	 * @param      string  $text      String to shorten
	 * @param      integer $chars     Max length to allow
	 * @return     string 
	 */
	public static function shortenText($text, $chars=300)
	{
		$text = trim($text);

		if (strlen($text) > $chars)
		{
			$text = $text . ' ';
			$text = substr($text, 0, $chars);
		}

		return $text;
	}
	
	/**
	 * Shorten user full name
	 * 
	 * @param      string $name
	 * @param      int $chars
	 * @return     string
	 */	
	public function shortenName( $name, $chars = 12 ) 
	{
		$name = trim($name);

		if (strlen($name) > $chars) 
		{
			$names = explode(' ',$name);
			$name = $names[0];
			if (count($names) > 0 && $names[1] != '') 
			{
				$name  = $name.' ';
				$name .= substr($names[1], 0, 1);
				$name .= '.';
			}
		}
		if ($name == '') 
		{
			$name = JText::_('COM_PROJECTS_UNKNOWN');
		}
	
		return $name;
	}
	
	/**
	 * Shorten user full name
	 * 
	 * @param      string $name
	 * @param      int $chars
	 * @return     string
	 */	
	public function shortenUrl( $name, $chars = 40 ) 
	{
		$name = trim($name);

		if (strlen($name) > $chars) 
		{
			$name = substr($name, 0, $chars);
			$name = $name . '...';
		}
	
		return $name;
	}		
	
	/**
	 * Shorten file name
	 * 
	 * @param      string $name
	 * @param      int $chars
	 * @return     string
	 */
	public function shortenFileName( $name, $chars = 30 ) 
	{
		$name = trim($name);
		$original = $name;
		
		$chars = $chars < 10 ? 10 : $chars;

		if (strlen($name) > $chars) 
		{
			$cutFront = $chars - 10;
			$name = substr($name, 0, $cutFront);
			$name = $name . '&#8230;';
			$name = $name . substr($original, -10, 10);
		}
		if ($name == '') 
		{
			$name = '&#8230;';
		}
	
		return $name;
	}
	
	//----------------------------------------------------------
	// Reviewers
	//----------------------------------------------------------
	
	/**
	 * Get admin notes
	 * 
	 * @param      string $notes
	 * @param      string $reviewer
	 * @return     string
	 */
	public function getAdminNotes($notes = '', $reviewer = '')
	{
		preg_match_all("#<nb:".$reviewer.">(.*?)</nb:".$reviewer.">#s", $notes, $matches);
		$ntext = '';
		if (count($matches) > 0) 
		{
			$notes = $matches[0];
			if (count($notes) > 0)
			{
				krsort($notes);
				foreach($notes as $match)
				{
					$ntext .= ProjectsHtml::parseAdminNote($match, $reviewer);
				}
			}
		}
		
		return $ntext;
	}
	
	/**
	 * Get admin notes count
	 * 
	 * @param      string $notes
	 * @param      string $reviewer
	 * @return     string
	 */
	public function getAdminNoteCount($notes = '', $reviewer = '')
	{

		preg_match_all("#<nb:".$reviewer.">(.*?)</nb:".$reviewer.">#s", $notes, $matches);

		if (count($matches) > 0) 
		{
			$notes = $matches[0];
			return count($notes);
		}
		
		return 0;
	}
	
	/**
	 * Parse admin notes
	 * 
	 * @param      string $note
	 * @param      string $reviewer
	 * @param      boolean $showmeta
	 * @param      int $shorten
	 * @return     string
	 */
	public function parseAdminNote($note = '', $reviewer = '', $showmeta = 1, $shorten = 0)
	{
		$note = str_replace('<nb:'.$reviewer.'>','', $note);
		$note = str_replace('</nb:'.$reviewer.'>','', $note);
		
		preg_match("#<meta>(.*?)</meta>#s", $note, $matches);
		if (count($matches) > 0) 
		{
			$meta = $matches[0];
			$note   = preg_replace( '#<meta>(.*?)</meta>#s', '', $note );
				
			if($shorten)
			{
				$note   = Hubzero_View_Helper_Html::shortenText($note, $shorten, 0);
			}
			if($showmeta)
			{
				$meta = str_replace('<meta>','' , $meta);
				$meta = str_replace('</meta>','', $meta);
				
				$note  .= '<span class="block mini faded">' . $meta . '</span>';
			}
		}
		$note = $note ? '<p class="admin-note">' . $note . '</p>' : '';
		
		return $note;
	}
		
	/**
	 * Get last admin note
	 * 
	 * @param      string $notes
	 * @param      string $reviewer
	 * @return     string
	 */
	public function getLastAdminNote($notes = '', $reviewer = '')
	{
		$match = '';
		preg_match_all("#<nb:".$reviewer.">(.*?)</nb:".$reviewer.">#s", $notes, $matches);

		if (count($matches) > 0) 
		{
			$notes = $matches[0];
			if(count($notes) > 0)
			{
				$match = ProjectsHtml::parseAdminNote(end($notes), $reviewer, 1, 100);
			}
		} 
		else 
		{
			$match = '';
		}
		
		return $match;
	}
	
	/**
	 * Email
	 * 
	 * @param      string $email
	 * @param      string $subject
	 * @param      string $message
	 * @param      array $from
	 * @return     void
	 */	
	public function email($email, $subject, $message, $from) 
	{
		if ($from) 
		{
			$args = "-f '" . $from['email'] . "'";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/plain; charset=utf-8\n";
			$headers .= 'From: ' . $from['name'] .' <'. $from['email'] . ">\n";
			$headers .= 'Reply-To: ' . $from['name'] .' <'. $from['email'] . ">\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: High\n";
			$headers .= 'X-Mailer: '. $from['name'] ."\n";
			if (mail($email, $subject, $message, $headers, $args)) 
			{
				return true;
			}
		}
		return false;
	}
}
