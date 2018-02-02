<?php // no direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('jquery.fancybox.css', 'system')
     ->js();

// Add projects stylesheet
\Hubzero\Document\Assets::addComponentStylesheet('com_projects');
\Hubzero\Document\Assets::addComponentScript('com_projects');
\Hubzero\Document\Assets::addPluginStylesheet('projects', 'files','uploader');
\Hubzero\Document\Assets::addPluginScript('projects', 'files','jquery.fileuploader.js');
\Hubzero\Document\Assets::addPluginScript('projects', 'files','jquery.queueuploader.js');

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<?php if ($this->pid && !empty($this->project) && $this->project->get('created_by_user') == User::get('id')) { ?>
	<p class="contrib-options">
		<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEED_A_PROJECT'); ?>
		<a href="<?php echo Route::url('index.php?option=com_projects&alias=' . $this->project->get('alias') . '&action=activate'); ?>">
		<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LEARN_MORE'); ?> &raquo;</a>
	</p>
<?php } ?>

<?php
	// Display status message
	$view = new \Hubzero\Component\View(array(
		'base_path' => Component::path('com_projects') . DS . 'site',
		'name'      => 'projects',
		'layout'    => '_statusmsg',
	));
	$view->error = $this->getError();
	$view->msg   = $this->msg;
	echo $view->loadTemplate();
?>

<section id="contrib-section" class="section">
	<?php echo $this->content; ?>
</section><!-- / .section -->