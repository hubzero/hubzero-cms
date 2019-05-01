<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('form.css')
     ->css('tablesorter.themes.blue.css', 'system')
     ->js('timepicker.js')
     ->js('deploy.js')
     ->js('jquery.tablesorter.min', 'system');
?>

<section class="main section courses-form">
	<form action="<?php echo Route::url($this->base); ?>" method="post" id="deployment">
		<?php require 'deployment_form.php'; ?>
		<fieldset>
			<input type="hidden" name="controller" value="form" />
			<input type="hidden" name="task" value="createDeployment" />
			<input type="hidden" name="formId" value="<?php echo $this->pdf->getId() ?>" />
			<?php if ($tmpl = Request::getWord('tmpl', false)): ?>
				<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
			<?php endif; ?>
			<div class="navbar">
				<div><a href="<?php echo Request::base(true); ?>/courses/form" id="cancel"><?php echo Lang::txt('JCANCEL'); ?></a></div>
				<button id="submit" type="submit"><?php echo Lang::txt('COM_COURSES_CREATE_DEPLOYMENT'); ?></button>
			</div>
		</fieldset>
	</form>
</section>