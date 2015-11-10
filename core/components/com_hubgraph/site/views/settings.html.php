<?php 
$settings = HubgraphConfiguration::instance();
?>
<form action="/hubgraph" method="post">
	<?php foreach ($settings as $k=>$v): ?>
		<p><label><?php echo h($settings->niceKey($k)); ?>: <input name="<?php echo $k; ?>" value="<?php echo a($v); ?>" /></label></p>
	<?php endforeach; ?>
	<p>
		<input type="hidden" name="task" value="updateSettings" />
		<input type="hidden" name="nonce" value="<?php echo createNonce(); ?>" />
		<button type="submit" value="submit">Save</button>
	</p>
</form>
