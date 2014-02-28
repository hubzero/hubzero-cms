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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//ximport('Hubzero_Document');

?>

<script type="text/javascript">
jQuery(document).ready(function() 
{	
	jQuery('.fancybox-inline').fancybox();

	jQuery('.actionBtn').click(function()
	{
		var x = jQuery(this).attr('id');
		var record_id = x.split("-").pop();
		var action = x.split("-");
		action = action[0];
		 if(action == 'remove')
		 {
			jQuery("#row-"+record_id).attr('style','background-color:red');
		 	jQuery("#row-"+record_id).remove();
		 	jQuery.fancybox.close();
		 	removedItems.push(record_id);
		 }
		 else
		 {
			 jQuery.fancybox.close();
			 jQuery.post("<?php echo JRoute::_('index.php?option=' . $this->option . '&task=updateStatus&no_html=1'); ?>",
			         {'id': record_id,
		         	  'action': action },
				     function(data) 
				     {
			         });
     		 if(action == "mark")
				{
     				jQuery('#status-'+record_id).text('under review');
	         		jQuery('#status-'+record_id).attr('style','color: purple');
	     						
				}
			else if(action == "approve")
			{
	         		 jQuery('#status-'+record_id).text('approved');	
	         		 jQuery('#status-'+record_id).attr('style','color: green');
			}
			 
		 }
		
	});
	
}); 

</script>

<style>
img { display:block; margin-bottom:10px; }
.postpreview p { word-wrap:break-word;}
.postpreview h1 { font-family: san-serif; font-size: x-large; }
.postpreview { padding: 20px;}
</style>

<style>
.myButton {
	-moz-box-shadow:inset 0px 1px 0px 0px #dcecfb;
	-webkit-box-shadow:inset 0px 1px 0px 0px #dcecfb;
	box-shadow:inset 0px 1px 0px 0px #dcecfb;
	background-color:#bedbfa;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:1px solid #84bbf3;
	display:inline-block;
	cursor:pointer;
	color:#ffffff;
	font-family:arial;
	font-size:15px;
	font-weight:bold;
	padding:6px 24px;
	text-decoration:none;
	text-shadow:0px 1px 0px #528ecc;
}
.myButton:hover {
	background-color:#80b5ea;
}
.myButton:active {
	position:relative;
	top:1px;
}

