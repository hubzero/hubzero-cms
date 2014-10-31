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

$canDo = TagsHelperPermissions::getActions();

JToolBarHelper::title(JText::_('COM_TAGS') . ': ' . JText::_('COM_TAGS_PIERCE'), 'tags.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save('pierce');
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
	<p class="warning"><?php echo JText::_('COM_TAGS_PIERCED_EXPLANATION'); ?></p>

	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_TAGS_PIERCING'); ?></span></legend>

			<div class="input-wrap">
				<ul>
					<?php
					foreach ($this->tags as $tag)
					{
						echo '<li>' . $this->escape(stripslashes($tag->get('raw_tag'))) . ' (' . $this->escape($tag->get('tag')) . ' - ' . $tag->objects('count') . ')</li>' . "\n";
					}
					?>
				</ul>
			</div>
		</fieldset>
	</div>
	<div class="col width-50 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_TAGS_PIERCE_TO'); ?></span></legend>

			<div class="input-wrap">
				<label for="newtag"><?php echo JText::_('COM_TAGS_NEW_TAG'); ?>:</label><br />
				<?php
				JPluginHelper::importPlugin('hubzero');
				$tf = JDispatcher::getInstance()->trigger(
					'onGetMultiEntry',
					array(
						array('tags', 'newtag', 'newtag')
					)
				);
				echo (count($tf) ? implode("\n", $tf) : '<input type="text" name="newtag" id="newtag" size="25" value="" />');
				?>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="ids" value="<?php echo $this->idstr; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="step" value="<?php echo $this->step; ?>" />
	<input type="hidden" name="task" value="pierce" />

	<?php echo JHTML::_('form.token'); ?>
</form>