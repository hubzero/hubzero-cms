<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
$rssrtl = 'ltr';

?>
<p class="sublinks"><a href="<?php echo str_replace( '&', '&amp', $feed->link ); ?>" rel="external"><?php echo JText::_('View all'); ?> &rsaquo;</a></p>
<div class="<?php echo $params->get('moduleclass_sfx'); ?>">
<?php
if( $feed != false )
{
	//image handling
	$iUrl 	= isset($feed->image->url)   ? $feed->image->url   : null;
	$iTitle = isset($feed->image->title) ? $feed->image->title : null;

	// feed description
	if (!is_null( $feed->title ) && $params->get('rsstitle', 1)) {
		?>
		<p><strong>
					<a href="<?php echo str_replace( '&', '&amp', $feed->link ); ?>" rel="external"><?php echo $feed->title; ?></a>
		</strong></p>
		<?php
	}
		
	// feed description
	if ($params->get('rssdesc', 1)) {
	?>
		<p><?php echo $feed->description; ?></p>
	<?php
	}

	// feed image
	if ($params->get('rssimage', 1) && $iUrl) {
	?>
			<p><image src="<?php echo $iUrl; ?>" alt="<?php echo @$iTitle; ?>"/></p>
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
			<ul class="<?php echo $params->get( 'moduleclass_sfx'); ?>"  >
			<?php
			
			$words = $params->def('word_count', 0);
			for ($j = 0; $j < $totalItems; $j ++)
			{
				$currItem =& $feed->items[$j];
				// item title
				?>
				<li>
				<?php
				if ( !is_null( $currItem->get_link() ) ) {
				?>
					<p class="news-date"><span class="month"><?php echo $currItem->get_date('M'); ?></span> <span class="day"><?php echo $currItem->get_date('d'); ?></span></p>
					<p class="news-title"><a href="<?php echo $currItem->get_link(); ?>" rel="external"><?php echo $currItem->get_title(); ?></a></p>
				<?php
				}

				// item description
				if ($params->get('rssitemdesc', 1))
				{
					// item description
					$text = $currItem->get_description();
					$text = str_replace('&apos;', "'", $text);
					// extra clean-up
					$text = preg_replace( '/align="left" hspace="20"/', ' ', $text );
					$text = preg_replace( '/<br>/', '<br /> ', $text );
								
					// get image name
					$match = array();
					$ima = '';
					$thumb = '';
					$remote_path = 'http://earthquake.usgs.gov/images/globes';
					$path  = '/site/feed';
					
					preg_match("/(?P<foo>globes\/)(.*)(?P<bar>\.jpg)/", $text , $match);

					if(count($match) > 2) {
						$ima = $match[2];
					}
					
					// copy image to server
					if($ima) {
						$img_src = $remote_path.DS.$ima.'.jpg';
						$thumb 	 = $path.DS.$ima.'.jpg';
						
						if (!is_file(JPATH_ROOT.$thumb)) {
							copy($img_src, JPATH_ROOT.$thumb);
						}
						if (!is_file(JPATH_ROOT.$thumb)) {
							$thumb = '';
						}
					}
					$text = $thumb ? preg_replace( '/http:\/\/earthquake.usgs.gov\/images\/globes/', $path, $text ) : $text;
			
					// word limit check
					if ($words) 
					{
						$texts = explode(' ', $text);
						$count = count($texts);
						if ($count > $words) 
						{
							$text = '';
							for ($i = 0; $i < $words; $i ++) {
								$text .= ' '.$texts[$i];
							}
							$text .= '...';
						}
					}
					?>
					<div class="news-description"><?php echo $text; ?></div>
					<?php
				}
				?>
				</li>
				<?php
			}
			?>
			</ul>
<?php } ?>
</div>