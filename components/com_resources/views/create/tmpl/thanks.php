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

$attachments = 0;
$authors = 0;
$tags = array();
$state = 'pending';
$type = '';
if ($this->resource->id)
{
	$database = JFactory::getDBO();
	$ra = new ResourcesAssoc($database);
	$rc = new ResourcesContributor($database);
	$rt = new ResourcesTags($this->resource->id);

	switch ($this->resource->published)
	{
		case 1: $state = 'published';  break;  // published
		case 2: $state = 'draft';      break;  // draft
		case 3: $state = 'pending';    break;  // pending
	}

	$type = $this->resource->getTypeTitle();

	$attachments = $ra->getCount($this->resource->id);

	$authors = $rc->getCount($this->resource->id, 'resources');

	$tags = $rt->tags('count');
}


$this->css('create.css');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=draft'); ?>">
				<?php echo JText::_('COM_CONTRIBUTE_NEW_SUBMISSION'); ?>
			</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

<section class="main section">
	<div class="subject">
		<?php if ($this->getError()) { ?>
			<p class="warning"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php } ?>

		<p class="passed">
			Thank you for your contribution!
		</p>

		<div class="container">
			<table class="summary">
				<caption>Contribution submitted:</caption>
				<tbody>
					<tr>
						<th scope="row"><?php echo JText::_('Type'); ?></th>
						<td>
							<?php echo ($type) ? $this->escape(stripslashes($type)) : JText::_('(none)'); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo JText::_('Title'); ?></th>
						<td>
							<?php echo ($this->resource->title) ? $this->escape(\Hubzero\Utility\String::truncate(stripslashes($this->resource->title), 150)) : JText::_('(none)'); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo JText::_('Attachments'); ?></th>
						<td>
							<?php echo $attachments; ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo JText::_('Authors'); ?></th>
						<td>
							<?php echo $authors; ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo JText::_('Tags'); ?></th>
						<td>
							<?php echo $tags; ?>
						</td>
					</tr>
					<tr>
						<th scope="crow"><?php echo JText::_('Status'); ?></th>
						<td>
							<span class="<?php echo $state; ?> status"><?php echo $state; ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="container">
			<div class="container-block">
				<h3>Frequently Asked Questions</h3>
				<div class="entry-content">
					<ul class="faq-list">
						<li><a href="#submission">What happens now?</a></li>
					<?php if ($this->config->get('autoapprove', 0) != 1) { ?>
						<li><a href="#status">How will I know when my contribution is accepted?</a></li>
					<?php } ?>
						<li><a href="#retract">Ooops! I missed something and/or submitted too early!</a></li>
					</ul>
				</div>
			<?php if ($this->config->get('autoapprove', 0) != 1) { ?>
				<div class="entry-content" id="submission">
					<h4>What happens now?</h4>
					<p>After submitting your contribution, it will be reviewed for completeness. If all appears satisfactory, the contribution will be approved and immediately appear in the <a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>">resources listing</a>.</p>
				</div>
				<div class="entry-content" id="status">
					<h4>How will I know when my contribution is accepted?</h4>
					<p>When a contribution passes the review stage and is published (made publicly available), an email is sent to all authors listed on the contribution.</p>
					<p>You may also continually monitor the status by:</p>
					<ul>
						<li>checking your "contributions" tab under your <a href="<?php echo JRoute::_('index.php?option=com_members&task=myaccount'); ?>">account</a></li>
						<li>checking the "My Drafts" module on your personalized dashboard (found <a href="<?php echo JRoute::_('index.php?option=com_members&task=myaccount'); ?>">here</a>). <strong>Note:</strong> The module must be present on your dashboard. If it isn't, you can easily add it from the "personalize dashboard" item.</li>
						<li>visiting the <a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=new'); ?>">new contribution</a> page</li>
					</ul>
				</div>
				<div class="entry-content" id="retract">
					<h4>Ooops! I missed something and/or submitted too early!</h4>
					<p>No worries! You can retract a submission by following these steps:</p>
					<ul>
						<li>Visit the <a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=new'); ?>">new contribution</a> page.</li>
						<li>You should be presented with a list of your "drafts" and "pending" submissions. Find the (pending) contribution you wish to retract.</li>
						<li>Click "retract".</li>
					</ul>
				</div>
			<?php } else { ?>
				<div class="entry-content" id="submission">
					<h4>What happens now?</h4>
					<p>Your contribution is now publicly available. You may view it <a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->resource->id); ?>">here</a>.</p>
				</div>
				<div class="entry-content" id="retract">
					<h4>Ooops! I missed something and/or submitted too early!</h4>
					<p>No worries! You can either <a href="<?php echo JRoute::_('index.php?option=com_support'); ?>">contact the site administrators</a> and ask the submission be retracted (set back to "draft" status) or modify a submission by following these steps:</p>
					<ul>
						<li>Visit the <a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->resource->id); ?>">resource's page</a> while <strong>logged in</strong>.</li>
						<li>You should see an "edit" button or link next to the title of the resource.</li>
						<li>Click "edit" and make the desired edits. Changes on approved resource take effect immediately and do not require approval.</li>
					</ul>
				</div>
			<?php } ?>
			</div><!-- / .container-block -->
		</div><!-- / .container -->
	</div><!-- /.subject -->
	<aside class="aside">
		<p>
			<a class="icon-prev btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=new'); ?>">Return to start</a>
		</p>
	</aside><!-- /.aside -->
</section><!-- / .main section -->
