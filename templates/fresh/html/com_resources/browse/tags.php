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
?>

<script src="site/flowplayer/flowplayer-3.2.4.min.js"></script>
<script src="site/flowplayer/flowplayer.playlist-3.0.8.js"></script>
<script src="/components/com_resources/introduction2.js"></script>

<!-- <div id="content-header">  -->
<div id="hubfancy-content-header">
	<h2 id="hubfancy-head"><?php echo $this->categoryTitle  ?></h2>
	<p id="hubfancy-sub"><?php echo $this->categoryDescription  ?></p>
<!--  </div>  -->	
</div><!-- / #content-header -->

	<div id="content-header-extra">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" method="get" id="tagBrowserForm" name="mainform">
		<fieldset>
		<strong>View Other Resource Types:</strong>
			<label>
				<span>spaceneededhere<?php echo JText::_('COM_RESOURCES_TYPE'); ?>:</span> 
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
			<input type="hidden" name="task" value="browsetags" />
			
			<p>
			<a href="<?php echo '/resources/'.$this->type.'/?task=discover'; ?>"class="addremovetag" title="Click to go to Resource Discovery">Try the new Resource Discovery</a>
			</p>				
			
		</fieldset>
</form>
	<?php //$browser = get_browser();
		$using_ie67 = false;
		/*
		$u_agent = $_SERVER['HTTP_USER_AGENT']; 
		//if browser is IE6/7 disable title search
	    if(preg_match('/MSIE [6,7]/i',$u_agent)) 
	    { 
	        $using_ie67 = True; 
	    } 
	    */	
	
	if (!$using_ie67) { ?>
	<form method="get" action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" id="hubfancy-searchform" name="srcfrm">
	<input type="hidden" name="task" value="browsetags" />
	<fieldset id="hubfancy-titlesearch">
	<input type="search" name="srcterms" id="searchword" size="20" value="" style="width: 185px; " placeholder="Title Search" autosave="bsn_search" results="5">
	<p id="hubfancy-titlecaption"><em>Type a title keyword then press [Enter] to quickly search resources</em></p>
	<?php if (isset($this->srcterms)) { ?>
			<div>Currently searching for: '<?php echo $this->srcterms;?>'</div>
			<input type="button" value="Start Over" ONCLICK="document.srcfrm.submit();"/>
			<?php if (isset($this->designator)){  ?>
			<div> and </div>
			
			<?php } ?>
	<?php } if (isset($this->designator)){  ?>
			<label>Only showing Resources tagged: '<?php echo $this->designator?>'</label>
	<?php } ?>
	<?php if (isset($this->designator)) { ?>
			<input type="hidden" name="designator" id="designator" value="<?php echo $this->designator; ?>" />
	<?php } ?>
	<input type="hidden" name="type" value="<?php echo $this->type;?>"/>
	</fieldset>
	</form>		
	<?php } else {?>
	<p><em>Title Search is not available for your browser</em></p>
	<?php } ?>
	</div><!-- / #content-header-extra -->
	<div id="hubfancy-browserinfo">
		<p>Select a <strong>tag in the first column</strong>, and then click on a <strong>resource in the second column</strong> to see its <strong>info in the third column</strong></p>
		</div>
	<div class="main section" id="browse-resources">
		<div id="tagbrowser">
			<p class="info"><?php echo JText::_('COM_RESOURCES_TAGBROWSER_EXPLANATION'); ?></p>
			<div id="level-1">
				<h3><?php echo JText::_('COM_RESOURCES_TAG'); ?></h3>
				<ul>
					<li id="level-1-loading"></li>
				</ul>
			</div><!-- / #level-1 -->
			<div id="level-2">
				<h3><?php echo JText::_('COM_RESOURCES'); ?> <select name="sortby" id="sortby"></select></h3>
				<ul>
					<li id="level-2-loading"></li>
				</ul>
			</div><!-- / #level-2 -->
			
			<div id="level-3" style="display:none;">
				<h3><?php echo JText::_('COM_RESOURCES_INFO'); ?></h3>
				<ul>
					<li><?php echo JText::_('COM_RESOURCES_TAGBROWSER_COL_EXPLANATION'); ?></li>
				</ul>
			</div><!-- / #level-3 -->
			<div>
			<?php //scan directory for slideshow files
					$dir = '/www/neeshub/site/resources/intro/'.$this->type.'/';
					$usableFiles = array();
					if (is_dir ( $dir )) { 
						$files = scandir($dir);
						
						foreach($files as $key => $value){
							if(($value != ".") && ($value != "..") && ($value != ".svn") ){ 
					              $usableFiles[] = '/site/resources/intro/'.$this->type.'/'.$value; 
					         }
							}
						
							//randomize array
						shuffle($usableFiles);
					}
					?>
			</div>
			<div id="hubfancy-introvideo" >
				<h3>Introduction</h3>
				<div id="hubfancy-player" ></div>
				<script type="text/javascript">
				//pass playlist from php to javascript so they can be played
				var ufiles = new Array("<?php echo implode('","',$usableFiles)?>");
				var playList = new Array();
				for (ti=0;ti<ufiles.length;ti++)
				{
					//add clip to playlist and set its duration
				    playList.push({url: ufiles[ti], duration: 3});
				}
				//add a nees logo to the end and make it return to the start (loop)
				playList.push({url: "/site/resources/intro/gen/end.jpg", duration: 2,
					   onBeforeFinish: function ()  { return false; }});
				//calls flowplayer and sets playing parameters
				HUB.ResourceIntroVideo.clip(playList);
				</script>
			</div>
			<div id="hubfancy-level3" style="display:none;">
			<h3>Info</h3>
			<div id="hubfancy-level3content" >
			<p>  &larr; Select a resource to see an abstract, citations, usage and more.</p>
			</div>
			</div>
			<input type="hidden" name="pretype" id="pretype" value="<?php echo $this->filters['type']; ?>" />
			<input type="hidden" name="id" id="id" value="" />
			<input type="hidden" name="preinput" id="preinput" value="<?php echo $this->tag; ?>" />
			<input type="hidden" name="preinput2" id="preinput2" value="<?php echo $this->tag2; ?>" />
			<?php if (isset($this->srcterms)) { ?>
			<input type="hidden" name="srcterms" id="srcterms" value="<?php echo $this->srcterms; ?>" />
			<?php } ?>
			<?php if (isset($this->designator)) { ?>
			<input type="hidden" name="designator" id="designator" value="<?php echo $this->designator; ?>" />
			<?php } ?>
			<div class="clear"></div>
		</div><!-- / #tagbrowser -->
		
		<p id="viewalltools"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&type='.$this->filters['type']); ?>"><?php echo JText::_('COM_RESOURCES_VIEW_MORE'); ?></a></p>
		<div class="clear"></div>
		
