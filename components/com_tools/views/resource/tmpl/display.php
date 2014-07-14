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

$this->css('resource.css')
     ->js('resource.js');
?>
<header id="content-header">
	<h2><?php echo $this->escape($this->title); ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="icon-status btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=pipeline&task=status&app=' . $this->row->alias); ?>"><?php echo JText::_('COM_TOOLS_TOOL_STATUS'); ?></a></li>
			<li class="last"><a class="icon-add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=pipeline&task=create'); ?>"><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_NEW_TOOL'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="section steps-section">
	<?php
	$this->view('stage')
	     ->set('stage', $this->step)
	     ->set('option', $this->option)
	     ->set('controller', $this->controller)
	     ->set('version', $this->version)
	     ->set('row', $this->row)
	     ->set('status', $this->status)
	     ->set('vnum', $v)
	     ->display();
	?>
</section>

<section class="main section">
	<form action="index.php" method="post" id="hubForm">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php } ?>

		<div style="float:left; width:70%;padding:1em 0 1em 0;">
		<?php if ($this->step !=1 ) { ?>
			<span style="float:left;width:100px;"><input type="button" value=" &lt; <?php echo ucfirst(JText::_('COM_TOOLS_PREVIOUS')); ?> " class="returntoedit" /></span>
		<?php } ?>
			<span style="float:right;width:120px;"><input type="submit" value="<?php echo ucfirst(JText::_('COM_TOOLS_SAVE_AND_GO_NEXT')); ?>" /></span>
		</div>
		<div class="clear"></div>

		<?php
			switch ($this->step)
			{
				//  registered
				case 1:
					$layout = 'compose';
				break;
				case 2:
					$layout = 'authors';
				break;
				case 3:
					$layout = 'attach';
				break;
				case 4:
					$layout = 'tags';
				break;
			}
			$this->view($layout)
			     ->set('step', $this->step)
			     ->set('option', $this->option)
			     ->set('controller', $this->controller)
			     ->set('version', $this->version)
			     ->set('row', $this->row)
			     ->set('status', $this->status)
			     ->set('dev', $dev)
			     ->set('tags', $this->tags)
			     ->set('tagfa', $this->tagfa)
			     ->set('fats', $this->fats)
			     ->set('authors', $this->authors)
			     ->display();
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
			<span style="float:right;width:120px;"><input type="submit" value="<?php echo ucfirst(JText::_('COM_TOOLS_SAVE_AND_GO_NEXT')); ?>" /></span>
		</div>
		<div class="clear"></div>
	</form>
</section>