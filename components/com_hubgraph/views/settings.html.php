<?
$settings = HubgraphConfiguration::instance();
?>
<form action="/hubgraph" method="post">
	<? foreach ($settings as $k=>$v): ?>
		<p><label><?= h($settings->niceKey($k)) ?>: <input name="<?= $k ?>" value="<?= a($v) ?>" /></label></p>
	<? endforeach; ?>
	<p>
		<input type="hidden" name="task" value="updateSettings" />
		<input type="hidden" name="nonce" value="<?= createNonce() ?>" />
		<button type="submit" value="submit">Save</button>
	</p>
</form>
