<?php $hostname = Request::root(); ?>

<style>
.autogen ul {
	list-style: none;
	padding: 0;
	margin: 0;
}
.autogen li {
	list-style: none;
}

.autogen-container {
	padding: 0 0 10px 0;
	margin: 0 0 10px 0;
}
</style>

<ul class="autogen">
<?php foreach ($this->object as $o): ?>
	<?php
		if (strpos($o->path, "http") === FALSE)
		{
			$path = Route::url($hostname . $o->path);
		}
		else
		{
			$path = $o->path;
		}
	?>
	<div class="autogen-container">
	<li class="autogen autogen-title"><a href="<?php echo $path; ?>"><?php echo $o->title; ?></a>
		<ul>
			<li class="autogen autogen-date"><?php echo $o->date; ?></li>
			<li class="autogen autogen-body"><?php echo strip_tags($o->body); ?></li>
			<li class="autogen autogen-path"> <a href="<?php echo $path; ?>"><?php echo Lang::txt('Read More'); ?></a></li>
		</ul>
	</li>
	</div>
	<?php endforeach; ?>
</ul>
