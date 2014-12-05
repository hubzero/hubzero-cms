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

$juser = JFactory::getUser();

if (!isset($this->controller))
{
	$this->controller = JRequest::getWord('controller', 'page');
}
?>
	<div id="<?php echo ($this->sub) ? 'sub-content-header-extra' : 'content-header-extra'; ?>">
		<ul id="<?php echo ($this->sub) ? 'page_options' : 'useroptions'; ?>">
		<?php if (!$juser->get('guest') && $this->page->access('create')) { ?>
			<li class="page-new" data-title="<?php echo JText::_('COM_WIKI_NEW_PAGE'); ?>">
				<a class="icon-add add btn" href="<?php echo JRoute::_($this->page->link('base') . '&' . ($this->sub ? 'action' : 'task') . '=new'); ?>">
					<?php echo JText::_('COM_WIKI_NEW_PAGE'); ?>
				</a>
			</li>
		<?php } ?>
			<li class="page-index" data-title="<?php echo JText::_('COM_WIKI_PAGE_INDEX'); ?>">
				<a class="icon-index index btn" href="<?php echo JRoute::_($this->page->link('base') . '&pagename=Special:AllPages'); ?>" title="<?php echo JText::_('COM_WIKI_PAGE_INDEX'); ?>">
					<span><?php echo JText::_('COM_WIKI_INDEX'); ?></span>
				</a>
			</li>
			<li class="page-search" data-title="<?php echo JText::_('COM_WIKI_SEARCH'); ?>">
				<a class="icon-search search btn" href="<?php echo JRoute::_($this->page->link('base') . '&pagename=Special:Search'); ?>">
					<?php echo JText::_('COM_WIKI_SEARCH'); ?>
				</a>
				<div class="page-search-form">
					<form action="<?php echo JRoute::_($this->page->link('base') . '&pagename=Special:Search'); ?>" method="post">
						<fieldset>
							<legend><?php echo JText::_('COM_WIKI_SEARCH_LEGEND'); ?></legend>
							<label for="page-search-q">
								<span><?php echo JText::_('COM_WIKI_SEARCH'); ?></span>
								<input type="text" name="q" id="page-search-q" value="" placeholder="<?php echo JText::_('COM_WIKI_SEARCH_PLACEHOLDER'); ?>" />
							</label>
							<input type="submit" class="page-search-submit" value="<?php echo JText::_('COM_WIKI_GO'); ?>" />
						</fieldset>
					</form>
				</div>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->

	<ul class="sub-menu">
		<li class="page-text<?php if ($this->controller == 'page' && ($this->task == 'display' || !$this->task)) { echo ' active'; } ?>">
			<a href="<?php echo JRoute::_($this->page->link()); ?>" title="<?php echo JText::_('COM_WIKI_TAB_ARTICLE'); ?>">
				<span><?php echo JText::_('COM_WIKI_TAB_ARTICLE'); ?></span>
			</a>
		</li>
<?php if ($this->page->exists() && !$this->page->isDeleted() && $this->page->get('namespace') != 'special') { ?>
	<?php if (($this->page->isLocked() && $this->page->access('manage')) || (!$this->page->isLocked() && $this->page->access('edit'))) { ?>
		<li class="page-edit<?php if ($this->controller == 'page' && $this->task == 'edit') { echo ' active'; } ?>">
			<a href="<?php echo JRoute::_($this->page->link('edit')); ?>" title="<?php echo JText::_('COM_WIKI_TAB_EDIT'); ?>">
				<span><?php echo JText::_('COM_WIKI_TAB_EDIT'); ?></span>
			</a>
		</li>
	<?php } ?>
	<?php if ($this->page->access('view', 'comment')) { ?>
		<li class="page-comments<?php if ($this->controller == 'comments') { echo ' active'; } ?>">
			<a href="<?php echo JRoute::_($this->page->link('comments')); ?>" title="<?php echo JText::_('COM_WIKI_TAB_COMMENTS'); ?>">
				<span><?php echo JText::_('COM_WIKI_TAB_COMMENTS'); ?></span>
			</a>
		</li>
	<?php } ?>
		<li class="page-history<?php if ($this->controller == 'history') { echo ' active'; } ?>">
			<a href="<?php echo JRoute::_($this->page->link('history')); ?>" title="<?php echo JText::_('COM_WIKI_TAB_HISTORY'); ?>">
				<span><?php echo JText::_('COM_WIKI_TAB_HISTORY'); ?></span>
			</a>
		</li>
		<li class="page-pdf">
			<a href="<?php echo JRoute::_($this->page->link('pdf')); ?>" title="<?php echo JText::_('COM_WIKI_TAB_PDF'); ?>">
				<span><?php echo JText::_('COM_WIKI_TAB_PDF'); ?></span>
			</a>
		</li>
	<?php
		if (($this->page->isLocked() && $this->page->access('manage', 'page'))
			|| (!$this->page->isLocked() && $this->page->access('delete', 'page'))) { ?>
		<li class="page-delete<?php if ($this->controller == 'page' && $this->task == 'delete') { echo ' active'; } ?>">
			<a href="<?php echo JRoute::_($this->page->link('delete')); ?>" title="<?php echo JText::_('COM_WIKI_DELETE_PAGE'); ?>">
				<span><?php echo JText::_('COM_WIKI_DELETE_PAGE'); ?></span>
			</a>
		</li>
	<?php } ?>
<?php } ?>
	</ul>
