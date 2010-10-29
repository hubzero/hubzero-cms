<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$database =& JFactory::getDBO();
$juser =& JFactory::getUser();

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
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="introduction" class="section">
	<div class="aside">
		<p id="getstarted"><a href="<?php echo JRoute::_('index.php?option='.$option.'&task=start'); ?>">Get Started &rsaquo;</a></p>
	</div><!-- / .aside -->
	<div class="subject">
		<div class="two columns first">
			<h3>Present your work!</h3>
			<p>Become a contributor and share your work with the community! Contributing content is easy. Our step-by-step forms will guide you through the process.</p>
		</div>
		<div class="two columns second">
			<h3>What do I need?</h3>
			<p>The submission process will guide you through step-by-step, but for more detailed instructions on what can be submitted and how, please see the list of submission types below.</p>
		</div>
		<div class="clear"></div>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / #introduction.section -->

<div class="section">

<?php if (!$juser->get('guest')) { ?>
	<div class="four columns first">
		<h2>In Progress</h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
<?php
		if ($submissions) {
?>
		<table id="submissions" summary="Contributions in progress">
			<thead>
				<tr>
					<th>Title</th>
					<th colspan="3">Associations</th>
					<th colspan="2">Status</th>
				</tr>
			</thead>
			<tbody>
<?php
			$ra = new ResourcesAssoc( $database );
			$rc = new ResourcesContributor( $database );
			$rt = new ResourcesTags( $database );
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
				
				$attachments = $ra->getCount( $submission->id );
			
				$authors = $rc->getCount( $submission->id, 'resources' );
				
				$tags = $rt->getTags( $submission->id );
?>
				<tr class="<?php echo $cls; ?>">
					<td><?php if ($submission->published == 2) { ?><a href="<?php echo JRoute::_('index.php?option=com_contribute&step=1&id='.$submission->id); ?>"><?php } ?><?php echo stripslashes($submission->title); ?><?php if ($submission->published == 2) { ?></a><?php } ?><br /><span class="type"><?php echo $submission->typetitle; ?></span></td>
					<td><?php if ($submission->published == 2) { ?><a href="<?php echo JRoute::_('index.php?option=com_contribute&step=2&id='.$submission->id); ?>"><?php } ?><?php echo count($attachments); ?> attachment(s)<?php if ($submission->published == 2) { ?></a><?php } ?></td>
					<td><?php if ($submission->published == 2) { ?><a href="<?php echo JRoute::_('index.php?option=com_contribute&step=3&id='.$submission->id); ?>"><?php } ?><?php echo count($authors); ?> author(s)<?php if ($submission->published == 2) { ?></a><?php } ?></td>
					<td><?php if ($submission->published == 2) { ?><a href="<?php echo JRoute::_('index.php?option=com_contribute&step=4&id='.$submission->id); ?>"><?php } ?><?php echo count($tags); ?> tag(s)<?php if ($submission->published == 2) { ?></a><?php } ?></td>
					<td>
						<span class="<?php echo $state; ?> status"><?php echo $state; ?></span>
						<?php if ($submission->published == 2) { ?>
						<br /><a class="review" href="<?php echo JRoute::_('index.php?option=com_contribute&step=5&id='.$submission->id); ?>"><?php echo JText::_('Review &amp; Submit &rsaquo;'); ?></a>
						<?php } elseif ($submission->published == 3) { ?>
						<br /><a class="retract" href="<?php echo JRoute::_('index.php?option=com_contribute&task=retract&id='.$submission->id); ?>"><?php echo JText::_('&lsaquo; Retract'); ?></a>
						<?php } ?>
					</td>
					<td><a class="delete" href="<?php echo JRoute::_('index.php?option=com_contribute&task=discard&id='.$submission->id); ?>" title="<?php echo JText::_('Delete'); ?>"><?php echo JText::_('Delete'); ?></a></td>
				</tr>
<?php
			}
?>
			</tbody>
		</table>
<?php
		} else {
?>
		<p class="info">
			<strong>You currently have no contributions in progress.</strong><br /><br />
			Once you've started a new contribution, you can proceed at your leisure. Stop half-way through and watch a presentation, go to lunch, even close the browser and come back a different day! Your contribution will be waiting just as you left it, ready to continue at any time.
		</p>
<?php 
		} 
?>
	</div><!-- / .four columns second third fourth -->
<?php } ?>

	<div class="four columns first">
		<h2>Before starting</h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="two columns first">
			<h3>Intellectual Property Considerations</h3>
			<p>All materials contributed must have <strong>clearly defined rights and privileges</strong>. Online presentations and instructional material are normally licensed under <a href="/legal/cc/" title="Learn more about Creative Commons">Creative Commons 3</a>. Read <a href="/legal/licensing/">more details</a> about our licensing policies.</p>
		</div>
		<div class="two columns second">
			<h3>Questions or concerns?</h3>
			<p>We hope that our self-service upload process is intuitive and easy to use. If you encounter any problems during the upload process or need assistance of any kind, please <a href="/feedback/report_problems/">file a trouble report</a>.</p>
		</div>
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>
	
<?php
$t = new ResourcesType( $database );
$categories = $t->getMajorTypes();
if ($categories) {
?>
	<div class="four columns first">
		<h2>What can I contribute?</h2>
		<!-- <p>If you have a contribution that does not seem to fit one of these categories, please contact our <a href="/support">support</a> for further assistance.</p> -->
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
<?php
	$i = 0;
	$clm = '';
	/*if (count($categories)%3!=0) { 
	    ;
	}*/
	foreach ($categories as $category) 
	{
		if ($category->contributable != 1) {
			continue;
		}
		
		$i++;
		switch ($clm) 
		{
			case 'second': $clm = 'third'; break;
			case 'first': $clm = 'second'; break;
			case '':
			default: $clm = 'first'; break;
		}
		
		$normalized = preg_replace("/[^a-zA-Z0-9]/", "", $category->type);
		$normalized = strtolower($normalized);
		
		if (substr($normalized, -3) == 'ies') {
			$cls = $normalized;
		} else {
			$cls = substr($normalized, 0, -1);
		}
?>
		<div class="three columns <?php echo $clm; ?>">
			<div class="<?php echo $cls; ?>">
				<h3><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&step=1&type='.$category->id); ?>"><?php echo stripslashes($category->type); ?></a></h3>
				<p><?php echo stripslashes($category->description); ?></p>
				<p><a class="read-more" href="/contribute/<?php echo $normalized; ?>" title="Learn more about <?php echo htmlentities(stripslashes($category->type),ENT_COMPAT,'UTF-8'); ?>">Learn more <span>about <?php echo stripslashes($category->type); ?> </span>&rsaquo;</a></p>
			</div>
		</div><!-- / .three columns <?php echo $clm; ?> -->
<?php
		if ($clm == 'third') {
			echo '<div class="clear"></div>';
			$clm = '';
			$i = 0;
		}
	}
	if ($i == 1) {
		?>
		<div class="three columns second">
			<p> </p>
		</div><!-- / .three columns second -->
		<?php
	}
	if ($i == 1 || $i == 2) {
		?>
		<div class="three columns third">
			<p> </p>
		</div><!-- / .three columns third -->
		<?php
	}
?>
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>
<?php
}
?>

</div><!-- / .section -->