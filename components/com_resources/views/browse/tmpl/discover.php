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
				if (JRequest::getVar( 'tmpl', '' ) != "component") { ?>
<div id="content-header">
	<h2>Resource Discovery</h2>
	<p>Use Tags to find new content, and create a <em>Pick List</em> of your favourites. Use the (+) and (X) buttons on each tag to add and remove it from the search list.</p><p><a href="/resources/?task=viewpicklist<?php 
				if (JRequest::getVar( 'tmpl', '' ) == "component") { echo '&tmpl=component'; }?>">&rarr; View your Pick List Here &larr;</a></p>
</div><!-- / #content-header -->
<?php } ?>
		
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
	<script type="text/javascript" src="/components/com_resources/discover/js/jquery.tinysort.js"></script>
	<script type="text/javascript" src="/components/com_resources/discover/js/jquery.tagcloud.js"></script>
	
			
	<!-- Define some layout constants -->
	<?php
	$page_start = JRequest::getInt( 'st', 0 );
	 $page_limit = 5;
	 $itemwidth = '300'; $itemheight='300';
	 //$itemwidth = '150'; $itemheight='200';
	 $sampleImagePath = '/site/images/highlights/ActiveDocumentDefault.jpg' ;
	 //$sampleImagePath = '/site/images/highlights/resource.gif';
	 $tooltipImage = '/site/images/highlights/moreInfo2.png' ;
	 $picklistImage = '/site/images/highlights/pickList2.png' ;
	 ?>
	 <?php 
		$with = $this->nonfilteredtags;//stripslashes(JRequest::getVar( 'with', ''));
	  ?>
	<!-- Define some styles (move into stylesheet soon) -->
	<style>
	.dragitem { width: <?php echo $itemwidth;?>px; height:<?php echo $itemheight + 160;?>px; padding: 0.5em; float:left; margin: 10px 10px 10px 0; overflow:none; position: relative;}
	#droplocation { width: 250px;  height: 330px; padding: 0.5em; margin: 0px; right: 5px; position:fixed;}
	#dtagbrowser { width: 100%; float: left; margin-right:100px; overflow:auto;}
	#tagcloudsection { } 
	#tagcloudblockwrap { height:100px; overflow:auto; }
	#tagcloudblock {  }
	#filterdiv { width:70%; border: 1px black; height: 210px; }
	#clouddroplocation: {  }
	.bit-tag { float:left; height: 25px;}
	.addremovetag { cursor:pointer;}
	.outpicklist { border: 5px solid lightgray;  }
	.inpicklist { border: 5px solid lightgreen;  }
	.resourcediscovertext { width:<?php echo ($itemwidth-10);?>px; height:<?php echo ($itemheight-10);?>px; border: solid 1px black; background-color: lightgrey; padding:0.5em;}
	.resourcediscoverimage { width:<?php echo ($itemwidth-10);?>px; height:<?php echo ($itemheight-10);?>px; border: solid 1px black; padding:0.5em; vertical-align:middle; overflow:hidden;}
	.resourcediscovertooltip { position: absolute; left:<?php echo $itemwidth - 64;?>px; top:<?php echo $itemheight - 64;?>px;  z-index: 10;}
	.resourcepicklisticon { position: absolute; left:20px ;top:<?php echo $itemheight - 64;?>px ;  z-index: 10;}
	.resourcetaginfo {  height:50px; overflow-x:auto;}
	.resourcediscovertitle {text-decoration: underline; height:50px; list-style-type:none; }
	</style>
	
	<!-- Functions used to add/remove tags from lightbox and fields on search -->
	<script type="text/javascript">
		window.addEvent('domready', HUB.DiscoverBrowser.initialize);

		function removetag(tag)
		{
			//alert(tag);
			var tagregex = '"'+tag+'"';
			var field = document.getElementById("withinput");
			field.value = field.value.replace(tagregex, '');
			field.value = field.value.replace(',,', ',');
			field.value = field.value.replace(/,$/g,'');
			field.value = field.value.replace(/^,/g,'');
			document.tagform.submit();
		}
		function addtag(arg)
		{
			
			var field = document.getElementById("withinput");
			if (field.value.indexOf('"'+arg+'"') == -1) {
				if (field.value != '') {
					field.value +=  ',';
				}
				field.value +=  '\"' + arg + '\"';
				document.tagform.submit();
			}
		}
		function appendwith()
		{
			var field = document.getElementById("withinput");
			var t = document.getElementById("txttoadd");
			if (field.value.indexOf('"'+t.value+'"') == -1) {
				if (field.value != '') {
					field.value +=  ',';
				}
				field.value +=  '\"' + t.value + '\"';
				document.tagform.submit();
			}
		}
		function clearfilters()
		{
			var field = document.getElementById("withinput");
			field.value = '';
			document.tagform.submit();
		}
		</script>
	

