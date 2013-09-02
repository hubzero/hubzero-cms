<p>
	You have <strong><?= FormHelper::timeDiff($realLimit*60) ?></strong> to complete this form.
	There are <strong><?= $this->pdf->getQuestionCount() ?></strong> questions.
	<? if ($this->dep->getAllowedAttempts() > 1) : ?>
		You are allowed <strong><?= $this->dep->getAllowedAttempts() ?></strong> attempts.
		This is your <strong><?= FormHelper::toOrdinal((int)$this->resp->getAttemptNumber()) ?></strong> attempt.
	<? endif; ?>
</p>
<? if ($realLimit == $limit): ?>
	<p><em>Time will begin counting when you click 'Continue' below.</em></p>
<? else: ?>
	<p><em>Time is already running because the form is close to expiring!</em></p>
<? endif; ?>
<form action="<?php echo JRoute::_($this->base); ?>" method="post">
	<fieldset>
		<input type="hidden" name="task" value="startWork" />
		<input type="hidden" name="crumb" value="<?= $this->dep->getCrumb() ?>" />
		<input type="hidden" name="attempt" value="<?= (int)$this->resp->getAttemptNumber() ?>" />
		<input type="hidden" name="controller" value="form" />
		<?= isset($_GET['tmpl']) ? '<input type="hidden" name="tmpl" value="'.str_replace('"', '&quot;', $_GET['tmpl']).'" />' : '' ?>
		<button type="submit">Continue</button>
	</fieldset>
</form>
