<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<tr class="<?php echo "row".$this->item->index % 2; ?>" <?php echo $this->item->style; ?>>
	<td><?php echo $this->pagination->getRowOffset($this->item->index); ?></td>
	<td>
		<input type="checkbox" id="cb<?php echo $this->item->index;?>" name="eid[]" value="<?php echo $this->item->extension_id; ?>" onclick="Joomla.isChecked(this.checked);" <?php echo $this->item->cbd; ?> />
		<span class="bold"><?php echo $this->item->name; ?></span>
	</td>
	<td>
		<?php echo $this->item->type ?>
	</td>
	<td class="center">
		<?php if (!$this->item->element) : ?>
		<strong>X</strong>
		<?php else : ?>
		<a href="<?php echo Route::url('index.php?option=com_installer&type=manage&task=' . $this->item->task . '&eid[]=' . $this->item->extension_id . '&limitstart=' .$this->pagination->limitstart . '&' . Session::getFormToken() . '=1'); ?>"><?php echo Html::asset('image', 'images/'.$this->item->img, $this->item->alt, array('title' => $this->item->action)); ?></a>
		<?php endif; ?>
	</td>
	<td class="center"><?php echo @$this->item->folder != '' ? $this->item->folder : 'N/A'; ?></td>
	<td class="center"><?php echo @$this->item->client != '' ? $this->item->client : 'N/A'; ?></td>
	<td>
		<span class="editlinktip hasTip" title="<?php echo addslashes(htmlspecialchars(Lang::txt('COM_INSTALLER_AUTHOR_INFORMATION').'::'.$this->item->author_info)); ?>">
			<?php echo @$this->item->author != '' ? $this->item->author : '&#160;'; ?>
		</span>
	</td>
</tr>
