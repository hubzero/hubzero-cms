<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->css('introduction.css', 'system')
     ->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section id="introduction" class="section">
	<div class="grid">
		<div class="col span8">
			<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=view'); ?>" method="get" class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('COM_TAGS_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<?php echo $this->autocompleter('tags', 'tag', '', 'actags'); ?>
				</fieldset>
			</form><!-- / .container -->
			<p><?php echo JText::_('COM_TAGS_ARE'); ?></p>
			<p><a class="popup" href="<?php echo JRoute::_('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index'); ?>"><?php echo JText::_('COM_TAGS_HOW_DO_TAGS_WORK'); ?></a></p>
		</div>
		<div class="col span3 offset1 omega">
			<a class="btn icon-browse" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>">
				<?php echo JText::_('COM_TAGS_BROWSE_LIST'); ?>
			</a>
		</div>
	</div>
</section><!-- / #introduction.section -->

<section class="section">

	<div class="grid">
		<div class="col span6 recent-tags">
			<h2><?php echo JText::_('COM_TAGS_RECENTLY_USED'); ?></h2>

			<?php
			$filters = array(
				'limit'    => 50,
				'admin'    => 0,
				'sort'     => 'taggedon',
				'sort_Dir' => 'DESC',
				'by'       => 'user'
			);

			if ($this->config->get('cache', 1))
			{
				$cache = JFactory::getCache('tags', 'callback');
				$cache->setCaching(1);
				$cache->setLifeTime(intval($this->config->get('cache_time', 15)));

				$cloud = $cache->call(
					array($this->cloud, 'render'),
					'html',
					$filters,
					true
				);
			}
			else
			{
				$cloud = $this->cloud->render('html', $filters, true);
			}

			echo ($cloud ? $cloud : '<p class="warning">' . JText::_('COM_TAGS_NO_TAGS') . '</p>' . "\n");
			?>
		</div><!-- / .col span6 -->
		<div class="col span6 omega top-tags">
			<h2><?php echo JText::_('COM_TAGS_TOP_USED'); ?></h2>

			<?php
			$filters = array(
				'limit'    => 50,
				'admin'    => 0,
				'sort'     => 'total',
				'sort_Dir' => 'DESC',
				'by'       => 'user'
			);

			if ($this->config->get('cache', 1))
			{
				$cloud = $cache->call(array($this->cloud, 'render'), 'html', $filters, true);
			}
			else
			{
				$cloud = $this->cloud->render('html', $filters, true);
			}

			echo ($cloud ? $cloud : '<p class="warning">' . JText::_('COM_TAGS_NO_TAGS') . '</p>' . "\n");
			?>
		</div><!-- / .col span6 omega -->
	</div><!-- / .grid -->

</section><!-- / .section -->
