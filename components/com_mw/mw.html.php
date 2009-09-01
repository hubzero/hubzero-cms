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
	// Sections
	//-------------------------------------------------------------

	public function sections( $sections, $cats, $active='about', $h, $c ) 
	{
		$html = '';
		
		if (!$sections) {
			return $html;
		}
		
		$k = 0;
		foreach ($sections as $section) 
		{
			if ($section['html'] != '') {
				$cls  = ($c) ? $c.' ' : '';
				if (key($cats[$k]) != $active) {
					$cls .= ($h) ? $h.' ' : '';
				}
				$html .= MwHtml::div( $section['html'], $cls.'section', key($cats[$k]).'-section' );
			}
			$k++;
		}
		
		return $html;
	}
	
	//-----------
	
	public function tabs( $option, $id, $cats, $active='session' ) 
	{
		$html  = '<div id="sub-menu">'.n;
		$html .= t.'<ul>'.n;
		$i = 1;
		foreach ($cats as $cat)
		{
			$name = key($cat);
			if ($name != '') {
				$html .= t.t.'<li id="sm-'.$i.'"';
				$html .= (strtolower($name) == $active) ? ' class="active"' : '';
				$html .= '><a class="tab" rel="'.$name.'" href="'.JRoute::_('index.php?option='.$option.a.'task=view'.a.'sess='.$id.a.'active='.$name).'"><span>'.$cat[$name].'</span></a></li>'.n;
				$i++;	
			}
		}
		$html .= t.'</ul>'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</div><!-- / #sub-menu -->'.n;
		
		return $html;
	}

	//-----------

	public function accessdenied( $option, $error='' ) 
	{
		$xhub =& XFactory::getHub();
		
		$error = ($error) ? '<br />'.$error : '';
		
		$html  = MwHtml::div(MwHtml::hed(2, JText::_('MW_ACCESS_DENIED')),'full','content-header').n;
		$html .= '<div class="main section">'.n;
		$html .= MwHtml::warning( JText::_('MW_ERROR_ACCESS_DENIED').$error );
		$html .= '<p>The majority of tools on '.$xhub->getCfg('hubShortName').' are Open Source and freely available to the public. However, this particular tool has restricted access.</p>'.n;
		$html .= '<h3>How do I fix this?</h3>'.n;
		$html .= '<ul>'.n;
		$html .= '<li>If you feel that you should be able to access this tool, please <a href="/feedback/report_problems/">contact us</a>, and we will check the permissions on your account.</li>'.n;
		$html .= '<li>You might also try <a href="/tools/">browsing through other tools</a> on this site, to see if there is another freely available tool that would work just as well for you.</li>'.n;
		$html .= '</ul>'.n;
		$html .= '</div>'.n;
		
		return $html;
	}

	//-----------

	public function quotaexceeded( $option, $sessions, $authorized ) 
	{
		$juser =& JFactory::getUser();
		
		$html  = MwHtml::div(MwHtml::hed(2, JText::_('MW_QUOTA_EXCEEDED')),'full','content-header').n;
		$html .= '<div class="main section">'.n;
		$html .= MwHtml::warning( JText::_('MW_ERROR_QUOTA_EXCEEDED') );
		
		$html .= '<table class="sessions" summary="'.JText::_('TABLE_SUMMARY').'">'.n;
		$html .= t.'<thead>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th>Session</th>'.n;
		if ($authorized === 'admin') {
			$html .= t.t.t.'<th>Owner</th>'.n;
		}
		$html .= t.t.t.'<th>Started</th>'.n;
		$html .= t.t.t.'<th>Last accessed</th>'.n;
		$html .= t.t.t.'<th>Option</th>'.n;
		$html .= t.t.'</tr>'.n;
		$html .= t.'</thead>'.n;
		$html .= t.'<tbody>'.n;
		$cls = 'even';
		foreach ($sessions as $session)
		{
			$cls = ($cls == 'odd') ? 'even' : 'odd';
			
			$html .= t.t.'<tr class="'.$cls.'">'.n;
			
			// Resume session link
			$html .= t.t.t.'<td><a href="'.JRoute::_('index.php?option='.$option.a.'task=view'.a.'sess='.$session->sessnum).'" title="'.JText::_('MY_SESSIONS_RESUME_TITLE').'">'.$session->sessname.'</a></td>'.n;
			
			if ($authorized === 'admin') {
				$html .= t.t.t.'<td>'.$session->username.'</td>'.n;
			}
			$html .= t.t.t.'<td>'.$session->start.'</td>'.n;
			$html .= t.t.t.'<td>'.$session->accesstime.'</td>'.n;
			
			// A button to terminate the session.
			if ($juser->get('username') == $session->username || $authorized === 'admin') {
				$html .= t.t.t.'<td><a class="close" href="'.JRoute::_('index.php?option='.$option.a.'task=stop'.a.'sess='.$session->sessnum).'" title="'.JText::_('MY_SESSIONS_TERMINATE_TITLE').'">'.JText::_('MY_SESSIONS_TERMINATE').'</a></td>'.n;
			} else {
				$html .= t.t.t.'<td><a class="disconnect" href="'.JRoute::_('index.php?option='.$option.a.'task=unshare'.a.'sess='.$session->sessnum).'" title="'.JText::_('MY_SESSIONS_DISCONNECT_TITLE').'">'.JText::_('MY_SESSIONS_DISCONNECT').'</a> <br />'.JText::_('MY_SESSIONS_OWNER').': '.$session->username.'</td>'.n;
			}
			
			$html .= t.t.'</tr>'.n;
		}
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;
		$html .= '</div>'.n;
		
		return $html;
	}
	
	//-----------

	public function storage( $option, $banking, $exceeded=false, $output, $error, $percentage, $browse) 
	{
		$xhub =& XFactory::getHub();
		
		$html  = MwHtml::div(MwHtml::hed(2, JText::_('MW_STORAGE_MANAGEMENT')),'','content-header').n;
		$html .= '<div id="content-header-extra">'.n;
		$html .= MwHtml::writeMonitor($percentage, '', 0, 0, 0, 0);
		if ($percentage >= 100) {
			$html .= MwHTML::storageQuotaWarning($percentage);
		}
		$html .= '</div>'.n;
		$html .= '<div class="main section">'.n;
		$html .= t.'<div class="aside">'.n;
		$html .= t.t.MwHtml::hed(3,'What does "purge" do to my files?').n;
		$html .= '<p>The <strong>purge</strong> option is an easy way to free up space on your account.  It goes through the "data" directory where '.$xhub->getCfg('hubShortName').' stores your simulation results, and discards all of the results that have built up since you started using '.$xhub->getCfg('hubShortName').', or since your last purge. <a href="/kb/tools/purge">'.JText::_('Learn more').'</a>.</p>'.n;
		//$html .= '<p>The default choice is to purge <strong>minimally</strong>, which means that the purge operation will delete the oldest simulation results first, and continue deleting newer and newer results, stopping as soon as you are under quota.</p>'.n;
		//$html .= '<p>Other choices are <strong>older than 1 day</strong> or <strong>older than 7 days</strong> or <strong>older than 30 days</strong>, where you explicitly set the time range of simulation results that you want to purge.  You can also choose to purge <strong>all</strong>, which means that all of your simulation results will be deleted.</p>'.n;
		//$html .= '<p>If you want to keep some of your simulation results, you should choose the <strong>manually browse</strong> option and follow the instructions to access your file system via <a href="/kb/tips/webdav">WebDAV</a>.  Once you have connected that way, you can move select simulation results from the "data" directory to another location where they won\'t be affected by the automatic purge.  You could also browse through other directories and free up space manually.</p>'.n;
		//$html .= '<p>Note that you might purge and still be over quota if you haven\'t done much simulation.  In that case, you\'ll need to browse manually and look for other files that can be deleted.  Or, you can contact the nanoHUB staff and beg for more storage space!</p>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<div class="subject">'.n;
		if ($exceeded) {
			$html .= MwHtml::warning( JText::_('MW_ERROR_STORAGE_EXCEEDED') );
		}
		if ($output) {
			$html .= MwHtml::passed( $output );
		} else if ($error) {
			$html .= MwHtml::error( $error);
		}
		$html .= '<form action="index.php" method="post" id="purgeForm">'.n;
		$html .= t.'<fieldset>'.n;	
		$html .= t.t.MwHtml::hed(3,'Clean up Disk Space').n;
		//$html .= t.t.t.'<label>'.JText::_('Purge').' '.n;
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.'<select  name="degree">'.n;
		$html .= t.t.t.t.'<option value="default">minimally</option>'.n;
		$html .= t.t.t.t.'<option value="olderthan1">older than 1 day</option>'.n;
		$html .= t.t.t.t.'<option value="olderthan7">older than 7 days</option>'.n;
		$html .= t.t.t.t.'<option value="olderthan30">older than 30 days</option>'.n;
		$html .= t.t.t.t.'<option value="all">all</option>'.n;
		$html .= t.t.t.t.'</select> '.n;
		$html .= t.t.t.'<input type="submit" class="option" name="action" value="Purge" /> '.n;
		//$html .= t.t.t.'<input type="submit" class="option" name="action" value="Purge" /> '.n;
		$html .= t.t.t.'</label>'.n;
		//$html .= t.t.t.'<a href="/kb/tools/purge">What does "purge" do to my files?</a>'.n;
		$html .= t.t.'<span class="or">'.JText::_('OR').'</span>'.n;
		$html .= t.t.'<a href="'.JRoute::_('index.php?option='.$option.a.'task=storage').'?browse=1">Manually browse</a> your storage space.'.n;
		//$html .= t.t.'<input type="submit" class="option" name="action" value="Manually Browse" /> your storage space.'.n;
		$html .= t.t.'<input type="hidden" name="option" value="'.$option.'" />'.n;
		$html .= t.t.'<input type="hidden" name="task" value="purge" />'.n;
		$html .= t.'</fieldset>'.n;
		if ($browse) {
			$html .= t.'<div class="filebrowser">'.n;
			$html .= t.t.'<iframe src="'.JRoute::_('index.php?option='.$option.a.'task=listfiles').DS.'?no_html=1" name="imgManager" id="imgManager" width="98%" height="180"></iframe>'.n;
			$html .= t.'</div>'.n;		
		}
		$html .= '</form>'.n;
		
		$html .= t.'</div>'.n;
		$html .= '</div><div class="clear"></div>'.n;
		return $html;
	}
	
	//-----------

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

	public function view( $app, $authorized, $option, $cats, $sections, $tab, $config ) 
	{
		$html  = MwHtml::title( $app['sess'], $app['caption'], $config->get('show_storage') );
		if ($config->get('show_storage')) {
			$html .= '<div id="content-header-extra">'.n;
			$html .= MwHtml::writeMonitor($app['percent']);
			if ($app['percent'] >= 100) {
				$html .= MwHTML::storageQuotaWarning($app['remaining']);
			}
			$html .= '</div>'.n;
		}
		$html .= MwHtml::tabs( $option, $app['sess'], $cats, $tab );
		$html .= MwHtml::sections( $sections, $cats, $tab, 'hide', 'main' );
		
		return $html;
	}
	
	//-----------

	public function session( $sess, $output, $option, $app, $toolname, $authorized, $config ) 
	{
		$html  = MwHtml::appOptions($sess, $option);
		$html .= MwHtml::userOptions();
		
		if (!$sess) {
			$html .= MwHtml::error( '<strong>'.JText::_('ERROR').'</strong><br /> '.implode('<br />', $output) );
		} else {
			$k = 0;
			foreach ($output as $line) 
			{
				if (strpos($line,"id='theapp'")) {
					$html .= '<div id="app-wrap">'.n;
				}
				$html .= $line.n;
				if (strpos($line,"</applet>") && $k==0) {
					$html .= '</div><div class="clear"></div>'.n;
					$k++;
				}
			}
		}
		
		if ($sess != 0) {
			$juser =& JFactory::getUser();

			// Get the middleware database
			$mwdb =& MwUtils::getMWDBO();

			// Load the viewperm
			$ms = new MwViewperm( $mwdb );
			$rows = $ms->loadViewperm( $sess );

			$html .= MwHtml::sharePrompt( $option, $sess, $app['name'], $app['caption'], $rows, $juser);
		}
		
		if ($authorized === 'admin') {
			$html .= '<p>Administrator viewing '.$app['username'].' '.$app['ip'].' '.$sess.'</p>';
		}
		
		$html .= '<p id="powered-by">Powered by <a href="https://www.nanohub.org/about/middleware/#Maxwell" rel="external">Maxwell&#146;s D&#xE6;mon</a> and hardware donations from <a href="about/hardware/">these companies</a>.</p>'.n;
		
		return $html;
	}
	
	//-----------

	public function appOptions($sessionid, $option)
	{	
		$html  = '<ul id="app-options">'.n;
		if ($sessionid) {
			$html .= ' <li><a href="javascript:document.theapp.refresh()">Refresh Window</a></li>'.n;
			$html .= ' <li><a href="javascript:document.theapp.popout()">Popout</a></li>'.n;
			$html .= ' <li class="app-close"><a href="'.JRoute::_('index.php?option='.$option.a.'task=stop'.a.'sess='.$sessionid).'" title="Terminate this session"><span>Close</span></a></li>'.n;
		}
		$html .= '</ul>'.n;
		
		return $html;
	}

	//-----------

	public function title($session, $caption, $du=false)
	{
		$full = ($du) ? '' : 'full';
		return MwHtml::div('<h2 class="session-title item:name id:'.$session.'">'.$caption.'</h2>',$full,'content-header').n;
	}

	//-----------

	public function userOptions()
	{
		//$html  = '<ul id="useroptions">'.n;
		//$html .= ' <li class="last"><a href="/myhub/sessions/" title="My Sessions">My Sessions</a></li>'.n;
		//$html .= '</ul>'.n.n;
		$html  = '<noscript>'.n;
		$html .= t.'<p class="warning">'.n;
		$html .= t.t.'This site works best when Javascript is enabled in your browser.'.n;
		$html .= t.t.'(<a href="/kb/misc/javascript/">How do I do this?</a>)'.n;
		$html .= t.t.'Without Javascript support some operations will not work.'.n;
		$html .= t.'</p>'.n;
		$html .= '</noscript>'.n.n;
		$html .= '<p id="troubleshoot" class="help">If your application fails to appear within a minute, <a href="/kb/tools/troubleshoot/">troubleshoot this problem.</a></p>'.n;

		return $html;
	}

	//----------------------------------------------------------
	
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
	
	//----------------------------------------------------------
	// Form lements
	//----------------------------------------------------------

	public function hInput($name, $value='', $id='')
	{
		$html  = '<input type="hidden" name="'.$name.'" value="'.$value.'"';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= ' />'.n;
		return $html;
	}

	//-----------

	public function sInput($name, $value='')
	{
		return '<input type="submit" name="'.$name.'" value="'.$value.'" />'.n;
	}
	
	//-----------
	
	public function sharePrompt( $option, $sess, $appname, $appcaption, &$rows, &$juser)
	{
		$html  = '<form name="share" id="app-share" method="get" action="index.php">'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.MwHtml::hInput('option',$option);
		$html .= t.t.MwHtml::hInput('task','share');
		$html .= t.t.MwHtml::hInput('sess',$sess);
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'Share session with (enter usernames separated by spaces or commas): '.n;
		$html .= t.t.t.'<input type="text" name="username" value="" />'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'<input type="checkbox" name="readonly" value="Yes" /> '.n;
		$html .= t.t.t.'Read-Only?'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.t.MwHtml::sInput('submit','Share');
		if (count($rows) <= 1) {
			$html .= t.t.'<span>(Session is currently not shared.)</span>'.n;
			$html .= '<br /><p>What does it mean to <a href="/kb/tips/share">share a session</a>?</p>'.n.n;
		}
		$html .= t.'</fieldset>'.n;
		$html .= '</form>'.n;

		if (count($rows) > 1) {
			$shared = 'This session is shared with: '.n;
			for ($i=0; $i < count($rows); $i++) 
			{
				$row = $rows[$i];
				if ($row->viewuser != $juser->get('username')) {
					$shared .= '&nbsp; <a href="'.JRoute::_('index.php?option='.$option.a.'task=unshare'.a.'sess='.$sess.a.'username='.$row->viewuser).'" title="Remove this user from sharing">'.$row->viewuser.'</a>';
				}
			}
			$html .= MwHtml::warning($shared);
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
				<?php if($dir !='data' && $dir!='sessions') { ?>
                <td><a href="index.php?option=<?php echo $option; ?>&amp;task=deletefolder&amp;delFolder=<?php echo $path; ?>&amp;listdir=<?php echo $listdir; ?>&amp;no_html=1" target="imgManager" onClick="return deleteFolder('<?php echo $dir; ?>', <?php echo $num_files; ?>);" title="<?php echo JText::_('Delete'); ?>"><img src="components/<?php echo $option; ?>/images/trash.gif" width="15" height="15" alt="<?php echo JText::_('Delete'); ?>" /></a></td>
                <?php } ?>
			</tr>
		<?php
	}
}
?>