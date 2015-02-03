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

?>
<form action="">
	<fieldset>
		<div class="configuration">
			<?php echo JText::_('COM_POLL_PREVIEV'); ?>
		</div>
	</fieldset>

	<br /><br />

	<table>
		<caption><?php echo $this->poll->title; ?></caption>
		<tfoot>
			<tr>
				<td colspan="2">
					<input type="button" name="submit" value="<?php echo JText::_('COM_POLL_VOTE'); ?>">&nbsp;&nbsp;
					<input type="button" name="result" value="<?php echo JText::_('COM_POLL_RESULTS'); ?>">
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->options as $option)
			{
				if ($option->text <> "")
				{
					?>
					<tr>
						<td valign="top" height="30"><input type="radio" name="poll" value="<?php echo $this->escape($option->text); ?>"></td>
						<td class="poll" width="100%" valign="top"><?php echo $option->text; ?></td>
					</tr>
					<?php
				}
			}
			?>
		</tbody>
	</table>
</form>