<div id="filterdiv" >
		<form id="hubForm" name="tagform" action="/resources/">
			<input type="hidden" name="task" value="<?php echo $this->task;?>"/>
			<input type="hidden" name="type" value="<?php echo $this->type;?>"/>
			<input type="hidden" name="option" value="<?php echo $this->option;?>"/>
			<input id="st" name="st" type="hidden" value="<?php echo $page_start?>"/>
			<?php 
				if (JRequest::getVar( 'tmpl', '' ) == "component") echo '<input type="hidden" name="tmpl" value="'.JRequest::getVar( 'tmpl', '' ).'"/>';?>
			<?php 
							
							echo '<script type="text/javascript">'."\n";
							echo 'var wargs = [';
							echo $with;
							echo ']';
							echo '</script>'."\n";
							
							if ( $with != '') {
								$withs = preg_split("/,/", preg_replace('/["]/', " ", $with));
								echo '<label> Filtering by Resources Tagged As:';
								echo '<ul class="textboxlist-holder">';
								
								foreach ($withs as $warg ) {
									$flttag = $warg;
									$link = '';
									if (preg_match('/~/',$warg) > 0)
									{
										$flttag = preg_replace('/~/','',$flttag);
									}
									else
										$link = '<a class="addremovetag" title="Remove '.trim($flttag).' from filters"ONCLICK="removetag(\''.trim($flttag).'\')">(X)</a>';
									echo '<li class="bit-box">'.$flttag.$link.'</li>';
								}
								echo '</ul>';
								echo '</label>';
							}
							echo '<input id="withinput" type="hidden" name="with" value=\''.$with.'\' />'."\n";
?>
	<div id="clouddroplocation"></div>
	
				
		<!-- 
		<label> Add another Filter:
		<input type="text" id="txttoadd" value="type_here"/><input type="button" value="Add Search Filter ->" ONCLICK="appendwith();"/>
		 <input type="button" value="Clear Filters ->" ONCLICK="clearfilters();"/> 
		</label>
		 -->
	</form>
</div> <!--  filter div  -->
<!-- 
		<div id="droplocation" class="ui-widget-header">
		<input type="button" ONCLICK="toggledisplay();" value="Toggle All">
		<h3>Items of Interest</h3>
			<ul id="sortablelist">
				<li class="placeholder">Drag Item Titles Here</li>
			</ul>
		</div>
		 -->
