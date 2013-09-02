<div id="content-header" class="full">
	<h2>Deploy: <?= htmlentities($this->title) ?></h2>
</div>

<div class="main section courses-form">
	<form action="<?php echo JRoute::_($this->base); ?>" method="post" id="deployment">
		<? require 'deployment_form.php'; ?>
		<fieldset>
			<input type="hidden" name="controller" value="form" />
			<input type="hidden" name="task" value="createDeployment" />
			<input type="hidden" name="formId" value="<?= $this->pdf->getId() ?>" />
			<? if($tmpl = JRequest::getWord('tmpl', false)): ?>
				<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
			<? endif; ?>
			<div class="navbar">
				<div><a href="/courses/form" id="cancel">Cancel</a></div>
				<button id="submit" type="submit">Create deployment</button>
			</div>
		</fieldset>
	</form>
</div>