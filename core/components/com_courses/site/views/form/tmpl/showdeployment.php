<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('form.css')
     ->css('tablesorter.themes.blue.css', 'system')
     ->js('showdeployment.js')
     ->js('timepicker.js')
     ->js('deploy.js')
     ->js('jquery.tablesorter.min', 'system');
?>
<header id="content-header">
	<h2>Deployment: <?php echo $this->escape($this->title) ?></h2>
</header>

<?php $link = Route::url($this->base . '&task=form.complete&crumb=' . $this->dep->getCrumb()); ?>

<section class="main section courses-form">
	<p class="distribution-link">Link to distribute: <a href="<?php echo $link ?>"><?php echo $link ?></a><span class="state <?php echo $this->dep->getState() ?>"><?php echo $this->dep->getState() ?></span></p>
	<form action="<?php echo Route::url($this->base); ?>" method="post" id="deployment">
		<?php require 'deployment_form.php'; ?>
		<fieldset>
			<input type="hidden" name="controller" value="form" />
			<input type="hidden" name="task" value="updateDeployment" />
			<input type="hidden" name="formId" value="<?php echo $this->pdf->getId() ?>" />
			<input type="hidden" name="deploymentId" value="<?php echo $this->dep->getId() ?>" />
			<input type="hidden" name="id" value="<?php echo $this->dep->getId() ?>" />
			<?php if ($tmpl = Request::getWord('tmpl', false)): ?>
				<input type="hidden" name="tmpl" value="<?php echo $tmpl ?>" />
			<?php endif; ?>
			<div class="navbar">
				<div><a href="<?php echo Request::base(true); ?>/courses/form" id="done">Done</a></div>
				<button type="submit">Update deployment</button>
			</div>
		</fieldset>
	</form>
</section>