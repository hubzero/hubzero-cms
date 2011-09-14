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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

