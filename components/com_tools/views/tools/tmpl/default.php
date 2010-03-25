<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
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

$document =& JFactory::getDocument();
$document->setTitle( $this->forgeName );

$app =& JFactory::getApplication();
$pathway =& $app->getPathway();
if (count($pathway->getPathWay()) <= 0) {
	$pathway->addItem( $this->forgeName, 'index.php?option=com_tools' );
}
$cls = 'even';
?>

<div class="full" id="content-header">
	<h2><?php echo $this->forgeName;?></h2>
</div>

<div id="introduction" class="section">
	<div class="aside">
		<h3>Help</h3>
		<ul>
<?php
$juser =& JFactory::getUser();
if ($juser->get('guest')) {
?>
			<li><a href="/register">Sign up for free!</a></li>
<?php } ?>
			<li><a href="http://subversion.tigris.org/" rel="external">Learn about Subversion</a></li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
			<h3>Tool Development</h3>
			<p>
				Welcome to <?php echo $this->forgeName;?>, the tool
		        development area of <a href="<?php echo $this->hubLongURL;?>"><?php echo $this->hubShortURL;?></a>.
		        The following pages are maintained by the various owners of each
		        tool.  Many of these tools are available as Open Source, and
		        you can download the code via Subversion from this site.  Some
		        tools are closed source at the request of the authors, and only
		        a restricted development team has access to the code.  See each
		        tool page for details.
			</p>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / #introduction.section -->

<div class="section">
	<div class="four columns first">
		<h2>Available Tools</h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<table summary="Tool projects">
			<thead>
				<tr>
					<th>Title</th>
					<th>Alias</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
<?php 
if (count($this->appTools) > 0) {
	ximport('Hubzero_View_Helper_Html');
	
	foreach ($this->appTools as $project) 
	{
		//if ($project->state == 1 || $project->state == 3) {
		if ($project->tool_state != 8) {
			if ($project->codeaccess == '@OPEN') {
				$status = 'open source';
			} else {
				$status = 'closed source';
			}
?>
				<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
					<?php /*<td><a href="<?php echo $this->forgeURL;?>/tools/<?php echo $project;?>/wiki"><?php echo $project;?></a></td>*/ ?>
					<td><a href="<?php echo $this->forgeURL;?>/tools/<?php echo $project->toolname;?>/wiki" title="<?php echo htmlentities(stripslashes($project->title),ENT_COMPAT,'UTF-8'); ?>"><?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($project->title), 50, 0); ?></a></td>
					<td><a href="<?php echo $this->forgeURL;?>/tools/<?php echo $project->toolname;?>/wiki"><?php echo $project->toolname; ?></a></td>
					<td><span class="<?php echo $status; ?>-code"><?php echo $status; ?></span></td>
				</tr>
<?php
		}
	}
} else {
?>
				<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
					<td>No tools found.</td>
				</tr>
<?php
}
?>
			</tbody>
		</table>
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>
</div><!-- / .section -->