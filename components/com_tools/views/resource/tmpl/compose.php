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

$database = JFactory::getDBO();

$this->status['fulltxt'] = stripslashes($this->status['fulltxt']);

$type = new ResourcesType($database);
$type->load(7);

$data = array();
preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->status['fulltxt'], $matches, PREG_SET_ORDER);
if (count($matches) > 0) 
{
	foreach ($matches as $match)
	{
		$data[$match[1]] = trim($match[2]);
	}
}

$this->status['fulltxt'] = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $this->status['fulltxt']);
$this->status['fulltxt'] = trim($this->status['fulltxt']);

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');

$elements = new ResourcesElements($data, $type->customFields);
$fields = $elements->render();
?>
	<div class="explaination"> 
		<p class="help"><?php echo $this->dev ? JText::_('COM_TOOLS_SIDE_EDIT_PAGE') : JText::_('COM_TOOLS_SIDE_EDIT_PAGE_CURRENT'); ?></p>
		<p><?php echo JText::_('COM_TOOLS_COMPOSE_ABSTRACT_HINT'); ?></p>
	</div>
	<fieldset>
		<legend><?php echo JText::_('COM_TOOLS_COMPOSE_ABOUT'); ?></legend>
		<label for="field-title">
			<?php echo JText::_('COM_TOOLS_COMPOSE_TITLE'); ?>: <span class="required"><?php echo JText::_('COM_TOOLS_REQUIRED'); ?></span>
<?php if ($this->dev) { ?>
			<input type="text" name="title" id="field-title" maxlength="127" value="<?php echo $this->escape(stripslashes($this->status['title'])); ?>" />
<?php } else { ?>
			<input type="text" name="rtitle" id="field-title" maxlength="127" value="<?php echo $this->escape(stripslashes($this->status['title'])); ?>" disabled="disabled" />
			<input type="hidden" name="title" maxlength="127" value="<?php echo $this->escape(stripslashes($this->status['title'])); ?>" />
			<p class="warning"><?php echo JText::_('COM_TOOLS_TITLE_CANT_CHANGE'); ?></p>
<?php } ?>
		</label>
		<label for="field-description">
			<?php echo JText::_('COM_TOOLS_COMPOSE_AT_A_GLANCE'); ?>: <span class="required"><?php echo JText::_('COM_TOOLS_REQUIRED'); ?></span>
			<input type="text" name="description" id="field-description" maxlength="256" value="<?php echo $this->escape(stripslashes($this->status['description'])); ?>" />
		</label>
		<label for="field-fulltxt">
			<?php echo JText::_('COM_TOOLS_COMPOSE_ABSTRACT'); ?>:
			<textarea name="fulltxt" id="field-fulltxt" cols="50" rows="20"><?php echo $this->escape(stripslashes($this->status['fulltxt'])); ?></textarea>
			<span class="hint"><a href="/wiki/Help:WikiFormatting"><?php echo JText::_('COM_TOOLS_WIKI_FORMATTING'); ?></a> <?php echo JText::_('COM_TOOLS_COMPOSE_TIP_ALLOWED'); ?>.</span>
		</label>
	</fieldset><div class="clear"></div>

	<div class="explaination">
		<p><?php echo JText::_('COM_TOOLS_COMPOSE_CUSTOM_FIELDS_EXPLANATION'); ?></p>
	</div>
	<fieldset>
		<legend><?php echo JText::_('COM_TOOLS_COMPOSE_DETAILS'); ?></legend>
<?php 
echo $fields;
?>