<?php
/**
 * @package		HUBzero CMS
 * @author		Jason Lambert <jblamber@purdue.edu>
 * @copyright	Copyright 2005-2010 by Purdue Research Foundation, West Lafayette, IN 47906
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
?>
<?php 
$xhub =& XFactory::getHub();
				//if (JRequest::getVar( 'tmpl', '' ) != "component") { ?>
<div id="content-header">
	<h2>Resource Picklist</h2>
	<p>A hand picked set of resources.</p>
</div><!-- / #content-header -->
<p>This is a hand picked list that either you created, or was shared with to you. To share this list, you can copy this link (below) and others will see your current selection. Any changes you make to the items in your list will not be shared with others untill you send them a new link.</p>
<strong>Permanent Link to this list:</strong>
<?php //$xhub->getCfg('hubLongURL']?>
<a target="_top" href="<?php echo "http://nees12.neeshub.org".'/resources/?task=viewpicklist&res_ids='.implode(",",$this->res_ids)?>"><?php echo 'nees12.neeshub.org'.'/resources/?task=viewpicklist&res_ids='.implode(",",$this->res_ids)?></a>
<?php //} ?>
<p>
<input type="button" ONCLICK="window.location.reload(true);" value=" Refresh the list "/>
</p>
		
	<!-- Include JQuery -->	
	<?php 
				if (JRequest::getVar( 'tmpl', '' ) == "component")
 					echo '		<script type="text/javascript" src="media/system/js/jquery-1.4.2.js"></script>
		<script type="text/javascript">var $jQ = jQuery.noConflict();</script> 	
		<script type="text/javascript" src="site/fancybox/jquery.fancybox-1.3.1.pack.js"></script>	
		<link rel="stylesheet" href="site/fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />';
	?>
			
	<!-- Include Needed Scripts -->
	<link type="text/css" href="/media/system/css/overcast/jquery-ui-1.8.5.custom.css" rel="stylesheet" />
	<link type="text/css" href="/plugins/tageditor/autocompleter.css" rel="stylesheet" />	
	<script type="text/javascript" src="/media/system/js/jquery-ui-1.8.5.custom.min.js"></script>
	<script src="/media/system/js/jquery.tools.min.js"></script>
	<script type="text/javascript" src="/components/com_resources/discover/js/jquery.tinysort.js"></script>
	<script type="text/javascript" src="/components/com_resources/discover/js/jquery.tagcloud.js"></script>
	
			
	<!-- Define some layout constants -->
	<?php $itemwidth = '300'; $itemheight='300';
	 //$itemwidth = '150'; $itemheight='200';
	 $sampleImagePath = '/site/images/highlights/ActiveDocumentDefault.jpg' ;
	 //$sampleImagePath = '/site/images/highlights/resource.gif';
	 $tooltipImage = '/site/images/highlights/moreInfo2.png' ;
	 $picklistImage = '/site/images/highlights/pickList2.png' ;
	 ?>
	<!-- Define some styles (move into stylesheet soon) -->
	<style>
	.dragitem { width: <?php echo $itemwidth;?>px; height:<?php echo $itemheight + 160;?>px; padding: 0.5em; float:left; margin: 10px 10px 10px 0; border: 5px solid lightgray; overflow:none; position: relative;}
	#droplocation { width: 250px;  height: 330px; padding: 0.5em; margin: 0px; right: 5px; position:fixed;}
	#dtagbrowser { width: 100%; float: left; margin-right:100px; overflow:auto;}
	#tagcloudsection { } 
	#tagcloudblockwrap { height:100px; overflow:auto; }
	#tagcloudblock {  }
	#filterdiv { width:70%; border: 1px black; height: 210px; }
	#clouddroplocation: {  }
	.bit-tag { float:left; height: 25px;}
	.addremovetag { cursor:pointer;}
	.resourcediscovertext { width:<?php echo ($itemwidth-10);?>px; height:<?php echo ($itemheight-10);?>px; border: solid 1px black; background-color: lightgrey; padding:0.5em;}
	.resourcediscoverimage { width:<?php echo ($itemwidth-10);?>px; height:<?php echo ($itemheight-10);?>px; border: solid 1px black; padding:0.5em; overflow:hidden;}
	.resourcediscovertooltip { position: absolute; left:<?php echo $itemwidth - 64;?>px; top:<?php echo $itemheight - 64;?>px;  z-index: 10;}
	.resourcepicklisticon { position: absolute; left:20px ;top:<?php echo $itemheight - 64;?>px ;  z-index: 10;}
	.resourcetaginfo {  height:50px; overflow-x:auto;}
	.resourcediscovertitle {text-decoration: underline; height:50px; list-style-type:none; }
	</style>
	
	<div id="clouddroplocation"></div>

<form action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" method="get" id="tagBrowserForm">

	<?php $completetaglist = array()?>
	<div class="" id="">
		<!-- <div id="slider"></div>  -->
		<div id="dtagbrowser">
		<ul id="sortablelist">
			<?php
			$counter = 0;
			foreach ($this->results as $resource) { ?>
				<div class="dragitem ui-state-default rdivitem<?php echo $counter;?>">
				<!-- heighht 180px @ normal -->
				<?php $sef = '/resources/';
				
				$sef .= ($resource->alias != NULL)? $resource->id: $resource->id ; 
				//$sef .= '/?task=preview&tmpl=component'; 
				$sectionclass = 'resinfo' . $counter;
				$key = false;//array_search($resource->id, $_SESSION['pickList']);
				
				$helper = new ResourcesHelper( $resource->id, $this->database );	
				$helper->getChildren( $resource->id, 0, '' );
				
				$thumb = null;
				$backup = null;
				//search for a thumbnail
				foreach ($helper->children as $child)
				{
					if ($child->type == 71 && $thumb == null) //thumbnail
						$thumb = $child;
					if ($child->type == 70 && $backup== null)	//image
						$backup = $child;
				}
				
				$noThumb = false;
				if (($thumb == null) & ($backup != null)) {
					$thumb = $backup;
					$noThumb = true;
				}
				
				//path to images for various types
				$typePath = '/site/images/types/' . $resource->type .'.jpg';		
				$imagepath = ($thumb != null)? '/site/resources/' . $thumb->path : $typePath;
				
				//show buttons not on a linked list
				$showButtons = ((JRequest::getVar( 'res_ids', '' ) == ""));
				
				
				?>
					
					
					
					<div ONCLICK="toggleitem('<?php echo $counter;?>');" style="display:none;" class="resourcediscovertext ritem<?php echo $counter;?>"><strong>Abstract</strong><p><?php echo substr($resource->introtext, 0, 400); ?><?php if (strlen($resource->introtext) > 400) echo " ..."?>&nbsp;<!--  <a class="riteminline" href="<?php echo '#'.$sectionclass;?>">(see preview...)</a>--></p>
					<div id="insertpoint<?php echo $counter;?>"></div></div>
					<div ONCLICK="toggleitem('<?php echo $counter;?>');" class="resourcediscoverimage ritem<?php echo $counter;?>"><img width="<?php echo $itemwidth-7;?>px"  src="<?php echo $imagepath;?>"></img></div>
					<div ONCLICK="toggleitem('<?php echo $counter;?>');" class="resourcediscovertooltip"><img src="<?php echo $tooltipImage;?>"></img></div>
					<div style="<?php if ($key == false ) echo 'display: none; ';?>" class="resourcepicklisticon ritem<?php echo $counter;?>"><img src="<?php echo $picklistImage;?>"></img></div>
					<li style="list-style-type:none;"><a class="itempreview" target="_top" href="<?php echo $sef;?>"><h4 class="resourcediscovertitle" ><?php echo substr($resource->title, 0, 100)." ..."; ?></h4></a></li>
					<input type="button" ONCLICK="leaveReview('<?php echo $resource->id;?>');" value="Review"/>
					
					<?php if ($showButtons) {?>
					<input id="pickbtn<?php echo $counter;?>" type="button" ONCLICK="pickList('<?php echo $counter;?>', '<?php echo $resource->id;?>');" value="<?php if ($key == false ) echo 'Add to Pick List '; else echo 'Remove from Pick List';?>" />
					<?php } ?>
					
					<?php if ($backup != null) {?>
					<input type="button" ONCLICK="fullresolution('<?php echo $counter;?>');" value="Popout"/>
					<div style="display:none;"><a id="fullres<?php echo $counter;?>" class="previewfullresolutionimage" href="<?php echo '/site/resources/'.$backup->path; ?>">hidden</a></div>
					<?php } //end backup != null?>
					<div class="resourcetaginfo">
					<?php 
							$tgs = $this->tagcloud->get_tags_on_object($resource->id, 0, 0, 0, 0, 1);
							if ( $tgs != '') {
								echo '<label>';
								echo '<ul class="textboxlist-holder">';
								foreach ($tgs as $tg ) {
									echo '<li class="bit-box">'.$tg['raw_tag'];
									$needle = ''.$tg['raw_tag'].'';
										//if (strpos($with,$needle) == false)
										//echo ' <a class="addremovetag" title="Add '.$tg['raw_tag'].' to filters"ONCLICK="addtag(\''.$tg['raw_tag'].'\')">(+)</a>';
									echo '</li>';
									
									if (isset ($completetaglist[$tg['raw_tag']])) {
										$completetaglist[$tg['raw_tag']]++;
									}
									else
										$completetaglist[$tg['raw_tag']] = 1;
										}
								echo '</ul>';
								echo '</label>';
							}	
					?>
					</div>
					<div style="display:none;"><div id="<?php echo $sectionclass;?>" style="width:350px;"><a class="itempreview" href="<?php echo $sef;?>"><?php echo $resource->title;?></a><br></br><strong>Full Text</strong><p><?php echo $resource->fulltext;?></p></div></div>
				</div>
			<?php $counter++; } ?>
			</ul>
		</div><!-- / #tagbrowser -->
		
		
		<div class="clear"></div>


	</div><!-- / .main section -->
</form>
<div id="tagcloudsection">
<label>
<?php echo 'Tags appearing on this page';?>

</label>
<div id="tagcloudblockwrap">
<ul id="tagcloudblock" class="textboxlist-holder">
<?php
$keys = array_keys($completetaglist);
foreach ($keys as $atagfromlist) {
	
	$needle = ''.$atagfromlist.'';
		echo '<li value='.$completetaglist[$atagfromlist];
		echo ' class="bit-box bit-tag"';
		echo ' title="'.$atagfromlist.'">'.$atagfromlist.' ('.$completetaglist[$atagfromlist].')</li>';
}?>
</ul>
</div>

</div>
