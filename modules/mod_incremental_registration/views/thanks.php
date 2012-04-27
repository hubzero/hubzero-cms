<div id="overlay"></div>
<div id="questions">
	<p>Thank you! You have been awarded <strong><?php echo $base_award; ?></strong> for your participation. <?php if ($base_award != $award) echo 'Additionally, you have been awarded <strong>'.($award - $base_award).'</strong> for previously filling in portions of your profile.'; ?> You will be directed back where you were in a few seconds.</p>
	<a href="<?php echo isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['REDIRECT_REQUEST_URI']; ?>">Click here if you are not redirected</a>
	<script type="text/javascript">
		setTimeout(function()
		{
			window.location = window.location;
		}, 6000);
	</script>
</div>
</div>
