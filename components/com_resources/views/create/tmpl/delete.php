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
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<p>
		<a class="add" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=start'); ?>">
			<?php echo JText::_('New submission'); ?>
		</a>
	</p>
</div><!-- / #content-header -->

<div class="main section">
<?php
	$view = new JView(array('name'=>'steps','layout'=>'steps'));
	$view->option   = $this->option;
	$view->step     = $this->step;
	$view->steps    = $this->steps;
	$view->id       = $this->id;
	$view->resource = $this->row;
	$view->progress = $this->progress;
	$view->display();
?>
<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
	<form name="hubForm" id="hubForm" method="post" action="index.php" class="contrib">
		<div class="explaination">
			<p class="warning"><?php echo JText::_('Canceling a contribution will permanently delete any stored description, linked files, and tags. These cannot be recovered.'); ?><p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('Contribution'); ?></legend>
			<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="discard" />
			<input type="hidden" name="step" value="2" />
			
			<p><strong><?php echo stripslashes($this->row->title); ?></strong><br />
			<?php echo $this->row->typetitle; ?></p>
			
			<label><input type="checkbox" name="confirm" value="confirmed" class="option" /> <?php echo JText::_('Confirm discard'); ?></label>
		</fieldset><div class="clear"></div>
		
		<div class="submit">
			<input type="submit" value="<?php echo JText::_('Delete'); ?>" />
		</div>
	</form>
</div><!-- / .main section -->
