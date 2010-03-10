<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
$rssrtl = 'ltr';

?>
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
					<p class="news-description"><?php echo $text; ?></p>
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