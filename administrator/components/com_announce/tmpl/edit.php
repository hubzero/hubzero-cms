<?php
try
{
	$ann = $this->getModel('announcements')->get_current(); 
}
catch (Exception $ex)
{
	$ann = array('title' => '', 'link' => '', 'category' => '', 'active_date' => date('Y/m/d'), 'expiration_date' => '', 'id' => '');
}
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<p>
		Title: <input type="text" name="title" value="<?php echo $ann['title']; ?>" />
	</p>
	<p>
		Link: <input type="text" name="link" value="<?php echo $ann['link']; ?>" />
	</p>
	<p>
		Category: <input type="text" name="category" value="<?php echo $ann['category']; ?>" />
	</p>
	<p>
		Active date: <input type="text" name="active_date" value="<?php echo date('Y/m/d', strtotime($ann['active_date'])); ?>" />
	</p>
	<p>
		Archive date: <input type="text" name="expiration_date" value="<?php echo $ann['expiration_date'] ? date('Y/m/d', strtotime($ann['expiration_date'])) : ''; ?>" />
	</p>
	<input type="hidden" name="id" value="<?php echo $ann['id']; ?>" />
	<input type="hidden" name="option" value="<?php echo $option ?>" />
	<input type="hidden" name="task" value="save" />
</form>
