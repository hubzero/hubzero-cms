<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 
?>

<div id="youtube_feed_<?php echo $youtube->id; ?>" class="youtube_<?php echo $params->get('layout')." ".$params->get('moduleclass_sfx'); ?>">
	<?php if($youtube->lazy) { ?>
		Loading Youtube Feed....
		<noscript><p class=\"error\">Javascript is required to view the Youtube feed.</p></noscript>
	<?php } else { ?>
		<?php echo $youtube->html; ?>
	<?php } ?>
</div>