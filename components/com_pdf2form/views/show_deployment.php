<?
$title = 'Deployment: '.htmlentities($pdf->getTitle());
$doc->setTitle($title);
$doc->addScript('/components/com_pdf2form/resources/deploy.js');
$doc->addScript('/components/com_pdf2form/resources/timepicker.js');
$doc->addScript('/components/com_pdf2form/resources/tablesorter/jquery.tablesorter.min.js');
$doc->addStyleSheet('/components/com_pdf2form/resources/tablesorter/themes/blue/style.css');
$doc->addStyleSheet('/plugins/system/jquery/css/jquery-ui-1.8.6.custom.css');
$path->addItem($title, $_SERVER['REQUEST_URI']);
?>
<h2>Deployment: <?= htmlentities($title) ?></h2>
<p class="distribution-link">Link to distribute: <a href="<?= $dep->getLink() ?>"><?= $dep->getLink() ?></a><span class="state <?= $dep->getState() ?>"><?= $dep->getState() ?></span></p>
<form action="/pdf2form" method="post" id="deployment">
	<? require 'deployment_form.php'; ?>
	<fieldset>
		<input type="hidden" name="task" value="updateDeployment" />
		<input type="hidden" name="formId" value="<?= $pdf->getId() ?>" />
		<input type="hidden" name="deploymentId" value="<?= $dep->getId() ?>" />
		<button type="submit">Update deployment</button>
	</fieldset>
</form>
<h3>Results</h3>
<table class="tablesorter">
	<thead>
		<tr><th>Name</th><th>Email</th><th>Version</th><th>Score</th><th>Submission date</th></tr>
	</thead>
	<tbody>
		<? foreach ($dep->getResults() as $row): ?>
			<tr><td><?= htmlentities($row['name']); ?></td><td><?= htmlentities($row['email']) ?></td><td><?= $row['version'] ?></td><td><?= number_format($row['score'], 1) ?></td><td><?= date('Y-m-d H:i', strtotime($row['finished'])) ?></td></tr>
		<? endforeach; ?>
	</tbody>
</table>
