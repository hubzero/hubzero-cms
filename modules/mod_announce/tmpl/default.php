<div class="announcements">
<?php 
$announcements = $this->getModel('announcements');
$last_category = null;
foreach ($announcements->get_published() as $ann) {
?>

<?php if ($ann['category'] != $last_category) : 
		if (!is_null($last_category)) echo '</ul>'; 
?>
	<h4><?php echo $ann['category']; ?></h4>
	<ul class="announcements">
<?php endif; ?>

	<li>
		<a href="<?php echo $ann['link']; ?>"><?php echo $ann['title']; ?></a><?php echo $this->get_new_indicator($ann['active_date']); ?>
	</li>

<?php 
	$last_category = $ann['category'];
} 
?>
</ul>
<p class="more"><a href="/announce/">Announcement Archive &rsaquo;</a></p>
</div>

