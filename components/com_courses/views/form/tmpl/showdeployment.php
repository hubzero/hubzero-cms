<div id="content-header" class="full">
	<h2>Deployment: <?= htmlentities($this->title) ?></h2>
</div>

<div class="main section courses-form">
	<p class="distribution-link">Link to distribute: <a href="<?= $this->dep->getLink() ?>"><?= $this->dep->getLink() ?></a><span class="state <?= $this->dep->getState() ?>"><?= $this->dep->getState() ?></span></p>
	<form action="/courses" method="post" id="deployment">	
		<? require 'deployment_form.php'; ?>
		<fieldset>
			<input type="hidden" name="controller" value="form" />
			<input type="hidden" name="task" value="updateDeployment" />
			<input type="hidden" name="formId" value="<?= $this->pdf->getId() ?>" />
			<input type="hidden" name="deploymentId" value="<?= $this->dep->getId() ?>" />
			<input type="hidden" name="id" value="<?= $this->dep->getId() ?>" />
			<? if ($tmpl = JRequest::getWord('tmpl', false)): ?>
				<input type="hidden" name="tmpl" value="<?= $tmpl ?>" />
			<? endif; ?>
			<div class="navbar">
				<div><a href="/courses/form" id="done">Done</a></div>
				<button type="submit">Update deployment</button>
			</div>
		</fieldset>
	</form>
</div>