.actionBtn {
	-moz-box-shadow:inset 0px 1px 0px 0px #cae3fc;
	-webkit-box-shadow:inset 0px 1px 0px 0px #cae3fc;
	box-shadow:inset 0px 1px 0px 0px #cae3fc;
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #79bbff), color-stop(1, #4197ee) );
	background:-moz-linear-gradient( center top, #79bbff 5%, #4197ee 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#79bbff', endColorstr='#4197ee');
	background-color:#79bbff;
	-webkit-border-top-left-radius:0px;
	-moz-border-radius-topleft:0px;
	border-top-left-radius:0px;
	-webkit-border-top-right-radius:0px;
	-moz-border-radius-topright:0px;
	border-top-right-radius:0px;
	-webkit-border-bottom-right-radius:0px;
	-moz-border-radius-bottomright:0px;
	border-bottom-right-radius:0px;
	-webkit-border-bottom-left-radius:0px;
	-moz-border-radius-bottomleft:0px;
	border-bottom-left-radius:0px;
	text-indent:0;
	border:1px solid #469df5;
	display:inline-block;
	color:#ffffff;
	font-family:Arial;
	font-size:11px;
	font-weight:bold;
	font-style:normal;
	height:28px;
	line-height:28px;
	/*width:100px;*/
	text-decoration:none;
	text-align:center;
	text-shadow:1px 1px 0px #287ace;
	margin: 5px;
	padding-bottom: 12px;
}
.actionBtn:hover {
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #4197ee), color-stop(1, #79bbff) );
	background:-moz-linear-gradient( center top, #4197ee 5%, #79bbff 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#4197ee', endColorstr='#79bbff');
	background-color:#4197ee;
}.actionBtn:active {
	position:relative;
	top:1px;
}
	
.feedtable td
{
padding-top: 10px;
margin-top: 10px;
}
</style>

<div id="content-header">
<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
<ul id="useroptions">
	<li>
	<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option='. $this->option . '&controller=posts&task=RetrieveNewPosts'); ?>"><?php echo JText::_('Retreive New Posts'); ?></a>
	</li>

	<li>
	<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option='. $this->option . '&controller=feeds'); ?>"><?php echo JText::_('View Feeds'); ?></a>
	</li>
	
	<li class="last">
		<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=feeds&task=new'); ?>"><?php echo JText::_('Add Feed'); ?></a>
	</li>
</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
<form method="get" action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
<div id="page-main" style="padding-bottom:50px;">
<a href="#helpbox" style="float:right" class="myButton fancybox-inline">Help</a>
<a href="#helpbox" style="background-color: green;" class="myButton fancybox-inline">Generate RSS Feed</a>
<br><br>

<?php if (count($this->posts) > 0):?>
<div class="container">
<ul class="entries-menu filter-options">
<li><a class="filter-all<?php if ($this->filters['filterby'] == 'all') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?&filterby=all'); ?>"><?php echo JText::_('All'); ?></a></li>
<li><a class="filter-all<?php if ($this->filters['filterby'] == 'new') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?&filterby=new'); ?>"><?php echo JText::_('New'); ?></a></li>
<li><a class="filter-all<?php if ($this->filters['filterby'] == 'review') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?&filterby=review'); ?>"><?php echo JText::_('Under Review'); ?></a></li>
<li><a class="filter-all<?php if ($this->filters['filterby'] == 'approved') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?&filterby=approved'); ?>"><?php echo JText::_('Approved'); ?></a></li>
<li><a class="filter-all<?php if ($this->filters['filterby'] == 'removed') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?&filterby=removed'); ?>"><?php echo JText::_('Removed'); ?></a></li>
</ul>
<select style="float:right;" name="timesort" id="timesort">
<option value="0">--</option>
<option value="day">Past 24 hours</option>
<option value="week">Past Week</option>
<option value="month">Past Month</option>
<option value="quarter">Past Quarter</option>
<option value="year">Past Year</option>
<option value="greater">> 1 Year</option>
</select>
	<table class="ideas entries feedtable">
	<caption>Showing all posts</caption>
		<!--<thead class="table-head">
			  <th><a href="#">Name</a></th>
			<th scope="col"><a href="#">Published</a></th>
			<th scope="col"><a href="#">Source</a></th>
			<th scope="col"><a href="#">Status</a></th>
			<th scope="col">Actions</th>
		</thead> -->
		<tbody>	
			<?php foreach($this->posts as $post): ?>
			<?php if(($post->status != "removed" AND $this->filters['filterby'] != "removed") OR 
					($post->status == "removed" AND $this->filters['filterby'] == "removed") OR
					($this->task == "PostsById")): ?>
			<tr id="row-<?php echo $post->id; ?>">
				<td><a class="fancybox-inline" href="#content-fancybox<?php echo $post->id; ?>"><?php echo $post->shortTitle; ?></a></td>
				<td><?php echo $post->created; ?>
				<td><?php echo $post->name;?></td>
				
				<td id="status-<?php echo $post->id; ?>">
				<?php if($post->status == "under review")
				{
					echo '<font color="purple">';
					echo $post->status;
					echo '</font>';
				}
				else if($post->status == "approved")
				{
					echo '<font color="green">';
					echo $post->status;
					echo '</font>';
				}
				else if($post->status == "new")
				{
					echo '<b>';
					echo $post->status;
					echo '</b>';
				}	
				else if($post->status == "removed")
				{
					echo '<font color="red">';
					echo $post->status;
					echo '</font>';
				}								
				?>				
				</td>
				
				<td><input type="button" class="actionBtn" value="Approve" id="approve-<?php echo $post->id;?>"> <input type="button" class="actionBtn" value="Mark for Review" id="mark-<?php echo $post->id;?>"><input type="button" class="actionBtn" value="Remove" id="remove-<?php echo $post->id;?>"></td>
			</tr>
			<div style="display:none">
				<div class="postpreview" id="content-fancybox<?php echo $post->id;?>">
						<h1><?php echo $post->title; ?></h1>
						<p class="description"><?php echo $post->description; ?></p>
						<p><a target="_blank" href="<?php echo $post->link; ?>">Link to original post.</a></p>
						<input type="button" class="actionBtn" value="Approve" id="approve-<?php echo $post->id;?>">
						<input type="button" class="actionBtn" value="Mark for Review" id="mark-<?php echo $post->id;?>">
						<input type="button" class="actionBtn" value="Remove" id="remove-<?php echo $post->id;?>">
				</div>
			</div>
			<?php endif;?>
	<?php endforeach; //end foreach ?>
	</table>
	</div> <!--  / .container  -->
	
<!-- Help Dialog -->
<div style="display:none">		
	<div class="postpreview" id="helpbox">
	<h1>Feed Aggregator Info</h1>
	<p>In order to have the ability to access the administrative/managerial functions of the Feed Aggregator,
the user must be added to a group with an access level higher than a registered user. For instance, the user must be either
an author, editor, or publisher.</p>
	</div>
</div>
<?php 
//$this->pageNav->setAdditionalUrlParam('q', $this->filters['q']);
//$this->pageNav->setAdditionalUrlParam('filterby', $this->filters['filterby']);
//$this->pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
//$this->pageNav->setAdditionalUrlParam('area', $this->filters['area']);
echo $this->pageNav->getListFooter(); 
?>
<?php elseif($this->filters['filterby'] == 'all' OR $this->filters['filterby'] == 'new') : ?>
<p>There are no posts here.</p>
<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option='. $this->option . '&controller=posts&task=RetrieveNewPosts'); ?>"><?php echo JText::_('Retreive New Posts'); ?></a>
<?php else: ?>
<p>You need to review some new posts before you can see anything here!</p>	
<a href="<?php echo JRoute::_('index.php?&filterby=new'); ?>"><?php echo JText::_('View New Posts'); ?></a>
<?php endif; ?>
</form>
</div><!-- /.main section -->
</div> <!--  main page -->
