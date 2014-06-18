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

$this->css('introduction.css', 'system')
     ->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section id="introduction" class="section">
	<div class="grid">
		<div class="col span8">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('COM_TAGS_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<?php
					JPluginHelper::importPlugin('hubzero');
					$tf = JDispatcher::getInstance()->trigger('onGetMultiEntry', array(array('tags', 'tag', 'actags','','')));

					echo (count($tf) > 0) ? implode("\n", $tf) : '<input type="text" name="tag" id="actags" value="" placeholder="' . JText::_('What are you interested in?') . '" />';
					?>
					<!-- <input type="text" name="tag" value="" placeholder="<?php echo JText::_('What are you interested in?'); ?>" /> -->
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				</fieldset>
			</div><!-- / .container -->
			<p>Tags are <strong>keywords</strong> or <strong>phrases</strong> that help you find content, events, and even members which have something <strong>in common</strong>.</p>
			<p><a class="popup" href="<?php echo JRoute::_('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index'); ?>">More on how tags work &raquo;</a></p>
		</div>
		<div class="col span3 offset1 omega">
			<a class="btn icon-browse" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>">
				<?php echo JText::_('Browse the full list'); ?>
			</a>
		</div>
	</div>
</section><!-- / #introduction.section -->

<section class="section">

	<div class="grid">
		<div class="col span6 recent-tags">
			<h2><?php echo JText::_('COM_TAGS_RECENTLY_USED'); ?></h2>

			<?php
			$cloud = $this->cloud->render('html', array(
				'limit'    => 25,
				'admin'    => 0,
				'sort'     => 'taggedon',
				'sort_Dir' => 'DESC',
				'by'       => 'user'
			), true);
			if ($cloud)
			{
				echo $cloud;
			}
			else
			{
				echo '<p class="warning">' . JText::_('COM_TAGS_NO_TAGS') . '</p>' . "\n";
			}
			?>
		</div><!-- / .col span6 -->

		<div class="col span6 omega top-tags">
			<h2><?php echo JText::_('COM_TAGS_TOP_100'); ?></h2>

			<?php
			$cloud = $this->cloud->render('html', array(
				'limit'    => 100,
				'admin'    => 0,
				'sort'     => 'total',
				'sort_Dir' => 'DESC',
				'by'       => 'user'
			), true);
			if ($cloud)
			{
				echo $cloud;
			}
			else
			{
				echo '<p class="warning">' . JText::_('COM_TAGS_NO_TAGS') . '</p>' . "\n";
			}
			?>
		</div><!-- / .col span6 omega -->
	</div><!-- / .grid -->

</dsection><!-- / .section -->
