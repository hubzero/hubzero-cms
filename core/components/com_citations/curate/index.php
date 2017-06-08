<?php $mediaRoot = str_replace(PATH_ROOT, '', __DIR__); ?>
<div id="app" class="vuex-seed" data-visualizations="<?php echo str_replace('"', '&quot;', json_encode($visualizations)); ?>">
	<app />
</div>
<script src="<?php echo $mediaRoot; ?>/dist/transliteration.min.js"></script>
<script src="<?php echo $mediaRoot; ?>/dist/build.js"></script>
