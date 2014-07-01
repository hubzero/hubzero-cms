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

if (!$this->sub)
{
	$this->css();
}
?>
	<header id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
		<h2><?php echo $this->title; ?></h2>
		<?php
		if (!$this->page->isStatic())
		{
			$this->view('authors', 'page')
			     ->setBasePath($this->base_path)
			     ->set('page', $this->page)
			     ->display();
		}
		?>
	</header><!-- /#content-header -->

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if ($this->message) { ?>
	<p class="passed"><?php echo $this->message; ?></p>
<?php } ?>

<?php
	$this->view('submenu', 'page')
	     ->setBasePath($this->base_path)
	     ->set('option', $this->option)
	     ->set('controller', $this->controller)
	     ->set('page', $this->page)
	     ->set('task', $this->task)
	     ->set('sub', $this->sub)
	     ->display();
?>

<section class="main section">
<?php if ($this->page->isLocked() && !$this->page->access('manage')) { ?>

	<p class="warning"><?php echo JText::_('COM_WIKI_WARNING_NOT_AUTH_EDITOR'); ?></p>

<?php } else { ?>

	<form action="<?php echo JRoute::_($this->page->link('base')); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p><?php echo JText::_('COM_WIKI_DELETE_PAGE_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_WIKI_DELETE_PAGE'); ?></legend>

			<label for="confirm-delete">
				<input class="option" type="checkbox" name="confirm" id="confirm-delete" value="1" />
				<?php echo JText::_('COM_WIKI_FIELD_CONFIRM_DELETE'); ?>
			</label>

			<p class="warning">
				<?php echo JText::_('COM_WIKI_FIELD_CONFIRM_DELETE_HINT'); ?>
			</p>

			<input type="hidden" name="pagename" value="<?php echo $this->escape($this->page->get('pagename')); ?>" />
			<input type="hidden" name="scope" value="<?php echo $this->escape($this->page->get('scope')); ?>" />
			<input type="hidden" name="pageid" value="<?php echo $this->escape($this->page->get('id')); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
		<?php if ($this->sub) { ?>
			<input type="hidden" name="active" value="<?php echo $this->escape($this->sub); ?>" />
			<input type="hidden" name="action" value="delete" />
		<?php } else { ?>
			<input type="hidden" name="task" value="delete" />
		<?php } ?>

			<?php echo JHTML::_('form.token'); ?>
		</fieldset><div class="clear"></div>

		<p class="submit">
			<input type="submit" class="btn btn-danger" value="<?php echo JText::_('COM_WIKI_SUBMIT'); ?>" />
		</p>
	</form>

<?php } ?>
</section><!-- / .main section -->
