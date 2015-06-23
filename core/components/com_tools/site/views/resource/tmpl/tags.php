<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

$showwarning = ($this->version=='current' or !$this->status['published']) ? 0 : 1;

?>
	<div class="explaination">
		<h4><?php echo Lang::txt('COM_TOOLS_TAGS_WHAT_ARE_TAGS'); ?></h4>
		<p><?php echo Lang::txt('COM_TOOLS_TAGS_EXPLANATION'); ?></p>
	</div>
	<fieldset>
		<legend><?php echo Lang::txt('COM_TOOLS_TAGS_ADD'); ?></legend>
<?php if (!empty($this->fats)) { ?>
		<fieldset>
			<legend><?php echo Lang::txt('COM_TOOLS_TAGS_SELECT_FOCUS_AREA'); ?>:</legend>
			<?php
			foreach ($this->fats as $key => $value)
			{
				?>
				<label>
					<input class="option" type="radio" name="tagfa" value="<?php echo $value; ?>"<?php if ($this->tagfa == $value) { echo ' checked="checked "'; } ?> />
					<?php echo $key; ?>
				</label>
				<?php
			}
			?>
		</fieldset>
<?php } ?>
		<label>
			<?php echo Lang::txt('COM_TOOLS_TAGS_ASSIGNED'); ?>:
			<?php
			$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->tags)));

			if (count($tf) > 0) {
				echo $tf[0];
			} else {
				echo '<textarea name="tags" id="tags-men" rows="6" cols="35">'. $this->tags .'</textarea>';
			}
			?>
		</label>
		<p><?php echo Lang::txt('COM_TOOLS_TAGS_NEW_EXPLANATION'); ?></p>
	</fieldset><div class="clear"></div>