<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(isset($this->filters['filterby']) != TRUE)
{
	$this->filters['filterby'] = 'all';
}

$this->js('posts');
$this->css('posts');


?>

<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li>
			<a href="#feedbox" id="generateFeed" class="fancybox-inline icon-feed btn">Generate RSS Feed</a>
		</li>
		<li>
		<a class="icon-download btn" href="<?php echo JRoute::_('index.php?option='. $this->option . '&controller=posts&task=RetrieveNewPosts'); ?>"><?php echo JText::_('Retrieve New Posts'); ?></a>
		</li>
	
		<li>
		<a class="icon-browse btn" href="<?php echo JRoute::_('index.php?option='. $this->option . '&controller=feeds'); ?>"><?php echo JText::_('View Feeds'); ?></a>
		</li>
		
		<li class="last">
			<a class="icon-add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=feeds&task=new'); ?>"><?php echo JText::_('Add Feed'); ?></a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<form method="get" action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
<div id="page-main">

<?php if (count($this->posts) > 0):?>
<div class="container">
	<ul class="entries-menu filter-options">
		<li><a class="filter-all<?php if ($this->filters['filterby'] == 'all') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?&filterby=all'); ?>"><?php echo JText::_('All'); ?></a></li>
		<li><a class="filter-all<?php if ($this->filters['filterby'] == 'new') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?&filterby=new'); ?>"><?php echo JText::_('New'); ?></a></li>
		<li><a class="filter-all<?php if ($this->filters['filterby'] == 'review') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?&filterby=review'); ?>"><?php echo JText::_('Under Review'); ?></a></li>
		<li><a class="filter-all<?php if ($this->filters['filterby'] == 'approved') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?&filterby=approved'); ?>"><?php echo JText::_('Approved'); ?></a></li>
		<li><a class="filter-all<?php if ($this->filters['filterby'] == 'removed') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?&filterby=removed'); ?>"><?php echo JText::_('Removed'); ?></a></li>
	</ul>
<!-- <select name="timesort" id="timesort">
<option value="0">--</option>
<option value="day">Past 24 hours</option>
<option value="week">Past Week</option>
<option value="month">Past Month</option>
<option value="quarter">Past Quarter</option>
<option value="year">Past Year</option>
<option value="greater">> 1 Year</option>
</select> -->
	<table class="ideas entries feedtable">
	<caption>Showing <?php echo $this->filters['filterby'];?> posts</caption>
		<tbody>	
			<?php foreach($this->posts as $post): ?>
			<?php if(($post->status != "removed" AND $this->filters['filterby'] != "removed") OR 
					($post->status == "removed" AND $this->filters['filterby'] == "removed") OR
					($this->task == "PostsById")): ?>
			<tr id="row-<?php echo $post->id; ?>">
				<td><a class="fancybox-inline" rel="group1" href="#content-fancybox<?php echo $post->id; ?>"><?php echo $post->shortTitle; ?></a></td>
				<td><?php echo $post->created; ?>
				<td><?php echo $post->name;?></td>
				
				<td id="status-<?php echo $post->id; ?>">
				<?php if ($post->status == "under review")
				{
					echo '<div class="review-status">';
					echo $post->status;
					echo '</div>';
				}
				else if ($post->status == "approved")
				{
					echo '<div class="approve-status">';
					echo $post->status;
					echo '</div>';
				}
				else if ($post->status == "new")
				{
					echo '<b>';
					echo $post->status;
					echo '</b>';
				}	
				else if ($post->status == "removed")
				{
					echo '<div class="remove-status">';
					echo $post->status;
					echo '</div>';
				}								
				?>				
				</td>
				
				<td>
					<input type="button" data-id="<?php echo $post->id; ?>" data-action="approve" class="approveBtn btn actionBtn <?php echo 'btnGrp'.$post->id; ?>" value="Approve" id="approve-<?php echo $post->id;?>" <?php echo ($post->status == "approved" ? 'disabled' : ''); ?>>	
					<input type="button" data-id="<?php echo $post->id; ?>" data-action="mark" class="reviewBtn btn actionBtn <?php echo 'btnGrp'.$post->id; ?>" value="Mark for Review" id="mark-<?php echo $post->id; ?>" <?php echo ($post->status == "under review" ? 'disabled' : ''); ?>>
					<input type="button" data-id="<?php echo $post->id; ?>" data-action="remove" class="removeBtn btn actionBtn <?php echo 'btnGrp'.$post->id; ?>" value="Remove" id="remove-<?php echo $post->id;?>" <?php echo ($post->status == "removed" ? 'disabled' : ''); ?> >
				</td>
			</tr>
			<div class="postpreview-container">
				<div class="postpreview" id="content-fancybox<?php echo $post->id;?>">
						<h1><?php echo $post->title; ?></h1>
						<p class="description"><?php echo $post->description; ?></p>
						<p><a target="_blank" href="<?php echo $post->link; ?>">Link to original post.</a></p>
						<div class="button-container">
						<hr />
						<input type="button" data-id="<?php echo $post->id; ?>" data-action="approve" class="actionBtn approveBtn btn <?php echo 'btnGrp'.$post->id; ?> " value="Approve" id="approve-prev-<?php echo $post->id;?>" <?php echo ($post->status == "approved" ? 'disabled' : ''); ?> >
						<input type="button" data-id="<?php echo $post->id; ?>" data-action="mark" class="reviewBtn btn actionBtn <?php echo 'btnGrp'.$post->id; ?> " value="Mark for Review" id="mark-prev-<?php echo $post->id;?>" <?php echo ($post->status == "under review" ? 'disabled' : ''); ?> >
						<input type="button" data-id="<?php echo $post->id; ?>" data-action="remove" class="removeBtn btn actionBtn <?php echo 'btnGrp'.$post->id; ?> " value="Remove" id="remove-prev-<?php echo $post->id;?>" <?php echo ($post->status == "removed" ? 'disabled' : ''); ?> >
						</div>
				</div>
			</div>
			<?php endif;?>
	<?php endforeach; //end foreach ?>
	</table>
	</div> <!--  / .container  -->
	
<?php 
if ($this->fromfeed != TRUE)
{
	echo $this->pageNav->getListFooter();
} 
?>
<?php elseif ($this->filters['filterby'] == 'all' OR $this->filters['filterby'] == 'new') : ?>
<p>There are no posts here.</p>
<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option='. $this->option . '&controller=posts&task=RetrieveNewPosts'); ?>"><?php echo JText::_('Retrieve New Posts'); ?></a>
<?php else: ?>
<p>You need to review some new posts before you can see anything here!</p>	
<a href="<?php echo JRoute::_('index.php?&filterby=new'); ?>"><?php echo JText::_('View New Posts'); ?></a>
<?php endif; ?>
</form>

<!--  Generate Feed -->
<div class="postpreview-container">		
	<div class="postpreview" id="feedbox">
	<h2><?php echo JText::_('COM_FEEDAGGREGATOR_GENERATE_HEADER'); ?></h2>
	<p><?php echo JText::_('COM_FEEDAGGREGATOR_GENERATE_INSTRUCTIONS'); ?></p>
	<p><a href="<?php echo JRoute::_(JFactory::getURI()->base().'index.php?option=com_feedaggregator&task=generateFeed&no_html=1'); ?>"><?php echo JRoute::_(JFactory::getURI()->base().'index.php?option=com_feedaggregator&task=generateFeed&no_html=1'); ?></a></p>
	</div>
</div>

</div><!-- /.main section -->
</div> <!--  main page -->
