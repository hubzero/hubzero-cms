<?php
$this->css();

$this->js()
	->js('handlebars', 'system');
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_DRWHO'); ?>: <?php echo Lang::txt('COM_DRWHO_SEASONS'); ?></h2>
</header>

<section class="main section">
	<ul class="sub-menu">
		<li>
			<a class="simple" data-target="#section-content" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=one'); ?>">
				One
			</a>
		</li>
		<li>
			<a class="simple" data-target="#section-content" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=two'); ?>">
				Two
			</a>
		</li>
		<li>
			<a class="verbose" data-target="#section-content" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=three'); ?>">
				Three
			</a>
		</li>
		<li>
			<a class="verbose" data-target="#section-content" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=four'); ?>">
				Four
			</a>
		</li>
		<li>
			<a class="api" data-target="#section-content" data-source="/api/blog/list" href="#five">
				Five (API data)
			</a>
		</li>
	</ul>

	<div id="section-content">
		<p>Click a link above to load the data</p>
	</div>

	<script id="new-row" type="text/x-handlebars-template">
		<div class="row grid" id="row-{{index}}">
			<div class="col span1">
				{{id}}
			</div>
			<div class="col span4">
				{{title}}
			</div>
			<div class="col span7 omega">
				{{url}}
			</div>
		</div>
	</script>
</section>