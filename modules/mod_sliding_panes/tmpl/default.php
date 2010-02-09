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
?>
<div id="<?php echo $modslidingpanes->container; ?>">
	<div class="panes">
		<div class="panes-content">
<?php
if ($modslidingpanes->content) {
	$i = 1;
	$panes = $modslidingpanes->content;
	foreach ($panes as $pane) 
	{
?>
			<div class="pane" id="<?php echo $modslidingpanes->container.'-pane'.$i; ?>">
				<div class="pane-wrap" id="<?php echo $pane->alias; ?>">
<?php echo stripslashes($pane->introtext); ?>
				</div><!-- / .pane-wrap #<?php echo $pane->alias; ?> -->
			</div><!-- / .pane #<?php echo $modslidingpanes->container.'-pane'.$i; ?> -->
<?php
		$i++;
	}
}
?>
		</div><!-- / .panes-content -->
	</div><!-- / .panes -->
</div><!-- / #pane-sliders -->