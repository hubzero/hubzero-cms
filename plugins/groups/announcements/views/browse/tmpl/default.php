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

//add styles and scripts
$this->css();
$this->js();
?>

<?php if ($this->authorized == 'manager') : ?>
	<ul id="page_options">
		<li>
			<a class="icon-add btn add" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=announcements&action=new'); ?>">
				<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_NEW'); ?>
			</a>
		</li>
	</ul>
<?php endif; ?>

<section class="main section">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>

	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=announcements'); ?>" method="post">
		<div class="container data-entry">
			<input class="entry-search-submit" type="submit" value="<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_SEARCH'); ?>" />
			<fieldset class="entry-search">
				<legend><?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_SEARCH_LEGEND'); ?></legend>
				<label for="entry-search-field"><?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_SEARCH_LABEL'); ?></label>
				<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_SEARCH_PLACEHOLDER'); ?>" />
			</fieldset>
		</div><!-- / .container -->

		<div class="acontainer">
			<?php if ($this->total > 0) : ?>
				<?php foreach ($this->rows as $row) : ?>
					<?php
						$this->view('item')
						     ->set('option', $this->option)
						     ->set('group', $this->group)
						     ->set('juser', $this->juser)
						     ->set('authorized', $this->authorized)
						     ->set('announcement', new GroupsModelAnnouncement($row))
						     ->set('showClose', false)
						     ->display();
					?>
				<?php endforeach; ?>
			<?php else : ?>
				<p class="warning">
					<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_NO_RESULTS'); ?>
				</p>
			<?php endif; ?>

			<?php
				jimport('joomla.html.pagination');
				$pageNav = new JPagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
				$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
				$pageNav->setAdditionalUrlParam('active', 'announcements');
				echo $pageNav->getListFooter();
			?>
			<div class="clearfix"></div>
		</div><!-- / .acontainer -->
	</form>
</section>