<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$this->css()
     ->css('jquery.fancybox.css', 'system')
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<?php if ($this->pid && $this->project && $this->project->created_by_user == $this->uid) { ?>
	<p class="contrib-options">
		<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEED_A_PROJECT'); ?>
		<a href="<?php echo Route::url('index.php?option=com_projects&alias=' . $this->project->alias . '&action=activate'); ?>">
		<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LEARN_MORE'); ?> &raquo;</a>
	</p>
<?php } ?>

<?php
	// Display status message
	$view = new \Hubzero\Component\View(array(
		'base_path' => PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'site',
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