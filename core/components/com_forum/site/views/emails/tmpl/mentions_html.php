<?php

// No direct access
defined('_HZEXEC_') or die();

$comment = $this->comment;
$commentNoTags = $this->commentNoTags;

$postLink = $this->postLink;
$postAuthor = $this->postAuthor;
?>

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td height="30"></td>
		</tr>
	</tbody>
</table>
<!-- End Spacer -->

<table id="course-discussions" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
	<tr>
		<td style="font-size: 16px; padding: 10px 0;">
			<?php echo $postAuthor; ?> mentioned you on a forum thread with the following post:
		</td>
	</tr>
	<tr style="border:1px solid #eaeaea;">
		<td style="font-size: 16px; padding: 10px 20px; margin: 20px 0;">
			<?php echo $commentNoTags; ?>
		</td>
	</tr>
	<tr>
		<td style="font-size: 16px; padding: 10px 0;">
			Please see the comment on this <a href="<?php echo $postLink; ?>" target="_blank">forum thread</a>.
		</td>
	</tr>
</table>

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td height="30"></td>
		</tr>
	</tbody>
</table>
<!-- End Spacer -->
