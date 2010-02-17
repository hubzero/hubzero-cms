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

$juser =& JFactory::getUser();
?>
<div id="content-header">
	<h2 class="session-title item:name id:<?php echo $this->app['sess']; ?>"><?php echo $this->app['caption']; ?></h2>
</div><!-- / #content-header -->

<?php if ($this->config->get('show_storage')) { ?>
<div id="content-header-extra">
<?php
	$view = new JView( array('name'=>'monitor') );
	$view->option = $this->option;
	$view->amt = $this->app['percent'];
	$view->du = NULL; 
	$view->percent = 0; 
	$view->msgs = 0;
	$view->ajax = 0;
	$view->writelink = 1;
	$view->display();
	
	if ($this->app['percent'] >= 100 && isset($this->app['remaining'])) {
		$view = new JView( array('name'=>'monitor','layout'=>'warning') );
		$view->sec = $this->app['remaining'];
		$view->padHours = false; 
		$view->option = $this->option;
		$view->display();
	}
?>
</div><!-- / #content-header-extra -->
<?php } ?>

<div id="sub-menu">
	<ul>
<?php
$i = 1;
foreach ($this->cats as $cat)
{
	$name = key($cat);
	if ($name != '' && $cat[$name] != '') {
?>
		<li id="sm-<?php echo $i; ?>"<?php echo (strtolower($name) == $this->tab) ? ' class="active"' : ''; ?>><a class="tab" rel="<?php echo $name; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=view&sess='.$this->app['sess'].'&active='.$name); ?>"><span><?php echo $cat[$name]; ?></span></a></li>
<?php
		$i++;	
	}
}
?>
	</ul>
	<div class="clear"></div>
</div><!-- / #sub-menu -->

<div class="main section" id="session-section">
<?php if ($this->app['sess']) { ?>
	<ul id="app-options">
		<li><a href="javascript:document.theapp.refresh()">Refresh Window</a></li>
		<li><a href="javascript:document.theapp.popout()">Popout</a></li>
		<li class="app-close"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&app='.$this->toolname.'&task=stop&sess='.$this->app['sess']); ?>" title="Terminate this session"><span>Close</span></a></li>
	</ul>
<?php } ?>
	
	<noscript>
		<p class="warning">
			This site works best when Javascript is enabled in your browser (<a href="/kb/misc/javascript/">How do I do this?</a>).
			Without Javascript support some operations will not work.
		</p>
	</noscript>
	<p id="troubleshoot" class="help">If your application fails to appear within a minute, <a href="/kb/tools/troubleshoot/">troubleshoot this problem.</a></p>

<?php
if (!$this->app['sess']) {
	echo '<p class="error"><strong>'.JText::_('ERROR').'</strong><br /> '.implode('<br />', $this->output).'</p>';
} else {
	$k = 0;
	$html = '';
	foreach ($this->output as $line) 
	{
		if (strpos($line,"id='theapp'")) {
			$html .= '<div id="app-wrap">'."\n";
		}
		$html .= $line."\n";
		if (strpos($line,"</applet>") && $k==0) {
			$html .= '</div><div class="clear"></div>'."\n";
			$k++;
		}
	}
	echo $html;
?>

	<form name="share" id="app-share" method="get" action="index.php">
		<fieldset>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="share" />
			<input type="hidden" name="sess" value="<?php echo $this->app['sess']; ?>" />
			<input type="hidden" name="app" value="<?php echo $this->toolname; ?>" />
			<label>
				Share session with (enter usernames separated by spaces or commas):
				<input type="text" name="username" value="" />
			</label>
			<label>
				<input type="checkbox" name="readonly" value="Yes" /> 
				Read-Only?
			</label>
			<input type="submit" value="Share" />
			<?php if (count($this->shares) <= 1) { ?>
			<span>(Session is currently not shared.)</span>
			<br /><p>What does it mean to <a href="/kb/tips/share">share a session</a>?</p>
			<?php } ?>
		</fieldset>
	</form>
	
	<?php if (count($this->shares) > 1) { ?>
	<p class="warning">
		This session is shared with: 
		<?php 
		foreach ($this->shares as $row) 
		{
			if ($row->viewuser != $juser->get('username')) {
				?>&nbsp; <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&app='.$this->toolname.'&task=unshare&sess='.$this->app['sess'].'&username='.$row->viewuser); ?>" title="Remove this user from sharing"><?php echo $row->viewuser; ?></a><?php
			}
		}
		?>
	</p>
	<?php } ?>
<?php } ?>

<?php if ($this->authorized === 'admin') {
	echo '<p>Administrator viewing '.$this->app['username'].' '.$this->app['ip'].' '.$this->app['sess'].'</p>';
} ?>

	<p id="powered-by">Powered by <a href="https://nanohub.org/about/middleware/#Maxwell" rel="external">Maxwell&#146;s D&#xE6;mon</a>.</p>
</div><!-- / .main section #session-section -->

<?php
$k = 0;
foreach ($this->sections as $section) 
{
	if ($section['html'] != '') {
		$cls  = '';
		if (key($this->cats[$k+1]) != $this->tab) {
			$cls = 'hide ';
		}
		echo '<div class="'.$cls.'main section" id="'.key($this->cats[$k+1]).'-section">'. $section['html'].'</div><!-- / #'.key($this->cats[$k+1]).'-section -->'."\n";
	}
	$k++;
}
?>