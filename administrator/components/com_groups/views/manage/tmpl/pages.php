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

JToolBarHelper::title(JText::_('COM_GROUPS') . ': <small><small>[ ' . JText::_('Group Pages') . ' ]</small></small>', 'groups.png');
JToolBarHelper::custom('newpage','new','new','New Page', false, false);
JToolBarHelper::cancel();
?>

<script type="text/javascript">
function submitbutton(pressbutton) 
{
	submitform(pressbutton);
}
</script>
<?php
if ($this->getError()) {
	echo '<p style="color: #c00;"><strong>'.$this->getError().'</p>';
}
?>

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;gid=<?php echo $this->group->cn; ?>" name="adminForm" id="adminForm" method="post">
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="save" />
	<div class="col width-100">
		<fieldset class="adminform">
			<legend><span><?php echo $this->group->get("description") . " - Pages"; ?></span></legend>
			<table class="admintable">
				<tbody>
					<?php if(count($this->pages) > 0) : ?>
						<?php foreach($this->pages as $page) : ?>
							<tr>
								<td><?php echo $page['title']; ?></td>
								<td>
									<?php if($page['active']) : ?>
										<font color="green">Active</font>
									<?php else : ?>
										<font color="red">Not Active</font>
									<?php endif; ?>
								</td>
								<td>
									<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;gid=<?php echo $this->group->cn; ?>&amp;task=editpage&amp;page=<?php echo $page['id']; ?>">Edit Page</a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="3">Currently there are no pages for this group.</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</fieldset>
	</div>
</form>