<?php
$database =& JFactory::getDBO();

if ($this->supportedtag) {
	include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php');
	
	$tag = new TagsTag( $database );
	$tag->loadTag($this->supportedtag);

	$sl = $this->config->get('supportedlink');
	if ($sl) {
		$link = $sl;
	} else {
		$link = JRoute::_('index.php?option=com_tags&tag='.$tag->tag);
	}
?>
		<p class="supported"><?php echo JText::_('COM_RESOURCES_WHATS_THIS'); ?> <a href="<?php echo $link; ?>"><?php echo JText::sprintf('COM_RESOURCES_ABOUT_TAG', $tag->raw_tag); ?></a></p>
<?php
}
?>

<?php
if ($this->results) {
?>
		<h3><?php echo JText::_('COM_RESOURCES_TOP_RATED'); ?></h3>
		<a id="hubfancy-seeresults" ONCLICK="HUB.BrowseEnhancement.showResults();">Show Results &darr;</a>
		<a id="hubfancy-hideresults" ONCLICK="HUB.BrowseEnhancement.hideResults();">Hide Results &uarr;</a>
		<div id="hubfancy-topratedresults">
		<p><?php echo JText::_('COM_RESOURCES_TOP_RATED_EXPLANATION'); ?></p>	
		<!-- 
		<div class="aside">
			<p><?php echo JText::_('COM_RESOURCES_TOP_RATED_EXPLANATION'); ?></p>
		</div>--><!-- / .aside -->  
		
		<div class="subject">
			<?php echo ResourcesHtml::writeResults( $database, $this->results, $this->authorized ); ?>
		</div><!-- / .subject -->
		</div>
<?php
}
?>
	</div><!-- / .main section -->