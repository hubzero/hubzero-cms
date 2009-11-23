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

defined( '_JEXEC' ) or die( 'Restricted access' );

if (!defined("n")) {
	define("t","\t");
	define("n","\n");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class MwHtml 
{
	public function error($msg, $tag='p')
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}

	//-----------

	public function warning($msg, $tag='p')
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'.n;
	}

	//-----------
	
	public function help( $msg, $tag='p' )
	{
		return '<'.$tag.' class="help">'.$msg.'</'.$tag.'>'.n;
	}	
	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	//-----------
	
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}
	//-----------

	public function passed( $msg, $tag='p' )
	{
		return '<'.$tag.' class="passed">'.$msg.'</'.$tag.'>'.n;
	}

	//-----------

	public function div($txt, $cls='', $id='')
	{
		$html  = '<div';
		$html .= ($cls) ? ' class="'.$cls.'"' : '';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= '>'.n;
		$html .= $txt.n;
		$html .= '</div><!-- / ';
		if ($id) {
			$html .= '#'.$id;
		}
		if ($cls) {
			$html .= '.'.$cls;
		}
		$html .= ' -->'.n;
		return $html;
	}

	//-------------------------------------------------------------
	// Storage indicator functions
	//-------------------------------------------------------------

	public function storageQuotaWarning($sec, $padHours=false) 
	{
		// holds formatted string
		$hms = '';
		
		$days = intval(intval($sec) / 86400);
		if ($days) {
			$hms .= $days.' days, ';
			$sec = intval($sec) - 86400;
		}
		// there are 3600 seconds in an hour, so if we
		// divide total seconds by 3600 and throw away
		// the remainder, we've got the number of hours
		$hours = intval(intval($sec) / 3600);
		
		// add to $hms, with a leading 0 if asked for
		if ($hours) {
			$hms .= ($padHours) 
					? str_pad($hours, 2, "0", STR_PAD_LEFT). ' hours,'
					: $hours. ' hours, ';
		}
		
		// dividing the total seconds by 60 will give us
		// the number of minutes, but we're interested in
		// minutes past the hour: to get that, we need to
		// divide by 60 again and keep the remainder
		$minutes = intval(($sec / 60) % 60);
		
		if ($minutes) {
			// then add to $hms (with a leading 0 if needed)
			$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ' minutes, ';
		}
		
		// seconds are simple - just divide the total
		// seconds by 60 and keep the remainder
		$seconds = intval($sec % 60);
		
		// add to $hms, again with a leading 0 if needed
		$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT).' seconds';
		
		$msg  = 'You are currently at or exceeding your storage limit. ';
		$msg .= $hms.' remain until you can no longer store more data. ';
		$msg .= '<a href="'.JRoute::_('index.php?option='.$this->_option.a.'task=storageexceeded').'">Learn more on how to resolve this</a>.';
		return MwHTML::warning($msg);
	}

	//-----------
	
	public function writeMonitor( $amt, $du=NULL, $percent=0, $msgs=0, $ajax=0, $writelink=1 )
	{
		$html = '';
		if (!$ajax) {
			$html .= '<dl id="diskusage">'.n;
		}
		if ($writelink) {
			$html .= t.'<dt>Storage (<a href="'.JRoute::_('index.php?option=com_mw&task=storage').'">manage</a>)</dt>'.n;
		} else {
			$html .= t.'<dt>Storage</dt>'.n;
		}
		$html .= t.'<dd id="du-amount"><div style="width:'.$amt.'%;"><strong>&nbsp;</strong><span>'.$amt.'%</span></div></dd>'.n;
		if ($msgs) {
			if (count($du) <=1) {
				$html .= '<dd id="du-msg"><p class="error">Error trying to retrieve disk usage.</p></dd>';
			}
			if ($percent == 100) {
				$html .= '<dd id="du-msg"><p class="warning">You are currently at your storage limit. <a href="/mw/storageexceeded">Learn more on how to resolve this</a>.</p></dd>';
			}
			if ($percent > 100) {
				$html .= '<dd id="du-msg"><p class="warning">You are currently exceeding your storage limit. <a href="/mw/storageexceeded">Learn more on how to resolve this</a>.</p></dd>';
			}
		}
		if (!$ajax) {
			$html .= '</dl>'.n.n;
		}
		return $html;
	}
	
	//-------------------------------------------------------------
	// Media manager functions
	//-------------------------------------------------------------
	
	public function pageTop( $option, $app, $title) 
	{
		?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php echo $title; ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<link rel="stylesheet" type="text/css" media="screen" href="/templates/<?php echo $app->getTemplate(); ?>/css/main.css" />
	<?php
		if (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$option.DS.'mw.css')) {
			echo '<link rel="stylesheet" type="text/css" media="screen" href="'.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$option.DS.'mw.css" />'.n;
		} else {
			echo '<link rel="stylesheet" type="text/css" media="screen" href="'.DS.'components'.DS.$option.DS.'mw.css" />'.n;
		}
	?> 
    </head>
 <body id="small-page">
 		<div class="databrowser">
		<?php
	}
	
	//-----------
	
	public function pageBottom() 
	{
		$html  = ' </div>'.n;
		$html .= ' </body>'.n;
		$html .= '</html>'.n;
		echo $html;
	}
	
	//-----------
	
	public function dir_name($dir)
	{
		$lastSlash = intval(strrpos($dir, '/'));
		if ($lastSlash == strlen($dir)-1) {
			return substr($dir, 0, $lastSlash);
		} else {
			return dirname($dir);
		}
	}

	//-----------
	
	public function draw_no_results()
	{
		MwHtml::draw_table_header(); 
		echo t.t.'<tr><td>'.JText::_('No files/folders found.').'</td></tr>'.n;
		MwHtml::draw_table_footer();
	}

	//-----------

	public function draw_no_dir() 
	{
		global $BASE_DIR, $BASE_ROOT;

		echo '<p class="alert">'.JText::_('CONFIG_PROBLEM').': &quot;'. $BASE_DIR.$BASE_ROOT .'&quot; '.JText::_('NOT_EXIST').'.</p>'.n;
	}

	//-----------

	public function draw_table_header() 
	{
		echo t.t.'<form action="index2.php" method="post" name="filelist" id="filelist">'.n;
		echo t.t.'<table border="0" cellpadding="0" cellspacing="0">'.n;
	}

	//-----------

	public function draw_table_footer() 
	{
		echo t.t.'</table>'.n;
		echo t.t.'</form>'.n;
	}

	//-----------

	public function show_doc($doc, $icon, $option, $listdir) 
	{
		?>
		 <tr>
		  <td><img src="<?php echo $icon; ?>" alt="<?php echo $doc; ?>" width="16" height="16" /></td>
		  <td width="100%" style="padding-left: 0;"><?php echo $doc; ?></td>
		  <td><a href="index.php?option=com_mw&amp;task=deletefile&amp;delFile=<?php echo $doc; ?>&amp;listdir=<?php echo $listdir; ?>&amp;no_html=1" target="imgManager" style="border:none;" onclick="return deleteImage('<?php echo $doc; ?>');" title="<?php echo JText::_('DELETE_THIS'); ?>"><img src="components/<?php echo $option; ?>/images/trash.gif" width="15" height="15" style="border:none;" alt="<?php echo JText::_('DELETE'); ?>" /></a></td>
		 </tr>
		<?php
	}

	//-----------

	public function parse_size($size)
	{
		if ($size < 1024) {
			return $size.' bytes';
		} else if ($size >= 1024 && $size < 1024*1024) {
			return sprintf('%01.2f',$size/1024.0).' Kb';
		} else {
			return sprintf('%01.2f',$size/(1024.0*1024)).' Mb';
		}
	}

	//-----------

	public function num_files($dir)
	{
		$total = 0;

		if (is_dir($dir)) {
			$d = @dir($dir);
			while (false !== ($entry = $d->read()))
			{
				if (substr($entry,0,1) != '.') {
					$total++;
				}
			}
			$d->close();
		}
		return $total;
	}
	
	//-----------
	
	public function imageStyle()
	{
		?>
		<script type="text/javascript">
		function updateDir()
		{
			var allPaths = window.top.document.forms[0].dirPath.options;
			for(i=0; i<allPaths.length; i++)
			{
				allPaths.item(i).selected = false;
				if((allPaths.item(i).value)== '<?php if (isset($listdir)) { echo $listdir ;} else { echo '/';}  ?>')
				{
					allPaths.item(i).selected = true;
				}
			}
		}

		function deleteImage(file)
		{
			if(confirm("Delete file \""+file+"\"?"))
				return true;

			return false;
		}
		
		function deleteFolder(folder, numFiles)
		{
			if(numFiles > 0) {
				alert('There are '+numFiles+' files/folders in "'+folder+'".\n\nPlease delete all files/folder in "'+folder+'" first.');
				return false;
			}
	
			if(confirm('Delete folder "'+folder+'"?'))
				return true;
	
			return false;
		}
		</script>
		<?php
	}
	
	//-----------

	public function showPath( $option, $dirtree) 
	{
		$html  = t.'<ul id="filepath">'.n;
		$html .= t.t.'<li class="home">'.n;
		if (!empty($dirtree)) {
			$html .= t.t.'<a href="'.JRoute::_('index.php?option='.$option.a.'task=listfiles').DS.'?no_html=1">'.JText::_('Home').'</a>'.n;
		} else {
			$html .= t.t.'<span>'.JText::_('Home').'</span>'.n;
		}
		$html .= t.t.'</li>'.n;
		if (!empty($dirtree)) {
			$path = '';
			$i = 0;
			foreach ($dirtree as $branch) 
			{
				if ($branch !='') {
					$path .= $branch.DS;
					$i++;
					$html .= t.t.'<li class="arrow">&raquo;</li>'.n;
					$html .= t.t.'<li class="folder">'.n;
					if ($i!=count($dirtree)) {
						$html .= t.t.'<a href="'.JRoute::_('index.php?option='.$option.a.'task=listfiles').DS.'?no_html=1&amp;listdir='.$path.'">'.ucfirst($branch).'</a>'.n;
					} else {
						$html .= t.t.'<span>'.ucfirst($branch).'</span>'.n;
					}
					$html .= t.t.'</li>'.n;
				}
			}
		}
		$html .= t.'</ul>'.n;
		
		echo $html;
	}
	
	//-----------

	public function show_dir( $option, $base, $path, $dir, $listdir, $num_files=0 ) 
	{
		$d = $listdir ? $listdir.$path : $path;
		//$num_files = MwHtml::num_files($base.$d);

		if ($listdir == '/') {
			$listdir = '';
		}
		?>
			<tr>
				<td><a href="<?php echo JRoute::_('index.php?option='.$option.a.'task=listfiles').DS.'?no_html=1&amp;listdir='.$d; ?>"><img src="components/<?php echo $option; ?>/images/folder.gif" alt="<?php echo $dir; ?>" width="16" height="16" /></a></td>
				<td width="100%" style="padding-left: 0;"><a href="<?php echo JRoute::_('index.php?option='.$option.a.'task=listfiles').DS.'?no_html=1&amp;listdir='.$d; ?>"><?php echo $dir; ?></a></td>
				<td>
				<?php if($dir !='data' && $dir!='sessions') { ?>
                <a href="index.php?option=<?php echo $option; ?>&amp;task=deletefolder&amp;delFolder=<?php echo $path; ?>&amp;listdir=<?php echo $listdir; ?>&amp;no_html=1" target="imgManager" onClick="return deleteFolder('<?php echo $dir; ?>', <?php echo $num_files; ?>);" title="<?php echo JText::_('Delete'); ?>"><img src="components/<?php echo $option; ?>/images/trash.gif" width="15" height="15" alt="<?php echo JText::_('Delete'); ?>" /></a>
                <?php } ?>
				</td>
			</tr>
		<?php
	}
}
?>