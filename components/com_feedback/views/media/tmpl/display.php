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

$this->css();
?>
<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" enctype="multipart/form-data" name="filelist" id="filelist" onsubmit="return validate();">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

	<table>
		<tbody>
		<?php
			$k = 0;

			if ($this->file && file_exists( JPATH_ROOT . $this->file_path . DS . $this->file )) {
				$this_size = filesize(JPATH_ROOT . $this->file_path . DS . $this->file);
				list($ow, $oh, $type, $attr) = getimagesize(JPATH_ROOT . $this->file_path . DS . $this->file);

				// scale if image is bigger than 120w x120h
				$num = max($ow/120, $oh/120);
				if ($num > 1) {
					$mw = round($ow/$num);
					$mh = round($oh/$num);
				} else {
					$mw = $ow;
					$mh = $oh;
				}
		?>
			<tr>
				<td>
					<img src="<?php echo $this->webpath . DS . $this->path . DS . $this->file; ?>" alt="" id="conimage" height="<?php echo $mh; ?>" width="<?php echo $mw; ?>" />
				</td>
				<td width="100%">
					<input type="hidden" name="conimg" value="<?php echo $this->webpath . DS . $this->path . DS . $this->file; ?>" />
					<input type="hidden" name="task" value="delete" />
					<input type="hidden" name="file" id="file" value="<?php echo $this->file; ?>" />
					<input type="submit" name="submit" value="<?php echo JText::_('DELETE'); ?>" />
				</td>
			</tr>
		<?php } else { ?>
			<tr>
				<td>
					<img src="<?php echo $this->default_picture; ?>" alt="" id="oimage" name="oimage" />
				</td>
				<td>
					<p><?php echo JText::_('COM_FEEDBACK_STORY_ADD_PICTURE'); ?><br /><small>(gif/jpg/jpeg/png - 200K max)</small></p>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="hidden" name="conimg" value="" />
					<input type="hidden" name="task" value="upload" />
					<input type="hidden" name="currentfile" value="<?php $this->file; ?>" />
					<input type="file" name="upload" id="upload" size="10" /> <input type="submit" value="<?php echo JText::_('COM_FEEDBACK_UPLOAD'); ?>" />
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
</form>
<script type="text/javascript">
	<!--
	function validate()
	{
		var apuf = document.getElementById('file');
		return apuf.value ? true : false;
	}

	function passparam()
	{
		parent.document.getElementById('picture').value = this.document.forms[0].conimg.value;
	}

	window.onload = passparam;
	//-->
</script>