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
		<div class="col span9">
			<div class="grid">
				<div class="col span6">
					<h3><?php echo JText::_('COM_TAGS_WHAT_ARE_TAGS'); ?></h3>
					<p><?php echo JText::_('COM_TAGS_WHAT_ARE_TAGS_EXPLANATION'); ?></p>
				</div>
				<div class="col span6 omega">
					<h3><?php echo JText::_('COM_TAGS_HOW_DO_TAGS_WORK'); ?></h3>
					<p><?php echo JText::_('COM_TAGS_HOW_DO_TAGS_WORK_EXPLANATION'); ?></p>
				</div>
			</div>
		</div><!-- / .subject -->
		<div class="col span3 omega">
			<h3><?php echo JText::_('COM_TAGS_QUESTIONS'); ?></h3>
			<ul>
				<li>
					<a class="popup" href="<?php echo JRoute::_('index.php?option=com_help&component=tags&page=index'); ?>">
						<?php echo JText::_('COM_TAGS_HELP_PAGES'); ?>
					</a>
				</li>
			</ul>
		</div><!-- / .aside -->
	</div>
</section><!-- / #introduction.section -->

<section class="section">

	<div class="grid find-tagged">
		<div class="col span3">
			<h2><?php echo JText::_('COM_TAGS_FIND_CONTENT_WITH_TAG'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="grid">
				<div class="col span6">
					<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=view'); ?>" method="get" class="search">
						<fieldset>
							<p>
								<label for="actags"><?php echo JText::_('COM_TAGS_SEARCH_ENTER_TAGS'); ?></label>
								<?php
								JPluginHelper::importPlugin('hubzero');
								$tf = JDispatcher::getInstance()->trigger('onGetMultiEntry', array(array('tags', 'tag', 'actags','','')));

								echo (count($tf) > 0) ? implode("\n", $tf) : '<input type="text" name="tag" id="actags" value="" />';
								?>
								<input type="submit" value="<?php echo JText::_('COM_TAGS_SEARCH'); ?>" />
							</p>
						</fieldset>
					</form>
				</div><!-- / .col span6 -->
				<div class="col span6 omega">
					<div>
						<p><?php echo JText::_('COM_TAGS_SEARCH_EXPLANATION'); ?></p>
					</div><!-- / .browse -->
				</div><!-- / .col span6 omega -->
			</div><!-- / .grid -->
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->

	<div class="grid recent-tags">
		<div class="col span3">
			<h2><?php echo JText::_('COM_TAGS_RECENTLY_USED'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="block">
			<?php
			$cache = JFactory::getCache('callback');
			$cache->setCaching(1);
			$cache->setLifeTime(900);

			$cloud = $cache->call(
				array($this->cloud, 'render'),
				'html',
				array(
					'limit'    => 25,
					'admin'    => 0,
					'sort'     => 'taggedon',
					'sort_Dir' => 'DESC',
					'by'       => 'user'
				),
				true
			);

			/*$cloud = $this->cloud->render('html', array(
				'limit'    => 25,
				'admin'    => 0,
				'sort'     => 'taggedon',
				'sort_Dir' => 'DESC',
				'by'       => 'user'
			), true);*/
			if ($cloud)
			{
				echo $cloud;
			}
			else
			{
				echo '<p class="warning">' . JText::_('COM_TAGS_NO_TAGS') . '</p>' . "\n";
			}
			?>
			</div><!-- / .block -->
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->

	<div class="grid top-tags">
		<div class="col span3">
			<h2><?php echo JText::_('COM_TAGS_TOP_100'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="block">
			<?php
			$cloud = $cache->call(
				array($this->cloud, 'render'),
				'html',
				array(
					'limit'    => 100,
					'admin'    => 0,
					'sort'     => 'total',
					'sort_Dir' => 'DESC',
					'by'       => 'user'
				),
				true
			);

			/*$cloud = $this->cloud->render('html', array(
				'limit'    => 100,
				'admin'    => 0,
				'sort'     => 'total',
				'sort_Dir' => 'DESC',
				'by'       => 'user'
			), true);*/
			if ($cloud)
			{
				echo $cloud;
			}
			else
			{
				echo '<p class="warning">' . JText::_('COM_TAGS_NO_TAGS') . '</p>' . "\n";
			}
			?>
			</div><!-- / .block -->
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->

	<div class="grid find-tags">
		<div class="col span3">
			<h2><?php echo JText::_('COM_TAGS_FIND_A_TAG'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="grid">
				<div class="col span6">
					<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>" method="get" class="search">
						<fieldset>
							<p>
								<label for="tsearch"><?php echo JText::_('CON_TAGS_FIND_LABEL'); ?></label>
								<input type="text" name="search" id="tsearch" value="" />
								<input type="submit" value="<?php echo JText::_('COM_TAGS_SEARCH'); ?>" />
							</p>
						</fieldset>
					</form>
				</div><!-- / .col span6 -->
				<div class="col span6 omega">
					<div class="browse">
						<p><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo JText::_('COM_TAGS_BROWSE_LIST'); ?></a></p>
					</div><!-- / .browse -->
				</div><!-- / .col span6 omega -->
			</div><!-- / .grid -->
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->

</section><!-- / .section -->
