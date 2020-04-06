<?php
/**
 * Template Name: Sidebar Template
 *
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// define base path (without doc root)
$base = rtrim(str_replace(PATH_ROOT, '', __DIR__), DS);

// define base url for links
$baseLink = 'index.php?option=com_groups&cn=' . $this->group->get('cn');

// check to see if were supposed to no display html (template frame)
$no_html = Request::getInt('no_html', 0);

// add stylesheets and scripts
Document::addStyleSheet("https://fonts.googleapis.com/css?family=Martel:200");
Document::addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css');
Document::addStyleSheet($base . '/assets/css/main.css?v=' . filemtime(__DIR__ . '/assets/css/main.css'));
Document::addScript($base . '/assets/js/main.js?v=' . filemtime(__DIR__ . '/assets/js/main.js'));
Document::addScript($base . '/assets/js/sidebar.js?v=' . filemtime(__DIR__ . '/assets/js/sidebar.js'));
Document::addScript($base . '/assets/js/ResizeSensor.js');
Document::addScript($base . '/assets/js/ElementQueries.js');
?>

<!-- begin: modify css for banner image -->
<?php $uploads = rtrim(str_replace(PATH_ROOT, '', __DIR__), 'template') . 'uploads'; ?>
<style>
.super-group-header-wrap {
	<?php
	if (file_exists(PATH_ROOT . $uploads . DS . "banner.jpg")):
		echo "background-image: url(" . $uploads . DS . "banner.jpg);";
	elseif (file_exists(PATH_ROOT . $uploads . DS . "banner.png")):
		echo "background-image: url(" . $uploads . DS . "banner.png);";
	else:
		echo "background-image: none;";
	endif;
	?>
}
</style>
<!-- end: modify css for banner image -->

<!-- begin: modify favicon if in upload folder -->
<?php
if (file_exists(PATH_ROOT . $uploads . DS . "favicon.ico")):
	$favicon = $uploads . DS . "favicon.ico";
else:
	$favicon = "/app/templates/qubes/favicon.ico";
endif;
?>

<script>
$("[rel='shortcut icon']")[0].setAttribute('href', "<?php echo $favicon; ?>");
</script>
<!-- end: modify favicon if in upload folder -->

<?php if (!$no_html) : ?>
<group:include type="content" scope="before" />

<div class="super-group-body-wrap group-<?php echo $this->group->get('cn'); ?>">
	<div class="super-group-body">
		<?php include_once 'includes/header.php'; ?>

		<div class="super-group-content-wrap">

			<!-- ###  Start Sideber ### -->
			<!-- Sidebar -->
			<nav id="sidebar-wrapper" aria-label="secondary navigation">
				<?php include_once 'includes/sidebar-menu.php'; ?>
			</nav><!-- /#sidebar-wrapper -->
			<!-- ###  End Sideber ### -->

			<main id="maincontent" class="super-group-content group_<?php echo $this->tab; ?>">
				<?php
				$title = (isset($this->page) && $this->page->get('title')) ? '' : Lang::txt('PLG_GROUPS_' . strtoupper($this->tab));
				$title = ($title == 'PLG_GROUPS_' . strtoupper($this->tab) ? ucfirst($this->tab) : $title);
				if ($title != '') :
					?>
				<h2><?php echo $title; ?></h2>
			<?php endif; ?>
			<!-- <?php endif; ?> -->
			<!-- ###  Start Content Include  ### -->
			<group:include type="content" />
			<!-- ###  End Content Include  ### -->
			<?php if (!$no_html) : ?>
			</main><!-- /.super-group-content -->

		</div><!-- /.super-group-content-wrap -->

		<?php include_once 'includes/footer.php'; ?>
	</div>
</div>

<group:include type="googleanalytics" account="" />
<?php endif; ?>
