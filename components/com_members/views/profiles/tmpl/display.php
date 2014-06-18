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
			<ul id="useroptions">
				<li class="last"><a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=com_members&controller=register'); ?>"><?php echo JText::_('Join now!'); ?></a></li>
			</ul>
		</div><!-- / #content-header-extra -->
	<?php } ?>
</header>

<section id="introduction" class="section">
	<div class="grid">
		<div class="col span9">
			<div class="grid">
				<div class="col span6">
					<h3>Why be a member?</h3>
					<p>As a member, you instantly become part of a community designed
					for you and your colleagues.  Being part of the community provides quick and easy access to share knowledge with
					fellow researchers around the world helping you achieve more of your
					goals.  Membership is free, get started today!</p>
				</div><!-- / .col span6 -->
				<div class="col span6 omega">
					<h3>How do I become a member?</h3>
					<p>To become a member, click on the register link at the top of the page,
					create a username and password, and complete the rest of the form.  After
					submitting, you will receive a confirmation email momentarily; please
					follow the instructions within.  You are now part of the unique experience
					that is the HUB!</p>
				</div><!-- / .col span6 -->
			</div>
		</div>
		<div class="col span3 omega">
			<ul>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">Forgot your username?</a>
				</li>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">Forgot your password?</a>
				</li>
				<li>
					<a class="popup" href="<?php echo JRoute::_('index.php?option=com_help&component=members'); ?>">Need Help?</a>
				</li>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_groups'); ?>">Groups</a>
				</li>
			</ul>
		</div>
	</div><!-- / .grid -->
</section><!-- / #introduction.section -->

<section class="section">

	<div class="grid">
		<div class="col span3">
			<h2>Find members</h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="grid">
				<div class="col span6">
					<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>" method="get" class="search">
						<fieldset>
							<p>
								<label for="gsearch">Keyword or phrase:</label>
								<input type="text" name="search" id="gsearch" value="" />
								<input type="submit" value="Search" />
							</p>
							<p>
								Search public members. Members with private profiles do not show up in results.
							</p>
						</fieldset>
					</form>
				</div><!-- / .col span6 -->
				<div class="col span6 omega">
					<div class="browse">
						<p><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>">Browse the list of available members</a></p>
						<p>A list of all public members. Members with private profiles do not show up in results.</p>
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
			<h2>Top contributors</h2>
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
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $contributor->get('uidNumber')); ?>">
						<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($contributor, 0); ?>" alt="<?php echo JText::sprintf('%s\'s photo', $this->escape(stripslashes($contributor->get('name')))); ?>" />
					</a>
				</p>
				<div class="contributor-content">
					<h4 class="contributor-name">
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $contributor->get('uidNumber')); ?>">
							<?php echo $this->escape(stripslashes($contributor->get('name'))); ?>
						</a>
					</h4>
				<?php if ($contributor->get('organization')) { ?>
					<p class="contributor-org">
						<?php echo $this->escape(stripslashes($contributor->get('organization'))); ?>
					</p>
				<?php } ?>
					<div class="clearfix"></div>
				</div>
				<p class="course-instructor-bio">
				<?php if ($contributor->get('bio')) { ?>
					<?php echo \Hubzero\Utility\String::truncate(stripslashes($contributor->get('bio')), 200); ?>
				<?php } else { ?>
					<em><?php echo JText::_('This contributor has yet to write their bio.'); ?></em>
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
			<p>No contributors found. <a href="<?php echo JRoute::_('index.php?option=com_resources&task=new'); ?>">Be the first!</a></p>
<?php
	}
?>
			</div>
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->
<?php } // div class grid ?>
</section><!-- / .section -->
