<?php
$baseUrl = '/resources/draft?controller=attachments&no_html=1';
$actionUrl = $baseUrl . '&task=create';
?>
<div class="col span">
	<input id="resource-finder" type="text" autocomplete="off" placeholder="Search Resources by ID or Title." data-script="<?php echo '/api/v1.1' . Route::url('index.php?option=com_resources&task=autocomplete');?>" />
	<a href="<?php echo $actionUrl;?>" class="btn" id="add-child" data-pid="<?php echo $this->id;?>" data-childid="" disabled>Add</a>
</div>
