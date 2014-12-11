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

$database = JFactory::getDBO();
$juser = JFactory::getUser();

$submissions = null;
if (!$juser->get('guest')) {
	$query  = "SELECT DISTINCT R.id, R.title, R.type, R.logical_type AS logicaltype,
						AA.subtable, R.created, R.created_by, R.published, R.publish_up, R.standalone,
						R.rating, R.times_rated, R.alias, R.ranking, rt.type AS typetitle ";
	$query .= "FROM #__author_assoc AS AA, #__resource_types AS rt, #__resources AS R ";
	$query .= "LEFT JOIN #__resource_types AS t ON R.logical_type=t.id ";
	$query .= "WHERE AA.authorid = ". $juser->get('id') ." ";
	$query .= "AND R.id = AA.subid ";
	$query .= "AND AA.subtable = 'resources' ";
	$query .= "AND R.standalone=1 AND R.type=rt.id AND (R.published=2 OR R.published=3) AND R.type!=7 ";
	$query .= "ORDER BY published ASC, title ASC";

	$database->setQuery($query);
	$submissions = $database->loadObjectList();
}

$this->css('introduction.css', 'system')
     ->css('create.css');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section id="introduction" class="contribute section">
	<div class="grid">
		<div class="col span9">
			<div class="col span6">
				<h3>Present your work!</h3>
				<p>Become a contributor and share your work with the community! Contributing content is easy. Our step-by-step forms will guide you through the process.</p>
			</div>
			<div class="col span6 omega">
				<h3>What do I need?</h3>
				<p>The submission process will guide you through step-by-step, but for more detailed instructions on what can be submitted and how, please see the list of submission types below.</p>
			</div>
		</div>
		<div class="col span3 omega">
			<p id="getstarted">
				<a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=draft'); ?>">Get Started &rsaquo;</a>
			</p>
		</div><!-- / .aside -->
	</div>
</section><!-- / #introduction.section -->

<section class="section">

