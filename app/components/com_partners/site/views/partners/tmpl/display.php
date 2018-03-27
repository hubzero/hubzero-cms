<?php
// this is a very basic cards display for the partners, needs to be edited before it is pushed to the real world
// Push CSS to the document
//
// The css() method provides a quick and convenient way to attach stylesheets.
//
// 1. The name of the stylesheet to be pushed to the document (file extension is optional).
//    If no name is provided, the name of the component or plugin will be used. For instance,
//    if called within a view of the component com_tags, the system will look for a stylesheet named tags.css.
//
// 2. The name of the extension to look for the stylesheet. For components, this will be
//    the component name (e.g., com_tags). For plugins, this is the name of the plugin folder
//    and requires the third argument be passed to the method.
//
// Method chaining is also allowed.
// $this->css()
//      ->css('another');

$this->css('cards');

// Similarly, a js() method is available for pushing javascript assets to the document.
// The arguments accepted are the same as the css() method described above.
//
$this->js('cards');
$this->js('https://use.fontawesome.com/88cd5351e6.js');

// Set the document title
//
// This sets the <title> tag of the document and will overwrite any previous
// title set. To append or modify an existing title, it must be retrieved first
// with $title = Document::getTitle();
Document::setTitle(Lang::txt('COM_PARTNERS'));

// Set the pathway (breadcrumbs)
//
// Breadcrumbs are displayed via a breadcrumbs module and may or may not be enabled for
// all hubs and/or templates. In general, it's good practice to set the pathway
// even if it's unknown if hey will be displayed or not.
// Pathway::append(
//	Lang::txt('COM_PARTNERS'),  // Text to display
//	'index.php?option=' . $this->option  // Link. Route::url() not needed.
// );
Pathway::append(
	Lang::txt('COM_PARTNERS'),
	'index.php?option=' . $this->option . '&controller=' . $this->controller
);
?>
<header id="content-header">
	<h2>Our Partners</h2>
</header>

<!-- Need to add checks if metadata is available before displaying -->
<section class="main section">
	<h2><?php echo Lang::txt('COM_PARTNERS_SPONSORED'); ?></h2>
	<form class="section-inner" action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="get">
		<div class="subject">
			<div class="cards">
				<?php foreach ($this->sponsored as $record) {
					if ($record->get('state')) { // Display only if published
						$this->view('_card')
							 ->set('option', $this->option)
							 ->set('controller', $this->controller)
							 ->set('record', $record)
							 ->display();
					} 
				} ?>
			</div>
		</div>
	</form>
	<h2><?php echo Lang::txt('COM_PARTNERS_FEATURED'); ?></h2>
		<form class="section-inner" action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="get">
		<div class="subject">
			<div class="cards">
				<?php foreach ($this->featured as $record) {
					if ($record->get('state')) { // Display only if published
						$this->view('_card')
							 ->set('option', $this->option)
							 ->set('controller', $this->controller)
							 ->set('record', $record)
							 ->display();
					} 
				} ?>
			</div>
		</div>
	</form>
	<h2><?php echo Lang::txt('COM_PARTNERS_OTHER'); ?></h2>
		<form class="section-inner" action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="get">
		<div class="subject">
			<div class="cards">
				<?php foreach ($this->other as $record) {
					if ($record->get('state')) { // Display only if published
						$this->view('_card')
							 ->set('option', $this->option)
							 ->set('controller', $this->controller)
							 ->set('record', $record)
							 ->display();
					} 
				} ?>
			</div>
		</div>
	</form>
</section>
