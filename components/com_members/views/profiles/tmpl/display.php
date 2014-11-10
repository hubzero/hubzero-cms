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

$this->css('introduction.css', 'system')
     ->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<?php if ($this->juser->get('guest')) { ?>
		<div id="content-header-extra">
			<p>
				<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=com_members&controller=register'); ?>"><?php echo JText::_('COM_MEMBERS_REGISTER_NOW'); ?></a>
			</p>
		</div><!-- / #content-header-extra -->
	<?php } ?>
</header>

<section id="introduction" class="section">
	<div class="grid">
		<div class="col span9">
			<div class="grid">
				<div class="col span6">
					<h3><?php echo JText::_('COM_MEMBERS_WHY_BECOME_MEMBER'); ?></h3>
					<p><?php echo JText::_('COM_MEMBERS_WHY_BECOME_MEMBER_EXPLANATION'); ?></p>
				</div><!-- / .col span6 -->
				<div class="col span6 omega">
					<h3><?php echo JText::_('COM_MEMBERS_HOW_TO_BECOME_MEMBER'); ?></h3>
					<p><?php echo JText::_('COM_MEMBERS_HOW_TO_BECOME_MEMBER_EXPLANATION'); ?></p>
				</div><!-- / .col span6 -->
			</div>
		</div>
		<div class="col span3 omega">
			<ul>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>"><?php echo JText::_('COM_MEMBERS_FORGOT_USERNAME'); ?></a>
				</li>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"><?php echo JText::_('COM_MEMBERS_FORGOT_PASSWORD'); ?></a>
				</li>
				<li>
					<a class="popup" href="<?php echo JRoute::_('index.php?option=com_help&component=members'); ?>"><?php echo JText::_('COM_MEMBERS_NEED_HELP'); ?></a>
				</li>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_groups'); ?>"><?php echo JText::_('COM_GROUPS'); ?></a>
				</li>
			</ul>
		</div>
	</div><!-- / .grid -->
</section><!-- / #introduction.section -->

<section class="section">

	<div class="grid">
		<div class="col span3">
			<h2><?php echo JText::_('COM_MEMBERS_FIND_MEMBERS'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="grid">
				<div class="col span6">
					<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>" method="get" class="search">
						<fieldset>
							<p>
								<label for="gsearch"><?php echo JText::_('COM_MEMBERS_FIND_MEMBERS_SEARCH_LABEL'); ?></label>
								<input type="text" name="search" id="gsearch" value="" />
								<input type="submit" value="Search" />
							</p>
							<p>
								<?php echo JText::_('COM_MEMBERS_FIND_MEMBERS_BY_SEARCH'); ?>
							</p>
						</fieldset>
					</form>
				</div><!-- / .col span6 -->
				<div class="col span6 omega">
					<div class="browse">
						<p><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo JText::_('COM_MEMBERS_FIND_MEMBERS_BY_BROWSING'); ?></a></p>
						<p><?php echo JText::_('COM_MEMBERS_FIND_MEMBERS_LISTING'); ?></p>
					</div><!-- / .browse -->
				</div><!-- / .col span6 -->
			</div><!-- / .grid -->
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->

	<?php if ($this->contribution_counting)
	{
	?>
	<div class="grid">
		<div class="col span3">
			<h2><?php echo JText::_('COM_MEMBERS_TOP_CONTRIBUTOR'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="grid">
<?php
	$db = JFactory::getDBO();
	$c = new MembersProfile($db);

	$filters = array(
		'limit'  => 4,
		'start'  => 0,
		'show'   => 'contributors',
		'sortby' => 'contributions',
		'public' => 1,
		'authorized' => false
	);

	if ($rows = $c->getRecords($filters, false))
	{
		$i = 0;
		foreach ($rows as $row)
		{
			$contributor = \Hubzero\User\Profile::getInstance($row->uidNumber);
			if (!$contributor || !$contributor->get('uidNumber'))
			{
				continue;
			}

			if ($i == 2)
			{
				$i = 0;
			}

			switch ($i)
			{
				case 2: $cls = ''; break;
				case 1: $cls = 'omega'; break;
				case 0:
				default: $cls = ''; break;
			}
?>
		<div class="col span-half <?php echo $cls; ?>">
			<div class="contributor">
				<p class="contributor-photo">
					<a href="<?php echo JRoute::_($contributor->getLink()); ?>">
						<img src="<?php echo $contributor->getPicture(); ?>" alt="<?php echo JText::sprintf('COM_MEMBERS_TOP_CONTRIBUTOR_PICTURE', $this->escape(stripslashes($contributor->get('name')))); ?>" />
					</a>
				</p>
				<div class="contributor-content">
					<h4 class="contributor-name">
						<a href="<?php echo JRoute::_($contributor->getLink()); ?>">
							<?php echo $this->escape(stripslashes($contributor->get('name'))); ?>
						</a>
					</h4>
					<?php if ($org = $contributor->get('organization')) { ?>
						<p class="contributor-org">
							<?php echo $this->escape(stripslashes($org)); ?>
						</p>
					<?php } ?>
					<div class="clearfix"></div>
				</div>
				<p class="course-instructor-bio">
					<?php if ($contributor->get('bio')) { ?>
						<?php echo $contributor->getBio('clean', 200); ?>
					<?php } else { ?>
						<em><?php echo JText::_('COM_MEMBERS_TOP_CONTRIBUTOR_NO_BIO'); ?></em>
					<?php } ?>
				</p>
			</div>
		</div><!-- / .col span-third -->
		<?php if ($i == 1) { ?>
		</div><!-- / .grid -->
		<div class="grid">
		<?php } ?>
<?php
			$i++;
		}
	}
	else
	{
?>
			<p><?php echo JText::sprintf('COM_MEMBERS_TOP_CONTRIBUTOR_NO_RESULTS', JRoute::_('index.php?option=com_resources&task=new')); ?></p>
<?php
	}
?>
			</div>
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->
<?php } // div class grid ?>
</section><!-- / .section -->