<?php if (!$juser->get('guest')) { ?>
	<div class="grid">
		<div class="col span3">
			<h2><?php echo JText::_('In Progress'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
		<?php if ($submissions) { ?>
			<table id="submissions">
				<thead>
					<tr>
						<th scope="col"><?php echo JText::_('Title'); ?></th>
						<th scope="col" colspan="3"><?php echo JText::_('Associations'); ?></th>
						<th scope="col" colspan="2"><?php echo JText::_('Status'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
					$ra = new ResourcesAssoc($database);
					$rc = new ResourcesContributor($database);
					$cls = 'even';
					foreach ($submissions as $submission)
					{
						$cls = ($cls == 'even') ? 'odd' : 'even';

						switch ($submission->published)
						{
							case 1: $state = 'published';  break;  // published
							case 2: $state = 'draft';      break;  // draft
							case 3: $state = 'pending';    break;  // pending
						}

						$attachments = $ra->getCount($submission->id);

						$authors = $rc->getCount($submission->id, 'resources');

						$rt = new ResourcesTags($submission->id);
						$tags = $rt->tags('count');
					?>
					<tr class="<?php echo $cls; ?>">
						<td><?php if ($submission->published == 2) { ?><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=draft&step=1&id='.$submission->id); ?>"><?php } ?><?php echo stripslashes($submission->title); ?><?php if ($submission->published == 2) { ?></a><?php } ?><br /><span class="type"><?php echo stripslashes($submission->typetitle); ?></span></td>
						<td><?php if ($submission->published == 2) { ?><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=draft&step=2&id='.$submission->id); ?>"><?php } ?><?php echo $attachments; ?> attachment(s)<?php if ($submission->published == 2) { ?></a><?php } ?></td>
						<td><?php if ($submission->published == 2) { ?><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=draft&step=3&id='.$submission->id); ?>"><?php } ?><?php echo $authors; ?> author(s)<?php if ($submission->published == 2) { ?></a><?php } ?></td>
						<td><?php if ($submission->published == 2) { ?><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=draft&step=4&id='.$submission->id); ?>"><?php } ?><?php echo $tags; ?> tag(s)<?php if ($submission->published == 2) { ?></a><?php } ?></td>
						<td>
							<span class="<?php echo $state; ?> status"><?php echo $state; ?></span>
							<?php if ($submission->published == 2) { ?>
							<br /><a class="review" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=draft&step=5&id='.$submission->id); ?>"><?php echo JText::_('Review &amp; Submit &rsaquo;'); ?></a>
							<?php } elseif ($submission->published == 3) { ?>
							<br /><a class="retract" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=retract&id='.$submission->id); ?>"><?php echo JText::_('&lsaquo; Retract'); ?></a>
							<?php } ?>
						</td>
						<td><a class="icon-delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=discard&id='.$submission->id); ?>" title="<?php echo JText::_('Delete'); ?>"><?php echo JText::_('Delete'); ?></a></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		<?php } else { ?>
			<p class="info">
				<strong>You currently have no contributions in progress.</strong><br /><br />
				Once you've started a new contribution, you can proceed at your leisure. Stop half-way through and watch a presentation, go to lunch, even close the browser and come back a different day! Your contribution will be waiting just as you left it, ready to continue at any time.
			</p>
		<?php } ?>
		</div><!-- / .col span9 omega -->
	</div>
<?php } ?>

	<div class="grid">
		<div class="col span3">
			<h2>Before starting</h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="grid">
				<div class="col span6">
					<h3>Intellectual Property Considerations</h3>
					<p>All materials contributed must have <strong>clearly defined rights and privileges</strong>. Online presentations and instructional material are normally licensed under <a class="legal creative-commons" href="/legal/cc" title="Learn more about Creative Commons">Creative Commons 3</a>. Read <a class="legal licensing" href="/legal/licensing">more details</a> about our licensing policies.</p>
				</div><!-- / .col span6 -->
				<div class="col span6 omega">
					<h3>Questions or concerns?</h3>
					<p>We hope that our self-service upload process is intuitive and easy to use. If you encounter any problems during the upload process or need assistance of any kind, please <a class="new-ticket" href="/support/ticket/new">file a trouble report</a>.</p>
				</div><!-- / .col span6 -->
			</div><!-- / .grid -->
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->

<?php
$t = new ResourcesType( $database );
$categories = $t->getMajorTypes();
if ($categories) {
?>
	<div class="grid">
		<div class="col span3">
			<h2>What can I contribute?</h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="grid">
		<?php
		$i = 0;
		$clm = '';

		foreach ($categories as $category)
		{
			if ($category->contributable != 1)
			{
				continue;
			}

			$i++;
			switch ($i)
			{
				case 3: $clm = 'omega'; break;
				case 2: $clm = ''; break;
				case 1:
				default: $clm = ''; break;
			}

			if (substr($category->alias, -3) == 'ies') {
				$cls = $category->alias;
			} else {
				$cls = substr($category->alias, 0, -1);
			}
			?>
			<div class="col span-third <?php echo $clm; ?>">
				<div class="<?php echo $cls; ?>">
					<h3><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=draft&step=1&type='.$category->id); ?>"><?php echo stripslashes($category->type); ?></a></h3>
					<p><?php echo stripslashes($category->description); ?></p>
				</div>
			</div><!-- / .col span-third <?php echo $clm; ?> -->
			<?php
			if ($clm == 'omega') {
				echo '</div><div class="grid">';
				$clm = '';
				$i = 0;
			}
		}
		if ($i == 1) {
			?>
			<div class="col span-third">
				<p> </p>
			</div><!-- / .col span-third -->
			<?php
		}
		if ($i == 1 || $i == 2) {
			?>
			<div class="col span-third omega">
				<p> </p>
			</div><!-- / .col span-third omega -->
			<?php
		}
		?>
			</div>
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->
<?php
}
?>

</section><!-- / .section -->
