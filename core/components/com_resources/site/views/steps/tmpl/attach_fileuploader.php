<?php
	$base = rtrim(Request::base(true), '/');
?>
<div class="col span-half">
	<div id="ajax-uploader" data-action="index.php?option=com_resources&amp;no_html=1&amp;controller=attachments&amp;task=save&amp;pid=<?php echo $this->id; ?>" data-list="index.php?option=com_resources&amp;no_html=1&amp;controller=attachments&amp;pid=<?php echo $this->id; ?>">
	</div>
</div><!-- / .col span-half -->
<div class="col span-half omega">
	<div id="link-adder" data-action="index.php?option=com_resources&amp;controller=attachments&amp;no_html=1&amp;task=create&amp;pid=<?php echo $this->id; ?>&amp;url=" data-list="index.php?option=com_resources&amp;controller=attachments&amp;no_html=1&amp;pid=<?php echo $this->id; ?>">
	</div>
</div><!-- / .col span-half omega -->
