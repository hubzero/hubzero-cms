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

$canDo = CitationsHelper::getActions('sponsor');

$text = ($this->task == 'edit' ? JText::_('EDIT') : JText::_('NEW'));

JToolBarHelper::title(JText::_('CITATIONS') . ' ' . JText::_('CITATION_SPONSORS') . ': ' . $text, 'citation.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('sponsor');

$id      = null;
$sponsor = null;
$link    = null;
$image   = null;
if ($this->sponsor)
{
	$id      = $this->sponsor[0]['id'];
	$sponsor = $this->escape(stripslashes($this->sponsor[0]['sponsor']));
	$link    = $this->escape(stripslashes($this->sponsor[0]['link']));
	$image   = $this->escape(stripslashes($this->sponsor[0]['image']));
}
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	return submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-70 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('CITATION_SPONSORS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-sponsor"><?php echo JText::_('CITATION_SPONSORS_NAME'); ?></label>
				<input type="text" name="sponsor[sponsor]" id="field-sponsor" value="<?php echo $sponsor; ?>" size="50" />
			</div>
			<div class="input-wrap">
				<label for="field-link"><?php echo JText::_('CITATION_SPONSORS_LINK'); ?></label>
				<input type="text" name="sponsor[link]" id="field-link" value="<?php echo $link; ?>" size="50" />
			</div>
			<div class="input-wrap">
				<label for="field-image"><?php echo JText::_('CITATION_SPONSORS_IMAGE'); ?></label>
				<input type="text" name="sponsor[image]" id="field-image" value="<?php echo $image; ?>" size="50" />
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="sponsor[id]" value="<?php echo $id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
