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

$nextstep = $this->step+1;
$task = ($nextstep == 5) ? 'preview' : 'start';
$dev = ($this->version == 'dev') ? 1: 0;

if ($this->version == 'dev') {
	$v = ($this->status['version'] && $this->status['version'] != $this->status['currentversion']) ? $this->status['version'] : '';
}
else {
	$v = $this->status['version'];
}
?>
<div id="content-header">
	<h2><?php echo $this->escape($this->title); ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul id="useroptions">
		<li><a class="status btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=pipeline&task=status&app=' . $this->row->alias); ?>"><?php echo JText::_('COM_TOOLS_TOOL_STATUS'); ?></a></li>
		<li class="last"><a class="add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=pipeline&task=create'); ?>"><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_NEW_TOOL'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="section steps-section">
<?php	
	$view = new JView(array(
		'name' => $this->controller,
		'layout' => 'stage'
	));
	$view->stage = $this->step;
	$view->option = $this->option;
	$view->controller = $this->controller;
	$view->version = $this->version;
	$view->row = $this->row;
	$view->status = $this->status;
	$view->vnum = $v;
	$view->display();
?>
</div>

<div class="main section">
	<form action="index.php" method="post" id="hubForm">
		<div style="float:left; width:70%;padding:1em 0 1em 0;">
<?php if ($this->step !=1 ) { ?>
			<span style="float:left;width:100px;"><input type="button" value=" &lt; <?php echo ucfirst(JText::_('COM_TOOLS_PREVIOUS')); ?> " class="returntoedit" /></span>
<?php } ?>
			<span style="float:right;width:120px;"><input type="submit" value="<?php echo ucfirst(JText::_('Save &amp; Go Next')); ?> &gt;" /></span>
		</div>
		<div class="clear"></div>

<?php 
	switch ($this->step) 
	{
		//  registered
		case 1: 
			$view = new JView(array(
				'name' => $this->controller,
				'layout' => 'compose'
			));
		break;
		case 2:
			$view = new JView(array(
				'name' => $this->controller,
				'layout' => 'authors'
			));
			// authors
			//ContribtoolHtml::stepAuthors( $rid, $this->version, $this->option);
		break;
		case 3:
			$view = new JView(array(
				'name' => $this->controller,
				'layout' => 'attach'
			));
			// attachments
			//ContribtoolHtml::stepAttach( $rid, $this->option, $this->version, $this->status['published']);
		break;
		case 4:
			$view = new JView(array(
				'name' => $this->controller,
				'layout' => 'tags'
			));
			// tags
			//ContribtoolHtml::stepTags( $rid, $tags, $tagfa, $fat, $this->option, $this->status['published'], $this->version );
		break;
	}
	$view->step = $this->step;
	$view->option = $this->option;
	$view->controller = $this->controller;
	$view->version = $this->version;
	$view->row = $this->row;
	$view->status = $this->status;
	$view->dev = $dev;
	$view->tags = $this->tags;
	$view->tagfa = $this->tagfa;
	$view->fats = $this->fats;
	$view->authors = $this->authors;
	$view->display();
?>
		</fieldset><div class="clear"></div>
		<input type="hidden" name="app" value="<?php echo $this->row->alias; ?>" />
		<input type="hidden" name="rid" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="<?php echo $task; ?>" />
		<input type="hidden" name="step" value="<?php echo $nextstep; ?>" />
		<input type="hidden" name="editversion" value="<?php echo $this->version; ?>" />
		<input type="hidden" name="toolname" value="<?php echo $this->status['toolname']; ?>" />

		<div style="float:left; width:70%;padding:1em 0 1em 0;">
<?php if ($this->step != 1) { ?>
			<span style="float:left;width:100px;"><input type="button" value=" &lt; <?php echo ucfirst(JText::_('COM_TOOLS_PREVIOUS')); ?> " class="returntoedit" /></span>
<?php } ?>
			<span style="float:right;width:120px;"><input type="submit" value="<?php echo ucfirst(JText::_('Save &amp; Go Next')); ?> &gt;" /></span>
		</div>
		<div class="clear"></div>
	</form>
</div>