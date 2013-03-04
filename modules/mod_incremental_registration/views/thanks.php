<div id="overlay"></div>
<div id="questions">
	<p>Thank you! 
	<?php if ($award): ?>
		You have been awarded <strong><?php echo $award; ?></strong> for your participation. 
	<?php endif; ?>
	 You will be directed back where you were in a few seconds.</p>
	<a href="<?php echo isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['REDIRECT_REQUEST_URI']; ?>">Click here if you are not redirected</a>
	<script type="text/javascript">
		setTimeout(function() {
			var divs = ['overlay', 'questions'];
			for (var idx = 0; idx < divs.length; ++idx) {
				var div = document.getElementById(divs[idx]);
				div.parentNode.removeChild(div);
			}
		}, 4000);
	</script>
</div>
</div>
