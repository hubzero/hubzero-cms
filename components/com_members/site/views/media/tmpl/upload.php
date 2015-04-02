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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//default all pic vars
$picture = new stdClass;
$picture->src = $this->config->get("defaultpic");
$picture->name = 'n/a';
$picture->size = 'n/a';
$picture->width = 'n/a';
$picture->height = 'n/a';

//get user id in nice format
$uid = \Hubzero\Utility\String::pad( $this->profile->get("uidNumber") );

//get profile pic and path to picture
$pic = $this->profile->get("picture");
$path = DS . trim($this->config->get("webpath", '/site/members'), DS) . DS . $uid . DS;

//if we have a picture and it exists in the file system
if ($pic && file_exists(JPATH_ROOT . $path . $pic))
{
	$size = filesize(JPATH_ROOT . $path . $pic);
	list($width, $height, $type, $attr) = getimagesize(JPATH_ROOT . $path . $pic);

	$picture->src = $path . $pic;
	$picture->name = $pic;
	$picture->size = \Hubzero\Utility\Number::formatBytes( $size );
	$picture->width = $width . " <abbr title=\"pixels\">px</abbr>";
	$picture->height = $height . " <abbr title=\"pixels\">px</abbr>";
}
?>
<div id="ajax-upload-container">
	<form action="index.php" method="post" enctype="multipart/form-data">
		<h2>Upload a New Profile Picture</h2>
		<div id="ajax-upload-left">
			<img id="picture-src" src="<?php echo $picture->src; ?>" alt="<?php echo $picture->name; ?>" data-default-pic="<?php echo $this->config->get('defaultpic', '/components/com_members/assets/img/profile.gif'); ?>" />
			<?php if ($this->profile->get("picture") != '') : ?>
			<a href="#" id="remove-picture">[Remove Picture]</a>
			<?php endif; ?>
		</div><!-- /#ajax-upload-left -->
		<div id="ajax-upload-right">
			<div id="ajax-uploader" data-action="<?php echo JURI::base(true); ?>/index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;id=<?php echo $this->profile->get('uidNumber'); ?>&amp;task=doajaxupload&amp;no_html=1"></div>
			<table>
				<tbody>
					<tr>
						<td class="key">Name:</td>
						<td id="picture-name"><?php echo $picture->name; ?></td>
					</tr>
					<tr>
						<td class="key">Size:</td>
						<td id="picture-size"><?php echo $picture->size; ?></td>
					</tr>
					<tr>
						<td class="key">Width:</td>
						<td id="picture-width"><?php echo $picture->width; ?></td>
					</tr>
					<tr>
						<td class="key">Height:</td>
						<td id="picture-height"><?php echo $picture->height; ?></td>
					</tr>
				</tbody>
			</table>
		</div><!-- /#ajax-upload-right -->
		<br class="clear" />
		<div id="ajax-upload-actions">
			<button class="section-edit-cancel">Cancel</button>
			<button class="section-edit-submit">Save Changes</button>
		</div><!-- /#ajax-upload-actions -->

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="ajaxuploadsave" />
	<input type="hidden" name="id" value="<?php echo $this->profile->get("uidNumber"); ?>" />
	<input type="hidden" name="profile[picture]" id="profile-picture" value="<?php echo $this->profile->get("picture"); ?>" />
	<input type="hidden" name="no_html" value="1" />
	</form>
</div><!-- /#ajax-upload-container -->