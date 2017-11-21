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
	<h2><?php echo Lang::txt('COM_PARTNERS'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-prev btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>"><?php echo Lang::txt('COM_PARTNERS_MAIN'); ?></a>
		</p>
	</div>
</header>

<!-- Need to add checks if metadata is available before displaying -->
<section class="main section">
	<form class="section-inner" action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="get">
		<div class="subject">
			<div class="cards">
				<?php foreach ($this->records as $record) {
					if ($record->get('state')) { // Display only if published ?>
				    <div class="card [ is-collapsed ]">
      					<div class="card__inner [ js-expander ]">
      						<img src="<?php echo 'app/site/media/images/partners/' . $record->get('logo_img') ?>" alt="<?php echo $record->get('name'); ?>" class="card-logo">
      					</div>
      					<div class="card__expander">
        					<i class="fa fa-close [ js-collapser ]" aria-hidden="true"></i>
        					<div class="inner-expander">
        						<?php echo 'About: ' . '<p>' . $record->get('about') . '</p>'; ?>
        						<div class="liason">
        							Partner Liaison:
        							<p><?php echo $record->get('partner_liason_primary'); ?></p>
        						</div>
        						<div class="liason">
        							QUBES Liaison:
        							<p><?php echo $record->get('QUBES_liason_primary'); ?></p>
        						</div>
        					</div>
        					<div class="inner-expander">
        						<?php echo 'Activities: ' . '<p>' . $record->get('activities') . '</p>'; ?>
        						<div class="social">
        							<a class="card-link" href="<?php echo Route::url('groups' . DS . $record->get('groups_cn')); ?>">Learn more</a><br>
        							<a class="social-icon" href="https://twitter.com/<?php echo $record->get('twitter_handle'); ?>" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a>
        						</div>
        					</div>
        				</div>
      				</div>
				<?php } } ?>
			</div>
		</div>
		<aside class="aside">
			<fieldset>
				<select name="partner_type">
					<option value=""><?php echo Lang::txt('COM_PARTNERS_PARTNER_TYPES_ALL'); ?></option>
					<?php foreach (\Components\Partners\Models\Partner_type::all() as $partner_type) { ?>
						<option<?php if ($this->filters['partner_type'] == $partner_type->get('id')) { echo ' selected="selected"'; } ?> value="<?php echo $this->escape($partner_type->get('id')); ?>"><?php echo $this->escape($partner_type->get('external')); ?></option>
					<?php } ?>
				</select>
				<input type="submit" value="<?php echo Lang::txt('COM_PARTNERS_GO'); ?>" />
			</fieldset>

		</aside>
	</form>
</section>
