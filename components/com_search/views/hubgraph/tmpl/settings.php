<?php
$settings = HubgraphConfiguration::instance();
?>
<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post">
	<?php foreach ($settings as $k => $v): ?>
		<p>
			<label for="field-<?php echo $k ?>">
				<?php echo h($settings->niceKey($k)) ?>:
				<input type="text" name="<?php echo $k ?>" id="field-<?php echo $k ?>" value="<?php echo a($v) ?>" />
			</label>
		</p>
	<?php endforeach; ?>

	<p>
		<input type="hidden" name="controller" value="hubgraph" />
		<input type="hidden" name="task" value="updatesettings" />
		<input type="hidden" name="nonce" value="<?php createNonce() ?>" />
		<button type="submit" value="submit">Save</button>
	</p>
</form>
