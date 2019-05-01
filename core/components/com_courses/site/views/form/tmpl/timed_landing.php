<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<p>
	You have <strong><?php echo \Components\Courses\Helpers\Form::timeDiff($realLimit*60) ?></strong> to complete this form.
	There are <strong><?php echo $this->pdf->getQuestionCount() ?></strong> questions.
	<?php if ($this->dep->getAllowedAttempts() > 1) : ?>
		You are allowed <strong><?php echo $this->dep->getAllowedAttempts() ?></strong> attempts.
		This is your <strong><?php echo \Components\Courses\Helpers\Form::toOrdinal((int)$this->resp->getAttemptNumber()) ?></strong> attempt.
	<?php endif; ?>
</p>
<?php if ($realLimit == $limit): ?>
	<p><em>Time will begin counting when you click 'Continue' below.</em></p>
<?php else: ?>
	<p><em>Time is already running because the form is close to expiring!</em></p>
<?php endif; ?>
<form action="<?php echo Route::url($this->base); ?>" method="post">
	<fieldset>
		<input type="hidden" name="task" value="startWork" />
		<input type="hidden" name="crumb" value="<?php echo $this->dep->getCrumb() ?>" />
		<input type="hidden" name="attempt" value="<?php echo (int)$this->resp->getAttemptNumber() ?>" />
		<input type="hidden" name="controller" value="form" />
		<?php echo isset($_GET['tmpl']) ? '<input type="hidden" name="tmpl" value="'.str_replace('"', '&quot;', $_GET['tmpl']).'" />' : '' ?>
		<button type="submit">Continue</button>
	</fieldset>
</form>
