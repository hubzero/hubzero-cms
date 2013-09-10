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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = TagsHelper::getActions();

JToolBarHelper::title(JText::_('TAGS') . ': <small><small>[ ' . JText::_('MERGE') . ' ]</small></small>', 'tags.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save('merge');
}
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" class="editform" id="item-form">
	<p><?php echo JText::_('MERGED_EXPLANATION'); ?></p>
	
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('MERGING'); ?></span></legend>
			
			<ul>
			<?php
			foreach ($this->tags as $tag)
			{
				echo '<li>' . $this->escape(stripslashes($tag->get('raw_tag'))) . ' (' . $this->escape($tag->get('tag')) . ' - ' . $tag->objects('count') . ')</li>' . "\n";
			}
			?>
			</ul>
		</fieldset>
	</div>
	<div class="col width-50">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('MERGE_TO'); ?></span></legend>
			
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="existingtag"><?php echo JText::_('EXISTING_TAG'); ?>:</label></td>
						<td>
							<select name="existingtag" id="existingtag">
								<option value=""><?php echo JText::_('OPT_SELECT'); ?></option>
								<?php
								foreach ($this->rows as $row)
								{
									echo '<option value="' . $row->get('id') . '">' . $this->escape(stripslashes($row->get('raw_tag'))) . '</option>' . "\n";
								}
								?>
							</select>
						</td>
					</tr>
<?php
	if (count($this->tags) > 1) {
?>
					<tr>
						<td colspan="2"><?php echo JText::_('OR'); ?></td>
					</tr>
					<tr>
						<td class="key"><label for="newtag"><?php echo JText::_('NEW_TAG'); ?>:</label></td>
						<td><input type="text" name="newtag" id="newtag" size="25" value="" /></td>
					</tr>
				</tbody>
			</table>
<?php
	} else {
?>
				</tbody>
			</table>
			<input type="hidden" name="newtag" id="newtag" value="" />
<?php
	}
?>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="ids" value="<?php echo $this->idstr; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="step" value="<?php echo $this->step; ?>" />
	<input type="hidden" name="task" value="merge" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>