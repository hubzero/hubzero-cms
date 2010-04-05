<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div class="youtubefeed<?php echo $params->get( 'moduleclass_sfx'); ?>">
<?php
if( $feed != false )
{
	//image handling
	//$iUrl 	= isset($feed->image->url)   ? $feed->image->url   : null;
	$iTitle = isset($feed->image->title) ? $feed->image->title : null;
	
	// Get layout
	$layout = $params->get('layout') ? $params->get('layout') : 'vertical';
	
	// Push some CSS to the template
	ximport('xdocument');
	XDocument::addModuleStylesheet('mod_feed_youtube');
	
	$youtube_ima  = $params->get('imagepath');
	if (substr($youtube_ima, 0, 1) != DS) {
		$youtube_ima = DS.$youtube_ima;
	}
	if (substr($youtube_ima, -1, 1) == DS) {
		$youtube_ima = substr($youtube_ima, 0, (strlen($youtube_ima) - 1));
	}
	if (!is_file(JPATH_ROOT.$youtube_ima)) {
		$youtube_ima = '';
	}
	
	// Link to more videos
	$morelink =  $params->get('moreurl') ? str_replace( '&', '&amp', $params->get('moreurl') ) : str_replace( '&', '&amp', $feed->link );
			
	?>
	<?php
	// feed image & title
	if ((!is_null( $feed->title ) or $params->get('feedtitle', '')) && $params->get('rsstitle', 1)) {
	?>
		<h3 class="feed_title">        
        <?php 
		// feed title
		if ((!is_null( $feed->title ) or $params->get('feedtitle', '')) && $params->get('rsstitle', 1)) {
		$feedtitle = $params->get('feedtitle') ? $params->get('feedtitle') : $feed->title;
		?>						
					<a href="<?php echo $morelink; ?>" rel="external"><?php echo $feedtitle; ?></a>				
		<?php
		} if ($params->get('rssimage', 1) && $youtube_ima) { ?>
         <a href="<?php echo str_replace( '&', '&amp', $feed->link ); ?>" rel="external"><img src="<?php echo $youtube_ima; ?>" alt="<?php echo @$iTitle; ?>"/></a>
        <?php } ?>
        </h3>
	<?php
	}
	if ((!is_null( $feed->description ) or $params->get('feeddesc', '')) && $params->get('rssdesc', 0)) {
		$feeddesc = $params->get('feeddesc') ? $params->get('feeddesc') : $feed->description;
	?>
    	<p><?php echo $feeddesc; ?></p>
    <?php
	}

	$actualItems = count( $feed->items );
	$setItems    = $params->get('rssitems', 5);

	if ($setItems > $actualItems) {
		$totalItems = $actualItems;
	} else {
		$totalItems = $setItems;
	}
	?>
			<ul class="layout_<?php echo $layout; ?>">
			<?php
			$words = $params->def('word_count', 0);
			for ($j = 0; $j < $totalItems; $j ++)
			{
				$currItem = & $feed->items[$j];
				// item title
				?>
				<li>
				<?php
				if ( !is_null( $currItem->get_link() ) ) {
		
					// get video id
					$match = array();
					$vid = 0;
					preg_match("/youtube\.com\/watch\?v=(.*)/", $currItem->get_link() , $match);
					if(count($match) > 1 && strlen($match[1]) > 11) {
						$vid = substr($match[1], 0, 11); 	
					}
					
					// copy thumbnail to server
					if($vid) {
						$img_src = 'http://img.youtube.com/vi/'.$vid.'/default.jpg';
						$thumb  = $params->get('webpath');
						if (substr($thumb, 0, 1) != DS) {
							$thumb = DS.$thumb;
						}
						if (substr($thumb, -1, 1) == DS) {
							$thumb = substr($thumb, 0, (strlen($thumb) - 1));
						}
						$thumb .= DS.$vid.'.jpg';
						
						if (!is_file(JPATH_ROOT.$thumb)) {
							copy($img_src, JPATH_ROOT.$thumb);
						}
						if (!is_file(JPATH_ROOT.$thumb)) {
							$vid = 0;
						}
					}
					
					// display with thumbnails
					if($vid) { ?>
						<a href="<?php echo $currItem->get_link(); ?>" rel="external">
                        	<img src="<?php echo $thumb; ?>" alt="" />
                        </a>
                        <a href="<?php echo $currItem->get_link(); ?>" rel="external">
                        	<span><?php echo $currItem->get_title(); ?></span>
                        </a>
					<?php
					} 
					else {				
				?>
					<a href="<?php echo $currItem->get_link(); ?>" rel="external">
					<span><?php echo $currItem->get_title(); ?></span></a>
				<?php
					} // end if no vid, simple display
				}
				?>
				</li>
				<?php
			}
			?>
			</ul>
             <?php if ($layout == 'horizontal') { ?>
            <div class="clear"></div>
            <?php } ?>
            <?php if (!is_null( $params->get('moreurl'))  && $params->get('showmorelink', 0)) { ?>
             <p class="more"><a href="<?php echo $morelink; ?>" rel="external"><?php echo JText::_('More videos'); ?> &rsaquo;</a></p>
            <?php
			}
			?>
<?php } ?>
</div>