<script type="text/javascript">
	function increasepage()
	{
		var field = document.getElementById("st");
		if (st.value < (<?php echo count($this->results) - $page_limit;?>))
		{
			st.value += <?php echo $page_limit;?>;
			document.tagform.submit();
		}
	}

	function decreasepage()
	{
		var field = document.getElementById("st");
		if (st.value >= <?php echo $page_limit;?>)
		{
			st.value -= <?php echo $page_limit;?>;
			document.tagform.submit();
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" method="get" id="tagBrowserForm">
	<div id="content-header-extra">
		<div class="explaination">	
		<?php 
				if (JRequest::getVar( 'tmpl', '' ) != "component") {
		?>	 
				<p id="primary-document">
				<a id="resquicksubmit" href="/contribute/?task=quickcontribute&tmpl=component">Add to this collection</a>
				</p>
				
				When you <em>add to this collection</em>, you can begin a new contribution process, a simple 5 step solution to upload your own work. Once approved, your work will appear with the same <span class="textboxlist-holder"><span class=" bit-box">tags</span></span> as you have selected.
		<?php 
				} 
				else {?>
				<br/>
				<br/>
				<p><a href="/resources/?task=viewpicklist<?php 
				if (JRequest::getVar( 'tmpl', '' ) == "component") { echo '&tmpl=component'; }?>">&rarr; View your Pick List Here &larr;</a></p>
		<?php }?>	
		<h4><?php echo count($this->results) ?> Results in this collection</h4>
		 <!-- 
		<em><?php echo $page_limit?> results per page</em>
		<?php if ($page_start < (count($this->results)- $page_limit)) { ?>
		<input type="button" value="Next page &rarr;" ONCLICK="increasepage();"/>
		<?php } if ($page_start >= $page_limit) {?>
		<input type="button" value="&larr; Prev page" ONCLICK="decreasepage();"/>
		<?php }?>
		 -->
		
		</div>
		<p></p>
		<?php 
				if (JRequest::getVar( 'tmpl', '' ) != "component") { ?>
		<fieldset>
		<strong>View Other Resource Types:</strong>
			<label>
			View Other Resource Types
				<span><?php echo JText::_('COM_RESOURCES_TYPE'); ?>:</span> 
				<select name="type">
				<?php 
				foreach ($this->types as $type) 
				{
				?>
					<option value="<?php echo $type->title; ?>"<?php if ($type->id == $this->filters['type']) { echo ' selected="selected"'; } ?>><?php echo $type->type; ?></option>
				<?php 
				} 
				?>
				</select>
			</label>
			<input type="submit" value="<?php echo JText::_('COM_RESOURCES_GO'); ?>"/>
			<input type="hidden" name="task" value="discover" />
						<p>
			<a href="<?php echo '/resources/'.$this->type; ?>"class="addremovetag" title="Click to Return - Your pick list will not be lost unless you log out">&larr; Return to Tri-fold Resource View</a>
			</p>
		</fieldset>
		<?php }?>
	</div><!-- / #content-header-extra -->
	<?php $completetaglist = array()?>
	<div class="" id="">
		<!-- <div id="slider"></div>  -->
		<div id="dtagbrowser">
		<ul id="sortablelist">
			<?php
			$counter = -1;
			foreach ($this->results as $resource) { 
				$counter++; 
				  ?>
				 
				<?php
				//pagination
				//echo $counter;
				//echo $page_start;
				//echo $page_limit;
				//if ($counter < $page_start) continue; if ($counter >= ($page_start+$page_limit)) break;?>
				
				
				<!-- heighht 180px @ normal -->
				<?php $sef = '/resources/';
				
				$sef .= ($resource->alias != NULL)? $resource->id: $resource->id ; 
				//$sef .= '/?task=preview&tmpl=component'; 
				$sectionclass = 'resinfo' . $counter;
				$key = array_search($resource->id, $_SESSION['pickList']);
				$helper = new ResourcesHelper( $resource->id, $this->database );	
				$helper->getChildren( $resource->id, 0, '' );
				
				$thumb = null;
				$backup = null;
				//search for a thumbnail
				foreach ($helper->children as $child)
				{
					if ($child->type == 71) //thumbnail
						$thumb = $child;
					if ($child->type == 70)	//image
						$backup = $child;
				}
				$noThumb = false;
				if (($thumb == null) & ($backup != null)) {
					$thumb = $backup;
					$noThumb = true;
				}
				
				$typePath = '/site/images/types/' . $resource->type .'.jpg';		
				$imagepath = ($thumb != null)? '/site/resources/' . $thumb->path : $typePath;
				
				//echo print_r($helper->children);
				?>	
				<div class="dragitem ui-state-default rdivitem<?php echo $counter;?> <?php if ($key==false) echo 'outpicklist'; else echo 'inpicklist';?>">
				<?php //echo ResourcesHtml::primary_child($this->option, $resource, $helper->firstChild);?>
				
					<div ONCLICK="toggleitem('<?php echo $counter;?>');" style="display:none;" class="resourcediscovertext ritem<?php echo $counter;?>"><strong>Abstract</strong><p><?php echo substr($resource->introtext, 0, 400); ?><?php if (strlen($resource->introtext) > 400) echo " ..."?>&nbsp;<!--  <a class="riteminline" href="<?php echo '#'.$sectionclass;?>">(see preview...)</a>--></p>
					<div id="insertpoint<?php echo $counter;?>"></div></div>
					<div ONCLICK="toggleitem('<?php echo $counter;?>');" class="resourcediscoverimage ritem<?php echo $counter;?>"><img width="<?php echo $itemwidth-7;?>px"  src="<?php echo $imagepath;?>"></img></div>
					<div ONCLICK="toggleitem('<?php echo $counter;?>');" class="resourcediscovertooltip"><img src="<?php echo $tooltipImage;?>"></img></div>
					<div style="<?php if ($key == false ) echo 'display: none; ';?>" class="resourcepicklisticon ritem<?php echo $counter;?>"><img src="<?php echo $picklistImage;?>"></img></div>
					<li style="list-style-type:none;"><a class="itempreview" target="_top" href="<?php echo $sef;?>"><h4 class="resourcediscovertitle" ><?php echo substr($resource->title, 0, 100)." ..."; ?></h4></a></li>
					<input type="button" ONCLICK="leaveReview('<?php echo $resource->id;?>');" value="Review"/>
					<input id="pickbtn<?php echo $counter;?>" type="button" ONCLICK="pickList('<?php echo $counter;?>', '<?php echo $resource->id;?>');" value="<?php if ($key == false ) echo 'Add to Pick List '; else echo 'Remove from Pick List';?>" />
					
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
										if (strpos($with,$needle) == false)
										echo ' <a class="addremovetag" title="Add '.$tg['raw_tag'].' to filters"ONCLICK="addtag(\''.$tg['raw_tag'].'\')">(+)</a>';
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
					
					<div style="display:none;"><div id="<?php echo $sectionclass;?>" style="width:350px;"><a class="itempreview" href="<?php echo $sef;?>"><?php echo $resource->title;?></a><br></br><strong>Full Text</strong><p><?php echo stripslashes($resource->fulltext);?></p></div></div>
				</div>
			<?php } ?>
			</ul>
		</div><!-- / #tagbrowser -->
		
		
		<div class="clear"></div>


	</div><!-- / .main section -->
</form>
<div id="tagcloudsection">
<label>
<?php if ($with != '') echo 'Other tags appearing on this page'; else echo 'Tags appearing on this page';?>

</label>
<div id="tagcloudblockwrap">
<ul id="tagcloudblock" class="textboxlist-holder">
<?php
$keys = array_keys($completetaglist);
foreach ($keys as $atagfromlist) {
	
	$needle = ''.$atagfromlist.'';
	if (strpos($with,$needle) == false)
	 {
		echo '<li value='.$completetaglist[$atagfromlist];
		echo ' class="bit-box bit-tag"';
		echo ' title="'.$atagfromlist.'">'.$atagfromlist.' ('.$completetaglist[$atagfromlist].') <a class="addremovetag" title="Add '.$atagfromlist.' to filters" ONCLICK="addtag(\''.$atagfromlist.'\')">(+)</a> </li>';
		}
}?>
</ul>
</div>

</div>
<?php 
				if (JRequest::getVar( 'tmpl', '' ) == "component"){ ?>
<script type="text/javascript">
	 
      // Tell the parent iframe what height the iframe needs to be
      function parentIframeResize()
      {
         var height = document.body.scrollHeight;//getParam('height');
         // This works as our parent's parent is on our domain..
         parent.parent.resizeIframe(height);
      }

      window.onload = parentIframeResize;
</script>
 <?php } ?>
 
 <script type="text/javascript">
 
 
 
 </script>
 
