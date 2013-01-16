<div id="content-header" class="full">
	<h2>Deploy: <?= htmlentities($this->title) ?></h2>
</div>

<div class="main section">
	<form action="/courses" method="post" id="deployment">
		<? require 'deployment_form.php'; ?>
		<fieldset>
			<input type="hidden" name="controller" value="form" />
			<input type="hidden" name="task" value="createDeployment" />
			<input type="hidden" name="formId" value="<?= $this->pdf->getId() ?>" />
			<input type="hidden" name="tmpl" value="<?php echo JRequest::getWord('tmpl', ''); ?>" />
			<button type="submit">Create deployment</button>
		</fieldset>
	</form>
</div>