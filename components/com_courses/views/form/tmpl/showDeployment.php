<h2>Deployment: <?= htmlentities($this->title) ?></h2>
<p class="distribution-link">Link to distribute: <a href="<?= $this->dep->getLink() ?>"><?= $this->dep->getLink() ?></a><span class="state <?= $this->dep->getState() ?>"><?= $this->dep->getState() ?></span></p>
<form action="/courses" method="post" id="deployment">	
	<? require 'deployment_form.php'; ?>
	<fieldset>
		<input type="hidden" name="controller" value="form" />
		<input type="hidden" name="task" value="updateDeployment" />
		<input type="hidden" name="formId" value="<?= $this->pdf->getId() ?>" />
		<input type="hidden" name="deploymentId" value="<?= $this->dep->getId() ?>" />
		<button type="submit">Update deployment</button>
	</fieldset>
</form>
<h3>Results</h3>
<table class="tablesorter">
	<thead>
		<tr><th>Name</th><th>Email</th><th>Version</th><th>Score</th><th>Submission date</th></tr>
	</thead>
	<tbody>
		<? foreach ($this->dep->getResults() as $row): ?>
			<tr><td><?= htmlentities($row['name']); ?></td><td><?= htmlentities($row['email']) ?></td><td><?= $row['version'] ?></td><td><?= number_format($row['score'], 1) ?></td><td><?= date('Y-m-d H:i', strtotime($row['finished'])) ?></td></tr>
		<? endforeach; ?>
	</tbody>
</table>
