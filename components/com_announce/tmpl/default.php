<?php $announcements = $this->getModel('announcements'); ?>
<h2>Archived Announcements</h2>
<form action="" method="post">
	<p>
		<input type="text" name="terms" value="<?php echo str_replace('"', '&quot;', $announcements->get_search_terms()); ?>" /><input type="submit" value="Search" />
	</p>
	<input type="hidden" name="task" value="search" />
</form>
<div class="announcements">
<?php 
$last_category = null;
$archived = $announcements->get_archived();
if (count($archived))
{
	foreach ($announcements->get_archived() as $ann) {
?>

<?php if ($ann['category'] != $last_category) : 
		if (!is_null($last_category)) echo '</ul>'; 
?>
	<h4><?php echo $ann['category']; ?></h4>
	<ul class="announcements">
<?php endif; ?>

	<li>
		<a href="<?php echo $ann['link']; ?>"><?php echo $ann['title']; ?></a><?php echo ' &ndash; '. date('Y/m/d', strtotime($ann['active_date'])); ?>
	</li>

<?php 
		$last_category = $ann['category'];
	} 
}
else
{
?>
	<li>No archived announcements found</li>
<?php
}
?>
</ul>
</div>
