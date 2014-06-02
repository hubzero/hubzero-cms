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
defined('_JEXEC') or die('Restricted access');

$this->css('create.css');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=draft'); ?>">
				<?php echo JText::_('New submission'); ?>
			</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

<section class="main section">
	<?php
		$this->view('steps', 'steps')
		     ->set('option', $this->option)
		     ->set('step', $this->step)
		     ->set('steps', $this->steps)
		     ->set('id', $this->id)
		     ->set('resource', $this->row)
		     ->set('progress', $this->progress)
		     ->display();
	?>

	<?php if ($this->getError()) { ?>
		<p class="warning"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>

	<form name="hubForm" id="hubForm" method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=discard&step=2&id=' . $this->row->id); ?>" class="contrib">
		<div class="explaination">
			<p class="warning"><?php echo JText::_('COM_CONTRIBUTE_DELETE_WARNING'); ?><p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_CONTRIBUTE_DELETE_LEGEND'); ?></legend>
			<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="discard" />
			<input type="hidden" name="step" value="2" />

			<p><strong><?php echo $this->escape(stripslashes($this->row->title)); ?></strong><br />
			<?php echo $this->escape(stripslashes($this->row->typetitle)); ?></p>

			<label><input type="checkbox" name="confirm" value="confirmed" class="option" /> <?php echo JText::_('COM_CONTRIBUTE_DELETE_CONFIRM'); ?></label>
		</fieldset><div class="clear"></div>

		<div class="submit">
			<input class="btn btn-danger" type="submit" value="<?php echo JText::_('COM_CONTRIBUTE_DELETE'); ?>" />
		</div>
	</form>
</section><!-- / .main section -->
