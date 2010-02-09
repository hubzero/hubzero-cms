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

if ($this->picture != '' && file_exists( $this->picture )) {
	$file = DS.$picture;
} else {  
	$file = DS.$this->config->get('defaultpic');
}
if ($file && file_exists( JPATH_ROOT.$file )) {
	list($ow, $oh) = getimagesize(JPATH_ROOT.$file);
}

//scale if image is bigger than 120w x120h
$num = max($ow/120, $oh/120);
if ($num > 1) {
	$mw = round($ow/$num);
	$mh = round($oh/$num);
} else {
	$mw = $ow;
	$mh = $oh;
}
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
	<p class="passed"><?php echo JText::_('COM_FEEDBACK_STORY_THANKS'); ?></p>
	
	<table class="storybox" summary="<?php echo JText::_('COM_FEEDBACK_SUCCESS_STORY_OVERVIEW'); ?>">
		<tbody>
			<tr>
				<td><img src="<?php echo $file; ?>" width="<?php echo $mw; ?>" height="<?php echo $mh; ?>" alt="" /></td>
				<td>
					<blockquote cite="<?php echo htmlentities($this->user['name'],ENT_COMPAT,'UTF-8'); ?>" class="quote">
						<?php echo stripslashes($this->quote); ?>
					</div>
					<div class="quote">
						<strong><?php echo $this->user['name']; ?></strong><br />
						<em><?php echo $this->user['org']; ?></em>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div><!-- / .main section -->
