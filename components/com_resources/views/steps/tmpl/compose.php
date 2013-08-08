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

$this->row->fulltxt = ($this->row->fulltxt) ? stripslashes($this->row->fulltxt): stripslashes($this->row->introtext);

$type = new ResourcesType( $this->database );
$type->load( $this->row->type );

$data = array();
preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->row->fulltxt, $matches, PREG_SET_ORDER);
if (count($matches) > 0) 
{
	foreach ($matches as $match)
	{
		$data[$match[1]] = ResourcesControllerCreate::_txtUnpee($match[2]);
	}
}

$this->row->fulltxt = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $this->row->fulltxt);
$this->row->fulltxt = trim($this->row->fulltxt);

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');

$elements = new ResourcesElements($data, $type->customFields);
$fields = $elements->render();
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<p>
		<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=draft'); ?>">
			<?php echo JText::_('COM_CONTRIBUTE_NEW_SUBMISSION'); ?>
		</a>
	</p>
</div><!-- / #content-header -->

<div class="main section">
<?php
$view = new JView(array(
	'name'   => 'steps',
	'layout' => 'steps'
));
$view->option = $this->option;
$view->step = $this->step;
$view->steps = $this->steps;
$view->id = $this->id;
$view->resource = $this->row;
$view->progress = $this->progress;
$view->display();
?>
<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=draft&step=' . $this->next_step . '&id=' . $this->id); ?>" method="post" id="hubForm" accept-charset="utf-8">
		<div class="explaination">
			<p><?php echo JText::_('COM_CONTRIBUTE_COMPOSE_EXPLANATION'); ?></p>

			<p><?php echo JText::_('COM_CONTRIBUTE_COMPOSE_ABSTRACT_HINT'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_CONTRIBUTE_COMPOSE_ABOUT'); ?></legend>

			<label for="field-title">
				<?php echo JText::_('COM_CONTRIBUTE_COMPOSE_TITLE'); ?>: <span class="required"><?php echo JText::_('COM_CONTRIBUTE_REQUIRED'); ?></span>
				<input type="text" name="title" id="field-title" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
			</label>

			<label for="field-fulltxt">
				<?php echo JText::_('COM_CONTRIBUTE_COMPOSE_ABSTRACT'); ?>:
				<textarea name="fulltxt" id="field-fulltxt" cols="50" rows="20"><?php echo ResourcesControllerCreate::_txtUnpee(stripslashes($this->row->fulltxt)); ?></textarea>
			</label>
		</fieldset><div class="clear"></div>
<?php if ($fields) { ?>
		<div class="explaination">
			<p><?php echo JText::_('COM_CONTRIBUTE_COMPOSE_CUSTOM_FIELDS_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_CONTRIBUTE_COMPOSE_DETAILS'); ?></legend>
			<?php 
			echo $fields;
			?>
		</fieldset><div class="clear"></div>
<?php } ?>
		<input type="hidden" name="published" value="<?php echo $this->row->published; ?>" />
		<input type="hidden" name="standalone" value="1" />
		<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="type" value="<?php echo $this->row->type; ?>" />
		<input type="hidden" name="created" value="<?php echo $this->row->created; ?>" />
		<input type="hidden" name="created_by" value="<?php echo $this->row->created_by; ?>" />
		<input type="hidden" name="publish_up" value="<?php echo $this->row->publish_up; ?>" />
		<input type="hidden" name="publish_down" value="<?php echo $this->row->publish_down; ?>" />
		<input type="hidden" name="group_owner" value="<?php echo $this->row->group_owner; ?>" />

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="step" value="<?php echo $this->next_step; ?>" />
		<p class="submit">
			<input type="submit" value="<?php echo JText::_('COM_CONTRIBUTE_NEXT'); ?>" />
		</p>
	</form>
</div><!-- / .main section -->
